<?php

namespace App\Http\Controllers;

use App\Services\PagoService;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    public function __construct(protected PagoService $pagoService) {}

    public function exito(Request $request)
    {
        $paymentId = $request->payment_id;

        if (!$paymentId) {
            return redirect()->route('carrito.index')->with('error', 'Datos de pago no válidos.');
        }

        try {
            $pedido = $this->pagoService->confirmarPago($paymentId);
        } catch (\Exception $e) {
            \Log::error('Error verificando pago', ['mensaje' => $e->getMessage()]);
            return redirect()->route('carrito.index')->with('error', 'Error al verificar el pago.');
        }

        if (!$pedido) {
            return redirect()->route('pago.fallo');
        }

        if ($pedido->estado_pedido === 'Anulado') {
            return redirect()->route('pago.fallo')
                ->with('error', 'Tu pago fue recibido pero el producto ya no tenía stock disponible. Nos pondremos en contacto contigo para el reembolso.');
        }

        return view('pagos.exito', compact('pedido'));
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
