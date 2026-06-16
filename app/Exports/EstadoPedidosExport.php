<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class EstadoPedidosExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    public function collection()
    {
        return DB::table('pedido')
            ->select(
                'estado_pedido as estado',
                DB::raw('COUNT(*) as cantidad_pedidos'),
                DB::raw('SUM(total_pedido) as monto_total'),
                DB::raw('ROUND(COUNT(*) * 100.0 / SUM(COUNT(*)) OVER(), 2) as porcentaje')
            )
            ->groupBy('estado_pedido')
            ->orderBy('cantidad_pedidos', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return ['Estado de la Orden', 'Cantidad Total', 'Monto Total (S/.)', '% del Total'];
    }

    public function columnWidths(): array
    {
        return ['A' => 25, 'B' => 18, 'C' => 20, 'D' => 15];
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
