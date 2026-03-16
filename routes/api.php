<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\CarritoController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\FavoritoController; 
use App\Http\Controllers\NotificacionController;
use Illuminate\Http\Request;
use Illuminate\Http\Middleware\HandleCors;

// protegida perfil
Route::middleware('auth:sanctum')->get('/perfil', function (Request $request) {
    return response()->json($request->user());
});

// autenticacion
Route::post('/register', [MobileAuthController::class, 'register']);
Route::post('/login', [MobileAuthController::class, 'login']);

// ==================== RUTAS DE FAVORITOS ====================
Route::prefix('favoritos')->group(function () {
    Route::get('/', [FavoritoController::class, 'obtener']); // Obtener favoritos del usuario
    Route::post('/agregar', [FavoritoController::class, 'agregar']); // Agregar a favoritos
    Route::delete('/eliminar', [FavoritoController::class, 'eliminar']); // Eliminar de favoritos
});

// ==================== RUTAS DE CARRITO ====================
Route::middleware('auth:sanctum')->prefix('carrito')->group(function () {
    Route::get('/', [CarritoController::class, 'obtener']);
    Route::post('/crear', [CarritoController::class, 'crear']);
    Route::post('/agregar', [CarritoController::class, 'agregar']);
    Route::put('/actualizar', [CarritoController::class, 'actualizar']);
    Route::delete('/eliminar', [CarritoController::class, 'eliminar']);
    Route::delete('/limpiar', [CarritoController::class, 'limpiar']);
    Route::get('/{idCarrito}/total', [CarritoController::class, 'total']);
});

// Ruta pública para verificar stock
Route::get('/variantes/{idVariante}/verificar-stock', [CarritoController::class, 'verificarStock']);

// ==================== RUTA DE IMÁGENES (NUEVA) ====================
Route::get('/imagen/{filename}', [ImageController::class, 'show'])
    ->where('filename', '.*\.(jpg|jpeg|png|gif|webp)$');

// ==================== RUTAS DE PRODUCTOS ====================
Route::prefix('productos')->group(function () {
    Route::get('/', [ProductoController::class, 'index']);
    Route::get('/recomendados', [ProductoController::class, 'recomendados']);
    Route::get('/populares', [ProductoController::class, 'populares']);
    Route::get('/ofertas', [ProductoController::class, 'ofertas']);
    Route::get('/buscar', [ProductoController::class, 'buscar']);
    
    // Obtener variantes de un producto
    Route::get('/{id}/variantes', [ProductoController::class, 'variantes'])->where('id', '[0-9]+');

    // Obtener producto por ID (va al final)
    Route::get('/{id}', [ProductoController::class, 'show'])->where('id', '[0-9]+');
});

// Rutas adicionales de productos (filtros)
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