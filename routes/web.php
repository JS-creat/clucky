<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Models\Producto;

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
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
