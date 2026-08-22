<?php

namespace App\Http\Controllers\Admin;

use App\Mail\PromocionMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Models\Genero;
use App\Models\Categoria;
use Illuminate\Support\Facades\File;
use App\Services\PusherBeamsService;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    protected $pusherBeams;

    public function __construct(PusherBeamsService $pusherBeams)
    {
        $this->pusherBeams = $pusherBeams;
    }

    /**
     * Mensajes de validación en español, reutilizados en store() y update().
     */
    private function mensajesValidacion(): array
    {
        return [
            'nombre_producto.required' => 'El nombre del producto es obligatorio.',
            'nombre_producto.max' => 'El nombre no puede superar los 150 caracteres.',
            'nombre_producto.unique' => 'Ya existe un producto con este nombre. Usa uno distinto o edita el existente.',
            'precio.required' => 'Debes indicar un precio.',
            'precio.numeric' => 'El precio debe ser un número.',
            'precio.min' => 'El precio no puede ser negativo.',
            'precio_oferta.numeric' => 'El precio de oferta debe ser un número.',
            'precio_oferta.lt' => 'El precio de oferta debe ser menor al precio normal.',
            'marca.max' => 'La marca no puede superar los 100 caracteres.',
            'descripcion.max' => 'La descripción no puede superar los 1000 caracteres.',
            'imagen.required' => 'Debes subir una imagen principal.',
            'imagen.image' => 'El archivo de imagen principal debe ser una imagen válida.',
            'imagen.mimes' => 'La imagen principal debe ser jpg, jpeg, png o webp.',
            'imagen.max' => 'La imagen principal no puede pesar más de 2MB.',
            'galeria.*.image' => 'Cada foto de la galería debe ser una imagen válida.',
            'galeria.*.mimes' => 'Las fotos de la galería deben ser jpg, jpeg, png o webp.',
            'galeria.*.max' => 'Cada foto de la galería no puede pesar más de 2MB.',
            'id_genero.required' => 'Debes seleccionar un género.',
            'id_genero.exists' => 'El género seleccionado no es válido.',
            'id_categoria.required' => 'Debes seleccionar una categoría.',
            'id_categoria.exists' => 'La categoría seleccionada no es válida.',
            'variantes.required' => 'Debes agregar al menos una variante de stock.',
            'variantes.min' => 'Debes agregar al menos una variante de stock.',
            'variantes.*.talla.required' => 'Cada variante necesita una talla.',
            'variantes.*.color.required' => 'Cada variante necesita un color.',
            'variantes.*.stock.required' => 'Cada variante necesita indicar el stock.',
            'variantes.*.stock.integer' => 'El stock debe ser un número entero.',
            'variantes.*.stock.min' => 'El stock no puede ser negativo.',
        ];
    }

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
            'nombre_producto' => ['required', 'string', 'max:150', Rule::unique((new Producto)->getTable(), 'nombre_producto')],
            'marca' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string|max:1000',
            'precio' => 'required|numeric|min:0',
            'precio_oferta' => 'nullable|numeric|min:0|lt:precio',
            'imagen' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'galeria.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'id_genero' => 'required|exists:genero,id_genero',
            'id_categoria' => 'required|exists:categoria,id_categoria',
            'variantes' => 'required|array|min:1',
            'variantes.*.talla' => 'required|string|max:50',
            'variantes.*.color' => 'required|string|max:50',
            'variantes.*.stock' => 'required|integer|min:0',
            'variantes.*.sku' => 'nullable|string|max:50',
        ], $this->mensajesValidacion());

        $combinaciones = collect($request->variantes)->map(function ($v) {
            return strtolower(trim($v['talla'])) . '-' . strtolower(trim($v['color'] ?? ''));
        });

        if ($combinaciones->duplicates()->isNotEmpty()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'errors' => ['variantes' => ['No puedes repetir la misma combinación de Talla y Color.']],
                ], 422);
            }
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

        if ($request->hasFile('imagen')) {
            $archivo = $request->file('imagen');
            $nombre = uniqid() . '.' . $archivo->getClientOriginalExtension();
            $archivo->move(public_path('productos'), $nombre);
            $producto->update(['imagen' => $nombre]);
        }

        if ($request->hasFile('galeria')) {
            $galeria = [];
            foreach ($request->file('galeria') as $foto) {
                $galeria[] = $this->cargarArchivo($foto);
            }
            $producto->update(['galeria' => $galeria]);
        }

        foreach ($request->variantes as $v) {
            $skuFinal = !empty($v['sku']) ? $v['sku'] : 'PROD-' . strtoupper(Str::random(6));

            // Garantizar SKU único
            while (ProductoVariante::where('sku', $skuFinal)->exists()) {
                $skuFinal = 'PROD-' . strtoupper(Str::random(6));
            }

            $variante = $producto->variantes()->create([
                'talla' => $v['talla'],
                'color' => $v['color'] ?? null,
                'stock' => $v['stock'],
                'sku' => $skuFinal,
            ]);

            if ($v['stock'] > 0) {
                $variante->movimientos()->create([
                    'tipo' => 'entrada',
                    'cantidad' => $v['stock'],
                    'motivo' => 'creacion',
                    'id_usuario' => auth()->id(),
                ]);
            }
        }

        try {
            $categoriaNombre = $producto->categoria->nombre ?? '';
            $this->pusherBeams->enviarLanzamiento(
                $producto->nombre_producto,
                $categoriaNombre
            );
        } catch (\Exception $e) {
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Producto creado exitosamente.',
                'redirect' => route('admin.productos.index'),
            ]);
        }

        return redirect()->route('admin.productos.index')->with('success', 'Producto creado exitosamente.');
    }

    public function edit($id)
    {
        $producto = Producto::with('variantes')->findOrFail($id);
        $generos = Genero::all();
        $categorias = Categoria::all();

        return view('admin.productos.edit', compact('producto', 'generos', 'categorias'));
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $teniaOfertaAntes = !is_null($producto->precio_oferta) && $producto->precio_oferta > 0;
        $precioOfertaAntes = $producto->precio_oferta;

        $request->validate([
            'nombre_producto' => ['required', 'string', 'max:150', Rule::unique($producto->getTable(), 'nombre_producto')->ignore($producto->getKey(), $producto->getKeyName())],
            'marca' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string|max:1000',
            'precio' => 'required|numeric|min:0',
            'precio_oferta' => 'nullable|numeric|min:0|lt:precio',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'galeria.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'id_genero' => 'required|exists:genero,id_genero',
            'id_categoria' => 'required|exists:categoria,id_categoria',
            'variantes' => 'required|array|min:1',
            'variantes.*.talla' => 'required|string|max:50',
            'variantes.*.color' => 'required|string|max:50',
            'variantes.*.stock' => 'required|integer|min:0',
            'variantes.*.sku' => 'nullable|string|max:50',
        ], $this->mensajesValidacion());

        $combinaciones = collect($request->variantes)->map(fn($v) => strtolower(trim($v['talla'])) . '-' . strtolower(trim($v['color'] ?? '')));
        if ($combinaciones->duplicates()->isNotEmpty()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'errors' => ['variantes' => ['Hay combinaciones de Talla y Color duplicadas.']],
                ], 422);
            }
            return back()->withErrors(['variantes' => 'Hay combinaciones de Talla y Color duplicadas.'])->withInput();
        }

        $datos = $request->only(['nombre_producto', 'descripcion', 'precio', 'precio_oferta', 'marca', 'estado_producto', 'id_genero', 'id_categoria']);

        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $this->cargarArchivo($request->file('imagen'));
        }

        $galeriaActual = $producto->galeria ?? [];

        if ($request->has('galeria_eliminar')) {
            foreach ($request->galeria_eliminar as $fotoEliminar) {
                File::delete(public_path('productos/' . $fotoEliminar));
            }
            $galeriaActual = array_diff($galeriaActual, $request->galeria_eliminar);
        }

        if ($request->hasFile('galeria')) {
            foreach ($request->file('galeria') as $foto) {
                $galeriaActual[] = $this->cargarArchivo($foto);
            }
        }
        $datos['galeria'] = array_values($galeriaActual);

        $producto->update($datos);

        $idsEnviados = [];
        foreach ($request->variantes as $v) {
            $stockAnterior = null;

            if (!empty($v['id_variante'])) {
                $existente = ProductoVariante::find($v['id_variante']);
                $stockAnterior = $existente?->stock;
            }

            $skuFinal = !empty($v['sku']) ? $v['sku'] : 'PROD-' . strtoupper(Str::random(6));

            $variante = $producto->variantes()->updateOrCreate(
                ['id_variante' => $v['id_variante'] ?? null],
                [
                    'talla' => $v['talla'],
                    'color' => $v['color'] ?? null,
                    'stock' => $v['stock'],
                    'sku'   => $skuFinal,
                ]
            );

            if ($stockAnterior === null) {
                if ($v['stock'] > 0) {
                    $variante->movimientos()->create([
                        'tipo' => 'entrada',
                        'cantidad' => $v['stock'],
                        'motivo' => 'creacion',
                        'id_usuario' => auth()->id(),
                    ]);
                }
            } else {
                $diferencia = $v['stock'] - $stockAnterior;

                if ($diferencia !== 0) {
                    $variante->movimientos()->create([
                        'tipo' => $diferencia > 0 ? 'entrada' : 'salida',
                        'cantidad' => abs($diferencia),
                        'motivo' => 'ajuste_manual',
                        'id_usuario' => auth()->id(),
                    ]);
                }
            }

            $idsEnviados[] = $variante->id_variante;
        }

        $producto->variantes()->whereNotIn('id_variante', $idsEnviados)->delete();

        $tieneOfertaAhora = !is_null($request->precio_oferta) && $request->precio_oferta > 0;

        if ($tieneOfertaAhora) {
            if (!$teniaOfertaAntes || $precioOfertaAntes != $request->precio_oferta) {
                try {
                    $categoriaNombre = $producto->categoria->nombre ?? '';
                    $this->pusherBeams->enviarOferta(
                        $producto->nombre_producto,
                        $request->precio_oferta,
                        $categoriaNombre
                    );
                } catch (\Exception $e) {
                }

                $usuarios = User::whereNotNull('email_verified_at')->get();

                foreach ($usuarios as $usuario) {
                    Mail::to($usuario->correo)
                        ->send(new PromocionMail($producto));
                }
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Producto actualizado correctamente.',
                'redirect' => route('admin.productos.index'),
            ]);
        }

        return redirect()->route('admin.productos.index')->with('success', 'Producto actualizado correctamente');
    }

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
        if (!$id_categoria) return;

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