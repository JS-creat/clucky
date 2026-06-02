<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class CuponesUsoExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    public function collection()
    {
        return DB::table('cupones as c')
            ->leftJoin('pedido as ped', 'c.id_cupon', '=', 'ped.id_cupon')
            ->select(
                'c.id_cupon',
                'c.codigo_cupon',
                'c.monto_cupon',
                'c.monto_compra_minima',
                'c.fecha_vencimiento',
                DB::raw('CASE WHEN c.estado_cupon = 1 THEN "Activo" ELSE "Inactivo" END as estado'),
                DB::raw('COUNT(ped.id_pedido) as veces_usado'),
                DB::raw('COALESCE(SUM(ped.total_pedido), 0) as ventas_generadas')
            )
            ->groupBy('c.id_cupon', 'c.codigo_cupon', 'c.monto_cupon', 'c.monto_compra_minima', 'c.fecha_vencimiento', 'c.estado_cupon')
            ->orderBy('veces_usado', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return ['ID', 'Código', 'Descuento (S/.)', 'Compra Mínima (S/.)', 'Vencimiento', 'Estado', 'Veces Usado', 'Ventas Generadas (S/.)'];
    }

    public function columnWidths(): array
    {
        return ['A' => 8, 'B' => 18, 'C' => 18, 'D' => 22, 'E' => 16, 'F' => 12, 'G' => 14, 'H' => 24];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A8A']],
            ],
        ];
    }
}
