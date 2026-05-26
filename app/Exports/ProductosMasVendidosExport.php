<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles; 
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; 
use Illuminate\Support\Facades\DB;

class ProductosMasVendidosExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return DB::table('detalle_pedido as dp')
            ->join('producto_variante as pv', 'dp.id_variante', '=', 'pv.id_variante')
            ->join('producto as p', 'pv.id_producto', '=', 'p.id_producto')
            ->join('pedido as ped', 'dp.id_pedido', '=', 'ped.id_pedido')
            ->whereIn('ped.estado_pedido', ['Pagado', 'Confirmado', 'En camino', 'Listo para recoger', 'Entregado'])
            ->select(
                'p.id_producto',
                'p.nombre_producto',
                DB::raw('SUM(dp.cantidad) as unidades_vendidas'),
                DB::raw('SUM(dp.subtotal) as dinero_generado')
            )
            ->groupBy('p.id_producto', 'p.nombre_producto')
            ->orderBy('unidades_vendidas', 'desc')
            ->limit(10)
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID Producto',
            'Nombre del Producto',
            'Unidades Vendidas',
            'Total Ventas Generadas (S/.)'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true, 
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size' => 11
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E293B'] 
                ]
            ],
        ];
    }
}