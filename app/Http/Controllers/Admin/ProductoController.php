<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Models\Genero;
use App\Models\Categoria;
use App\Models\Promocion;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::orderBy('id_producto', 'desc')->get();
        return view('admin.productos.index', compact('productos'));
    }

    public function create()
    {
        $generos = Genero::all();
        $categorias = Categoria::all();

        return view('admin.productos.create', compact(
            'generos',
            'categorias'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_producto' => 'required|string|max:150',
            'precio' => 'required|numeric|min:0',
            'imagen' => $request->isMethod('post')
                ? 'required|image|mimes:jpg,jpeg,png,webp'
                : 'nullable|image|mimes:jpg,jpeg,png,webp',
            'galeria' => 'nullable|array',
            'galeria.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',

            'variantes' => 'required|array|min:1',
            'variantes.*.talla' => 'required|string|max:50',
            'variantes.*.stock' => 'required|integer|min:0',
            'variantes.*.sku' => 'nullable|string|max:50|distinct',
        ]);

        $producto = Producto::create($request->only([
            'nombre_producto',
            'descripcion',
            'precio',
            'precio_oferta',
            'marca',
            'id_genero',
            'id_categoria'
        ]));

        if ($request->hasFile('imagen')) {
            $archivo = $request->file('imagen');

            $extension = $archivo->getClientOriginalExtension();

            $nombre = uniqid() . '.' . $extension;

            $archivo->move(public_path('productos'), $nombre);

            $producto->imagen = $nombre;
            $producto->save();
        }

        foreach ($request->variantes as $v) {
            ProductoVariante::create([
                'id_producto' => $producto->id_producto,
                'talla' => $v['talla'],
                'color' => $v['color'] ?? null,
                'stock' => $v['stock'],
                'sku' => $v['sku'] ?? 'SKU-' . uniqid(),
            ]);
        }
        $imagenesGaleria = [];

        if ($request->hasFile('galeria')) {
            foreach ($request->file('galeria') as $img) {
                $nombre = uniqid() . '_' . $img->getClientOriginalName();
                $img->move(public_path('productos'), $nombre);
                $imagenesGaleria[] = $nombre;
            }

            $producto->galeria = $imagenesGaleria;
            $producto->save();
        }
        return redirect()
            ->route('admin.productos.index')
            ->with('success', 'Producto creado correctamente');
    }

    public function edit($id)
    {
        $producto = Producto::with('variantes')->findOrFail($id);
        $generos = Genero::all();
        $categorias = Categoria::all();
        $promociones = Promocion::where('estado_promocion', 1)->get();

        return view('admin.productos.edit', compact(
            'producto',
            'generos',
            'categorias',
            'promociones'
        ));
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $request->validate([
            'nombre_producto' => 'required|string|max:150',
            'precio' => 'required|numeric|min:0',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'variantes' => 'required|array|min:1',
            'variantes.*.talla' => 'required|string|max:50',
            'variantes.*.stock' => 'required|integer|min:0',
            'galeria' => 'nullable|array',
            'galeria.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // actualizar producto
        $producto->update($request->only([
            'nombre_producto',
            'descripcion',
            'precio',
            'precio_oferta',
            'marca',
            'estado_producto',
            'id_genero',
            'id_categoria',
            'id_promocion'
        ]));

        // imagen
        if ($request->hasFile('imagen')) {
            if ($producto->imagen && file_exists(public_path('productos/' . $producto->imagen))) {
                unlink(public_path('productos/' . $producto->imagen));
            }

            $archivo = $request->file('imagen');
            $nombre = uniqid() . '.' . $archivo->getClientOriginalExtension();
            $archivo->move(public_path('productos'), $nombre);

            $producto->imagen = $nombre;
            $producto->save();
        }

        // ===== VARIANTES =====
        $idsEnviados = [];

        foreach ($request->variantes as $v) {

            if (!empty($v['id_variante'])) {
                // actualizar existente
                $variante = ProductoVariante::find($v['id_variante']);
                $variante->update([
                    'talla' => $v['talla'],
                    'color' => $v['color'] ?? null,
                    'stock' => $v['stock'],
                    'sku' => $v['sku'] ?? $variante->sku,
                ]);

                $idsEnviados[] = $variante->id_variante;
            } else {
                // crear nueva
                $nueva = $producto->variantes()->create([
                    'talla' => $v['talla'],
                    'color' => $v['color'] ?? null,
                    'stock' => $v['stock'],
                    'sku' => $v['sku'] ?? 'SKU-' . uniqid(),
                ]);

                $idsEnviados[] = $nueva->id_variante;
            }
        }

        // ===== ELIMINAR IMAGENES DE GALERIA =====
        if ($request->has('galeria_eliminar')) {

            $galeria = $producto->galeria ?? [];

            foreach ($request->galeria_eliminar as $img) {
                if (in_array($img, $galeria)) {

                    if (file_exists(public_path('productos/' . $img))) {
                        unlink(public_path('productos/' . $img));
                    }

                    $galeria = array_values(array_diff($galeria, [$img]));
                }
            }

            $producto->galeria = $galeria;
            $producto->save();
        }

        // ===== AGREGAR NUEVAS IMAGENES A GALERIA =====
        if ($request->hasFile('galeria')) {

            $galeriaActual = $producto->galeria ?? [];

            foreach ($request->file('galeria') as $img) {
                $nombre = uniqid() . '_' . $img->getClientOriginalName();
                $img->move(public_path('productos'), $nombre);
                $galeriaActual[] = $nombre;
            }

            $producto->galeria = $galeriaActual;
            $producto->save();
        }

        // eliminar variantes removidas del formulario
        $producto->variantes()
            ->whereNotIn('id_variante', $idsEnviados)
            ->delete();

        return redirect()
            ->route('admin.productos.index')
            ->with('success', 'Producto actualizado correctamente');
    }
}
