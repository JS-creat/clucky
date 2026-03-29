<?php

namespace App\Http\Controllers;

use App\Models\Provincia;
use App\Models\Distrito;
use App\Models\Agencia;

class UbicacionController extends Controller
{
    public function provincias($id)
    {
        return Provincia::where('id_departamento', $id)
            ->orderBy('nombre_provincia')
            ->get(['id_provincia', 'nombre_provincia']);
    }

    public function distritos($id)
    {
        return Distrito::where('id_provincia', $id)
            ->orderBy('nombre_distrito')
            ->get(['id_distrito', 'nombre_distrito']);
    }

    public function agencias($id)
    {
        return Agencia::where('id_distrito', $id)
            ->where('estado', 1)
            ->orderBy('nombre_agencia')
            ->get(['id_agencia', 'nombre_agencia', 'direccion', 'costo_envio']);
    }
}
