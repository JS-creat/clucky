<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EstadoPedidosExport;
use App\Exports\ProductosMasVendidosExport;
use App\Exports\ClientesTopExport;
use App\Exports\VentasPorCategoriaExport;
use App\Exports\VentasPorPeriodoExport;
use App\Exports\StockCriticoExport;
use App\Exports\CuponesUsoExport;
use App\Exports\PedidosDetalladoExport;

class ReporteController extends Controller
{
    public function index()
    {
        return view('admin.reportes.index');
    }

    // ── Reporte 1: Estado de Pedidos ───────────────────────────────────────────
    public function exportPedidos()
    {
        return Excel::download(
            new EstadoPedidosExport,
            'reporte_estado_pedidos_' . date('d-m-Y') . '.xlsx'
        );
    }

    // ── Reporte 2: Top Productos ───────────────────────────────────────────────
    public function exportProductos()
    {
        $limite = (int) request('limite', 10);
        $desde  = request('desde');
        $hasta  = request('hasta');

        return Excel::download(
            new ProductosMasVendidosExport($limite, $desde, $hasta),
            'reporte_top_productos_' . date('d-m-Y') . '.xlsx'
        );
    }

    // ── Reporte 3: Top Clientes ────────────────────────────────────────────────
    public function exportClientes()
    {
        $limite = (int) request('limite', 10);
        $desde  = request('desde');
        $hasta  = request('hasta');

        return Excel::download(
            new ClientesTopExport($limite, $desde, $hasta),
            'reporte_top_clientes_' . date('d-m-Y') . '.xlsx'
        );
    }

    // ── Reporte 4: Ventas por Categoría ────────────────────────────────────────
    public function exportCategoria()
    {
        $desde = request('desde');
        $hasta = request('hasta');

        return Excel::download(
            new VentasPorCategoriaExport($desde, $hasta),
            'reporte_ventas_categoria_' . date('d-m-Y') . '.xlsx'
        );
    }

    // ── Reporte 5: Ventas por Período (día/semana/mes) ─────────────────────────
    public function exportPeriodo()
    {
        $agrupacion = request('agrupacion', 'mes'); // dia | semana | mes
        $desde      = request('desde');
        $hasta      = request('hasta');

        return Excel::download(
            new VentasPorPeriodoExport($agrupacion, $desde, $hasta),
            'reporte_ventas_periodo_' . date('d-m-Y') . '.xlsx'
        );
    }

    // ── Reporte 6: Stock Crítico ───────────────────────────────────────────────
    public function exportStock()
    {
        $umbral = (int) request('umbral', 5);

        return Excel::download(
            new StockCriticoExport($umbral),
            'reporte_stock_critico_' . date('d-m-Y') . '.xlsx'
        );
    }

    // ── Reporte 7: Uso de Cupones ──────────────────────────────────────────────
    public function exportCupones()
    {
        return Excel::download(
            new CuponesUsoExport,
            'reporte_cupones_' . date('d-m-Y') . '.xlsx'
        );
    }

    // ── Reporte 8: Pedidos Detallado ───────────────────────────────────────────
    public function exportPedidosDetallado()
    {
        $estado = request('estado');        // null = todos
        $desde  = request('desde');
        $hasta  = request('hasta');
        $limite = (int) request('limite', 100);

        return Excel::download(
            new PedidosDetalladoExport($estado, $desde, $hasta, $limite),
            'reporte_pedidos_detallado_' . date('d-m-Y') . '.xlsx'
        );
    }
}
