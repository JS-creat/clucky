<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Genero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GeneroController extends Controller
{
    /**
     * Listar todos los géneros
     */
    public function index(Request $request)
    {
        try {
            $generos = Genero::orderBy('nombre_genero', 'asc')->get();
            
            return response()->json([
                'success' => true,
                'data' => $generos
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en index de géneros: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar los géneros'
            ], 500);
        }
    }

    /**
     * Obtener productos por género
     */
    public function productos(Request $request, $id)
    {
        try {
            $genero = Genero::find($id);
            
            if (!$genero) {
                return response()->json([
                    'success' => false,
                    'message' => 'Género no encontrado'
                ], 404);
            }
            
            $productos = Producto::activos()
                ->where('id_genero', $id)
                ->with(['categoria', 'genero', 'promocion', 'variantes'])
                ->conStock()
                ->orderBy('created_at', 'desc')
                ->paginate($request->get('limit', 10));
            
            return response()->json([
                'success' => true,
                'data' => $productos,
                'genero' => $genero
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en productos por género: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar productos del género'
            ], 500);
        }
    }
}