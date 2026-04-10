<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\Departamento;
use App\Models\DetallePedido;
use App\Models\Pedido;
use App\Models\ProductoVariante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\TipoEntrega;
use App\Models\TipoDocumento;
use App\Models\Agencia;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;

class CheckoutController extends Controller
{
    // ── Vista del checkout ───────────────────────────────────────────────────

    public function index()
    {
        if (session()->has('carrito')) {
            $carrito = Carrito::firstOrCreate(['id_usuario' => Auth::id()]);

            foreach (session('carrito') as $idVariante => $item) {
                \App\Models\DetalleCarrito::updateOrCreate(
                    ['id_carrito' => $carrito->id_carrito, 'id_variante' => $idVariante],
                    ['cantidad'   => $item['cantidad']]
                );
            }

            session()->forget('carrito');
        }

        $carrito        = Carrito::with('detalles.variante.producto')
            ->where('id_usuario', Auth::id())
            ->first();
        $departamentos  = Departamento::all();
        $tiposEntrega   = TipoEntrega::where('estado', 1)->get();
        $tiposDocumento = TipoDocumento::all();
        $agencias       = Agencia::where('estado', 1)->get();

        return view('carrito.checkout', compact(
            'carrito',
            'departamentos',
            'tiposEntrega',
            'tiposDocumento',
            'agencias'
        ));
    }


    // ── Confirmar: SOLO crea preferencia, NO guarda pedido ──────────────────

    public function confirmar(Request $request)
    {
        $request->validate([
            'id_tipo_entrega' => 'required|integer|in:1,2',
            'id_distrito'     => 'nullable|integer|exists:distrito,id_distrito',
        ]);

        if ((int) $request->id_tipo_entrega === 2 && empty($request->id_distrito)) {
            return back()
                ->withInput()
                ->with('error', 'Debes seleccionar un distrito para el envío.');
        }

        $carrito = Carrito::with('detalles.variante.producto')
            ->where('id_usuario', Auth::id())
            ->firstOrFail();

        if ($carrito->detalles->isEmpty()) {
            return back()->with('error', 'Tu carrito está vacío.');
        }

        // ── Calcular total ──────────────────────────────────────────────────
        $total = 0;
        foreach ($carrito->detalles as $detalle) {
            $producto = $detalle->variante->producto;
            $precio   = $producto->precio_oferta ?? $producto->precio;
            $total   += $precio * $detalle->cantidad;
        }

        $costoEnvio = (float) $request->costo_envio;
        $total     += $costoEnvio;

        // ── Crear pedido en BD ──────────────────────────────────────────────
        $pedido = null;
        $agencia = null;

        if ($request->id_tipo_entrega == 2 && $request->id_agencia) {
            $agencia = Agencia::find($request->id_agencia);
        }

        DB::transaction(function () use ($carrito, $request, $total, &$pedido, $agencia) {

            $pedido = Pedido::create([
                'numero_pedido'   => $this->generarNumeroPedido(),
                'total_pedido'    => $total,
                'estado_pedido'   => 'Pendiente',
                'id_usuario'      => Auth::id(),
                'id_tipo_entrega' => $request->id_tipo_entrega,
                'id_distrito'     => $request->id_tipo_entrega == 2 ? $request->id_distrito : null,
                'id_agencia'      => $request->id_tipo_entrega == 2 ? $request->id_agencia : null,
                'costo_envio'     => $request->costo_envio ?? 0,
                'nombre_agencia'    => $agencia?->nombre_agencia,
                'direccion' => $agencia?->direccion,
            ]);

            foreach ($carrito->detalles as $detalle) {
                $producto = $detalle->variante->producto;
                $precio   = $producto->precio_oferta ?? $producto->precio;

                DetallePedido::create([
                    'id_pedido'       => $pedido->id_pedido,
                    'id_variante'     => $detalle->id_variante,
                    'cantidad'        => $detalle->cantidad,
                    'precio_unitario' => $precio,
                    'subtotal'        => $precio * $detalle->cantidad,
                ]);

                ProductoVariante::where('id_variante', $detalle->id_variante)
                    ->decrement('stock', $detalle->cantidad);
            }

            $carrito->detalles()->delete();
        });

        // ── Armar ítems para MP ─────────────────────────────────────────────
        $items = [];
        $pedido->load('detalles.variante.producto');

        foreach ($pedido->detalles as $detalle) {
            $producto = $detalle->variante->producto;

            $items[] = [
                "title"       => $producto->nombre_producto . ' (' . $detalle->variante->color . ' - Talla ' . $detalle->variante->talla . ')',
                "quantity"    => (int) $detalle->cantidad,
                "unit_price"  => (float) $detalle->precio_unitario,
                "currency_id" => "PEN",
            ];
        }

        if ($costoEnvio > 0) {
            $items[] = [
                "title"       => "Costo de envío",
                "quantity"    => 1,
                "unit_price"  => $costoEnvio,
                "currency_id" => "PEN",
            ];
        }

        // ── Crear preferencia en MP ─────────────────────────────────────────
        MercadoPagoConfig::setAccessToken(env('MP_ACCESS_TOKEN'));
        $client = new PreferenceClient();

        try {
            $preference = $client->create([
                "items"              => $items,
                "payer"              => [
                    "name"  => Auth::user()->nombres,
                    "email" => Auth::user()->correo,
                ],
                "back_urls"          => [
                    "success" => route('pago.exito'),
                    "failure" => route('pago.fallo'),
                    "pending" => route('pago.pendiente'),
                ],
                "external_reference" => (string) $pedido->id_pedido,
            ]);
        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            dd([
                'mensaje'   => $e->getMessage(),
                'respuesta' => $e->getApiResponse()->getContent(),
            ]);
        }

        return redirect($preference->sandbox_init_point);
    }


    // ── Helper ───────────────────────────────────────────────────────────────

    private function generarNumeroPedido(): string
    {
        $fecha       = now()->format('Ymd');
        $cantidad    = Pedido::whereDate('created_at', today())->count() + 1;
        $correlativo = str_pad($cantidad, 3, '0', STR_PAD_LEFT);

        return "CLK-{$fecha}-{$correlativo}";
    }
}
