<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;


class HomeController extends Controller
{
    public function index(Request $request)
    {

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

         // Filtro por promoción activa
    if ($request->has('promocion')) {
        $query->whereNotNull('id_promocion')
              ->whereHas('promocion', function ($q) {
                  $q->where('estado_promocion', 1)
                    ->where('fecha_inicio', '<=', now())
                    ->where('fecha_fin', '>=', now());
              });
    }

    $productos = $query->orderBy('created_at', 'desc')->get();

    // Cargar banners activos desde la BD, ordenados
    $banners = \App\Models\Banner::where('estado', 1)
                ->orderBy('orden', 'asc')
                ->get();


        $productos = $query->get();

        return view('home.index', compact('productos', 'banners'));
    }

}
