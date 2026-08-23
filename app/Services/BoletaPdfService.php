<?php

namespace App\Services;

use App\Models\Pedido;
use Barryvdh\DomPDF\Facade\Pdf;

class BoletaPdfService
{
    /**
     * Genera el PDF personalizado de la boleta.
     */
    public function generar(Pedido $pedido, array $cliente)
    {
        $pedido->loadMissing([
            'detalles.variante.producto',
            'usuario',
        ]);

        // Productos
        $productos = $pedido->detalles->map(function ($detalle) {
            $producto = $detalle->variante?->producto;

            return [
                'descripcion' => $producto?->nombre_producto
                    ?? 'Producto',
                'variante' => trim(
                    ($detalle->variante?->color
                        ? 'Color: ' . $detalle->variante->color
                        : '') .
                    ($detalle->variante?->talla
                        ? ' | Talla: ' . $detalle->variante->talla
                        : '')
                ),
                'cantidad' => (int) $detalle->cantidad,
                'precio_unitario' => (float) $detalle->precio_unitario,
                'subtotal' => (float) $detalle->subtotal,
            ];
        });

        // Total de productos
        $totalProductos = round(
            $productos->sum('subtotal'),
            2
        );

        // Costo de envío
        $costoEnvio = round(
            (float) ($pedido->costo_envio ?? 0),
            2
        );

        // Total final del pedido
        $total = round(
            (float) $pedido->total_pedido,
            2
        );
        $valorVenta = round($total / 1.18, 2);
        $igv = round($total - $valorVenta, 2);

        // Número de comprobante
        $serie = $pedido->serie_boleta ?? 'B001';
        $correlativo = $pedido->correlativo_boleta
            ?? $pedido->id_pedido;

        $pdf = Pdf::loadView('pdf.boleta', [
            'pedido' => $pedido,
            'cliente' => $cliente,
            'productos' => $productos,
            'totalProductos' => $totalProductos,
            'costoEnvio' => $costoEnvio,
            'total' => $total,
            'valorVenta' => $valorVenta,
            'igv' => $igv,
            'serie' => $serie,
            'correlativo' => $correlativo,
        ]);

        // Tamaño A4
        $pdf->setPaper('A4', 'portrait');

        return $pdf->output();
    }
}