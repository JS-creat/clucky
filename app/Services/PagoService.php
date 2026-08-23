<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\Carrito;
use App\Models\Cupon;
use App\Models\ProductoVariante;
use App\Services\FacturacionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\BoletaEmail;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;

class PagoService
{
    public function __construct(
        protected FacturacionService $facturacionService
    ) {}

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

        $pedido = Pedido::with(['detalles.variante', 'usuario'])->find($payment->external_reference);

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

                // 🟢 Registrar el uso del cupón
                if ($pedido->id_cupon) {
                    $cupon = Cupon::find($pedido->id_cupon);
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

        // 🟢 3. EMISIÓN DE BOLETA ELECTRÓNICA + ENVÍO DE CORREO (Fuera de la transacción DB)
        try {
            $usuario = \App\Models\User::where('id_usuario', $pedido->id_usuario)->first();

            $cliente = [
                'tipo_doc' => strlen($usuario?->dni ?? '') === 11 ? '6' : '1', // '6' si es RUC, '1' si es DNI
                'num_doc'  => $usuario?->dni ?? $payment->payer->identification->number ?? '00000000',
                'nombre'   => $usuario?->name ?? trim(($payment->payer->first_name ?? 'CLIENTE') . ' ' . ($payment->payer->last_name ?? 'GENERAL'))
            ];

            // Emitimos la boleta ante SUNAT (registro/validación)
            $respuestaFactura = $this->facturacionService->emitirBoleta($pedido, $cliente);

            if ($respuestaFactura['success']) {
                Log::info("Boleta emitida para el Pedido #{$pedido->id_pedido}.");

                // Traemos el binario del PDF directamente desde APIs Perú
                $pdfBinary = $this->facturacionService->obtenerPdf($pedido, $cliente);

                if ($pdfBinary && $usuario?->correo) {
                    Mail::to($usuario->correo)
                        ->send(new BoletaEmail($pedido, $pdfBinary));

                    Log::info("Correo de boleta enviado para el Pedido #{$pedido->id_pedido} a {$usuario->correo}.");
                } else {
                    Log::error("No se pudo enviar el correo de boleta para el Pedido #{$pedido->id_pedido}: " .
                        (!$pdfBinary ? 'PDF vacío. ' : '') .
                        (!$usuario?->correo ? 'Sin correo de usuario.' : '')
                    );
                }
            } else {
                Log::error("Error emitiendo boleta para Pedido #{$pedido->id_pedido}", $respuestaFactura);
            }
        } catch (\Exception $e) {
            Log::error("Excepción al emitir/enviar comprobante: " . $e->getMessage());
        }

        return $pedido->fresh();
    }
}