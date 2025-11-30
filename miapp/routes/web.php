<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProductoController;



Route::get('/', function () {
    return view('inicio');
})->name('inicio');

// Listar clientes
Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');

// Guardar cliente nuevo
Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');

Route::put('/clientes', [ClienteController::class, 'update'])->name('clientes.update');

Route::delete('/clientes', [ClienteController::class, 'destroy'])->name('clientes.destroy');

Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedor.index');

Route::post('/proveedores', [ProveedorController::class, 'store'])->name('proveedor.store');

Route::put('/proveedores', [ProveedorController::class, 'update'])->name('proveedor.update');

Route::delete('/proveedores', [ProveedorController::class, 'destroy'])->name('proveedor.destroy');

use App\Http\Controllers\AuthController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// (Opcional para después)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::get('/admin', function () {
    return view('admin.inicio');
})->name('admin.inicio');

Route::get('/productos',  [ProductoController::class, 'index'])->name('productos.index');
Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
Route::put('/productos',  [ProductoController::class, 'update'])->name('productos.update');
Route::delete('/productos', [ProductoController::class, 'destroy'])->name('productos.destroy');