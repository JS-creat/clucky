<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;


class HomeController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::check() && Auth::user()->id_rol == 1) {


            return redirect()->route('admin.dashboard');

        }

        $query = Producto::query();

        // Filtro por género
        if ($request->has('categoria') && $request->categoria != 'Todo') {
                $query->whereHas('genero', function ($q) use ($request) {
                $q->where('nombre_genero', $request->categoria);
            });
        }


        // Filtro por promociones
        if ($request->has('promocion')) {
            $query->whereNotNull('precio_oferta')
            ->where('precio_oferta', '>', 0);
        }


        $productos = $query->get();

        return view('home.index', compact('productos'));
    }

}
