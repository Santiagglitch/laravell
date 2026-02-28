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
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\MigracionController;
use App\Http\Controllers\AuditoriaController;

// ===============================================
// RUTA PRINCIPAL
// ===============================================
Route::get('/', function () {
    return view('inicio');
})->name('inicio');

// ===============================================
// RUTAS DE MIGRACIÓN
// ===============================================
Route::post('/migracion/importar',         [MigracionController::class, 'importar']);
Route::post('/migracion/iniciar',          [MigracionController::class, 'iniciar']);
Route::post('/migracion/lote',             [MigracionController::class, 'lote']);
Route::post('/migracion/buscar-venta',     [MigracionController::class, 'buscarVenta']);
Route::post('/migracion/buscar-producto',  [MigracionController::class, 'buscarProducto']);
Route::post('/migracion/buscar-proveedor', [MigracionController::class, 'buscarProveedor']);
Route::get('/migracion/historial',         [MigracionController::class, 'historial']);

Route::post('/migracion/empleados/importar', [MigracionController::class, 'importarEmpleados']);
Route::post('/migracion/empleados/iniciar',  [MigracionController::class, 'iniciarEmpleados']);
Route::post('/migracion/empleados/lote',     [MigracionController::class, 'loteEmpleados']);

Route::post('/migracion/productos/importar', [MigracionController::class, 'importarProductos']);
Route::post('/migracion/productos/iniciar',  [MigracionController::class, 'iniciarProductos']);
Route::post('/migracion/productos/lote',     [MigracionController::class, 'loteProductos']);

// ===============================================
// AUTENTICACIÓN
// ===============================================
Route::get('/login',          [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login',         [AuthController::class, 'login'])->name('login');
Route::post('/logout',        [AuthController::class, 'logout'])->name('logout');

// ===============================================
// RECUPERACIÓN DE CONTRASEÑA
// ===============================================
Route::get('/forgot-password',       [PasswordResetController::class, 'showForgotForm'])->name('password.forgot');
Route::post('/forgot-password',      [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}',[PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password',       [PasswordResetController::class, 'resetPassword'])->name('password.update');

// ===============================================
// PERFIL
// ===============================================
Route::get('/perfil',            [PerfilController::class, 'mostrar'])->name('perfil');
Route::post('/perfil/actualizar',[PerfilController::class, 'actualizar'])->name('perfil.actualizar');

// ===============================================
// OTRAS VISTAS
// ===============================================
Route::get('/admin', function () {
    return view('admin.inicio');
})->name('admin.inicio');

Route::get('/InicioE', function () {
    return view('InicioE.index');
})->name('InicioE.index');

Route::get('/pie-pag', function () {
    return view('Pie_pag.index');
})->name('pie.pag');

// ===============================================
// AUDITORÍA
// ===============================================
Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');

// ===============================================
// CLIENTES (ADMIN)
// ===============================================
Route::get('/clientes',    [ClienteController::class, 'get'])->name('clientes.index');
Route::post('/clientes',   [ClienteController::class, 'post'])->name('clientes.store');
Route::put('/clientes',    [ClienteController::class, 'put'])->name('clientes.update');
Route::delete('/clientes', [ClienteController::class, 'delete'])->name('clientes.destroy');

// ===============================================
// PROVEEDORES (ADMIN)
// ===============================================
Route::get('/proveedores',    [ProveedorController::class, 'get'])->name('proveedor.index');
Route::post('/proveedores',   [ProveedorController::class, 'post'])->name('proveedor.store');
Route::put('/proveedores',    [ProveedorController::class, 'put'])->name('proveedor.update');
Route::delete('/proveedores', [ProveedorController::class, 'delete'])->name('proveedor.destroy');

// ===============================================
// EMPLEADOS (ADMIN)
// ===============================================
Route::get('/empleados',    [EmpleadoController::class, 'get'])->name('empleados.index');
Route::post('/empleados',   [EmpleadoController::class, 'post'])->name('empleados.store');
Route::put('/empleados',    [EmpleadoController::class, 'put'])->name('empleados.update');
Route::delete('/empleados', [EmpleadoController::class, 'delete'])->name('empleados.destroy');

// ===============================================
// PRODUCTOS (ADMIN)
// ===============================================
Route::get('/productos',    [ProductoController::class, 'get'])->name('productos.index');
Route::post('/productos',   [ProductoController::class, 'post'])->name('productos.store');
Route::put('/productos',    [ProductoController::class, 'put'])->name('productos.update');
Route::delete('/productos', [ProductoController::class, 'delete'])->name('productos.destroy');

// ===============================================
// VENTAS (ADMIN)
// ===============================================
Route::get('/ventas',                     [VentaController::class, 'get'])->name('ventas.index');
Route::post('/ventas',                    [VentaController::class, 'post'])->name('ventas.store');
Route::put('/ventas/update',              [VentaController::class, 'put'])->name('ventas.update');
Route::delete('/ventas',                  [VentaController::class, 'delete'])->name('ventas.destroy');
Route::get('/ventas/{id}/detalles',       [VentaController::class, 'obtenerDetalles']);
Route::get('/api/buscar-cliente/{documento}', [VentaController::class, 'buscarClienteAjax']);

// ===============================================
// DETALLE VENTAS (ADMIN)
// ===============================================
Route::get('/detalleventas',    [DetalleVentaController::class, 'get'])->name('detalleventas.index');
Route::post('/detalleventas',   [DetalleVentaController::class, 'post'])->name('detalleventas.store');
Route::put('/detalleventas',    [DetalleVentaController::class, 'put'])->name('detalleventas.update');
Route::delete('/detalleventas', [DetalleVentaController::class, 'delete'])->name('detalleventas.destroy');

// AJAX - Detalle Ventas
Route::get('/detalleventas/buscar-producto/{nombre}', [DetalleVentaController::class, 'buscarProducto'])->name('detalleventas.buscarProducto');
Route::get('/detalleventas/venta-info/{idVenta}',     [DetalleVentaController::class, 'ventaInfo'])->name('detalleventas.ventaInfo');

// (esta ruta estaba en tu web.php original, se mantiene)
Route::get('/ventas/por-producto/{nombre}', [DetalleVentaController::class, 'ventaPorProducto']);

// ===============================================
// DEVOLUCIONES (ADMIN)
// ===============================================
Route::get('/devolucion',             [DevolucionController::class, 'get'])->name('devolucion.index');
Route::post('/devolucion',            [DevolucionController::class, 'post'])->name('devolucion.store');
Route::put('/devolucion',             [DevolucionController::class, 'put'])->name('devolucion.update');
Route::delete('/devolucion',          [DevolucionController::class, 'delete'])->name('devolucion.destroy');
Route::get('/devolucion/{id}/detalles',[DevolucionController::class, 'obtenerDetalles'])->name('devolucion.detalles');

// ===============================================
// DETALLE DEVOLUCIONES (ADMIN)
// ===============================================
Route::get('/detalledevolucion',                        [DetalleDevolucionController::class, 'get'])->name('detalledevolucion.index');
Route::post('/detalledevolucion',                       [DetalleDevolucionController::class, 'post'])->name('detalledevolucion.store');
Route::put('/detalledevolucion/{ID_Devolucion}',        [DetalleDevolucionController::class, 'update'])->name('detalledevolucion.update');
Route::delete('/detalledevolucion/{ID_Devolucion}',     [DetalleDevolucionController::class, 'destroy'])->name('detalledevolucion.destroy');

// AJAX - Detalle Devoluciones (admin)
Route::get('/ventas/por-documento/{documento}',         [DetalleDevolucionController::class, 'ventaPorDocumento'])->name('detalledevolucion.ventaPorDocumento');
Route::get('/venta-info/{idVenta}',                     [DetalleDevolucionController::class, 'ventaInfo'])->name('detalledevolucion.ventaInfo');

// ===============================================
// COMPRAS (ADMIN)
// ===============================================
Route::get('/compras',                   [ComprasController::class, 'get'])->name('compras.index');
Route::post('/compras',                  [ComprasController::class, 'post'])->name('compras.store');
Route::put('/compras/{ID_Entrada}',      [ComprasController::class, 'put'])->name('compras.update');
Route::delete('/compras/{ID_Entrada}',   [ComprasController::class, 'delete'])->name('compras.destroy');
Route::get('/compras/{ID_Entrada}/detalles', [ComprasController::class, 'getDetalles'])->name('compras.detalles');

// ===============================================
// DETALLE COMPRAS (ADMIN)
// ===============================================
Route::get('/detallecompras',    [DetalleComprasController::class, 'get'])->name('detallecompras.index');
Route::post('/detallecompras',   [DetalleComprasController::class, 'post'])->name('detallecompras.store');
Route::put('/detallecompras',    [DetalleComprasController::class, 'put'])->name('detallecompras.update');
Route::delete('/detallecompras', [DetalleComprasController::class, 'delete'])->name('detallecompras.destroy');

// ===============================================
// RUTAS DE EMPLEADO
// ===============================================
Route::prefix('empleado')->group(function () {

    // CLIENTES
    Route::get('/clientes',          [ClienteController::class, 'indexEmpleado'])->name('clientes.indexEm');
    Route::post('/clientes/store',   [ClienteController::class, 'storeEmpleado'])->name('clientes.storeEm');
    Route::put('/clientes/update',   [ClienteController::class, 'updateEmpleado'])->name('clientes.updateEm');
    Route::delete('/clientes/destroy',[ClienteController::class, 'destroyEmpleado'])->name('clientes.destroyEm');

    // PRODUCTOS
    Route::get('/productos',           [ProductoController::class, 'indexEmpleado'])->name('productos.indexEm');
    Route::post('/productos/store',    [ProductoController::class, 'storeEmpleado'])->name('productos.storeEm');
    Route::put('/productos/update',    [ProductoController::class, 'updateEmpleado'])->name('productos.updateEm');
    Route::delete('/productos/destroy',[ProductoController::class, 'destroyEmpleado'])->name('productos.destroyEm');

  // VENTAS
Route::get('/ventas',            [VentaController::class, 'indexEmpleado'])->name('ventas.indexEm');
Route::post('/ventas/store',     [VentaController::class, 'storeEmpleado'])->name('ventas.storeEm');
Route::put('/ventas/update',     [VentaController::class, 'updateEmpleado'])->name('ventas.updateEm');
Route::delete('/ventas/destroy', [VentaController::class, 'destroyEmpleado'])->name('ventas.destroyEm');
Route::get('/ventas/{id}/detalles', [VentaController::class, 'obtenerDetalles'])->name('ventas.detallesEm');          // 👈 NUEVA
Route::get('/api/buscar-cliente/{documento}', [VentaController::class, 'buscarClienteAjax'])->name('ventas.buscarClienteEm'); // 👈 NUEVA

// Detalle ventas 
Route::get('/detalleventas',            [DetalleVentaController::class, 'indexEmpleado'])->name('detalleventas.indexEm');
Route::post('/detalleventas/store',     [DetalleVentaController::class, 'postEm'])->name('detalleventas.storeEm');
Route::put('/detalleventas/update',     [DetalleVentaController::class, 'putEm'])->name('detalleventas.updateEm');
Route::delete('/detalleventas/destroy', [DetalleVentaController::class, 'deleteEm'])->name('detalleventas.destroyEm');

Route::get('/detalleventas/buscar-producto/{nombre}', [DetalleVentaController::class, 'buscarProducto']);
Route::get('/detalleventas/venta-info/{idVenta}',     [DetalleVentaController::class, 'ventaInfo']);


//perfil 
Route::get('/perfil',             [PerfilController::class, 'showEm'])->name('perfilEm');
Route::post('/perfil/actualizar', [PerfilController::class, 'updateEm'])->name('perfilEm.actualizar');

// DEVOLUCIONES
    Route::get('/devolucion',            [DevolucionController::class, 'indexEmpleado'])->name('devolucion.indexEm');
    Route::post('/devolucion/store',     [DevolucionController::class, 'storeEmpleado'])->name('devolucion.storeEm');
    Route::put('/devolucion/update',     [DevolucionController::class, 'updateEmpleado'])->name('devolucion.updateEm');
    Route::delete('/devolucion/destroy', [DevolucionController::class, 'destroyEmpleado'])->name('devolucion.destroyEm');
    Route::get('/devolucion/{id}/detalles', [DevolucionController::class, 'obtenerDetalles'])->name('devolucion.detallesEm');

    // DETALLE DEVOLUCIONES
    Route::get('/detalledevolucion',                           [DetalleDevolucionController::class, 'indexEmpleado'])->name('detalledevolucion.indexEm');
    Route::post('/detalledevolucion/store',                    [DetalleDevolucionController::class, 'storeEmpleado'])->name('detalledevolucion.storeEm');
    Route::put('/detalledevolucion/update/{ID_Devolucion}',    [DetalleDevolucionController::class, 'updateEmpleado'])->name('detalledevolucion.updateEm');
    Route::delete('/detalledevolucion/delete/{ID_Devolucion}', [DetalleDevolucionController::class, 'destroyEmpleado'])->name('detalledevolucion.destroyEm');

    // AJAX - Devoluciones empleado
    Route::get('/ventas/por-documento/{documento}', [DetalleDevolucionController::class, 'ventaPorDocumento'])->name('detalledevolucion.ventaPorDocumentoEm');
    Route::get('/venta-info/{idVenta}',             [DetalleDevolucionController::class, 'ventaInfo'])->name('detalledevolucion.ventaInfoEm');
});