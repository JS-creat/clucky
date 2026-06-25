<?php

namespace App\Http\Controllers\Admin;

use App\Mail\PedidoListo;
use Illuminate\Support\Facades\Mail;
use App\Mail\PedidoAnulado;
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
        $request->validate([
            'estado_pedido' => 'required|in:Confirmado,En camino,Listo para recoger,Entregado,Anulado',
        ]);

        $pedido = Pedido::findOrFail($id);

        if ($pedido->estado_pedido === 'Pendiente') {
            return back()->with('error', 'Este pedido está esperando confirmación de pago y no puede modificarse manualmente.');
        }

        if (in_array($pedido->estado_pedido, ['Entregado', 'Anulado'])) {
            return back()->with('error', 'Este pedido ya está en un estado final y no puede modificarse.');
        }

        if ($request->estado_pedido === 'Anulado') {
            $request->validate([
                'motivo_anulacion' => 'required|string|max:500',
            ]);
        }

        $data = ['estado_pedido' => $request->estado_pedido];

        if ($request->filled('fecha_entrega_estimada')) {
            $data['fecha_entrega_estimada'] = $request->fecha_entrega_estimada;
        }

        if ($request->estado_pedido === 'En camino' && !$pedido->fecha_envio) {
            $data['fecha_envio'] = now();
        }

        if ($request->estado_pedido === 'Entregado' && !$pedido->fecha_entrega_real) {
            $data['fecha_entrega_real'] = now();
        }

        if ($request->estado_pedido === 'Anulado') {
            $data['motivo_anulacion'] = $request->motivo_anulacion;
        }

        $pedido->update($data);
        if ($request->estado_pedido === 'Anulado') {
            $pedido->load('usuario');

            Mail::to($pedido->usuario->correo)
                ->send(new PedidoAnulado($pedido));
        }

        if ($request->estado_pedido === 'Listo para recoger') {
            $pedido->load('usuario');

            Mail::to($pedido->usuario->correo)
                ->send(new PedidoListo($pedido));
        }

        return back()->with('success', "Pedido #{$pedido->numero_pedido} actualizado a '{$request->estado_pedido}'.");
    }
}
