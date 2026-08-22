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
use App\Http\Controllers\Api\CuponApiController;
use App\Http\Controllers\Api\PagoMovilApiController;
use App\Http\Controllers\Api\PasswordResetApiController;
use Illuminate\Http\Request;

// --- Autenticación y Perfil ---
Route::post('/register', [MobileAuthController::class, 'register']);
Route::post('/login', [MobileAuthController::class, 'login']);

// 🟢 Recuperación de contraseña (pública, con límite de tasa)
Route::post('/password/forgot', [PasswordResetApiController::class, 'enviarEnlace'])
    ->middleware('throttle:1,1')
    ->name('api.password.forgot');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/perfil', [MobileAuthController::class, 'perfil']);
    Route::put('/perfil', [MobileAuthController::class, 'updatePerfil']);
    Route::put('/perfil/datos-contacto', [MobileAuthController::class, 'updateDatosContacto']);
    Route::post('/chat/message', [ChatController::class, 'sendMessage']);
    
    // Mis Pedidos
    Route::get('/mis-pedidos', [App\Http\Controllers\Api\PedidoController::class, 'misPedidos']);
    Route::get('/pedidos/{id}', [App\Http\Controllers\Api\PedidoController::class, 'show']);
});

// --- Favoritos ---
Route::prefix('favoritos')->group(function () {
    Route::get('/', [FavoritoController::class, 'obtener']);
    Route::post('/agregar', [FavoritoController::class, 'agregar']);
    Route::delete('/eliminar', [FavoritoController::class, 'eliminar']);
});

// --- Carrito ---
Route::middleware('auth:sanctum')->prefix('carrito')->group(function () {
    Route::get('/', [CarritoController::class, 'obtener']);
    Route::post('/crear', [CarritoController::class, 'crear']);
    Route::post('/agregar', [CarritoController::class, 'agregar']);
    Route::put('/actualizar', [CarritoController::class, 'actualizar']);
    Route::delete('/eliminar', [CarritoController::class, 'eliminar']);
    Route::delete('/limpiar', [CarritoController::class, 'limpiar']);
    Route::get('/{idCarrito}/total', [CarritoController::class, 'total']);
});

// --- Checkout ---
Route::middleware('auth:sanctum')->prefix('checkout')->group(function () {
    Route::post('/confirmar', [CheckoutApiController::class, 'confirmar']);
    Route::post('/calcular-envio', [CheckoutApiController::class, 'calcularEnvio']);
});

// --- Cupones ---
Route::middleware('auth:sanctum')->group(function () {
    Route::get('cupones/disponibles', [CuponApiController::class, 'disponibles'])
        ->name('api.cupones.disponibles');

    Route::post('cupones/validar', [CuponApiController::class, 'validar'])
        ->name('api.cupones.validar');

    Route::post('cupones/aplicar', [CuponApiController::class, 'aplicar'])
        ->name('api.cupones.aplicar');
});

// --- Mercado Pago (Redirección Móvil) ---
Route::prefix('pago/movil')->name('api.pago.movil.')->group(function () {
    Route::get('/exito', [PagoMovilApiController::class, 'exito'])->name('exito');
    Route::get('/fallo', [PagoMovilApiController::class, 'fallo'])->name('fallo');
    Route::get('/pendiente', [PagoMovilApiController::class, 'pendiente'])->name('pendiente');
});

// --- Catálogo y Productos ---
Route::get('/variantes/{idVariante}/verificar-stock', [CarritoController::class, 'verificarStock']);

Route::prefix('productos')->group(function () {
    Route::get('/', [ProductoController::class, 'index']);
    Route::get('/recomendados', [ProductoController::class, 'recomendados']);
    Route::get('/populares', [ProductoController::class, 'populares']);
    Route::get('/ofertas', [ProductoController::class, 'ofertas']);
    Route::get('/buscar', [ProductoController::class, 'buscar']);
    Route::get('/talla/{talla}', [ProductoController::class, 'porTalla']);
    Route::get('/color/{color}', [ProductoController::class, 'porColor']);
    Route::get('/rango-precio', [ProductoController::class, 'porRangoPrecio']);

    Route::get('/{id}/variantes', [ProductoController::class, 'variantes'])->where('id', '[0-9]+');
    Route::get('/{id}', [ProductoController::class, 'show'])->where('id', '[0-9]+');
});

Route::get('/categorias', [CategoriaController::class, 'index']);
Route::get('/categorias/{id}/productos', [CategoriaController::class, 'productos']);

Route::get('/generos', [App\Http\Controllers\Api\GeneroController::class, 'index']);
Route::get('/generos/{id}/productos', [App\Http\Controllers\Api\GeneroController::class, 'productos']);

// --- Recursos Multimedia / Banners / Ubicaciones ---
Route::get('/imagen/{filename}', [ImageController::class, 'show'])
    ->where('filename', '.*\.(jpg|jpeg|png|gif|webp)$');

Route::get('/banner/{filename}', function ($filename) {
    $path = public_path('banners/' . $filename);

    if (!file_exists($path)) {
        return response()->json(['error' => 'Imagen no encontrada'], 404);
    }

    return response()->file($path);
})->where('filename', '.*');

Route::prefix('banners')->group(function () {
    Route::get('/', [BannerController::class, 'index']);
});

Route::prefix('ubicaciones')->group(function () {
    Route::get('/tipos-documento', [UbicacionController::class, 'tiposDocumento']);
    Route::get('/departamentos', [UbicacionController::class, 'departamentos']);
    Route::get('/provincias/{idDepartamento}', [UbicacionController::class, 'provincias']);
    Route::get('/distritos/{idProvincia}', [UbicacionController::class, 'distritos']);
});

Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'time' => now()]);
});git