<?php

namespace App\Services;

use App\Models\Pedido;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacturacionService
{
    protected string $apiUrl;
    protected string $token;

    public function __construct()
    {
        $this->apiUrl = config('services.apisperu.url', 'https://facturacion.apisperu.com/api/v1');
        $this->token  = config('services.apisperu.token');
    }

    /**
     * Emite la boleta a SUNAT pasando los ítems reales del pedido
     */
    public function emitirBoleta($pedido, array $cliente): array
    {
        $payload = $this->buildPayload($pedido, $cliente, '03');

        if (empty($payload) || empty($payload['details'])) {
            return [
                'success' => false,
                'error'   => 'No se estructuraron detalles válidos para la boleta.'
            ];
        }

        try {
            $response = Http::withToken($this->token)
                ->asJson()
                ->post("{$this->apiUrl}/invoice/send", $payload);

            return [
                'success' => $response->successful(),
                'data'    => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('Error enviando boleta a APIs Perú: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Obtiene el binario del PDF desde APIs Perú
     */
    public function obtenerPdf($pedido, array $cliente, string $tipoDoc = '03')
    {
        try {
            $payload = $this->buildPayload($pedido, $cliente, $tipoDoc);

            if (empty($payload)) {
                return null;
            }

            $response = Http::withToken($this->token)
                ->asJson()
                ->post("{$this->apiUrl}/invoice/pdf", $payload);

            if ($response->successful() && !empty($response->body())) {
                return $response->body();
            }

            Log::error('Error API PDF: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Error descargando PDF: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Construye la estructura JSON unificada requerida por APIs Perú
     */
    private function buildPayload($pedidoInput, array $cliente, string $tipoDoc = '03'): array
    {
        $pedido = null;

        // Validar y resolver el modelo Pedido
        if ($pedidoInput instanceof Pedido) {
            $pedido = $pedidoInput;
        } else {
            $idPedido = is_array($pedidoInput) 
                ? ($pedidoInput['id_pedido'] ?? $pedidoInput['id'] ?? null) 
                : ($pedidoInput->id_pedido ?? $pedidoInput->id ?? (is_scalar($pedidoInput) ? $pedidoInput : null));

            if (!empty($idPedido)) {
                $pedido = Pedido::find($idPedido);
            }
        }

        if (!$pedido) {
            Log::error('FacturacionService: No se pudo resolver el registro del Pedido.', ['input' => $pedidoInput]);
            return [];
        }

        $pedido->loadMissing('detalles');

        $details = [];
        foreach ($pedido->detalles as $detalle) {
            $precioUnitario = (float) $detalle->precio_unitario;
            $cantidad       = (int) $detalle->cantidad;

            if ($precioUnitario <= 0 || $cantidad <= 0) {
                continue;
            }

            $valorUnitario = round($precioUnitario / 1.18, 2);
            $valorVenta    = round($valorUnitario * $cantidad, 2);
            $mtoIgv        = round(($precioUnitario * $cantidad) - $valorVenta, 2);

            $details[] = [
                'codProducto'       => (string) ($detalle->id_variante ?? 'PROD1'),
                'unidad'            => 'NIU',
                'descripcion'       => 'Producto ID ' . ($detalle->id_variante ?? '1'),
                'cantidad'          => $cantidad,
                'mtoValorUnitario'  => $valorUnitario,
                'mtoPrecioUnitario' => $precioUnitario,
                'mtoValorVenta'     => $valorVenta,
                'mtoBaseIgv'        => $valorVenta,
                'mtoIgv'            => $mtoIgv,
                'igv'               => $mtoIgv,
                'porcentajeIgv'     => 18,
                'totalImpuestos'    => $mtoIgv,
                'tipAfeIgv'         => 10,
            ];
        }

        $mtoImpVenta = (float) $pedido->detalles->sum('subtotal');
        if ($mtoImpVenta <= 0) {
            $mtoImpVenta = (float) $pedido->total_pedido;
        }

        $mtoOperGravadas = round($mtoImpVenta / 1.18, 2);
        $mtoIGV          = round($mtoImpVenta - $mtoOperGravadas, 2);

        return [
            'ublVersion'    => '2.1',
            'tipoOperacion' => '0101',
            'tipoDoc'       => $tipoDoc,
            'serie'         => 'B001',
            'correlativo'   => (string) $pedido->id_pedido,
            'fechaEmision'  => now()->format('Y-m-d\TH:i:sP'),
            'formaPago'     => [
                'moneda' => 'PEN',
                'tipo'   => 'Contado'
            ],
            'tipoMoneda'    => 'PEN',
            'client' => [
                'tipoDoc'   => (string) ($cliente['tipo_doc'] ?? '1'),
                'numDoc'    => (string) ($cliente['num_doc'] ?? '00000000'),
                'rznSocial' => (string) ($cliente['nombre'] ?? 'CLIENTE GENERAL'),
                'address'   => [
                    'direccion' => $pedido->direccion_envio ?? $pedido->direccion ?? 'CONCEPCION, JUNIN',
                ]
            ],
            'company' => [
                'ruc'             => '10472160678',
                'razonSocial'     => 'DIAZ ZEA EDUARDO ARTURO',
                'nombreComercial' => 'B-EDEN',
                'address'         => [
                    'ubigueo'      => '12126',
                    'codigoPais'   => 'PE',
                    'departamento' => 'JUNIN',
                    'provincia'    => 'CONCEPCION',
                    'distrito'     => 'CONCEPCION',
                    'urbanizacion' => '-',
                    'direccion'    => 'JR. BOLOGNESI N° 908, CONCEPCION'
                ]
            ],
            'mtoOperGravadas' => $mtoOperGravadas,
            'mtoIGV'          => $mtoIGV,
            'valorVenta'      => $mtoOperGravadas,
            'totalImpuestos'  => $mtoIGV,
            'subTotal'        => $mtoImpVenta,
            'mtoImpVenta'     => $mtoImpVenta,
            'details'         => $details,
        ];
    }
}