<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class StockCriticoExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    public function __construct(private int $umbral = 5) {}

    public function collection()
    {
        return DB::table('producto_variante as pv')
            ->join('producto as p', 'pv.id_producto', '=', 'p.id_producto')
            ->leftJoin('categoria as cat', 'p.id_categoria', '=', 'cat.id_categoria')
            ->where('pv.stock', '<=', $this->umbral)
            ->where('p.estado_producto', 1)
            ->select(
                'p.id_producto',
                'p.nombre_producto',
                DB::raw('COALESCE(cat.nombre_categoria, "Sin categoría") as categoria'),
                'p.marca',
                'pv.talla',
                'pv.color',
                'pv.sku',
                'pv.stock',
                'p.precio'
            )
            ->orderBy('pv.stock', 'asc')
            ->orderBy('p.nombre_producto', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return ['ID Producto', 'Nombre', 'Categoría', 'Marca', 'Talla', 'Color', 'SKU', 'Stock Actual', 'Precio (S/.)'];
    }

    public function columnWidths(): array
    {
        return ['A' => 12, 'B' => 30, 'C' => 18, 'D' => 15, 'E' => 10, 'F' => 12, 'G' => 12, 'H' => 14, 'I' => 14];
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
