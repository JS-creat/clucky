<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::with('usuario')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.pedidos.index', compact('pedidos'));
    }

    public function show($id)
    {
        $pedido = Pedido::with([
            'usuario',
            'tipoEntrega',
            'distrito.provincia.departamento',
            'detalles.variante.producto',
        ])->findOrFail($id);

        return view('admin.pedidos.show', compact('pedido'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'estado_pedido' => 'required|in:Pendiente,Confirmado,En camino,Listo para recoger,Entregado,Anulado',
        ]);

        $pedido = Pedido::findOrFail($id);

        $data = ['estado_pedido' => $request->estado_pedido];

        // Solo actualizar fecha estimada si viene con valor
        if ($request->filled('fecha_entrega_estimada')) {
            $data['fecha_entrega_estimada'] = $request->fecha_entrega_estimada;
        }

        // Auto-registrar fecha de envío
        if ($request->estado_pedido === 'En camino' && !$pedido->fecha_envio) {
            $data['fecha_envio'] = now();
        }

        // Auto-registrar fecha real de entrega
        if ($request->estado_pedido === 'Entregado' && !$pedido->fecha_entrega_real) {
            $data['fecha_entrega_real'] = now();
        }

        $pedido->update($data);

        return back()->with('success', "Pedido #{$pedido->numero_pedido} actualizado a '{$request->estado_pedido}'.");
    }
}
