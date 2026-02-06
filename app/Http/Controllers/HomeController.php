<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto; // <--- TE FALTABA ESTA LÍNEA

class HomeController extends Controller
{
    public function index() {
        // Asegúrate de tener el modelo Producto creado antes de recargar
        $productos = Producto::with('promocion')->where('estado_producto', 1)->take(8)->get();

        return view('welcome', compact('productos'));
    }
}
