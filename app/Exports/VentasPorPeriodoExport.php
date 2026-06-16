<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class VentasPorPeriodoExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    public function __construct(
        private string $agrupacion = 'mes',  // 'dia' | 'semana' | 'mes'
        private ?string $desde = null,
        private ?string $hasta = null
    ) {}

    public function collection()
    {
        $estadosValidos = ['Pagado', 'Confirmado', 'En camino', 'Listo para recoger', 'Entregado'];

        $periodoExpr = match ($this->agrupacion) {
            'dia'    => "DATE(ped.fecha_pedido)",
            'semana' => "DATE(DATE_SUB(ped.fecha_pedido, INTERVAL WEEKDAY(ped.fecha_pedido) DAY))",
            default  => "DATE_FORMAT(ped.fecha_pedido, '%Y-%m')",
        };

        $query = DB::table('pedido as ped')
            ->whereIn('ped.estado_pedido', $estadosValidos)
            ->select(
                DB::raw("$periodoExpr as periodo"),
                DB::raw('COUNT(ped.id_pedido) as total_pedidos'),
                DB::raw('SUM(ped.total_pedido) as ingresos_totales'),
                DB::raw('ROUND(AVG(ped.total_pedido), 2) as ticket_promedio'),
                DB::raw('COUNT(DISTINCT ped.id_usuario) as clientes_unicos')
            )
            ->groupBy(DB::raw($periodoExpr))
            ->orderBy(DB::raw($periodoExpr), 'asc');

        if ($this->desde) {
            $query->where('ped.fecha_pedido', '>=', $this->desde . ' 00:00:00');
        }
        if ($this->hasta) {
            $query->where('ped.fecha_pedido', '<=', $this->hasta . ' 23:59:59');
        }

        return $query->get();
    }

    public function headings(): array
    {
        $label = match ($this->agrupacion) {
            'dia'    => 'Día',
            'semana' => 'Semana (inicio)',
            default  => 'Mes (YYYY-MM)',
        };

        return [$label, 'Total Pedidos', 'Ingresos Totales (S/.)', 'Ticket Promedio (S/.)', 'Clientes Únicos'];
    }

    public function columnWidths(): array
    {
        return ['A' => 18, 'B' => 16, 'C' => 24, 'D' => 24, 'E' => 18];
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
