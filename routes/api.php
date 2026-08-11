<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Controllers API
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
use App\Http\Controllers\Api\CuponApiController;
use App\Http\Controllers\Api\PagoMovilApiController;
use App\Http\Controllers\Api\PedidoController;
use App\Http\Controllers\Api\GeneroController;

// Controllers generales
use App\Http\Controllers\NotificacionController;


/*
|--------------------------------------------------------------------------
| AUTENTICACIÓN
|--------------------------------------------------------------------------
*/

// Registro
Route::post('/register', [MobileAuthController::class, 'register']);

// Login
Route::post('/login', [MobileAuthController::class, 'login']);

// Perfil
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/perfil', [MobileAuthController::class, 'perfil']);

    Route::put('/perfil', [MobileAuthController::class, 'updatePerfil']);

    Route::put(
        '/perfil/datos-contacto',
        [MobileAuthController::class, 'updateDatosContacto']
    );
});


/*
|--------------------------------------------------------------------------
| FAVORITOS
|--------------------------------------------------------------------------
*/

Route::prefix('favoritos')->group(function () {

    Route::get('/', [FavoritoController::class, 'obtener']);

    Route::post('/agregar', [FavoritoController::class, 'agregar']);

    Route::delete('/eliminar', [FavoritoController::class, 'eliminar']);
});


/*
|--------------------------------------------------------------------------
| CARRITO
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')
    ->prefix('carrito')
    ->group(function () {

        Route::get('/', [CarritoController::class, 'obtener']);

        Route::post('/crear', [CarritoController::class, 'crear']);

        Route::post('/agregar', [CarritoController::class, 'agregar']);

        Route::put('/actualizar', [CarritoController::class, 'actualizar']);

        Route::delete('/eliminar', [CarritoController::class, 'eliminar']);

        Route::delete('/limpiar', [CarritoController::class, 'limpiar']);

        Route::get(
            '/{idCarrito}/total',
            [CarritoController::class, 'total']
        );
    });


/*
|--------------------------------------------------------------------------
| CHECKOUT
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')
    ->prefix('checkout')
    ->group(function () {

        Route::post(
            '/confirmar',
            [CheckoutApiController::class, 'confirmar']
        );

        Route::post(
            '/calcular-envio',
            [CheckoutApiController::class, 'calcularEnvio']
        );
    });


/*
|--------------------------------------------------------------------------
| STOCK / VARIANTES
|--------------------------------------------------------------------------
*/

Route::get(
    '/variantes/{idVariante}/verificar-stock',
    [CarritoController::class, 'verificarStock']
);


/*
|--------------------------------------------------------------------------
| IMÁGENES
|--------------------------------------------------------------------------
*/

Route::get('/imagen/{filename}', [ImageController::class, 'show'])
    ->where('filename', '.*\.(jpg|jpeg|png|gif|webp)$');


/*
|--------------------------------------------------------------------------
| BANNERS - IMÁGENES
|--------------------------------------------------------------------------
*/

Route::get('/banner/{filename}', function ($filename) {

    $path = public_path('banners/' . $filename);

    if (!file_exists($path)) {
        return response()->json([
            'error' => 'Imagen no encontrada'
        ], 404);
    }

    return response()->file($path);

})->where('filename', '.*');


/*
|--------------------------------------------------------------------------
| PRODUCTOS
|--------------------------------------------------------------------------
*/

Route::prefix('productos')->group(function () {

    // Listados
    Route::get('/', [ProductoController::class, 'index']);

    Route::get(
        '/recomendados',
        [ProductoController::class, 'recomendados']
    );

    Route::get(
        '/populares',
        [ProductoController::class, 'populares']
    );

    Route::get(
        '/ofertas',
        [ProductoController::class, 'ofertas']
    );

    Route::get(
        '/buscar',
        [ProductoController::class, 'buscar']
    );

    // Filtros
    Route::get(
        '/talla/{talla}',
        [ProductoController::class, 'porTalla']
    );

    Route::get(
        '/color/{color}',
        [ProductoController::class, 'porColor']
    );

    Route::get(
        '/rango-precio',
        [ProductoController::class, 'porRangoPrecio']
    );

    // Variantes
    Route::get(
        '/{id}/variantes',
        [ProductoController::class, 'variantes']
    )->where('id', '[0-9]+');

    // Detalle del producto
    Route::get(
        '/{id}',
        [ProductoController::class, 'show']
    )->where('id', '[0-9]+');
});


/*
|--------------------------------------------------------------------------
| CATEGORÍAS
|--------------------------------------------------------------------------
*/

Route::get(
    '/categorias',
    [CategoriaController::class, 'index']
);

Route::get(
    '/categorias/{id}/productos',
    [CategoriaController::class, 'productos']
);


/*
|--------------------------------------------------------------------------
| GÉNEROS
|--------------------------------------------------------------------------
*/

Route::get(
    '/generos',
    [GeneroController::class, 'index']
);

Route::get(
    '/generos/{id}/productos',
    [GeneroController::class, 'productos']
);


/*
|--------------------------------------------------------------------------
| CHAT
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::post(
        '/chat/message',
        [ChatController::class, 'sendMessage']
    );
});


/*
|--------------------------------------------------------------------------
| BANNERS
|--------------------------------------------------------------------------
*/

Route::prefix('banners')->group(function () {

    Route::get(
        '/',
        [BannerController::class, 'index']
    );
});


/*
|--------------------------------------------------------------------------
| UBICACIONES
|--------------------------------------------------------------------------
*/

Route::prefix('ubicaciones')->group(function () {

    Route::get(
        '/tipos-documento',
        [UbicacionController::class, 'tiposDocumento']
    );

    Route::get(
        '/departamentos',
        [UbicacionController::class, 'departamentos']
    );

    Route::get(
        '/provincias/{idDepartamento}',
        [UbicacionController::class, 'provincias']
    );

    Route::get(
        '/distritos/{idProvincia}',
        [UbicacionController::class, 'distritos']
    );
});


/*
|--------------------------------------------------------------------------
| HEALTH CHECK
|--------------------------------------------------------------------------
*/

Route::get('/health', function () {

    return response()->json([
        'status' => 'ok',
        'time' => now()
    ]);

});


/*
|--------------------------------------------------------------------------
| PEDIDOS
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::get(
        '/mis-pedidos',
        [PedidoController::class, 'misPedidos']
    );

    Route::get(
        '/pedidos/{id}',
        [PedidoController::class, 'show']
    );
});


/*
|--------------------------------------------------------------------------
| CUPONES
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')
    ->prefix('cupones')
    ->group(function () {

        Route::get(
            '/disponibles',
            [CuponApiController::class, 'disponibles']
        )->name('api.cupones.disponibles');

        Route::post(
            '/validar',
            [CuponApiController::class, 'validar']
        )->name('api.cupones.validar');

        Route::post(
            '/aplicar',
            [CuponApiController::class, 'aplicar']
        )->name('api.cupones.aplicar');
    });


/*
|--------------------------------------------------------------------------
| PAGO MÓVIL
|--------------------------------------------------------------------------
|
| Estas rutas NO utilizan auth:sanctum porque el WebView puede regresar
| a estos endpoints sin enviar el token de autenticación.
|
| La validación real del pago debe realizarse mediante PagoService
| consultando/verificando el estado correspondiente en Mercado Pago.
|
*/

Route::prefix('pago/movil')
    ->name('api.pago.movil.')
    ->group(function () {

        Route::get(
            '/exito',
            [PagoMovilApiController::class, 'exito']
        )->name('exito');

        Route::get(
            '/fallo',
            [PagoMovilApiController::class, 'fallo']
        )->name('fallo');

        Route::get(
            '/pendiente',
            [PagoMovilApiController::class, 'pendiente']
        )->name('pendiente');
});

