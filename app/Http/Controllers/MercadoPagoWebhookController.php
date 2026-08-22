<?php

namespace App\Http\Controllers;

use App\Services\PagoService;
use Illuminate\Http\Request;

class MercadoPagoWebhookController extends Controller
{
    public function __construct(protected PagoService $pagoService) {}

    public function handle(Request $request)
    {
        $topic     = $request->input('topic') ?? $request->input('type');
        $paymentId = $request->input('data.id') ?? $request->input('id');

        if ($topic !== 'payment' || !$paymentId) {
            return response()->json(['status' => 'ignorado'], 200);
        }

        try {
            $this->pagoService->confirmarPago((string) $paymentId);
        } catch (\Exception $e) {
            \Log::error('Error en webhook de Mercado Pago', [
                'payment_id' => $paymentId,
                'mensaje'    => $e->getMessage(),
            ]);
        }

        return response()->json(['status' => 'ok'], 200);
    }
}
