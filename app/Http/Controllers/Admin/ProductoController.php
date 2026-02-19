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

            $producto->galeria = json_encode($imagenesGaleria);
            $producto->save();
        }
        return redirect()
            ->route('admin.productos.index')
            ->with('success', 'Producto creado correctamente');
    }

    public function edit($id)
    {
        $producto = Producto::findOrFail($id);
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
        ]);

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

        if ($request->hasFile('imagen')) {

            // borrar imagen anterior
            if ($producto->imagen && file_exists(public_path('productos/' . $producto->imagen))) {
                unlink(public_path('productos/' . $producto->imagen));
            }

            $archivo = $request->file('imagen');
            $nombre = uniqid() . '.' . $archivo->getClientOriginalExtension();
            $archivo->move(public_path('productos'), $nombre);

            $producto->imagen = $nombre;
            $producto->save();
        }

        return redirect()
            ->route('admin.productos.index')
            ->with('success', 'Producto actualizado');
    }
}
