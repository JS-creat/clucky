<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MovimientoStock;
use Illuminate\Http\Request;

class MovimientoStockController extends Controller
{
    public function index(Request $request)
    {
        $movimientos = MovimientoStock::with(['variante.producto', 'usuario'])
            ->when($request->filled('buscar'), function ($query) use ($request) {
                $termino = $request->buscar;
                $query->whereHas('variante.producto', function ($q) use ($termino) {
                    $q->where('nombre_producto', 'like', "%{$termino}%");
                });
            })
            ->when($request->filled('tipo'), function ($query) use ($request) {
                $query->where('tipo', $request->tipo);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Si la petición viene de Fetch / AJAX, devolvemos solo la subvista
        if ($request->ajax()) {
            return view('admin.movimientos.resultados', compact('movimientos'));
        }

        // Si se entra recargando la página o por URL directa
        return view('admin.movimientos.index', compact('movimientos'));
    }
}