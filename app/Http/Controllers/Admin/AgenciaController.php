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
    public function index(Request $request)
    {
        $query = Agencia::with([
            'distrito.provincia.departamento'
        ]);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nombre_agencia', 'like', "%{$search}%")
                    ->orWhere('direccion', 'like', "%{$search}%")
                    ->orWhereHas('distrito', function ($q) use ($search) {
                        $q->where('nombre_distrito', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->has('estado') && $request->estado !== '') {
            $query->where('estado', $request->estado);
        }

        $agencias = $query
            ->orderByDesc('estado')
            ->orderBy('nombre_agencia')
            ->paginate(12);

        return view('admin.agencias.index', compact('agencias'));
    }

    public function create()
    {
        $departamentos = Departamento::orderBy('nombre_departamento')->get();
        return view('admin.agencias.create', compact('departamentos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_agencia' => 'required|string|max:100',
            'direccion'      => 'required|string',
            'costo_envio'    => 'required|numeric|min:0',
            'id_distrito'    => 'required|exists:distrito,id_distrito',
            'estado'         => 'nullable|boolean',
        ]);

        // Asignar explícitamente el valor booleano del checkbox
        $validated['estado'] = $request->boolean('estado');

        Agencia::create($validated);

        return redirect()->route('admin.agencias.index')
            ->with('success', 'Agencia creada correctamente.');
    }

    public function edit(Agencia $agencia)
    {
        $departamentos = Departamento::orderBy('nombre_departamento')->get();

        // Carga segura usando opcional / nullsafe
        $provinciaActual    = $agencia->distrito?->provincia;
        $departamentoActual = $provinciaActual?->departamento;

        $provincias = $departamentoActual
            ? Provincia::where('id_departamento', $departamentoActual->id_departamento)->orderBy('nombre_provincia')->get()
            : collect();

        $distritos  = $provinciaActual
            ? Distrito::where('id_provincia', $provinciaActual->id_provincia)->orderBy('nombre_distrito')->get()
            : collect();

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
        $validated = $request->validate([
            'nombre_agencia' => 'required|string|max:100',
            'direccion'      => 'required|string',
            'costo_envio'    => 'required|numeric|min:0',
            'id_distrito'    => 'required|exists:distrito,id_distrito',
            'estado'         => 'nullable|boolean',
        ]);

        $validated['estado'] = $request->boolean('estado');

        $agencia->update($validated);

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
        $agencia->update(['estado' => false]);
        return back()->with('success', 'Agencia desactivada del sistema.');
    }

    // --- MÉTODOS PARA API AJAX EN VISTAS ---

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
            ->get(['id_distrito', 'nombre_distrito']);

        return response()->json($distritos);
    }
}
