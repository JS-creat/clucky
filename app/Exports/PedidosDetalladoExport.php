<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class PedidosDetalladoExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    public function __construct(
        private ?string $estado = null,
        private ?string $desde = null,
        private ?string $hasta = null,
        private int $limite = 100
    ) {}

    public function collection()
    {
        $query = DB::table('pedido as ped')
            ->leftJoin('usuario as u', 'ped.id_usuario', '=', 'u.id_usuario')
            ->leftJoin('cupones as c', 'ped.id_cupon', '=', 'c.id_cupon')
            ->leftJoin('tipo_entrega as te', 'ped.id_tipo_entrega', '=', 'te.id_tipo_entrega')
            ->select(
                'ped.numero_pedido',
                'ped.fecha_pedido',
                'ped.estado_pedido',
                DB::raw("COALESCE(CONCAT(u.nombres, ' ', u.apellidos), 'Sin usuario') as cliente"),
                'u.correo',
                DB::raw('COALESCE(te.nombre_tipo_entrega, "N/A") as tipo_entrega'),
                'ped.costo_envio',
                DB::raw('COALESCE(c.codigo_cupon, "Sin cupón") as cupon'),
                'ped.total_pedido',
                'ped.direccion'
            )
            ->orderBy('ped.fecha_pedido', 'desc')
            ->limit($this->limite);

        if ($this->estado) {
            $query->where('ped.estado_pedido', $this->estado);
        }
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
        return ['N° Pedido', 'Fecha', 'Estado', 'Cliente', 'Correo', 'Tipo Entrega', 'Costo Envío (S/.)', 'Cupón', 'Total (S/.)', 'Dirección'];
    }

    public function columnWidths(): array
    {
        return ['A' => 22, 'B' => 20, 'C' => 18, 'D' => 28, 'E' => 28, 'F' => 18, 'G' => 18, 'H' => 14, 'I' => 14, 'J' => 35];
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
