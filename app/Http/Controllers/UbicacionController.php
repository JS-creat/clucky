<?php

namespace App\Http\Controllers;

use App\Models\Provincia;
use App\Models\Distrito;

class UbicacionController extends Controller
{
    public function provincias($id)
    {
        return Provincia::where('id_departamento', $id)->get();
    }

    public function distritos($id)
    {
        return Distrito::where('id_provincia', $id)->get();
    }
}
