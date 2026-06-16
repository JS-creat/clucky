<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class ClientesTopExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    public function __construct(
        private int $limite = 10,
        private ?string $desde = null,
        private ?string $hasta = null
    ) {}

    public function collection()
    {
        $estadosValidos = ['Pagado', 'Confirmado', 'En camino', 'Listo para recoger', 'Entregado'];

        $query = DB::table('pedido as ped')
            ->join('usuario as u', 'ped.id_usuario', '=', 'u.id_usuario')
            ->whereIn('ped.estado_pedido', $estadosValidos)
            ->select(
                'u.id_usuario',
                DB::raw("CONCAT(u.nombres, ' ', u.apellidos) as nombre_cliente"),
                'u.correo',
                'u.telefono',
                DB::raw('COUNT(ped.id_pedido) as total_compras'),
                DB::raw('SUM(ped.total_pedido) as dinero_dejado'),
                DB::raw('ROUND(AVG(ped.total_pedido), 2) as ticket_promedio'),
                DB::raw('MAX(ped.fecha_pedido) as ultima_compra')
            )
            ->groupBy('u.id_usuario', 'u.nombres', 'u.apellidos', 'u.correo', 'u.telefono')
            ->orderBy('dinero_dejado', 'desc')
            ->limit($this->limite);

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
        return ['ID', 'Nombre del Cliente', 'Correo', 'Teléfono', 'Órdenes', 'Total Gastado (S/.)', 'Ticket Promedio (S/.)', 'Última Compra'];
    }

    public function columnWidths(): array
    {
        return ['A' => 8, 'B' => 30, 'C' => 30, 'D' => 15, 'E' => 10, 'F' => 20, 'G' => 22, 'H' => 20];
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
