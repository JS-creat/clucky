<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\Carrito;
use App\Models\Cupon;
use App\Models\ProductoVariante;
use App\Services\FacturacionService; // 1. IMPORTAR EL SERVICIO
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;

class PagoService
{
    // 2. INYECTAR EL SERVICIO EN EL CONSTRUCTOR
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

        // 🟢 3. NUEVO: EMISIÓN DE BOLETA ELECTRÓNICA (Fuera de la transacción DB)
        try {
            $usuario = \App\Models\User::where('id_usuario', $pedido->id_usuario)->first();

            $datosPago = [
                'monto'       => $payment->transaction_amount,
                'descripcion' => 'Pedido #' . $pedido->id_pedido,
                'producto_id' => 'PEDIDO-' . $pedido->id_pedido,
            ];

            // Ajusta los campos según la estructura de tu tabla de usuarios/pedidos
            $cliente = [
                'tipo_doc' => strlen($usuario?->dni ?? '') === 11 ? '6' : '1', // '6' si es RUC, '1' si es DNI
                'num_doc'  => $usuario?->dni ?? $payment->payer->identification->number ?? '00000000',
                'nombre'   => $usuario?->name ?? trim(($payment->payer->first_name ?? 'CLIENTE') . ' ' . ($payment->payer->last_name ?? 'GENERAL'))
            ];

            $respuestaFactura = $this->facturacionService->emitirBoleta($pedido, $cliente);

            if ($respuestaFactura['success']) {
                $pdfUrl = $respuestaFactura['data']['links']['pdf'] ?? null;
                Log::info("Boleta emitida para el Pedido #{$pedido->id_pedido}. PDF: {$pdfUrl}");

                // Si deseas guardar el PDF en la BD (opcional):
                // $pedido->update(['pdf_boleta' => $pdfUrl]);
            } else {
                Log::error("Error emitiendo boleta para Pedido #{$pedido->id_pedido}", $respuestaFactura);
            }
        } catch (\Exception $e) {
            // Evitamos que una falla en la API de SUNAT/APIs Perú interrumpa la confirmación del pago
            Log::error("Excepción al emitir comprobante: " . $e->getMessage());
        }

        return $pedido->fresh();
    }
}