<?php

namespace App\Exports;

// Estas son las "herramientas" que le importamos a la clase
use Maatwebsite\Excel\Concerns\FromCollection; // Sirve para que acepte listas de datos de la BD
use Maatwebsite\Excel\Concerns\WithHeadings;   // Sirve para poder ponerle títulos a las columnas
use Illuminate\Support\Facades\DB;             // Sirve para usar el constructor de consultas SQL nativas

class ProductosMasVendidosExport implements FromCollection, WithHeadings
{
    /**
     * Esta función se encarga de ir a la base de datos y traer la información
     */
    public function collection()
    {
        return DB::table('detalle_pedido as dp')
            ->join('producto_variante as pv', 'dp.id_variante', '=', 'pv.id_variante')
            ->join('producto as p', 'pv.id_producto', '=', 'p.id_producto')
            ->join('pedido as ped', 'dp.id_pedido', '=', 'ped.id_pedido')
            // Filtramos solo los pedidos que tengan valor económico real
            ->whereIn('ped.estado_pedido', ['Pagado', 'Confirmado', 'En camino', 'Listo para recoger', 'Entregado'])
            // Seleccionamos las columnas que queremos que aparezcan en el Excel
            ->select(
                'p.id_producto',
                'p.nombre_producto',
                DB::raw('SUM(dp.cantidad) as unidades_vendidas'),
                DB::raw('SUM(dp.subtotal) as dinero_generado')
            )
            ->groupBy('p.id_producto', 'p.nombre_producto')
            ->orderBy('unidades_vendidas', 'desc') // Ordenamos del más vendido al menos vendido
            ->limit(10) // Solo queremos el Top 10
            ->get(); // Ejecuta la consulta y devuelve el resultado
    }

    /**
     * Esta función define la primera fila del Excel
     */
    public function headings(): array
    {
        return [
            'ID Producto',
            'Nombre del Producto',
            'Unidades Vendidas',
            'Total Ventas Generadas (S/.)'
        ];
    }
}
