<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductoResource;
use App\Http\Resources\VarianteResource;
use App\Models\Producto;
use App\Models\ProductoVariante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductoController extends Controller
{
    /**
     * Listar productos con paginación
     */
    public function index(Request $request)
    {
        try {
            $query = Producto::where('estado_producto', 1)
                ->with(['categoria', 'genero', 'variantes']);

            // Filtros
            if ($request->has('categoria')) {
                $query->where('id_categoria', $request->categoria);
            }

            if ($request->has('genero')) {
                $query->where('id_genero', $request->genero);
            }

            if ($request->has('talla') && !empty($request->talla)) {
                $query->whereHas('variantes', function($q) use ($request) {
                    $q->where('talla', $request->talla)->where('stock', '>', 0);
                });
            }

            if ($request->has('color') && !empty($request->color)) {
                $query->whereHas('variantes', function($q) use ($request) {
                    $q->where('color', $request->color)->where('stock', '>', 0);
                });
            }

            // Corregido: Se usa precio_venta que es el campo real en tu BD
            if ($request->has('precio_min') && $request->has('precio_max')) {
                $query->whereBetween('precio_venta', [$request->precio_min, $request->precio_max]);
            }

            // Filtro de búsqueda básico y seguro
            if ($request->has('busqueda') && !empty($request->busqueda)) {
                $query->where('nombre_producto', 'LIKE', '%' . $request->busqueda . '%');
            }

            // Ordenamiento - Adaptado a tus columnas reales
            $orden = $request->get('orden', 'created_at');
            $direccion = $request->get('direccion', 'desc');

            $ordenPermitido = ['created_at', 'nombre_producto', 'precio_venta', 'id_producto'];
            if (!in_array($orden, $ordenPermitido)) {
                $orden = 'created_at';
            }

            $query->orderBy($orden, $direccion);

            $productos = $query->paginate($request->get('limit', 10));

            return response()->json([
                'success' => true,
                'data' => ProductoResource::collection($productos),
                'current_page' => $productos->currentPage(),
                'last_page' => $productos->lastPage(),
                'total' => $productos->total(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error en index: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al listar productos'], 500);
        }
    }

    /**
     * Obtener un producto por ID
     */
    public function show($id)
    {
        $producto = Producto::where('estado_producto', 1)
            ->with(['categoria', 'genero', 'variantes'])
            ->find($id);

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new ProductoResource($producto)
        ]);
    }

    /**
     * Obtener variantes de un producto
     */
    public function variantes($id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }

        $variantes = $producto->variantes()
            ->where('stock', '>', 0)
            ->get();

        return response()->json([
            'success' => true,
            'data' => VarianteResource::collection($variantes)
        ]);
    }

    /**
     * Obtener productos recomendados
     */
    public function recomendados(Request $request)
    {
        try {
            $query = Producto::where('estado_producto', 1)
                ->with(['categoria', 'genero', 'variantes']);

            if ($request->has('genero_id')) {
                $query->where('id_genero', $request->genero_id);
            }

            if ($request->has('categoria_id')) {
                $query->where('id_categoria', $request->categoria_id);
            }

            $limit = $request->get('limit', 10);
            $productos = $query->inRandomOrder()->limit($limit)->get();

            return response()->json([
                'success' => true,
                'data' => ProductoResource::collection($productos)
            ]);
        } catch (\Exception $e) {
            Log::error('Error en recomendados: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar recomendados: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener productos populares
     */
    public function populares(Request $request)
    {
        try {
            $query = Producto::where('estado_producto', 1)
                ->with(['categoria', 'genero', 'variantes']);

            if ($request->has('genero_id')) {
                $query->where('id_genero', $request->genero_id);
            }

            if ($request->has('categoria_id')) {
                $query->where('id_categoria', $request->categoria_id);
            }

            $limit = $request->get('limit', 10);
            // Ordenamos por id_producto descendente para mostrar novedades como populares
            $productos = $query->orderBy('id_producto', 'desc')->limit($limit)->get();

            return response()->json([
                'success' => true,
                'data' => ProductoResource::collection($productos)
            ]);
        } catch (\Exception $e) {
            Log::error('Error en populares: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar populares: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener productos en oferta
     */
    public function ofertas(Request $request)
    {
        try {
            $query = Producto::where('estado_producto', 1)
                ->whereNotNull('precio_oferta')
                ->where('precio_oferta', '>', 0)
                ->with(['categoria', 'genero', 'variantes']);

            if ($request->has('genero_id')) {
                $query->where('id_genero', $request->genero_id);
            }

            $productos = $query->paginate($request->get('limit', 10));

            return response()->json([
                'success' => true,
                'data' => ProductoResource::collection($productos),
                'current_page' => $productos->currentPage(),
                'last_page' => $productos->lastPage(),
                'total' => $productos->total(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error en ofertas: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al cargar ofertas'], 500);
        }
    }

    /**
     * Buscar productos
     */
    public function buscar(Request $request)
    {
        try {
            $request->validate([
                'q' => 'required|string|min:2'
            ]);

            $query = Producto::where('estado_producto', 1)
                ->where('nombre_producto', 'LIKE', '%' . $request->q . '%')
                ->with(['categoria', 'genero', 'variantes']);

            if ($request->has('genero_id')) {
                $query->where('id_genero', $request->genero_id);
            }

            $productos = $query->paginate($request->get('limit', 10));

            return response()->json([
                'success' => true,
                'data' => ProductoResource::collection($productos),
                'current_page' => $productos->currentPage(),
                'last_page' => $productos->lastPage(),
                'total' => $productos->total(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error en buscar: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error en la búsqueda'], 500);
        }
    }

    /**
     * Filtrar productos por rango de precio
     */
    public function porRangoPrecio(Request $request)
    {
        try {
            $request->validate([
                'min' => 'required|numeric|min:0',
                'max' => 'required|numeric|min:0|gt:min',
            ]);

            $query = Producto::where('estado_producto', 1)
                ->whereBetween('precio_venta', [$request->min, $request->max])
                ->with(['categoria', 'genero', 'variantes']);

            if ($request->has('genero_id')) {
                $query->where('id_genero', $request->genero_id);
            }

            $productos = $query->paginate($request->get('limit', 10));

            return response()->json([
                'success' => true,
                'data' => ProductoResource::collection($productos),
                'current_page' => $productos->currentPage(),
                'last_page' => $productos->lastPage(),
                'total' => $productos->total(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error en porRangoPrecio: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al filtrar por rango de precio'], 500);
        }
    }
}
