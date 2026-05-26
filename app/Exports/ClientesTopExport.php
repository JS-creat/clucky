<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles; 
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; 
use Illuminate\Support\Facades\DB;

class ClientesTopExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return DB::table('pedido as ped')
            ->join('usuario as u', 'ped.id_usuario', '=', 'u.id_usuario')
            ->whereIn('ped.estado_pedido', ['Pagado', 'Confirmado', 'En camino', 'Listo para recoger', 'Entregado'])
            ->select(
                'u.id_usuario',
                DB::raw("CONCAT(u.nombres, ' ', u.apellidos) as nombre_cliente"),
                'u.correo',
                DB::raw('COUNT(ped.id_pedido) as total_compras'),
                DB::raw('SUM(ped.total_pedido) as dinero_dejado')
            )
            ->groupBy('u.id_usuario', 'u.nombres', 'u.apellidos', 'u.correo')
            ->orderBy('dinero_dejado', 'desc')
            ->limit(10)
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID Usuario',
            'Nombre del Cliente',
            'Correo Electrónico',
            'Cantidad de Órdenes Compradas',
            'Total Dinero Dejado (S/.)'
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