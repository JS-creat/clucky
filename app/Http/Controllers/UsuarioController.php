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

        // Validamos que si envían nombres o apellidos sean válidos, pero no obligatorios si solo se envía el DNI
        $request->validate([
            'nombres'           => 'nullable|string',
            'apellidos'         => 'nullable|string',
            'telefono'          => 'nullable|string',
            'numero_documento'  => 'required|digits:8',
            'id_tipo_documento' => 'nullable',
        ]);

        // Conservar nombres actuales por defecto
        $nombres     = $request->filled('nombres') ? $request->nombres : $usuario->nombres;
        $apellidos   = $request->filled('apellidos') ? $request->apellidos : $usuario->apellidos;
        $avisoReniec = null;

        // Si se ingresó un DNI, intentamos consultar a RENIEC
        if ($request->filled('numero_documento')) {
            $datos = $reniec->consultarDni($request->numero_documento);

            if ($datos && isset($datos['nombres'])) {
                $nombres   = $datos['nombres'];
                $apellidos = trim(($datos['apellido_paterno'] ?? '') . ' ' . ($datos['apellido_materno'] ?? ''));
            } else {
                $avisoReniec = 'No pudimos verificar el DNI con RENIEC en este momento; se guardó únicamente el DNI.';
            }
        }

        // Actualizamos al usuario en la BD con DNI y tipo de documento (1 = DNI)
        $usuario->update([
            'nombres'           => $nombres,
            'apellidos'         => $apellidos,
            'telefono'          => $request->filled('telefono') ? $request->telefono : $usuario->telefono,
            'numero_documento'  => $request->numero_documento,
            'id_tipo_documento' => $request->id_tipo_documento ?? $usuario->id_tipo_documento ?? 1,
        ]);

        return back()
            ->with('success', 'DNI actualizado correctamente')
            ->with('aviso', $avisoReniec);
    }
}