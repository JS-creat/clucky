<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Banner;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::check() && (int) Auth::user()->id_rol === 1) {
            return redirect()->route('admin.dashboard');
        }
        // Iniciamos la consulta
        $query = Producto::query()->where('estado_producto', 1);

        // Filtro por género
        if ($request->filled('categoria') && $request->categoria != 'Todo') {
            $query->whereHas('genero', function ($q) use ($request) {
                $q->where('nombre_genero', $request->categoria);
            });
        }

        //  Filtro por promoción activa
        if ($request->has('promocion')) {
            $query->whereNotNull('id_promocion')
                ->whereHas('promocion', function ($q) {
                    $q->where('estado_promocion', 1)
                        ->where('fecha_inicio', '<=', now())
                        ->where('fecha_fin', '>=', now());
                });
        }

        //  Filtro por búsqueda de texto
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre_producto', 'like', "%{$buscar}%")
                    ->orWhere('marca', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%");
            });
        }

        $productos = $query->orderBy('created_at', 'desc')->get();

        $banners = Banner::where('estado', 1)
            ->orderBy('orden', 'asc')
            ->get();

        return view('home.index', compact('productos', 'banners'));
    }
}
