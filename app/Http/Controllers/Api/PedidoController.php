<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PedidoController extends Controller
{
    /**
     * Obtener todos los pedidos del usuario autenticado
     */
    public function misPedidos(Request $request)
    {
        $user = Auth::user();
        
        $pedidos = Pedido::where('id_usuario', $user->id_usuario)
            ->with(['detalles.variante.producto'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $pedidos
        ]);
    }

    public function show($id)
    {
        $user = Auth::user();
        
        $pedido = Pedido::where('id_usuario', $user->id_usuario)
            ->with(['detalles.variante.producto'])
            ->find($id);
        
        if (!$pedido) {
            return response()->json([
                'success' => false,
                'message' => 'Pedido no encontrado'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $pedido
        ]);
    }
}