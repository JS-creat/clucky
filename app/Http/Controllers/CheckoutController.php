<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\Departamento;
use Illuminate\Support\Facades\Auth;
use App\Models\TipoEntrega;
use App\Models\TipoDocumento;
use App\Models\Agencia;

class CheckoutController extends Controller
{
    public function index()
    {
        // Si hay carrito en sesión, lo pasamos a la BD
        if (session()->has('carrito')) {

            $carrito = Carrito::firstOrCreate([
                'id_usuario' => Auth::id(),
            ]);

            foreach (session('carrito') as $idVariante => $item) {

                \App\Models\DetalleCarrito::updateOrCreate(
                    [
                        'id_carrito' => $carrito->id_carrito,
                        'id_variante' => $idVariante,   // ✅ era id_producto
                    ],
                    [
                        'cantidad' => $item['cantidad']
                    ]
                );
            }

            session()->forget('carrito');
        }


        $carrito = Carrito::with('detalles.variante.producto')
            ->where('id_usuario', Auth::id())
            ->first();

        $departamentos  = Departamento::all();
        $tiposEntrega   = TipoEntrega::where('estado', 1)->get();
        $tiposDocumento = TipoDocumento::all();
        $agencias       = Agencia::where('estado', 1)->get();

        return view('carrito.checkout', compact(
            'carrito',
            'departamentos',
            'tiposEntrega',
            'tiposDocumento',
            'agencias'
        ));
    }
}
