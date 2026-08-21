<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Pedido;
use App\Models\Carrito;
use App\Services\FacturacionService;
use App\Mail\BoletaEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;

class MercadoPagoWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('WEBHOOK MP RECIBIDO', [
            'topic' => $request->input('topic'),
            'id'    => $request->input('id'),
            'data'  => $request->all(),
        ]);

        $topic = $request->input('topic');
        $id    = $request->input('id');

        if ($topic !== 'payment' || !$id) {
            return response()->json(['status' => 'ignored'], 200);
        }

        try {
            MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
            $client  = new PaymentClient();
            $payment = $client->get($id);

            Log::info('WEBHOOK MP PAYMENT', [
                'status'    => $payment->status,
                'ref'       => $payment->external_reference,
                'amount'    => $payment->transaction_amount,
            ]);

            if ($payment->status !== 'approved') {
                return response()->json(['status' => 'not_approved'], 200);
            }

            $idPedido = $payment->external_reference;
            $pedido   = Pedido::with('detalles.variante.producto', 'usuario')->find($idPedido);

            if (!$pedido) {
                Log::error('Webhook: pedido no encontrado', ['id_pedido' => $idPedido]);
                return response()->json(['status' => 'not_found'], 404);
            }

            if ($pedido->estado_pedido === 'Pagado') {
                return response()->json(['status' => 'already_paid'], 200);
            }

            $this->procesarPedidoPagado($pedido);

            return response()->json(['status' => 'processed'], 200);

        } catch (\Throwable $e) {
            Log::error('Webhook MP error fatal', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['status' => 'error'], 500);
        }
    }

    private function procesarPedidoPagado(Pedido $pedido): void
    {
        $facturacion = app(FacturacionService::class);
        $usuario     = $pedido->usuario;

        if (!$usuario) {
            Log::error("Webhook: Pedido #{$pedido->id_pedido} sin usuario");
            return;
        }

        DB::transaction(function () use ($pedido) {
            $pedido->update(['estado_pedido' => 'Pagado']);
            Carrito::where('id_usuario', $pedido->id_usuario)->delete();
        });

        $clienteData = [
            'tipo_doc' => '1',
            'num_doc'  => $usuario->dni ?? $usuario->numero_documento ?? '00000000',
            'nombre'   => trim(($usuario->nombres ?? '') . ' ' . ($usuario->apellidos ?? '')),
        ];

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

            $correoDestino = $usuario->correo ?? $usuario->email ?? null;

            if ($correoDestino) {
                $pdfContent = $facturacion->obtenerPdf($pedido, $clienteData, '03');
                if ($pdfContent) {
                    try {
                        Mail::to($correoDestino)->send(new BoletaEmail($pedido, $pdfContent));
                    } catch (\Throwable $e) {
                        Log::error("Error enviando correo pedido #{$pedido->id_pedido}: " . $e->getMessage());
                    }
                }
            }
        } else {
            Log::error("Error boleta pedido #{$pedido->id_pedido}", $resFactura);
        }
    }
}