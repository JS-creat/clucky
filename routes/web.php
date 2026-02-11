<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Models\Producto;
use App\Http\Controllers\CarritoController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Ruta para usuarios normales
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'role:2'])->name('dashboard');

// Ruta para administradores
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'verified', 'role:1'])->name('admin.dashboard');

//Ruta de detalles de producto
Route::get('/producto/{id}', function ($id) {
    $producto = Producto::findOrFail($id);
    return view('producto-detalle', compact('producto'));
})->name('producto.show');


// Cualquiera puede ver el carrito
Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');

Route::post('/carrito/add/{id}', [CarritoController::class, 'add'])->name('carrito.add');

// Solo los logueados pueden ir al Checkout (pagar)
Route::middleware(['auth'])->group(function () {
    Route::get('/finalizar-compra', [CarritoController::class, 'checkout'])->name('carrito.checkout');
});

//eliminar productos del carrito
Route::get('/carrito/aumentar/{id}', [CarritoController::class, 'aumentar'])->name('carrito.aumentar');
Route::get('/carrito/disminuir/{id}', [CarritoController::class, 'disminuir'])->name('carrito.disminuir');
Route::get('/carrito/eliminar/{id}', [CarritoController::class, 'eliminar'])->name('carrito.eliminar');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
