<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agencia;
use App\Models\Carrito;
use App\Models\Cupon;
use App\Models\DetallePedido;
use App\Models\Pedido;
use App\Models\ProductoVariante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CheckoutApiController extends Controller
{
    /**
     * Confirmar pedido y crear la orden
     */
    public function confirmar(Request $request)
    {
        $request->validate([
            'id_tipo_documento' => 'required|integer|exists:tipo_documento,id_tipo_documento',
            'numero_documento'  => 'required|string|max:20',
            'telefono'          => 'required|string|max:20',
            'id_tipo_entrega'   => 'required|integer|in:1,2',
            'id_distrito'       => 'nullable|integer|exists:distrito,id_distrito',
            // 🟢 NUEVO: código de cupón opcional
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

        // 🟢 NUEVO: validar y calcular el descuento del cupón AQUÍ, en el
        // backend — nunca confiamos en un monto de descuento que venga
        // calculado desde la app móvil, porque alguien podría manipular
        // la petición y pagar menos de lo debido. El backend es la única
        // fuente de verdad para el monto final.
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

        // El descuento se aplica sobre el subtotal, el envío se cobra
        // completo (no está sujeto a descuento de cupón).
        $total = max(0, $subtotal - $montoDescuento) + $costoEnvio;

        DB::beginTransaction();

        try {
            $usuario->update([
                'id_tipo_documento' => $request->id_tipo_documento,
                'numero_documento'  => $request->numero_documento,
                'telefono'          => $request->telefono,
            ]);

            $pedido = Pedido::create([
                'numero_pedido'   => $this->generarNumeroPedido(),
                'total_pedido'    => $total,
                'estado_pedido'   => 'Pendiente',
                'id_usuario'      => $usuario->id_usuario,
                'id_tipo_entrega' => $request->id_tipo_entrega,
                'id_distrito'     => (int) $request->id_tipo_entrega === 2
                    ? $request->id_distrito
                    : null,
                'nombre_agencia' => $nombreAgencia,
                'direccion_agencia' => $direccionAgencia,
                // 🟢 NUEVO: se guarda qué cupón (si hubo) se usó en el pedido
                'id_cupon' => $cupon?->id_cupon,
            ]);

            foreach ($carrito->detalles as $detalle) {
                $producto = $detalle->variante->producto;
                $precio   = $producto->precio_oferta ?? $producto->precio;

                if ($detalle->variante->stock < $detalle->cantidad) {
                    throw new \Exception("Stock insuficiente para {$producto->nombre_producto}");
                }

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

            // 🟢 NUEVO: registrar el uso del cupón dentro de la misma
            // transacción del pedido. Si algo falla más adelante, el
            // rollback también deshace el uso del cupón.
            if ($cupon) {
                $registrado = $cupon->registrarUso($usuario, $subtotal, $montoDescuento);
                if (!$registrado) {
                    throw new \Exception('No se pudo aplicar el cupón. Intenta nuevamente.');
                }
            }

            $carrito->detalles()->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pedido realizado con éxito.',
                'data' => [
                    'id_pedido' => $pedido->id_pedido,
                    'numero_pedido' => $pedido->numero_pedido,
                    'subtotal' => $subtotal,
                    'costo_envio' => $costoEnvio,
                    'nombre_agencia' => $nombreAgencia,
                    'direccion_agencia' => $direccionAgencia,
                    'tiempo_estimado' => $tiempoEstimado,
                    // 🟢 NUEVO: info del cupón aplicado, si hubo
                    'codigo_cupon' => $codigoCuponNormalizado,
                    'monto_descuento' => $montoDescuento,
                    'total_pedido' => $pedido->total_pedido,
                    'estado_pedido' => $pedido->estado_pedido,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calcular costo de envío sin crear pedido
     * Útil para mostrar al usuario antes de confirmar
     */
    public function calcularEnvio(Request $request)
    {
        $request->validate([
            'id_distrito' => 'required|integer|exists:distrito,id_distrito',
        ]);

        $usuario = Auth::user();

        // Obtener carrito del usuario
        $carrito = Carrito::with('detalles.variante.producto')
            ->where('id_usuario', $usuario->id_usuario)
            ->first();

        if (!$carrito || $carrito->detalles->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tu carrito está vacío.',
            ], 422);
        }

        // Calcular subtotal
        $subtotal = 0;
        foreach ($carrito->detalles as $detalle) {
            $producto = $detalle->variante->producto;
            $precio = $producto->precio_oferta ?? $producto->precio;
            $subtotal += $precio * $detalle->cantidad;
        }

        // Buscar agencia en el distrito
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

    /**
     * Generar número de pedido único
     * Formato: CLK-YYYYMMDD-XXX
     */
    private function generarNumeroPedido(): string
    {
        $fecha       = now()->format('Ymd');
        $cantidad    = Pedido::whereDate('created_at', today())->count() + 1;
        $correlativo = str_pad($cantidad, 3, '0', STR_PAD_LEFT);

        return "CLK-{$fecha}-{$correlativo}";
    }
}