<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductoResource;
use Illuminate\Http\Request;
use App\Models\Favorito;

class FavoritoController extends Controller
{
    public function obtener(Request $request)
    {
        $favoritos = Favorito::where('id_usuario', $request->id_usuario)
            ->with('producto.variantes')
            ->get()
            ->pluck('producto');

        return response()->json([
            'success' => true,
            'data' => ProductoResource::collection($favoritos)
        ]);
    }

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
            'data' => new ProductoResource($favorito->load('producto.variantes')->producto)
        ]);
    }

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