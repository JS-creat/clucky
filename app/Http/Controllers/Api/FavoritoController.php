<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductoResource; // 👈 IMPORTAR
use Illuminate\Http\Request;
use App\Models\Favorito;
use App\Models\Producto;

class FavoritoController extends Controller
{
    // Obtener favoritos del usuario
    public function obtener(Request $request)
    {
        $favoritos = Favorito::where('id_usuario', $request->id_usuario)
            ->with('producto')
            ->get()
            ->pluck('producto');

        return response()->json([
            'success' => true,
            'data' => ProductoResource::collection($favoritos) // 👈 USAR RESOURCE
        ]);
    }

    // Agregar producto a favoritos
    public function agregar(Request $request)
    {
        $request->validate([
            'id_usuario' => 'required|integer',
            'id_producto' => 'required|integer|exists:producto,id_producto'
        ]);

        $favorito = Favorito::firstOrCreate([
            'id_usuario' => $request->id_usuario,
            'id_producto' => $request->id_producto
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado a favoritos',
            'data' => new ProductoResource($favorito->producto) // 👈 USAR RESOURCE
        ]);
    }

    // Eliminar producto de favoritos
    public function eliminar(Request $request)
    {
        $request->validate([
            'id_usuario' => 'required|integer',
            'id_producto' => 'required|integer'
        ]);

        $deleted = Favorito::where('id_usuario', $request->id_usuario)
            ->where('id_producto', $request->id_producto)
            ->delete();

        return response()->json([
            'success' => $deleted > 0,
            'message' => $deleted > 0 ? 'Eliminado de favoritos' : 'No se encontró el favorito'
        ]);
    }
}