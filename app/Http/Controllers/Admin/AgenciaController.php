<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agencia;
use App\Models\Distrito;
use App\Models\Provincia;
use App\Models\Departamento;
use Illuminate\Http\Request;

class AgenciaController extends Controller
{
    public function index()
    {
        $agencias = Agencia::with(['distrito.provincia.departamento'])
            ->orderBy('estado', 'desc')
            ->orderBy('nombre_agencia')
            ->paginate(15);

        return view('admin.agencias.index', compact('agencias'));
    }

    public function create()
    {
        $departamentos = Departamento::orderBy('nombre_departamento')->get();
        return view('admin.agencias.create', compact('departamentos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_agencia' => 'required|string|max:100',
            'direccion'      => 'required|string',
            'costo_envio'    => 'required|numeric|min:0',
            'id_distrito'    => 'required|exists:distrito,id_distrito',
            'estado'         => 'boolean',
        ]);

        Agencia::create($request->only([
            'nombre_agencia',
            'direccion',
            'costo_envio',
            'id_distrito',
            'estado'
        ]));

        return redirect()->route('admin.agencias.index')
            ->with('success', 'Agencia creada correctamente.');
    }

    public function edit(Agencia $agencia)
    {
        $departamentos = Departamento::orderBy('nombre_departamento')->get();

        // Pre-cargar provincia y departamento actuales para el select encadenado
        $provinciaActual   = $agencia->distrito->provincia;
        $departamentoActual = $provinciaActual->departamento;

        $provincias = Provincia::where('id_departamento', $departamentoActual->id_departamento)->get();
        $distritos  = Distrito::where('id_provincia', $provinciaActual->id_provincia)->get();

        return view('admin.agencias.edit', compact(
            'agencia',
            'departamentos',
            'provincias',
            'distritos',
            'provinciaActual',
            'departamentoActual'
        ));
    }

    public function update(Request $request, Agencia $agencia)
    {
        $request->validate([
            'nombre_agencia' => 'required|string|max:100',
            'direccion'      => 'required|string',
            'costo_envio'    => 'required|numeric|min:0',
            'id_distrito'    => 'required|exists:distrito,id_distrito',
            'estado'         => 'boolean',
        ]);

        $agencia->update($request->only([
            'nombre_agencia',
            'direccion',
            'costo_envio',
            'id_distrito',
            'estado'
        ]));

        return redirect()->route('admin.agencias.index')
            ->with('success', 'Agencia actualizada correctamente.');
    }

    public function toggleEstado(Agencia $agencia)
    {
        $agencia->update(['estado' => !$agencia->estado]);
        $msg = $agencia->estado ? 'Agencia activada.' : 'Agencia desactivada.';
        return back()->with('success', $msg);
    }

    public function destroy(Agencia $agencia)
    {
        // Solo desactiva no borra para preservar historial de pedidos
        $agencia->update(['estado' => 0]);
        return back()->with('success', 'Agencia desactivada del sistema.');
    }

    public function provincias($id_departamento)
    {
        $provincias = Provincia::where('id_departamento', $id_departamento)
            ->orderBy('nombre_provincia')
            ->get(['id_provincia', 'nombre_provincia']);
        return response()->json($provincias);
    }

    public function distritos($id_provincia)
    {
        $distritos = Distrito::where('id_provincia', $id_provincia)
            ->orderBy('nombre_distrito')
            ->with(['agencia' => function ($query) {
                $query->where('estado', 1)
                    ->orderBy('id_agencia')
                    ->limit(1);
            }])
            ->get()
            ->map(function ($distrito) {
                return [
                    'id_distrito'     => $distrito->id_distrito,
                    'nombre_distrito' => $distrito->nombre_distrito,
                    'costo_envio'     => $distrito->agencia->first()?->costo_envio ?? 0,
                    'nombre_agencia'  => $distrito->agencia->first()?->nombre_agencia ?? null,
                ];
            });

        return response()->json($distritos);
    }
}
