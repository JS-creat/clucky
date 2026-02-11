<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;

class CarritoController extends Controller
{

    public function add(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);
        $carrito = session()->get('carrito', []);
        $cantidadActual = isset($carrito[$id]) ? $carrito[$id]['cantidad'] : 0;

        if ($cantidadActual + 1 > $producto->stock) {
            return redirect()->back()->with('error', 'Stock no disponible');
        }

        if(isset($carrito[$id])) {
            $carrito[$id]['cantidad']++;
        } else {
            $carrito[$id] = [
                "nombre" => $producto->nombre_producto,
                "cantidad" => 1,
                "precio" => $producto->precio_oferta ?? $producto->precio,
                "imagen" => $producto->imagen
            ];
        }

        session()->put("carrito", $carrito);

        return redirect()->back()->with('success', 'Producto añadido correctamente');

    }


    public function index()
    {
        $carrito = session()->get('carrito', []);
        $total = 0;

        foreach($carrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }

        return view('carrito.index', compact('carrito', 'total'));
    }


    public function aumentar($id)
    {
        $carrito = session()->get('carrito', []);
        $producto = Producto::findOrFail($id);

        if(isset($carrito[$id])) {

            if($carrito[$id]['cantidad'] < $producto->stock) {
                $carrito[$id]['cantidad']++;
            } else {
            return redirect()->back()->with('error', 'No hay más stock disponible');
            }

            session()->put('carrito', $carrito);
        }

        return redirect()->back();
    }


    public function disminuir($id)
    {
        $carrito = session()->get('carrito', []);

        if(isset($carrito[$id])) {

            if($carrito[$id]['cantidad'] > 1){
                $carrito[$id]['cantidad']--;
            } else {
                unset($carrito[$id]);
            }

            session()->put('carrito', $carrito);
        }

        return redirect()->route('carrito.index');
    }


    public function eliminar($id)
    {
        $carrito = session()->get('carrito', []);

        if(isset($carrito[$id])) {
            unset($carrito[$id]);
            session()->put('carrito', $carrito);
        }

        return redirect()->route('carrito.index');
    }


    public function checkout()
    {
        $carrito = session()->get('carrito', []);

        // VALIDAR STOCK ANTES DE PAGAR
        foreach($carrito as $id => $item) {

            $producto = Producto::findOrFail($id);

            if($item['cantidad'] > $producto->stock) {
                return redirect()->route('carrito.index')
                    ->with('error', 'Algunos productos ya no tienen stock suficiente');
            }
        }

        return view('carrito.checkout');
        }

    }
