<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class VentasPorCategoriaExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    public function __construct(
        private ?string $desde = null,
        private ?string $hasta = null
    ) {}

    public function collection()
    {
        $estadosValidos = ['Pagado', 'Confirmado', 'En camino', 'Listo para recoger', 'Entregado'];

        $query = DB::table('detalle_pedido as dp')
            ->join('producto_variante as pv', 'dp.id_variante', '=', 'pv.id_variante')
            ->join('producto as p', 'pv.id_producto', '=', 'p.id_producto')
            ->join('pedido as ped', 'dp.id_pedido', '=', 'ped.id_pedido')
            ->leftJoin('categoria as cat', 'p.id_categoria', '=', 'cat.id_categoria')
            ->whereIn('ped.estado_pedido', $estadosValidos)
            ->select(
                DB::raw('COALESCE(cat.nombre_categoria, "Sin categoría") as categoria'),
                DB::raw('COUNT(DISTINCT p.id_producto) as productos_distintos'),
                DB::raw('SUM(dp.cantidad) as unidades_vendidas'),
                DB::raw('SUM(dp.subtotal) as ingresos_totales'),
                DB::raw('ROUND(SUM(dp.subtotal) * 100.0 / SUM(SUM(dp.subtotal)) OVER(), 2) as porcentaje_ingresos')
            )
            ->groupBy('cat.nombre_categoria')
            ->orderBy('ingresos_totales', 'desc');

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
        return ['Categoría', 'Productos Distintos', 'Unidades Vendidas', 'Ingresos Totales (S/.)', '% de Ingresos'];
    }

    public function columnWidths(): array
    {
        return ['A' => 25, 'B' => 22, 'C' => 22, 'D' => 25, 'E' => 18];
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
