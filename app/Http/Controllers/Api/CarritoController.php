<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Carrito;
use App\Models\DetalleCarrito;
use App\Models\ProductoVariante;

class CarritoController extends Controller
{
    public function obtener(Request $request)
    {
        $carrito = Carrito::where('id_usuario', $request->id_usuario)
            ->with(['detalles.variante.producto'])
            ->first();
            
        return response()->json([
            'success' => true,
            'data' => $carrito
        ]);
    }

    public function crear(Request $request)
    {
        $carrito = Carrito::create([
            'id_usuario' => $request->id_usuario
        ]);
        
        $carrito->load(['detalles.variante.producto']);
        
        return response()->json([
            'success' => true,
            'data' => $carrito
        ]);
    }

    public function agregar(Request $request)
    {
        $carrito = Carrito::where('id_usuario', $request->id_usuario)->first();
        
        if (!$carrito) {
            $carrito = Carrito::create(['id_usuario' => $request->id_usuario]);
        }
        
        $variante = ProductoVariante::find($request->id_variante);
        if (!$variante) {
            return response()->json([
                'success' => false,
                'message' => 'Variante no encontrada'
            ], 404);
        }
        
        $detalle = DetalleCarrito::updateOrCreate(
            [
                'id_carrito' => $carrito->id_carrito,
                'id_variante' => $request->id_variante
            ],
            [
                'cantidad' => $request->cantidad
            ]
        );
        
        $carrito = Carrito::with(['detalles.variante.producto'])
            ->find($carrito->id_carrito);
        
        return response()->json([
            'success' => true,
            'data' => $carrito
        ]);
    }

    public function actualizar(Request $request)
    {
        $detalle = DetalleCarrito::find($request->id_detalle_carrito);
        
        if (!$detalle) {
            return response()->json([
                'success' => false,
                'message' => 'Detalle no encontrado'
            ], 404);
        }
        
        $detalle->update(['cantidad' => $request->cantidad]);
        
        $carrito = Carrito::with(['detalles.variante.producto'])
            ->find($detalle->id_carrito);
        
        return response()->json([
            'success' => true,
            'data' => $carrito
        ]);
    }

    public function eliminar(Request $request)
    {
        $detalle = DetalleCarrito::find($request->id_detalle_carrito);
        
        if (!$detalle) {
            return response()->json([
                'success' => false,
                'message' => 'Detalle no encontrado'
            ], 404);
        }
        
        $idCarrito = $detalle->id_carrito;
        $detalle->delete();
        
        $carrito = Carrito::with(['detalles.variante.producto'])
            ->find($idCarrito);
        
        return response()->json([
            'success' => true,
            'data' => $carrito
        ]);
    }

    public function limpiar(Request $request)
    {
        $carrito = Carrito::where('id_usuario', $request->id_usuario)->first();
        
        if ($carrito) {
            $carrito->detalles()->delete();
            
            $carrito = Carrito::with(['detalles.variante.producto'])
                ->find($carrito->id_carrito);
        }
        
        return response()->json([
            'success' => true,
            'data' => $carrito,
            'message' => 'Carrito limpiado'
        ]);
    }

    public function total($idCarrito)
    {
        $carrito = Carrito::with('detalles.variante.producto')->find($idCarrito);
        
        if (!$carrito) {
            return response()->json([
                'success' => false,
                'message' => 'Carrito no encontrado'
            ], 404);
        }
        
        $total = 0;
        foreach ($carrito->detalles as $detalle) {
            $precio = $detalle->variante->producto->precio_oferta ?? $detalle->variante->producto->precio;
            $total += $precio * $detalle->cantidad;
        }
        
        return response()->json([
            'success' => true,
            'total' => $total
        ]);
    }

    public function verificarStock($idVariante, Request $request)
    {
        $variante = ProductoVariante::find($idVariante);
        
        if (!$variante) {
            return response()->json([
                'success' => false,
                'disponible' => false
            ], 404);
        }
        
        $disponible = $variante->stock >= ($request->cantidad ?? 1);
        
        return response()->json([
            'success' => true,
            'disponible' => $disponible,
            'stock' => $variante->stock
        ]);
    }
}