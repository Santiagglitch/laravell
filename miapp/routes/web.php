<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\DetalleVentaController;
use App\Http\Controllers\DetalleDevolucionController;
use App\Http\Controllers\DevolucionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComprasController;
use App\Http\Controllers\DetalleComprasController;
use App\Http\Controllers\PerfilController;

Route::get('/', function () {
    return view('inicio');
})->name('inicio');

// Buscar venta por documento (AJAX)
Route::get('/ventas/por-documento/{documento}', [DetalleDevolucionController::class, 'ventaPorDocumento']);

// Detalle Devolucion - ADMIN
Route::get('/detalledevolucion', [DetalleDevolucionController::class, 'get'])->name('detalledevolucion.index');
Route::post('/detalledevolucion', [DetalleDevolucionController::class, 'post'])->name('detalledevolucion.store');
Route::put('/detalledevolucion/{ID_Devolucion}', [DetalleDevolucionController::class, 'update'])->name('detalledevolucion.update');
Route::delete('/detalledevolucion/{ID_Devolucion}', [DetalleDevolucionController::class, 'destroy'])->name('detalledevolucion.destroy');

// Clientes
Route::get('/clientes', [ClienteController::class, 'get'])->name('clientes.index');
Route::post('/clientes', [ClienteController::class, 'post'])->name('clientes.store');
Route::put('/clientes', [ClienteController::class, 'put'])->name('clientes.update');
Route::delete('/clientes', [ClienteController::class, 'delete'])->name('clientes.destroy');


//Proveedor
Route::get('/proveedores', [ProveedorController::class, 'get'])->name('proveedor.index');
Route::post('/proveedores', [ProveedorController::class, 'post'])->name('proveedor.store');
Route::put('/proveedores', [ProveedorController::class, 'put'])->name('proveedor.update');
Route::delete('/proveedores', [ProveedorController::class, 'delete'])->name('proveedor.destroy');



//Empleados
Route::get('/empleados', [EmpleadoController::class, 'get'])->name('empleados.index');
Route::post('/empleados', [EmpleadoController::class, 'post'])->name('empleados.store');
Route::put('/empleados', [EmpleadoController::class, 'put'])->name('empleados.update');
Route::delete('/empleados', [EmpleadoController::class, 'delete'])->name('empleados.destroy');



// Productos
Route::get('/productos',  [ProductoController::class, 'get'])->name('productos.index');
Route::post('/productos', [ProductoController::class, 'post'])->name('productos.store');
Route::put('/productos',  [ProductoController::class, 'put'])->name('productos.update');
Route::delete('/productos', [ProductoController::class, 'delete'])->name('productos.destroy');


Route::get('/empleado/productos', [ProductoController::class, 'indexEmpleado'])
    ->name('productos.indexEm');



// Ventas
Route::get('/ventas', [VentaController::class, 'get'])->name('ventas.index');
Route::get('/api/buscar-cliente/{documento}', [VentaController::class, 'buscarClienteAjax']);
Route::post('/ventas', [VentaController::class, 'post'])->name('ventas.store');
Route::put('/ventas/update', [VentaController::class, 'put'])->name('ventas.update');
Route::delete('/ventas', [VentaController::class, 'delete'])->name('ventas.destroy');
Route::get('/ventas/{id}/detalles', [VentaController::class, 'obtenerDetalles']);



// Detalle Ventas
Route::get('/detalleventas', [DetalleVentaController::class, 'get'])->name('detalleventas.index');
Route::post('/detalleventas', [DetalleVentaController::class, 'post'])->name('detalleventas.store');
Route::put('/detalleventas', [DetalleVentaController::class, 'put'])->name('detalleventas.update');
Route::delete('/detalleventas', [DetalleVentaController::class, 'delete'])->name('detalleventas.destroy');
Route::get('/ventas/por-producto/{nombre}', [DetalleVentaController::class, 'ventaPorProducto']);
// Info de venta para modal editar ADMIN
Route::get('/venta-info/{idVenta}', [DetalleDevolucionController::class, 'ventaInfo'])->name('detalledevolucion.ventaInfoAdmin');

// Devolucio
Route::get('/devolucion', [DevolucionController::class, 'get'])->name('devolucion.index');
Route::get('/devolucion/{id}/detalles', [DevolucionController::class, 'obtenerDetalles'])
    ->name('devolucion.detalles');
Route::post('/devolucion', [DevolucionController::class, 'post'])->name('devolucion.store');
Route::put('/devolucion', [DevolucionController::class, 'put'])->name('devolucion.update');
Route::delete('/devolucion', [DevolucionController::class, 'delete'])->name('devolucion.destroy');


// Detalle Devolucion
Route::get('/detalledevolucion', [DetalleDevolucionController::class, 'get'])->name('detalledevolucion.index');
Route::post('/detalledevolucion', [DetalleDevolucionController::class, 'post'])->name('detalledevolucion.store');
Route::put('/detalledevolucion', [DetalleDevolucionController::class, 'put'])->name('detalledevolucion.update');
Route::delete('/detalledevolucion', [DetalleDevolucionController::class, 'delete'])->name('detalledevolucion.destroy');


// Compras (mantén estas como están)
Route::get('/compras', [ComprasController::class, 'get'])->name('compras.index');
Route::post('/compras', [ComprasController::class, 'post'])->name('compras.store');
Route::put('/compras/{ID_Entrada}', [ComprasController::class, 'put'])->name('compras.update');
Route::delete('/compras/{ID_Entrada}', [ComprasController::class, 'delete'])->name('compras.destroy');
Route::get('/compras/{ID_Entrada}/detalles', [ComprasController::class, 'getDetalles'])->name('compras.detalles'); // ← SOLO AGREGA ESTA LÍNEA

// Detalle Compras (mantén estas como están)
Route::get('/detallecompras', [DetalleComprasController::class, 'get'])->name('detallecompras.index');
Route::post('/detallecompras', [DetalleComprasController::class, 'post'])->name('detallecompras.store');
Route::put('/detallecompras', [DetalleComprasController::class, 'put'])->name('detallecompras.update');
Route::delete('/detallecompras', [DetalleComprasController::class, 'delete'])->name('detallecompras.destroy');

//Login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');



Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::get('/admin', function () {
    return view('admin.inicio');
})->name('admin.inicio');




Route::prefix('empleado')->group(function () {

    // CLIENTES 
    Route::get('/clientes', [ClienteController::class, 'indexEmpleado'])
        ->name('clientes.indexEm');

    Route::post('/clientes/store', [ClienteController::class, 'storeEmpleado'])
        ->name('clientes.storeEm');

    Route::put('/clientes/update', [ClienteController::class, 'updateEmpleado'])
        ->name('clientes.updateEm');

    Route::delete('/clientes/destroy', [ClienteController::class, 'destroyEmpleado'])
        ->name('clientes.destroyEm');


    // PRODUCTOS
    Route::get('/productos', [ProductoController::class, 'indexEmpleado'])
    ->name('productos.indexEm');

    Route::post('/productos/store', [ProductoController::class, 'storeEmpleado'])
    ->name('productos.storeEm');

    Route::put('/productos/update', [ProductoController::class, 'updateEmpleado'])
    ->name('productos.updateEm');

    Route::delete('/productos/destroy', [ProductoController::class, 'destroyEmpleado'])
    ->name('productos.destroyEm');

    // VENTAS 
    Route::get('/ventas', [VentaController::class, 'indexEmpleado'])
    ->name('ventas.indexEm');

    Route::post('/ventas/store', [VentaController::class, 'storeEmpleado'])
    ->name('ventas.storeEm');

    Route::put('/ventas/update', [VentaController::class, 'updateEmpleado'])
    ->name('ventas.updateEm');

    Route::delete('/ventas/destroy', [VentaController::class, 'destroyEmpleado'])
    ->name('ventas.destroyEm');


    // DETALLE VENTAS
    Route::get('/detalleventas', [DetalleVentaController::class, 'indexEmpleado'])
    ->name('detalleventas.indexEm');

    Route::post('/detalleventas/store', [DetalleVentaController::class, 'storeEmpleado'])
    ->name('detalleventas.storeEm');

    Route::put('/detalleventas/update', [DetalleVentaController::class, 'updateEmpleado'])
    ->name('detalleventas.updateEm');

    Route::delete('/detalleventas/destroy', [DetalleVentaController::class, 'destroyEmpleado'])
    ->name('detalleventas.destroyEm');


    // DEVOLUCIONES 
    Route::get('/devolucion', [DevolucionController::class, 'indexEmpleado'])
    ->name('devolucion.indexEm');

    Route::post('/devolucion/store', [DevolucionController::class, 'storeEmpleado'])
    ->name('devolucion.storeEm');

    Route::put('/devolucion/update', [DevolucionController::class, 'updateEmpleado'])
    ->name('devolucion.updateEm');

    Route::delete('/devolucion/destroy', [DevolucionController::class, 'destroyEmpleado'])
    ->name('devolucion.destroyEm');
// Dentro del grupo prefix('empleado'), junto a las demás rutas de devolucion:
Route::get('/devolucion/{id}/detalles', [DevolucionController::class, 'obtenerDetalles'])
    ->name('devolucion.detallesEm');



    // DETALLE DEVOLUCIONES 
    Route::get('/detalledevolucion', [DetalleDevolucionController::class, 'indexEmpleado'])
    ->name('detalledevolucion.indexEm');
Route::put('/detalledevolucion/update/{ID_Devolucion}', [DetalleDevolucionController::class, 'updateEmpleado'])
    ->name('detalledevolucion.updateEm');
    Route::post('/detalledevolucion/store', [DetalleDevolucionController::class, 'storeEmpleado'])
    ->name('detalledevolucion.storeEm');
    // Obtener info de una venta para el modal editar
Route::get('/venta-info/{idVenta}', [DetalleDevolucionController::class, 'ventaInfo'])
    ->name('detalledevolucion.ventaInfo');
Route::get('/venta-info/{idVenta}', [DetalleDevolucionController::class, 'ventaInfo'])
        ->name('detalledevolucion.ventaInfo');

 // ← cierre del grupo prefix('empleado')
   Route::prefix('empleado')->group(function () {

    Route::delete('/detalledevolucion/delete/{ID_Devolucion}',
        [DetalleDevolucionController::class, 'destroyEmpleado']
    )->name('detalledevolucion.destroyEm');

});

});

        Route::get('/InicioE', function () {
        return view('InicioE.index');
        })->name('InicioE.index');

Route::get('/pie-pag', function () {
    return view('Pie_pag.index');
})->name('pie.pag');


Route::get('/perfil', [PerfilController::class, 'mostrar'])->name('perfil');
Route::put('/perfil/actualizar-datos', [PerfilController::class, 'actualizarDatos'])->name('perfil.actualizarDatos');
Route::put('/perfil/actualizar-foto', [PerfilController::class, 'actualizarFoto'])->name('perfil.actualizarFoto');
Route::post('/perfil/actualizar-contrasena', [PerfilController::class, 'actualizarContrasena'])->name('perfil.actualizarContrasena');



