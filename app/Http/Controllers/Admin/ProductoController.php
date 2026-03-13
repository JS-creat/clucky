<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Models\Genero;
use App\Models\Categoria;
use App\Models\Promocion;
use Illuminate\Support\Facades\File;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');
        $perPage = $request->get('perPage', 10);

        $productos = Producto::where('nombre_producto', 'LIKE', '%' . $buscar . '%')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.productos.index', compact('productos'));
    }

    public function create()
    {
        $generos = Genero::all();
        $categorias = Categoria::all();
        return view('admin.productos.create', compact('generos', 'categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_producto' => 'required|string|max:150',
            'precio' => 'required|numeric|min:0',
            'imagen' => 'required|image|mimes:jpg,jpeg,png,webp',
            'variantes' => 'required|array|min:1',
            'variantes.*.talla' => 'required|string|max:50',
            'variantes.*.stock' => 'required|integer|min:0',
            'variantes.*.sku' => 'nullable|string|max:50|distinct|unique:producto_variante,sku',
        ]);

        // validamos que Talla + Color son unicos en el formulario
        $combinaciones = collect($request->variantes)->map(function ($v) {
            return strtolower(trim($v['talla'])) . '-' . strtolower(trim($v['color'] ?? ''));
        });

        if ($combinaciones->duplicates()->isNotEmpty()) {
            return back()->withErrors(['variantes' => 'No puedes repetir la misma combinación de Talla y Color.'])->withInput();
        }

        $producto = Producto::create($request->only([
            'nombre_producto',
            'descripcion',
            'precio',
            'precio_oferta',
            'marca',
            'id_genero',
            'id_categoria'
        ]));

        // imagen principal
        if ($request->hasFile('imagen')) {
            $archivo = $request->file('imagen');
            $nombre = uniqid() . '.' . $archivo->getClientOriginalExtension();
            $archivo->move(public_path('productos'), $nombre);
            $producto->update(['imagen' => $nombre]);
        }

        // Creación de variantes
        foreach ($request->variantes as $v) {
            $producto->variantes()->create([
                'talla' => $v['talla'],
                'color' => $v['color'] ?? null,
                'stock' => $v['stock'],
                // Si el usuario no pone SKU generamos uno basado en el nombre para que sea más legible
                'sku' => $v['sku'] ?? strtoupper(substr($producto->nombre_producto, 0, 3)) . '-' . uniqid(),
            ]);
        }
        return redirect()->route('admin.productos.index')->with('success', 'Producto creado exitosamente.');
    }

    public function edit($id)
    {
        // Solo carga las relaciones reales (aquellas que tienen un método en el modelo)
        $producto = Producto::with('variantes')->findOrFail($id);

        $generos = Genero::all();
        $categorias = Categoria::all();
        $promociones = Promocion::where('estado_promocion', 1)->get();

        return view('admin.productos.edit', compact('producto', 'generos', 'categorias', 'promociones'));
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        //  Validación
        $request->validate([
            'nombre_producto' => 'required|string|max:150',
            'precio' => 'required|numeric|min:0',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'galeria.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'variantes' => 'required|array|min:1',
            'variantes.*.talla' => 'required|string|max:50',
            'variantes.*.stock' => 'required|integer|min:0',
            'variantes.*.sku' => 'required|string|max:50|distinct',
        ]);

        // Lógica de combinaciones duplicadas y SKU
        $combinaciones = collect($request->variantes)->map(fn($v) => strtolower(trim($v['talla'])) . '-' . strtolower(trim($v['color'] ?? '')));
        if ($combinaciones->duplicates()->isNotEmpty()) {
            return back()->withErrors(['variantes' => 'Hay combinaciones de Talla y Color duplicadas.'])->withInput();
        }

        foreach ($request->variantes as $index => $v) {
            $exists = ProductoVariante::where('sku', $v['sku'])
                ->where('id_producto', '!=', $producto->id_producto)
                ->exists();
            if ($exists) {
                return back()->withErrors(["variantes.$index.sku" => "El SKU '{$v['sku']}' ya está en uso."])->withInput();
            }
        }

        // ACTUALIZAR IMÁGENES
        $datos = $request->only(['nombre_producto', 'descripcion', 'precio', 'precio_oferta', 'marca', 'estado_producto', 'id_genero', 'id_categoria', 'id_promocion']);

        // Imagen Principal
        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $this->cargarArchivo($request->file('imagen'));
        }

        // Galería de Imágenes
        $galeriaActual = $producto->galeria ?? [];

        // Eliminar fotos marcadas en el checkbox
        if ($request->has('galeria_eliminar')) {
            foreach ($request->galeria_eliminar as $fotoEliminar) {
                File::delete(public_path('productos/' . $fotoEliminar)); // <-- Más corto y limpio
            }
            $galeriaActual = array_diff($galeriaActual, $request->galeria_eliminar);
        }

        // Agregar nuevas fotos a la galería
        if ($request->hasFile('galeria')) {
            foreach ($request->file('galeria') as $foto) {
                $galeriaActual[] = $this->cargarArchivo($foto);
            }
        }
        $datos['galeria'] = array_values($galeriaActual); // Reindexamos para evitar huecos en el array

        //  Guardar cambios del Producto
        $producto->update($datos);

        //  Sincronizar Variantes
        $idsEnviados = [];
        foreach ($request->variantes as $v) {
            $variante = $producto->variantes()->updateOrCreate(
                ['id_variante' => $v['id_variante'] ?? null],
                [
                    'talla' => $v['talla'],
                    'color' => $v['color'] ?? null,
                    'stock' => $v['stock'],
                    'sku'   => $v['sku'],
                ]
            );
            $idsEnviados[] = $variante->id_variante;
        }

        $producto->variantes()->whereNotIn('id_variante', $idsEnviados)->delete();

        return redirect()->route('admin.productos.index')->with('success', 'Producto actualizado correctamente');
    }

    /**
     * Método privado para procesar la subida de cualquier imagen
     */
    private function cargarArchivo($file)
    {
        $nombre = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('productos'), $nombre);
        return $nombre;
    }

    public function destroy($id)
    {
        $producto = Producto::with('variantes')->findOrFail($id);

        if ($producto->variantes()->where('stock', '>', 0)->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar un producto con stock.');
        }

        // Eliminar archivos
        if ($producto->imagen) File::delete(public_path('productos/' . $producto->imagen));
        if ($producto->galeria) {
            foreach ($producto->galeria as $img) File::delete(public_path('productos/' . $img));
        }

        $idCat = $producto->id_categoria;
        $producto->delete();

        $this->actualizarEstadoCategoria($idCat);

        return redirect()->route('admin.productos.index')->with('success', 'Producto eliminado.');
    }

    private function actualizarEstadoCategoria($id_categoria)
    {
        $categoria = Categoria::find($id_categoria);
        if ($categoria) {
            $tieneStock = $categoria->productos()
                ->whereHas('variantes', fn($q) => $q->where('stock', '>', 0))
                ->exists();
            $categoria->estado_categoria = $tieneStock ? 1 : 0;
            $categoria->save();
        }
    }
}
