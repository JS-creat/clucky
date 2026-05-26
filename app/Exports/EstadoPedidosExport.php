<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class EstadoPedidosExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // cuántos registros hay agrupados por cada tipo de estado
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
}
