<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\AuthController;



Route::get('/', function () {
    return view('inicio');
})->name('inicio');


// Clientes
Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');

Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');

Route::put('/clientes', [ClienteController::class, 'update'])->name('clientes.update');

Route::delete('/clientes', [ClienteController::class, 'destroy'])->name('clientes.destroy');



//Proveedor

Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedor.index');

Route::post('/proveedores', [ProveedorController::class, 'store'])->name('proveedor.store');

Route::put('/proveedores', [ProveedorController::class, 'update'])->name('proveedor.update');

Route::delete('/proveedores', [ProveedorController::class, 'destroy'])->name('proveedor.destroy');



//Empleados
Route::get('/empleados', [EmpleadoController::class, 'index'])->name('empleados.index');

Route::post('/empleados', [EmpleadoController::class, 'store'])->name('empleados.store');

Route::put('/empleados', [EmpleadoController::class, 'update'])->name('empleados.update');

Route::delete('/empleados', [EmpleadoController::class, 'destroy'])->name('empleados.destroy');



//Login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// (Opcional para después)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::get('/admin', function () {
    return view('admin.inicio');
})->name('admin.inicio');


// Productos
Route::get('/productos',  [ProductoController::class, 'index'])->name('productos.index');
Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
Route::put('/productos',  [ProductoController::class, 'update'])->name('productos.update');
Route::delete('/productos', [ProductoController::class, 'destroy'])->name('productos.destroy');