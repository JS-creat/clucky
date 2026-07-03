<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MovimientoStock;

class MovimientoStockController extends Controller
{
    public function index()
    {
        $movimientos = MovimientoStock::with(['variante.producto', 'usuario'])
            ->latest()
            ->paginate(20);

        return view('admin.movimientos.index', compact('movimientos'));
    }
}
