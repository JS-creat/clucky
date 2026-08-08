<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Carrito;
use App\Models\DetalleCarrito;
use App\Models\ProductoVariante;

class CarritoController extends Controller
{

    /**
     * Arma el resumen completo del carrito (items + total) leyendo
     * el estado ACTUAL desde BD o sesión, según corresponda.
     * Lo reutilizamos tanto en index() como en las respuestas JSON.
     */
    private function resumenCarrito(): array
    {
        $items = [];
        $total = 0;

        if (Auth::check()) {

            $carrito = Carrito::with('detalles.variante.producto')
                ->where('id_usuario', Auth::id())
                ->first();

            if ($carrito) {
                foreach ($carrito->detalles as $detalle) {

                    $variante = $detalle->variante;
                    $producto = $variante->producto;

                    $items[$detalle->id_variante] = [
                        "id_variante" => $detalle->id_variante,
                        "nombre" => $producto->nombre_producto,
                        "cantidad" => $detalle->cantidad,
                        "precio" => $producto->precio_oferta ?? $producto->precio,
                        "imagen" => $producto->imagen,
                        "talla" => $variante->talla,
                        "color" => $variante->color
                    ];

                    $total += $items[$detalle->id_variante]['precio'] * $detalle->cantidad;
                }
            }
        } else {

            $items = session()->get('carrito', []);

            foreach ($items as $item) {
                $total += $item['precio'] * $item['cantidad'];
            }
        }

        return [
            'items' => $items,
            'total' => $total,
            'cantidad_items' => count($items),
        ];
    }

    /**
     * Responde JSON si la petición lo pide (fetch de Alpine),
     * o redirige normal si es una navegación tradicional (fallback).
     */
    private function responder(Request $request, string $mensaje = null, string $tipoMensaje = 'success')
    {
        if ($request->wantsJson()) {

            $resumen = $this->resumenCarrito();

            return response()->json([
                'ok' => $tipoMensaje !== 'error',
                'mensaje' => $mensaje,
                'items' => $resumen['items'],
                'total' => $resumen['total'],
                'cantidad_items' => $resumen['cantidad_items'],
            ], $tipoMensaje === 'error' ? 422 : 200);
        }

        $redirect = redirect()->route('carrito.index');

        return $mensaje
            ? $redirect->with($tipoMensaje, $mensaje)
            : $redirect;
    }


    // AGREGAR
    public function add(Request $request, $id)
    {
        $request->validate([
            'id_variante' => 'required|integer|exists:producto_variante,id_variante',
        ]);

        $id_variante = $request->id_variante;

        return DB::transaction(function () use ($id_variante, $request) {

            // lockForUpdate() bloquea la fila hasta que termine la transacción,
            // así ninguna otra request puede leer/modificar el stock al mismo tiempo
            $variante = ProductoVariante::with('producto')
                ->lockForUpdate()
                ->findOrFail($id_variante);

            if ($variante->stock < 1) {
                return $this->responder($request, 'Stock no disponible', 'error');
            }

            // USUARIO LOGUEADO → BD
            if (Auth::check()) {

                $carrito = Carrito::firstOrCreate([
                    'id_usuario' => Auth::id()
                ]);

                $detalle = DetalleCarrito::where('id_carrito', $carrito->id_carrito)
                    ->where('id_variante', $id_variante)
                    ->lockForUpdate()
                    ->first();

                if ($detalle) {

                    if ($detalle->cantidad + 1 > $variante->stock) {
                        return $this->responder($request, 'Stock no disponible', 'error');
                    }

                    $detalle->cantidad++;
                    $detalle->save();
                } else {

                    DetalleCarrito::create([
                        'id_carrito' => $carrito->id_carrito,
                        'id_variante' => $id_variante,
                        'cantidad' => 1
                    ]);
                }
            }

            // INVITADO → SESSION
            // Nota: aquí no hay concurrencia real entre requests (cada sesión es
            // independiente), pero SÍ seguimos comparando contra el stock real
            // que acabamos de leer con el lock.
            else {

                $carrito = session()->get('carrito', []);

                if (isset($carrito[$id_variante])) {

                    if ($carrito[$id_variante]['cantidad'] + 1 > $variante->stock) {
                        return $this->responder($request, 'Stock no disponible', 'error');
                    }

                    $carrito[$id_variante]['cantidad']++;
                } else {

                    $carrito[$id_variante] = [
                        "id_variante" => $id_variante,
                        "nombre" => $variante->producto->nombre_producto,
                        "cantidad" => 1,
                        "precio" => $variante->producto->precio_oferta ?? $variante->producto->precio,
                        "imagen" => $variante->producto->imagen,
                        "talla" => $variante->talla,
                        "color" => $variante->color
                    ];
                }

                session()->put('carrito', $carrito);
            }

            return $this->responder($request, 'Producto agregado al carrito');
        });
    }


    // MOSTRAR
    public function index()
    {
        $resumen = $this->resumenCarrito();

        return view('carrito.index', [
            'items' => $resumen['items'],
            'total' => $resumen['total'],
        ]);
    }


    // AUMENTAR
    public function aumentar(Request $request, $id_variante)
    {
        return DB::transaction(function () use ($id_variante, $request) {

            $variante = ProductoVariante::lockForUpdate()->findOrFail($id_variante);

            if (Auth::check()) {

                $carrito = Carrito::where('id_usuario', Auth::id())->first();

                if ($carrito) {

                    $detalle = DetalleCarrito::where('id_carrito', $carrito->id_carrito)
                        ->where('id_variante', $id_variante)
                        ->lockForUpdate()
                        ->first();

                    if ($detalle && $detalle->cantidad < $variante->stock) {
                        $detalle->cantidad++;
                        $detalle->save();
                    } else {
                        return $this->responder($request, 'Stock no disponible', 'error');
                    }
                }
            } else {

                $carrito = session()->get('carrito', []);

                if (isset($carrito[$id_variante])) {

                    if ($carrito[$id_variante]['cantidad'] < $variante->stock) {
                        $carrito[$id_variante]['cantidad']++;
                        session()->put('carrito', $carrito);
                    } else {
                        return $this->responder($request, 'Stock no disponible', 'error');
                    }
                }
            }

            return $this->responder($request);
        });
    }


    // DISMINUIR
    public function disminuir(Request $request, $id_variante)
    {
        return DB::transaction(function () use ($id_variante, $request) {

            // Aquí no necesitamos leer stock (solo restamos), pero sí bloqueamos
            // el detalle para evitar que dos clics simultáneos lo dejen en un
            // estado raro (ej. restar dos veces sobre cantidad = 1)
            if (Auth::check()) {

                $carrito = Carrito::where('id_usuario', Auth::id())->first();

                if ($carrito) {

                    $detalle = DetalleCarrito::where('id_carrito', $carrito->id_carrito)
                        ->where('id_variante', $id_variante)
                        ->lockForUpdate()
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
            } else {

                $carrito = session()->get('carrito', []);

                if (isset($carrito[$id_variante])) {

                    if ($carrito[$id_variante]['cantidad'] > 1) {
                        $carrito[$id_variante]['cantidad']--;
                    } else {
                        unset($carrito[$id_variante]);
                    }

                    session()->put('carrito', $carrito);
                }
            }

            return $this->responder($request);
        });
    }


    // ELIMINAR
    public function eliminar(Request $request, $id_variante)
    {
        if (Auth::check()) {

            $carrito = Carrito::where('id_usuario', Auth::id())->first();

            if ($carrito) {

                DetalleCarrito::where('id_carrito', $carrito->id_carrito)
                    ->where('id_variante', $id_variante)
                    ->delete();
            }
        } else {

            $carrito = session()->get('carrito', []);

            unset($carrito[$id_variante]);

            session()->put('carrito', $carrito);
        }

        return $this->responder($request, 'Producto eliminado del carrito');
    }
}
