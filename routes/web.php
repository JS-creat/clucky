<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\UbicacionController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\Admin\ProductoController;
use App\Models\Producto;
use App\Models\Agencia;
use App\Models\Provincia;
use App\Models\Distrito;

//rutas publicas

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/producto/{id}', function ($id) {
    $producto = Producto::with('variantes')->findOrFail($id);
    return view('producto.detalle', compact('producto'));
})->name('producto.show');

//CARRITO

Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
Route::post('/carrito/add/{id}', [CarritoController::class, 'add'])->name('carrito.add');
Route::get('/carrito/aumentar/{id}', [CarritoController::class, 'aumentar'])->name('carrito.aumentar');
Route::get('/carrito/disminuir/{id}', [CarritoController::class, 'disminuir'])->name('carrito.disminuir');
Route::get('/carrito/eliminar/{id}', [CarritoController::class, 'eliminar'])->name('carrito.eliminar');


// USUARIOS AUTENTICADOS


Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/finalizar-compra', [CheckoutController::class, 'index'])
        ->name('carrito.checkout');

    Route::put('/usuario/actualizar', [UsuarioController::class, 'actualizar'])
        ->name('usuario.actualizar');

    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil.index');
    Route::get('/perfil/editar', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
});


//UBICACIÓN / AGENCIAS


Route::get('/ubicacion/provincias/{id}', [UbicacionController::class, 'provincias']);
Route::get('/ubicacion/distritos/{id}', [UbicacionController::class, 'distritos']);

Route::get('/agencias/{idDistrito}', function ($idDistrito) {
    return Agencia::where('id_distrito', $idDistrito)
        ->where('estado', 1)
        ->get();
});

//ubicacion
Route::get('/provincias/{id}', function($id){
    return Provincia::where('id_departamento', $id)->get();
});

Route::get('/distritos/{id}', function($id){
    return Distrito::where('id_provincia', $id)->get();
});


//ADMINISTRADOR


Route::middleware(['auth', 'verified', 'role:1'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard admin
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Productos (CRUD)
        Route::resource('productos', ProductoController::class)
            ->except(['show']);
    });


// PERFIL (BREEZE)



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
