<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EstadoPedidosExport;
use App\Exports\ProductosMasVendidosExport;
use App\Exports\ClientesTopExport;

class ReporteController extends Controller
{
    public function index()
    {
        return view('admin.reportes.index');
    }

    // Descarga de Excel: Estado de Pedidos
    public function exportPedidos()
    {
        return Excel::download(new EstadoPedidosExport, 'reporte_estado_pedidos_' . date('d-m-Y') . '.xlsx');
    }

    // Descarga de Excel: Productos Más Vendidos
    public function exportProductos()
    {
        return Excel::download(new ProductosMasVendidosExport, 'reporte_top_productos_' . date('d-m-Y') . '.xlsx');
    }

    // Descarga de Excel: Clientes Top
    public function exportClientes()
    {
        return Excel::download(new ClientesTopExport, 'reporte_top_clientes_' . date('d-m-Y') . '.xlsx');
    }
}
