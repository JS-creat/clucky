<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;
use App\Models\Carrito;
use App\Models\DetalleCarrito;
class CarritoController extends Controller
{

    public function add(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        if (Auth::check()) {

            $carrito = Carrito::firstOrCreate([
            'id_usuario' => Auth::id()
            ]);

            $detalle = DetalleCarrito::where('id_carrito', $carrito->id_carrito)
                ->where('id_producto', $id)
                ->first();

            if ($detalle) {

                if ($detalle->cantidad + 1 > $producto->stock) {
                    return redirect()->back()->with('error', 'Stock no disponible');
                }

                $detalle->cantidad++;
                $detalle->save();

            } else {

                if ($producto->stock < 1) {
                    return redirect()->back()->with('error', 'Stock no disponible');
                }

                DetalleCarrito::create([
                    'id_carrito' => $carrito->id_carrito,
                    'id_producto' => $id,
                    'cantidad' => 1
                ]);
            }

        } else {

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
        }

        return redirect()->back()->with('success', 'Producto añadido correctamente');
    }



    public function index()
    {
        if (Auth::check()) {

            $carrito = Carrito::with('detalles.producto')
                ->where('id_usuario', Auth::id())
                ->first();

            $items = [];
            $total = 0;

            if ($carrito) {
                foreach ($carrito->detalles as $detalle) {
                    $items[$detalle->id_producto] = [
                        "nombre" => $detalle->producto->nombre_producto,
                        "cantidad" => $detalle->cantidad,
                        "precio" => $detalle->producto->precio_oferta ?? $detalle->producto->precio,
                        "imagen" => $detalle->producto->imagen
                    ];

                    $total += $items[$detalle->id_producto]['precio'] * $detalle->cantidad;
                }
            }

            return view('carrito.index', [
                'carrito' => $items,
                'total' => $total
            ]);

        } else {

            $carrito = session()->get('carrito', []);
            $total = 0;

            foreach($carrito as $item) {
                $total += $item['precio'] * $item['cantidad'];
            }

            return view('carrito.index', compact('carrito', 'total'));
        }
    }



    public function aumentar($id)
    {
        $producto = Producto::findOrFail($id);

        if (Auth::check()) {

            $carrito = Carrito::where('id_usuario', Auth::id())->first();

            if ($carrito) {

                $detalle = DetalleCarrito::where('id_carrito', $carrito->id_carrito)
                    ->where('id_producto', $id)
                    ->first();

                if ($detalle && $detalle->cantidad < $producto->stock) {
                    $detalle->cantidad++;
                    $detalle->save();
                } else {
                    return redirect()->back()->with('error', 'No hay más stock disponible');
                }
            }

        } else {

            $carrito = session()->get('carrito', []);

            if (isset($carrito[$id])) {

                if ($carrito[$id]['cantidad'] < $producto->stock) {
                    $carrito[$id]['cantidad']++;
                    session()->put('carrito', $carrito);
                } else {
                    return redirect()->back()->with('error', 'No hay más stock disponible');
                }
            }
        }

        return redirect()->back();
    }


    public function disminuir($id)
    {
        if (Auth::check()) {

            $carrito = Carrito::where('id_usuario', Auth::id())->first();

            if ($carrito) {

                $detalle = DetalleCarrito::where('id_carrito', $carrito->id_carrito)
                    ->where('id_producto', $id)
                    ->first();

                if ($detalle) {

                    if ($detalle->cantidad > 1) {
                        $detalle->cantidad--;
                        $detalle->save();
                    } else {
                        $detalle->delete();
                    }
                }
            }

            return redirect()->route('carrito.index');

        } else {

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
    }



    public function eliminar($id)
    {
        if (Auth::check()) {

            $carrito = Carrito::where('id_usuario', Auth::id())->first();

            if ($carrito) {

                DetalleCarrito::where('id_carrito', $carrito->id_carrito)
                    ->where('id_producto', $id)
                    ->delete();
            }

            return redirect()->route('carrito.index');

        } else {

            $carrito = session()->get('carrito', []);

            if(isset($carrito[$id])) {
                unset($carrito[$id]);
                session()->put('carrito', $carrito);
            }

            return redirect()->route('carrito.index');
        }
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

        return view('carrito.checkout', compact('carrito'));

        }

    }
