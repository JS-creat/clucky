<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\ReniecService;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    public function actualizar(Request $request, ReniecService $reniec)
    {
        /** @var User $usuario */
        $usuario = Auth::user();

        $request->validate([
            'nombres'           => 'required',
            'apellidos'         => 'required',
            'telefono'          => 'nullable',
            'numero_documento'  => 'nullable|digits:8',
            'id_tipo_documento' => 'nullable',
        ]);

        $nombres     = $request->nombres;
        $apellidos   = $request->apellidos;
        $avisoReniec = null;

        // Si el DNI es nuevo o cambió respecto al guardado, lo verificamos
        // contra RENIEC y usamos el nombre oficial — no el que el usuario tipeó.
        if ($request->filled('numero_documento') && $request->numero_documento !== $usuario->numero_documento) {
            $datos = $reniec->consultarDni($request->numero_documento);

            if ($datos) {
                $nombres   = $datos['nombres'];
                $apellidos = trim($datos['apellido_paterno'] . ' ' . $datos['apellido_materno']);
            } else {
                $avisoReniec = 'No pudimos verificar el DNI con RENIEC en este momento; guardamos el nombre que ingresaste.';
            }
        }

        $usuario->update([
            'nombres'           => $nombres,
            'apellidos'         => $apellidos,
            'telefono'          => $request->telefono,
            'numero_documento'  => $request->numero_documento,
            'id_tipo_documento' => $request->id_tipo_documento,
        ]);

        return back()
            ->with('success', 'Datos actualizados correctamente')
            ->with('aviso', $avisoReniec);
    }
}