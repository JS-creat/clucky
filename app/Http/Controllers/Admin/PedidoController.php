<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        $query = Pedido::with('usuario')->orderByDesc('created_at');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('numero_pedido', 'like', "%{$search}%")
                    ->orWhere('estado_pedido', 'like', "%{$search}%")
                    ->orWhereHas('usuario', function ($q2) use ($search) {
                        $q2->where('nombres', 'like', "%{$search}%")
                            ->orWhere('apellidos', 'like', "%{$search}%")
                            ->orWhere('correo', 'like', "%{$search}%");
                    });
            });
        }

        $pedidos = $query->paginate(15)->withQueryString();

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
        // Pendiente ya no es un estado válido para el admin
        $request->validate([
            'estado_pedido' => 'required|in:Confirmado,En camino,Listo para recoger,Entregado,Anulado',
        ]);

        $pedido = Pedido::findOrFail($id);

        // Protección extra: si el pedido está Pendiente, el admin no puede tocarlo
        if ($pedido->estado_pedido === 'Pendiente') {
            return back()->with('error', 'Este pedido está esperando confirmación de pago y no puede modificarse manualmente.');
        }

        // Protección extra: si ya está en estado final, no se puede cambiar
        if (in_array($pedido->estado_pedido, ['Entregado', 'Anulado'])) {
            return back()->with('error', 'Este pedido ya está en un estado final y no puede modificarse.');
        }

        // Validar motivo si se está anulando
        if ($request->estado_pedido === 'Anulado') {
            $request->validate([
                'motivo_anulacion' => 'required|string|max:500',
            ]);
            $data['motivo_anulacion'] = $request->motivo_anulacion;
        }

        $data = ['estado_pedido' => $request->estado_pedido];

        // Fecha estimada solo si viene con valor
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

        // Guardar motivo si se anula
        if ($request->estado_pedido === 'Anulado') {
            $data['motivo_anulacion'] = $request->motivo_anulacion;
        }

        $pedido->update($data);

        return back()->with('success', "Pedido #{$pedido->numero_pedido} actualizado a '{$request->estado_pedido}'.");
    }
}
