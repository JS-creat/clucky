<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Genero;

class GeneroController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nombre_genero' => 'required|string|max:50|unique:genero,nombre_genero',
        ]);

        Genero::create([
            'nombre_genero' => $request->nombre_genero
        ]);

        return back()->with('success', 'Género creado correctamente');
    }
}
