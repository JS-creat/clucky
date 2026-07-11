<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Carrito;
use App\Models\ProductoVariante;
use Illuminate\Support\Facades\DB;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;

class PagoController extends Controller
{
    public function exito(Request $request)
    {
        $paymentId = $request->payment_id;
        $orderId = $request->external_reference;

        if (!$paymentId || !$orderId) {
            return redirect()->route('carrito.index')->with('error', 'Datos de pago no válidos.');
        }

        try {
            MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
            $client = new PaymentClient();

            $payment = $client->get($paymentId);

            if ($payment->status === 'approved' && $payment->external_reference == $orderId) {

                $pedido = Pedido::with('detalles.variante')->find($orderId);

                if ($pedido) {
                    if ($pedido->payment_id === $paymentId) {
                        return view('pagos.exito', compact('pedido'));
                    }

                    try {
                        DB::transaction(function () use ($pedido, $paymentId) {

                            $sinStock = [];

                            foreach ($pedido->detalles as $detalle) {
                                if ($detalle->variante) {
                                    $variante = ProductoVariante::where('id_variante', $detalle->variante->id_variante)
                                        ->lockForUpdate()
                                        ->first();

                                    $cantidad = $detalle->amount ?? $detalle->cantidad;

                                    if ($variante->stock < $cantidad) {
                                        $sinStock[] = $variante->talla . ' / ' . ($variante->color ?? '');
                                        continue;
                                    }

                                    $variante->decrement('stock', $cantidad);

                                    $variante->movimientos()->create([
                                        'tipo'       => 'salida',
                                        'cantidad'   => $cantidad,
                                        'motivo'     => 'venta',
                                        'id_pedido'  => $pedido->id_pedido,
                                        'id_usuario' => $pedido->id_usuario,
                                    ]);
                                }
                            }

                            if (!empty($sinStock)) {
                                throw new \Exception('SIN_STOCK: ' . implode(', ', $sinStock));
                            }

                            $pedido->update([
                                'payment_id'    => $paymentId,
                                'estado_pedido' => 'Confirmado',
                            ]);

                            $carrito = Carrito::where('id_usuario', $pedido->id_usuario)->first();
                            if ($carrito) {
                                $carrito->detalles()->delete();
                            }
                        });
                    } catch (\Exception $e) {
                        if (str_starts_with($e->getMessage(), 'SIN_STOCK:')) {
                            // El pago se cobró pero no había stock: anulamos y dejamos rastro claro
                            $pedido->update([
                                'payment_id'       => $paymentId,
                                'estado_pedido'    => 'Anulado',
                                'motivo_anulacion' => str_replace('SIN_STOCK: ', 'Sin stock disponible al confirmar el pago: ', $e->getMessage()),
                            ]);

                            return redirect()->route('pago.fallo')
                                ->with('error', 'Tu pago fue recibido pero el producto ya no tenía stock disponible. Nos pondremos en contacto contigo para el reembolso.');
                        }

                        throw $e; // Si fue otro tipo de error, lo dejamos subir al catch de afuera
                    }
                }

                return view('pagos.exito', compact('pedido'));
            }
        } catch (\Exception $e) {
            return redirect()->route('carrito.index')->with('error', 'Error al verificar el pago: ' . $e->getMessage());
        }

        return redirect()->route('pago.fallo');
    }

    public function fallo()
    {
        return view('pagos.fallo');
    }

    public function pendiente()
    {
        return view('pagos.pendiente');
    }
}
