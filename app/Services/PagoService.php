<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\Carrito;
use App\Models\Cupon;
use App\Models\ProductoVariante;
use Illuminate\Support\Facades\DB;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;

class PagoService
{
    /**
     * Verifica un pago contra Mercado Pago y confirma el pedido si corresponde.
     * La llaman: PagoController (web, cuando el cliente regresa), el
     * webhook, y PagoMovilApiController (retorno del WebView en la app).
     */
    public function confirmarPago(string $paymentId): ?Pedido
    {
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
        $client  = new PaymentClient();
        $payment = $client->get($paymentId);

        if ($payment->status !== 'approved') {
            return null;
        }

        $pedido = Pedido::with('detalles.variante')->find($payment->external_reference);

        if (!$pedido) {
            return null;
        }

        // Si ya se procesó este mismo pago antes, no repetir el descuento de stock
        if ($pedido->payment_id === $paymentId) {
            return $pedido;
        }

        try {
            DB::transaction(function () use ($pedido, $paymentId) {
                $sinStock = [];

                foreach ($pedido->detalles as $detalle) {
                    if (!$detalle->variante) {
                        continue;
                    }

                    $variante = ProductoVariante::where('id_variante', $detalle->variante->id_variante)
                        ->lockForUpdate()
                        ->first();

                    if ($variante->stock < $detalle->cantidad) {
                        $sinStock[] = $variante->talla . ' / ' . ($variante->color ?? '');
                        continue;
                    }

                    $variante->decrement('stock', $detalle->cantidad);

                    $variante->movimientos()->create([
                        'tipo'       => 'salida',
                        'cantidad'   => $detalle->cantidad,
                        'motivo'     => 'venta',
                        'id_pedido'  => $pedido->id_pedido,
                        'id_usuario' => $pedido->id_usuario,
                    ]);
                }

                if (!empty($sinStock)) {
                    throw new \Exception('SIN_STOCK: ' . implode(', ', $sinStock));
                }

                $pedido->update([
                    'payment_id'    => $paymentId,
                    'estado_pedido' => 'Confirmado',
                ]);

                // 🟢 NUEVO: registrar el uso del cupón AQUÍ, recién cuando
                // el pago está confirmado de verdad — no al crear el
                // pedido. Si el usuario nunca completa el pago, el cupón
                // nunca se "gasta". El descuento se recalcula sobre el
                // subtotal real guardado en los detalles del pedido (no
                // se necesita una columna nueva para guardarlo aparte).
                if ($pedido->id_cupon) {
                    $cupon = Cupon::find($pedido->id_cupon);
                    // No asumimos que Pedido tenga una relación `usuario()`
                    // definida; buscamos directo por el ID guardado.
                    $usuarioPedido = \App\Models\User::where('id_usuario', $pedido->id_usuario)->first();

                    if ($cupon && $usuarioPedido) {
                        $subtotalPedido = $pedido->detalles->sum('subtotal');
                        $descuento = $cupon->calcularDescuento($subtotalPedido);
                        $cupon->registrarUso($usuarioPedido, $subtotalPedido, $descuento);
                    }
                }

                Carrito::where('id_usuario', $pedido->id_usuario)
                    ->first()?->detalles()->delete();
            });
        } catch (\Exception $e) {
            if (str_starts_with($e->getMessage(), 'SIN_STOCK:')) {
                $pedido->update([
                    'payment_id'       => $paymentId,
                    'estado_pedido'    => 'Anulado',
                    'motivo_anulacion' => str_replace('SIN_STOCK: ', 'Sin stock disponible al confirmar el pago: ', $e->getMessage()),
                ]);

                return $pedido;
            }

            throw $e;
        }

        return $pedido->fresh();
    }
}