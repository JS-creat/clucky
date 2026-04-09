<?php

namespace App\Http\Controllers\Admin;


use App\Models\Cupon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CuponController extends Controller
{
    public function index()
    {
        $cupones = Cupon::orderBy('fecha_vencimiento', 'desc')->paginate(10);
        return view('admin.cupones.index', compact('cupones'));
    }

    public function create()
    {
        return view('admin.cupones.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo_cupon' => 'required|unique:cupones|max:50',
            'monto_cupon' => 'required|numeric|min:0',
            'monto_compra_minima' => 'required|numeric|min:0',
            'fecha_vencimiento' => 'required|date|after:today',
            'estado_cupon' => 'boolean'
        ]);

        Cupon::create($data);

        return redirect()->route('admin.cupones.index')
            ->with('success', 'Cupón creado');
    }

    public function edit($id)
    {
        $cupon = Cupon::findOrFail($id);
        return view('admin.cupones.edit', compact('cupon'));
    }

    public function update(Request $request, $id)
    {
        $cupon = Cupon::findOrFail($id);

        $data = $request->validate([
            'monto_cupon' => 'required|numeric',
            'fecha_vencimiento' => 'required|date'
        ]);

        $cupon->update($data);

        return back()->with('success', 'Actualizado');
    }

    public function destroy($id)
    {
        Cupon::destroy($id);
        return back()->with('success', 'Eliminado');
    }
}