<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\CarritoController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\FavoritoController; 
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\UbicacionController;
use App\Http\Controllers\Api\CheckoutApiController;
use App\Http\Controllers\NotificacionController;
use Illuminate\Http\Request;

// protegida perfil
Route::middleware('auth:sanctum')->get('/perfil', [MobileAuthController::class, 'perfil']);

// autenticacion
Route::post('/register', [MobileAuthController::class, 'register']);
Route::post('/login', [MobileAuthController::class, 'login']);

// Actualizar perfil
Route::middleware('auth:sanctum')->put('/perfil', [MobileAuthController::class, 'updatePerfil']);

// ==================== RUTAS DE FAVORITOS ====================
Route::prefix('favoritos')->group(function () {
    Route::get('/', [FavoritoController::class, 'obtener']);
    Route::post('/agregar', [FavoritoController::class, 'agregar']);
    Route::delete('/eliminar', [FavoritoController::class, 'eliminar']);
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

// ==================== RUTAS DE CHECKOUT ====================
Route::middleware('auth:sanctum')->prefix('checkout')->group(function () {
    Route::post('/confirmar', [CheckoutApiController::class, 'confirmar']);
    Route::post('/calcular-envio', [CheckoutApiController::class, 'calcularEnvio']); 
});

// Ruta pública para verificar stock
Route::get('/variantes/{idVariante}/verificar-stock', [CarritoController::class, 'verificarStock']);

// ==================== RUTA DE IMÁGENES (PRODUCTOS) ====================
Route::get('/imagen/{filename}', [ImageController::class, 'show'])
    ->where('filename', '.*\.(jpg|jpeg|png|gif|webp)$');

// ==================== RUTA DE IMÁGENES DE BANNERS ====================
Route::get('/banner/{filename}', function ($filename) {
    $path = public_path('banners/' . $filename);
    
    if (!file_exists($path)) {
        return response()->json(['error' => 'Imagen no encontrada'], 404);
    }
    
    return response()->file($path);
})->where('filename', '.*');

// ==================== RUTAS DE PRODUCTOS ====================
Route::prefix('productos')->group(function () {
    Route::get('/', [ProductoController::class, 'index']);
    Route::get('/recomendados', [ProductoController::class, 'recomendados']);
    Route::get('/populares', [ProductoController::class, 'populares']);
    Route::get('/ofertas', [ProductoController::class, 'ofertas']);
    Route::get('/buscar', [ProductoController::class, 'buscar']);
    
    Route::get('/{id}/variantes', [ProductoController::class, 'variantes'])->where('id', '[0-9]+');
    Route::get('/{id}', [ProductoController::class, 'show'])->where('id', '[0-9]+');
});

// Rutas adicionales de productos (filtros)
Route::prefix('productos')->group(function () {
    Route::get('/talla/{talla}', [ProductoController::class, 'porTalla']);
    Route::get('/color/{color}', [ProductoController::class, 'porColor']);
    Route::get('/rango-precio', [ProductoController::class, 'porRangoPrecio']);
});

// Rutas para categorías
Route::get('/categorias', [CategoriaController::class, 'index']);
Route::get('/categorias/{id}/productos', [CategoriaController::class, 'productos']);

// Rutas para géneros
Route::get('/generos', [App\Http\Controllers\Api\GeneroController::class, 'index']);
Route::get('/generos/{id}/productos', [App\Http\Controllers\Api\GeneroController::class, 'productos']);

// Rutas del chat
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/chat/message', [ChatController::class, 'sendMessage']);
});

// Rutas de banners
Route::prefix('banners')->group(function () {
    Route::get('/', [BannerController::class, 'index']);
});

// ==================== RUTAS DE UBICACIONES ====================
Route::prefix('ubicaciones')->group(function () {
    Route::get('/tipos-documento', [UbicacionController::class, 'tiposDocumento']);
    Route::get('/departamentos', [UbicacionController::class, 'departamentos']);
    Route::get('/provincias/{idDepartamento}', [UbicacionController::class, 'provincias']);
    Route::get('/distritos/{idProvincia}', [UbicacionController::class, 'distritos']);
});

