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
        $this->apiUrl = config(
            'services.apisperu.url',
            'https://facturacion.apisperu.com/api/v1'
        );

        $this->token = config('services.apisperu.token');
    }

    /**
     * Emite la boleta electrónica a SUNAT mediante APIs Perú.
     */
    public function emitirBoleta($pedido, array $cliente): array
    {
        $payload = $this->buildPayload($pedido, $cliente, '03');

        if (empty($payload) || empty($payload['details'])) {
            return [
                'success' => false,
                'error' => 'No se estructuraron detalles válidos para la boleta.'
            ];
        }

        try {
            $response = Http::withToken($this->token)
                ->asJson()
                ->post("{$this->apiUrl}/invoice/send", $payload);

            return [
                'success' => $response->successful(),
                'data' => $response->json(),
            ];

        } catch (\Exception $e) {
            Log::error(
                'Error enviando boleta a APIs Perú: ' . $e->getMessage()
            );

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene el PDF de la boleta desde APIs Perú.
     */
    public function obtenerPdf(
        $pedido,
        array $cliente,
        string $tipoDoc = '03'
    ) {
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
            Log::error(
                'Error descargando PDF: ' . $e->getMessage()
            );
        }

        return null;
    }

    /**
     * Construye el payload requerido por APIs Perú.
     *
     * Los precios de productos y envío se consideran importes
     * finales con IGV incluido.
     */
    private function buildPayload(
        $pedidoInput,
        array $cliente,
        string $tipoDoc = '03'
    ): array {

        $pedido = null;

        /*
         * Resolver el modelo Pedido.
         */
        if ($pedidoInput instanceof Pedido) {

            $pedido = $pedidoInput;

        } else {

            $idPedido = is_array($pedidoInput)
                ? ($pedidoInput['id_pedido'] ?? $pedidoInput['id'] ?? null)
                : (
                    $pedidoInput->id_pedido
                    ?? $pedidoInput->id
                    ?? (is_scalar($pedidoInput) ? $pedidoInput : null)
                );

            if (!empty($idPedido)) {
                $pedido = Pedido::find($idPedido);
            }
        }

        if (!$pedido) {

            Log::error(
                'FacturacionService: No se pudo resolver el registro del Pedido.',
                ['input' => $pedidoInput]
            );

            return [];
        }

        /*
         * Cargar productos del pedido.
         */
        $pedido->loadMissing('detalles.variante.producto');

        $details = [];

        /*
         * Totales acumulados.
         */
        $totalVenta = 0.00;
        $totalBaseIgv = 0.00;
        $totalIgv = 0.00;

        /*
         * ─────────────────────────────────────────────
         * PRODUCTOS
         * ─────────────────────────────────────────────
         */
        foreach ($pedido->detalles as $detalle) {

            $precioUnitario = (float) $detalle->precio_unitario;
            $cantidad = (int) $detalle->cantidad;

            if ($precioUnitario <= 0 || $cantidad <= 0) {
                continue;
            }

            /*
             * El precio almacenado incluye IGV.
             *
             * Ejemplo:
             *
             * S/118.00
             * Base = 118 / 1.18 = S/100.00
             * IGV  = S/18.00
             */
            $valorUnitario = round($precioUnitario / 1.18, 2);

            $valorVenta = round(
                $valorUnitario * $cantidad,
                2
            );

            $importeTotal = round(
                $precioUnitario * $cantidad,
                2
            );

            $mtoIgv = round(
                $importeTotal - $valorVenta,
                2
            );

            $totalVenta += $importeTotal;
            $totalBaseIgv += $valorVenta;
            $totalIgv += $mtoIgv;

            $details[] = [

                'codProducto' => (string) (
                    $detalle->id_variante ?? 'PROD1'
                ),

                'unidad' => 'NIU',

                'descripcion' =>
                    $detalle->variante?->producto?->nombre_producto
                    ??
                    $detalle->variante?->producto?->nombre
                    ??
                    ('Producto ID ' . ($detalle->id_variante ?? '1')),

                'cantidad' => $cantidad,

                'mtoValorUnitario' => $valorUnitario,

                'mtoPrecioUnitario' => $precioUnitario,

                'mtoValorVenta' => $valorVenta,

                'mtoBaseIgv' => $valorVenta,

                'mtoIgv' => $mtoIgv,

                'igv' => $mtoIgv,

                'porcentajeIgv' => 18,

                'totalImpuestos' => $mtoIgv,

                'tipAfeIgv' => 10,
            ];
        }

        /*
         * ─────────────────────────────────────────────
         * COSTO DE ENVÍO
         * ─────────────────────────────────────────────
         *
         * El costo de envío está guardado directamente
         * en el pedido.
         */
        $costoEnvio = round(
            (float) ($pedido->costo_envio ?? 0),
            2
        );

        if ($costoEnvio > 0) {

            /*
             * El costo enviado al cliente incluye IGV.
             */
            $valorEnvio = round(
                $costoEnvio / 1.18,
                2
            );

            $igvEnvio = round(
                $costoEnvio - $valorEnvio,
                2
            );

            $totalVenta += $costoEnvio;
            $totalBaseIgv += $valorEnvio;
            $totalIgv += $igvEnvio;

            /*
             * El envío aparece como un concepto independiente
             * en la boleta.
             */
            $details[] = [

                'codProducto' => 'ENVIO',

                'unidad' => 'ZZ',

                'descripcion' => 'Servicio de envío',

                'cantidad' => 1,

                'mtoValorUnitario' => $valorEnvio,

                'mtoPrecioUnitario' => $costoEnvio,

                'mtoValorVenta' => $valorEnvio,

                'mtoBaseIgv' => $valorEnvio,

                'mtoIgv' => $igvEnvio,

                'igv' => $igvEnvio,

                'porcentajeIgv' => 18,

                'totalImpuestos' => $igvEnvio,

                'tipAfeIgv' => 10,
            ];
        }

        /*
         * No permitir emitir una boleta sin detalles.
         */
        if (empty($details)) {

            Log::error(
                'FacturacionService: El pedido no tiene detalles facturables.',
                [
                    'id_pedido' => $pedido->id_pedido
                ]
            );

            return [];
        }

        /*
         * Redondear los totales finales.
         */
        $totalVenta = round($totalVenta, 2);
        $totalBaseIgv = round($totalBaseIgv, 2);
        $totalIgv = round($totalIgv, 2);

        /*
         * Validación contra el total almacenado del pedido.
         */
        $totalPedido = round(
            (float) $pedido->total_pedido,
            2
        );

        if (abs($totalVenta - $totalPedido) > 0.02) {

            Log::warning(
                'Diferencia entre total del pedido y total de facturación.',
                [
                    'id_pedido' => $pedido->id_pedido,
                    'total_pedido' => $totalPedido,
                    'total_facturacion' => $totalVenta,
                    'diferencia' => round(
                        $totalPedido - $totalVenta,
                        2
                    ),
                ]
            );
        }

        /*
         * ─────────────────────────────────────────────
         * PAYLOAD APIs PERÚ
         * ─────────────────────────────────────────────
         */
        return [

            'ublVersion' => '2.1',

            'tipoOperacion' => '0101',

            'tipoDoc' => $tipoDoc,

            'serie' => 'B001',

            'correlativo' => (string) $pedido->id_pedido,

            'fechaEmision' => now()->format(
                'Y-m-d\TH:i:sP'
            ),

            'formaPago' => [
                'moneda' => 'PEN',
                'tipo' => 'Contado'
            ],

            'tipoMoneda' => 'PEN',

            'client' => [

                'tipoDoc' => (string) (
                    $cliente['tipo_doc'] ?? '1'
                ),

                'numDoc' => (string) (
                    $cliente['num_doc'] ?? '00000000'
                ),

                'rznSocial' => (string) (
                    $cliente['nombre'] ?? 'CLIENTE GENERAL'
                ),

                'address' => [

                    'direccion' =>
                        $pedido->direccion_envio
                        ??
                        $pedido->direccion
                        ??
                        'CONCEPCION, JUNIN',
                ],
            ],

            'company' => [

                'ruc' => '10472160678',

                'razonSocial' => 'DIAZ ZEA EDUARDO ARTURO',

                'nombreComercial' => 'B-EDEN',

                'address' => [

                    'ubigueo' => '12126',

                    'codigoPais' => 'PE',

                    'departamento' => 'JUNIN',

                    'provincia' => 'CONCEPCION',

                    'distrito' => 'CONCEPCION',

                    'urbanizacion' => '-',

                    'direccion' =>
                        'JR. BOLOGNESI N° 908, CONCEPCION'
                ]
            ],

            /*
             * Totales de la operación.
             */
            'mtoOperGravadas' => $totalBaseIgv,

            'mtoIGV' => $totalIgv,

            'valorVenta' => $totalBaseIgv,

            'totalImpuestos' => $totalIgv,

            'subTotal' => $totalVenta,

            'mtoImpVenta' => $totalVenta,

            /*
             * Productos + envío.
             */
            'details' => $details,
        ];
    }
}