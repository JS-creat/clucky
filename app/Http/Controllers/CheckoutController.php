<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\Departamento;
use App\Models\DetallePedido;
use App\Models\Pedido;
use App\Models\TipoEntrega;
use App\Models\TipoDocumento;
use App\Models\Agencia;
use App\Services\FacturacionService;
use App\Mail\BoletaEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;

class CheckoutController extends Controller
{
    // ── Vista del checkout ───────────────────────────────────────────────────

    public function index()
    {
        if (Session::has('carrito')) {
            $carrito = Carrito::firstOrCreate(['id_usuario' => Auth::id()]);

            foreach (Session::get('carrito', []) as $idVariante => $item) {
                \App\Models\DetalleCarrito::updateOrCreate(
                    ['id_carrito' => $carrito->id_carrito, 'id_variante' => $idVariante],
                    ['cantidad'   => $item['cantidad']]
                );
            }

            Session::forget('carrito');
        }

        $carrito = Carrito::with('detalles.variante.producto')
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

    // ── Confirmar: Crea o REUTILIZA el pedido y genera preferencia ──────────

    public function confirmar(Request $request)
    {
        $request->validate([
            'id_tipo_entrega' => 'required|integer|in:1,2',
            'id_distrito'     => 'nullable|integer|exists:distrito,id_distrito',
        ]);

        if ((int) $request->id_tipo_entrega === 2 && empty($request->id_distrito)) {
            return back()->withInput()->with('error', 'Debes seleccionar un distrito para el envío.');
        }

        $carrito = Carrito::with('detalles.variante.producto')
            ->where('id_usuario', Auth::id())
            ->firstOrFail();

        if ($carrito->detalles->isEmpty()) {
            return back()->with('error', 'Tu carrito está vacío.');
        }

        // Validar stock
        foreach ($carrito->detalles as $detalle) {
            if ($detalle->cantidad > $detalle->variante->stock) {
                return back()->with('error', "El producto {$detalle->variante->producto->nombre_producto} no tiene suficiente stock disponible.");
            }
        }

        // Calcular total
        $total = 0;
        foreach ($carrito->detalles as $detalle) {
            $producto = $detalle->variante->producto;
            $precio   = $producto->precio_oferta ?? $producto->precio;
            $total   += $precio * $detalle->cantidad;
        }

        $costoEnvio = (float) $request->costo_envio;
        $total     += $costoEnvio;

        // Crear o reutilizar pedido en BD
        $pedido  = null;
        $agencia = null;

        if ($request->id_tipo_entrega == 2 && $request->id_agencia) {
            $agencia = Agencia::find($request->id_agencia);
        }

        DB::transaction(function () use ($carrito, $request, $total, &$pedido, $agencia) {

            $pedidoExistente = Pedido::where('id_usuario', Auth::id())
                ->where('estado_pedido', 'Pendiente')
                ->first();

            $datosPedido = [
                'total_pedido'    => $total,
                'id_tipo_entrega' => $request->id_tipo_entrega,
                'id_distrito'     => $request->id_tipo_entrega == 2 ? $request->id_distrito : null,
                'id_agencia'      => $request->id_tipo_entrega == 2 ? $request->id_agencia : null,
                'costo_envio'     => $request->costo_envio ?? 0,
                'nombre_agencia'  => $agencia?->nombre_agencia,
                'direccion'       => $agencia?->direccion,
            ];

            if ($pedidoExistente) {
                $pedidoExistente->update($datosPedido);
                DetallePedido::where('id_pedido', $pedidoExistente->id_pedido)->delete();
                $pedido = $pedidoExistente;
            } else {
                $pedido = Pedido::create(array_merge($datosPedido, [
                    'numero_pedido' => $this->generarNumeroPedido(),
                    'estado_pedido' => 'Pendiente',
                    'id_usuario'    => Auth::id(),
                ]));
            }

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
            }
        });

        // Mercado Pago Payload
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

        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
        $client = new PreferenceClient();

        try {
            $preference = $client->create([
                "items"              => $items,
                "payer"              => [
                    "name"  => Auth::user()->nombres,
                    "email" => Auth::user()->correo ?? Auth::user()->email,
                ],
                "back_urls"          => [
                    "success" => route('pago.exito'),
                    "failure" => route('pago.fallo'),
                    "pending" => route('pago.pendiente'),
                ],
                "auto_return"        => "approved",
                "external_reference" => (string) $pedido->id_pedido,
            ]);
        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            Log::error('Error creando preferencia MercadoPago', [
                'mensaje'   => $e->getMessage(),
                'respuesta' => $e->getApiResponse()->getContent(),
            ]);

            return back()->with('error', 'No se pudo iniciar el pago. Intenta nuevamente.');
        }

        return redirect($preference->init_point);
    }

    // ── Pago Exitoso: Procesa boleta SUNAT y correo ──────────────────────────

    public function pagoExito(Request $request, FacturacionService $facturacion)
    {
        $idPedido = $request->query('external_reference');
        $pedido   = Pedido::with('detalles.variante.producto', 'usuario')->findOrFail($idPedido);

        if ($pedido->estado_pedido !== 'Pagado') {
            // 1. Cambiar estado y limpiar carrito
            $pedido->update(['estado_pedido' => 'Pagado']);
            Carrito::where('id_usuario', $pedido->id_usuario)->delete();

            // 2. Preparar cliente para SUNAT
            $usuario = $pedido->usuario ?? Auth::user();
            $clienteData = [
                'tipo_doc' => '1',
                'num_doc'  => $usuario->dni ?? '00000000',
                'nombre'   => trim(($usuario->nombres ?? '') . ' ' . ($usuario->apellidos ?? '')),
            ];

            // 3. Emitir comprobante electrónico
            $resFactura = $facturacion->emitirBoleta($pedido, $clienteData);

            if ($resFactura['success'] && ($resFactura['data']['sunatResponse']['success'] ?? false)) {
                $data = $resFactura['data'];

                if (!empty($data['sunatResponse']['cdrZip'])) {
                    Storage::put("comprobantes/CDR-B001-{$pedido->id_pedido}.zip", base64_decode($data['sunatResponse']['cdrZip']));
                }

                $pedido->update([
                    'serie_boleta'       => 'B001',
                    'correlativo_boleta' => $pedido->id_pedido,
                ]);

                // 4. Obtener PDF y enviar correo de forma segura
                $correoDestino = $usuario->correo ?? $usuario->email ?? null;

                if ($correoDestino) {
                    $pdfContent = $facturacion->obtenerPdf($pedido, $clienteData, '03');

                    if ($pdfContent) {
                        try {
                            Mail::to($correoDestino)->send(new BoletaEmail($pedido, $pdfContent));
                        } catch (\Throwable $e) {
                            Log::error("Error enviando correo de boleta pedido #{$pedido->id_pedido}: " . $e->getMessage());
                        }
                    }
                }
            }
        }

        return view('carrito.exito', compact('pedido'));
    }

    // ── Descargar / Ver Boleta en PDF ────────────────────────────────────────

    public function verBoleta($idPedido, FacturacionService $facturacion)
    {
        $pedido  = Pedido::with(['detalles.variante.producto', 'usuario'])->findOrFail($idPedido);
        $usuario = $pedido->usuario ?? Auth::user();

        $clienteData = [
            'tipo_doc' => '1',
            'num_doc'  => $usuario->dni ?? '00000000',
            'nombre'   => trim(($usuario->nombres ?? '') . ' ' . ($usuario->apellidos ?? '')),
        ];

        $pdfContent = $facturacion->obtenerPdf($pedido, $clienteData, '03');

        if ($pdfContent) {
            return response($pdfContent, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="Boleta-B001-' . $pedido->id_pedido . '.pdf"');
        }

        return back()->with('error', 'No se pudo cargar el PDF de la boleta.');
    }

    // ── Helper ───────────────────────────────────────────────────────────────

    private function generarNumeroPedido(): string
    {
        $fecha       = now()->format('Ymd');
        $cantidad    = Pedido::whereDate('created_at', today())->count() + 1;
        $correlativo = str_pad($cantidad, 3, '0', STR_PAD_LEFT);

        return "BND-{$fecha}-{$correlativo}";
    }
}