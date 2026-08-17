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

class ProductoController extends Controller
{
    protected $pusherBeams;

    public function __construct(PusherBeamsService $pusherBeams)
    {
        $this->pusherBeams = $pusherBeams;
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
            'nombre_producto' => 'required|string|max:150',
            'precio' => 'required|numeric|min:0',
            'precio_oferta' => 'nullable|numeric|min:0|lt:precio',
            'imagen' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'galeria.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'id_genero' => 'nullable|exists:genero,id_genero',
            'id_categoria' => 'nullable|exists:categoria,id_categoria',
            'variantes' => 'required|array|min:1',
            'variantes.*.talla' => 'required|string|max:50',
            'variantes.*.stock' => 'required|integer|min:0',
            'variantes.*.sku' => 'nullable|string|max:50',
        ]);

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
            'nombre_producto' => 'required|string|max:150',
            'precio' => 'required|numeric|min:0',
            'precio_oferta' => 'nullable|numeric|min:0|lt:precio',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'galeria.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'id_genero' => 'nullable|exists:genero,id_genero',
            'id_categoria' => 'nullable|exists:categoria,id_categoria',
            'variantes' => 'required|array|min:1',
            'variantes.*.talla' => 'required|string|max:50',
            'variantes.*.stock' => 'required|integer|min:0',
            'variantes.*.sku' => 'nullable|string|max:50',
        ]);

        $combinaciones = collect($request->variantes)->map(fn($v) => strtolower(trim($v['talla'])) . '-' . strtolower(trim($v['color'] ?? '')));
        if ($combinaciones->duplicates()->isNotEmpty()) {
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