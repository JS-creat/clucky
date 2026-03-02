<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\CarritoController; // ← AGREGAR
use Illuminate\Http\Request;

// protegida perfil
Route::middleware('auth:sanctum')->get('/perfil', function (Request $request) {
    return response()->json($request->user());
});

// autenticacion
Route::post('/register', [MobileAuthController::class, 'register']);
Route::post('/login', [MobileAuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/perfil', function (Request $request) {
    return response()->json($request->user());
});

// ==================== RUTAS DE CARRITO ====================
Route::middleware('auth:sanctum')->prefix('carrito')->group(function () {
    Route::get('/', [CarritoController::class, 'obtener']); // Obtener carrito
    Route::post('/crear', [CarritoController::class, 'crear']); // Crear carrito
    Route::post('/agregar', [CarritoController::class, 'agregar']); // Agregar producto
    Route::put('/actualizar', [CarritoController::class, 'actualizar']); // Actualizar cantidad
    Route::delete('/eliminar', [CarritoController::class, 'eliminar']); // Eliminar producto
    Route::delete('/limpiar', [CarritoController::class, 'limpiar']); // Limpiar carrito
    Route::get('/{idCarrito}/total', [CarritoController::class, 'total']); // Obtener total
});

// Ruta pública para verificar stock
Route::get('/variantes/{idVariante}/verificar-stock', [CarritoController::class, 'verificarStock']);

// Rutas públicas de productos
Route::prefix('productos')->group(function () {
    Route::get('/', [ProductoController::class, 'index']);
    Route::get('/recomendados', [ProductoController::class, 'recomendados']);
    Route::get('/populares', [ProductoController::class, 'populares']);
    Route::get('/ofertas', [ProductoController::class, 'ofertas']);
    Route::get('/buscar', [ProductoController::class, 'buscar']);
    
    // ✅ NUEVA RUTA: Para obtener variantes de un producto
    Route::get('/{id}/variantes', [ProductoController::class, 'variantes'])->where('id', '[0-9]+');
    
    // Ruta para servir imágenes - DEBE IR ANTES DE /{id}
    Route::get('/{filename}', function ($filename) {
        // Validar que sea un archivo de imagen
        if (!preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $filename)) {
            abort(404);
        }
        
        $path = public_path('productos/' . $filename);
        
        if (!file_exists($path)) {
            abort(404, 'Imagen no encontrada');
        }
        
        return response()->file($path, [
            'Content-Type' => mime_content_type($path),
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET',
            'Access-Control-Allow-Headers' => '*',
        ]);
    })->where('filename', '.*\.(jpg|jpeg|png|gif|webp)$');

    // Esta ruta va al final para que no interfiera con las imágenes
    Route::get('/{id}', [ProductoController::class, 'show'])->where('id', '[0-9]+');
});

// ✅ NUEVAS RUTAS: Para búsqueda por talla, color, etc.
Route::prefix('productos')->group(function () {
    Route::get('/talla/{talla}', [ProductoController::class, 'porTalla']);
    Route::get('/color/{color}', [ProductoController::class, 'porColor']);
    Route::get('/rango-precio', [ProductoController::class, 'porRangoPrecio']);
});

// Rutas para categorías
Route::get('/categorias', [App\Http\Controllers\Api\CategoriaController::class, 'index']);
Route::get('/categorias/{id}/productos', [App\Http\Controllers\Api\CategoriaController::class, 'productos']);

// Rutas para géneros
Route::get('/generos', [App\Http\Controllers\Api\GeneroController::class, 'index']);
Route::get('/generos/{id}/productos', [App\Http\Controllers\Api\GeneroController::class, 'productos']);