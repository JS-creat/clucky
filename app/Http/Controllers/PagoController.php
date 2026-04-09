<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagoController extends Controller
{
    public function exito(Request $request)
    {
        // El pedido ya fue guardado en CheckoutController@confirmar
        // Aquí solo actualizamos el estado a Pagado si MP confirma
        if ($request->external_reference && $request->status === 'approved') {
            \App\Models\Pedido::where('id_pedido', $request->external_reference)
                ->update([
                    'estado_pedido' => 'Pagado',
                    'payment_id'    => $request->payment_id,
                ]);
        }

        return view('pagos.exito');
    }

    public function fallo()
    {
        return view('pagos.fallo');
    }

    public function pendiente()
    {
        return view('pagos.pendiente');
    }
}
