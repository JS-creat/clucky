<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agencia;
use App\Models\Carrito;
use App\Models\Cupon;
use App\Models\DetallePedido;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;

class CheckoutApiController extends Controller
{
    public function confirmar(Request $request)
    {
        $request->validate([
            'id_tipo_documento' => 'required|integer|exists:tipo_documento,id_tipo_documento',
            'numero_documento'  => 'required|string|max:20',
            'telefono'          => 'required|string|max:20',
            'id_tipo_entrega'   => 'required|integer|in:1,2',
            'id_distrito'       => 'nullable|integer|exists:distrito,id_distrito',
            'codigo_cupon'      => 'nullable|string|max:50',
        ]);

        if ((int) $request->id_tipo_entrega === 2 && empty($request->id_distrito)) {
            return response()->json([
                'success' => false,
                'message' => 'Debes seleccionar un distrito para el envío.',
            ], 422);
        }

        $usuario = Auth::user();

        $carrito = Carrito::with('detalles.variante.producto')
            ->where('id_usuario', $usuario->id_usuario)
            ->first();

        if (!$carrito || $carrito->detalles->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tu carrito está vacío.',
            ], 422);
        }

        // Validar stock (solo verificar, NO descontar todavía)
        foreach ($carrito->detalles as $detalle) {
            if ($detalle->cantidad > $detalle->variante->stock) {
                return response()->json([
                    'success' => false,
                    'message' => "El producto {$detalle->variante->producto->nombre_producto} no tiene suficiente stock disponible.",
                ], 422);
            }
        }

        $subtotal = 0;
        $costoEnvio = 0;
        $nombreAgencia = null;
        $direccionAgencia = null;
        $tiempoEstimado = null;

        foreach ($carrito->detalles as $detalle) {
            $producto = $detalle->variante->producto;
            $precio   = $producto->precio_oferta ?? $producto->precio;
            $subtotal += $precio * $detalle->cantidad;
        }

        if ((int) $request->id_tipo_entrega === 2) {
            $agencia = Agencia::where('id_distrito', $request->id_distrito)
                ->where('estado', 1)
                ->first();

            if (!$agencia) {
                return response()->json([
                    'success' => false,
                    'message' => 'No existe una agencia activa para el distrito seleccionado.',
                ], 422);
            }

            $costoEnvio = $agencia->costo_envio ?? 0;
            $nombreAgencia = $agencia->nombre_agencia ?? null;
            $direccionAgencia = $agencia->direccion ?? null;
            $tiempoEstimado = $agencia->tiempo_estimado ?? '3-5 días hábiles';
        }

        // Validar y calcular descuento del cupón (sin registrar el uso todavía)
        $cupon = null;
        $montoDescuento = 0;
        $codigoCuponNormalizado = null;

        if (!empty($request->codigo_cupon)) {
            $codigoCuponNormalizado = strtoupper(trim($request->codigo_cupon));
            $cupon = Cupon::vigentes()->porCodigo($codigoCuponNormalizado)->first();

            if (!$cupon) {
                return response()->json([
                    'success' => false,
                    'message' => 'El cupón ingresado no es válido o ya no está vigente.',
                ], 422);
            }

            $verificacion = $cupon->puedeUsar($usuario);
            if (!$verificacion['valido']) {
                return response()->json([
                    'success' => false,
                    'message' => $verificacion['errores'][0],
                ], 422);
            }

            if ($subtotal < $cupon->monto_compra_minima) {
                return response()->json([
                    'success' => false,
                    'message' => 'El monto mínimo de compra para este cupón es S/ '
                        . number_format($cupon->monto_compra_minima, 2),
                ], 422);
            }

            $montoDescuento = $cupon->calcularDescuento($subtotal);
        }

        $total = max(0, $subtotal - $montoDescuento) + $costoEnvio;

        try {
            $pedido = null;

            DB::transaction(function () use (
                $usuario,
                $carrito,
                $request,
                $total,
                $cupon,
                $nombreAgencia,
                $direccionAgencia,
                &$pedido
            ) {
                $usuario->update([
                    'id_tipo_documento' => $request->id_tipo_documento,
                    'numero_documento'  => $request->numero_documento,
                    'telefono'          => $request->telefono,
                ]);

                $pedidoExistente = Pedido::where('id_usuario', $usuario->id_usuario)
                    ->where('estado_pedido', 'Pendiente')
                    ->where('created_at', '>=', now()->subHours(2))
                    ->first();

                $datosPedido = [
                    'total_pedido'      => $total,
                    'id_tipo_entrega'   => $request->id_tipo_entrega,
                    'id_distrito'       => (int) $request->id_tipo_entrega === 2
                        ? $request->id_distrito
                        : null,
                    'nombre_agencia'    => $nombreAgencia,
                    'direccion_agencia' => $direccionAgencia,
                    'id_cupon'          => $cupon?->id_cupon,
                ];

                if ($pedidoExistente) {
                    $pedidoExistente->update(array_merge($datosPedido, [
                        'created_at' => now(),
                    ]));
                    DetallePedido::where('id_pedido', $pedidoExistente->id_pedido)->delete();
                    $pedido = $pedidoExistente;
                } else {
                    $pedido = Pedido::create(array_merge($datosPedido, [
                        'numero_pedido' => $this->generarNumeroPedido(),
                        'estado_pedido' => 'Pendiente',
                        'id_usuario'    => $usuario->id_usuario,
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

            // Armar ítems para Mercado Pago (mismo formato que la web)
            $pedido->load('detalles.variante.producto');
            $items = [];

            foreach ($pedido->detalles as $detalle) {
                $producto = $detalle->variante->producto;
                $items[] = [
                    'title'       => $producto->nombre_producto . ' (' . $detalle->variante->color . ' - Talla ' . $detalle->variante->talla . ')',
                    'quantity'    => (int) $detalle->cantidad,
                    'unit_price'  => (float) $detalle->precio_unitario,
                    'currency_id' => 'PEN',
                ];
            }

            if ($costoEnvio > 0) {
                $items[] = [
                    'title'       => 'Costo de envío',
                    'quantity'    => 1,
                    'unit_price'  => (float) $costoEnvio,
                    'currency_id' => 'PEN',
                ];
            }

            if ($montoDescuento > 0) {
                $items[] = [
                    'title'       => 'Descuento (' . ($cupon?->codigo_cupon ?? $codigoCuponNormalizado) . ')',
                    'quantity'    => 1,
                    'unit_price'  => -1 * (float) $montoDescuento,
                    'currency_id' => 'PEN',
                ];
            }

            MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
            $client = new PreferenceClient();

            $preference = $client->create([
                'items' => $items,
                'payer' => [
                    'name'  => $usuario->nombres,
                    'email' => $usuario->correo,
                ],
                // 🟢 Rutas PÚBLICAS específicas para móvil (el WebView no
                // comparte la sesión web con cookies). Verifican el pago
                // directamente contra la API de Mercado Pago, no confían
                // en nada que venga del cliente.
                'back_urls' => [
                    'success' => route('api.pago.movil.exito'),
                    'failure' => route('api.pago.movil.fallo'),
                    'pending' => route('api.pago.movil.pendiente'),
                ],
                'auto_return'        => 'approved',
                'external_reference' => (string) $pedido->id_pedido,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id_pedido'       => $pedido->id_pedido,
                    'numero_pedido'   => $pedido->numero_pedido,
                    'subtotal'        => $subtotal,
                    'costo_envio'     => $costoEnvio,
                    'monto_descuento' => $montoDescuento,
                    'codigo_cupon'    => $codigoCuponNormalizado,
                    'total_pedido'    => $total,
                    'init_point'      => $preference->init_point,
                ],
            ]);
        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            \Log::error('Error creando preferencia MercadoPago (móvil)', [
                'mensaje'   => $e->getMessage(),
                'respuesta' => $e->getApiResponse()->getContent(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo iniciar el pago. Intenta nuevamente.',
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calcular costo de envío sin crear pedido (sin cambios respecto a antes)
     */
    public function calcularEnvio(Request $request)
    {
        $request->validate([
            'id_distrito' => 'required|integer|exists:distrito,id_distrito',
        ]);

        $usuario = Auth::user();

        $carrito = Carrito::with('detalles.variante.producto')
            ->where('id_usuario', $usuario->id_usuario)
            ->first();

        if (!$carrito || $carrito->detalles->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tu carrito está vacío.',
            ], 422);
        }

        $subtotal = 0;
        foreach ($carrito->detalles as $detalle) {
            $producto = $detalle->variante->producto;
            $precio = $producto->precio_oferta ?? $producto->precio;
            $subtotal += $precio * $detalle->cantidad;
        }

        $agencia = Agencia::where('id_distrito', $request->id_distrito)
            ->where('estado', 1)
            ->first();

        if (!$agencia) {
            return response()->json([
                'success' => false,
                'message' => 'No existe una agencia activa para el distrito seleccionado.',
            ], 422);
        }

        $costoEnvio = $agencia->costo_envio ?? 0;
        $nombreAgencia = $agencia->nombre_agencia ?? null;
        $direccionAgencia = $agencia->direccion ?? null;
        $tiempoEstimado = $agencia->tiempo_estimado ?? '3-5 días hábiles';

        return response()->json([
            'success' => true,
            'data' => [
                'costo_envio' => (float) $costoEnvio,
                'nombre_agencia' => $nombreAgencia,
                'direccion_agencia' => $direccionAgencia,
                'tiempo_estimado' => $tiempoEstimado,
                'subtotal' => (float) $subtotal,
                'total_con_envio' => (float) ($subtotal + $costoEnvio),
            ]
        ]);
    }

    private function generarNumeroPedido(): string
    {
        $fecha       = now()->format('Ymd');
        $cantidad    = Pedido::whereDate('created_at', today())->count() + 1;
        $correlativo = str_pad($cantidad, 3, '0', STR_PAD_LEFT);

        return "BND-{$fecha}-{$correlativo}";
    }
}
