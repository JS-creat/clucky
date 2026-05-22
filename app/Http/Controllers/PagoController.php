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
            MercadoPagoConfig::setAccessToken(env('MP_ACCESS_TOKEN'));
            $client = new PaymentClient();

            $payment = $client->get($paymentId);

            if ($payment->status === 'approved' && $payment->external_reference == $orderId) {

                $pedido = Pedido::with('detalles.variante')->find($orderId);

                if ($pedido) {
                    if ($pedido->payment_id === $paymentId) {
                        return view('pagos.exito', compact('pedido'));
                    }

                    DB::transaction(function () use ($pedido, $paymentId) {

                        $pedido->update([
                            'payment_id' => $paymentId,
                            // Aquí también podrías cambiar el 'estado_pago' a 'Aprobado' si tuvieras esa columna
                        ]);

                        foreach ($pedido->detalles as $detalle) {
                            if ($detalle->variante) {
                                $detalle->variante->decrement('stock', $detalle->cantidad);
                            }
                        }

                        $carrito = Carrito::where('id_usuario', $pedido->id_usuario)->first();
                        if ($carrito) {
                            $carrito->detalles()->delete();
                        }
                    });
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
