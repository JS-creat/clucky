<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerfilController extends Controller
{
    // MOSTRAR PERFIL
    public function index()
    {
        return view('perfil.index');
    }

    // FORMULARIO EDITAR
    public function edit()
    {
        return view('perfil.edit');
    }

    // GUARDAR CAMBIOS
    public function update(Request $request)
    {
        $usuario = Auth::user();

        $usuario->update([
            'nombres'   => $request->nombres,
            'apellidos' => $request->apellidos,
            'telefono'  => $request->telefono,
        ]);

        return redirect()->route('perfil.index')
            ->with('success', 'Perfil actualizado');
    }
}
