<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles; 
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; 
use Illuminate\Support\Facades\DB;

class EstadoPedidosExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return DB::table('pedido')
            ->select('estado_pedido as estado', DB::raw('COUNT(*) as cantidad_pedidos'))
            ->groupBy('estado_pedido')
            ->orderBy('cantidad_pedidos', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Estado de la Orden',
            'Cantidad Total de Pedidos'
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
