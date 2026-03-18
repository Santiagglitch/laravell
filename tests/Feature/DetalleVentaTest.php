<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;

class DetalleVentaTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        session([
            'documento' => '1013262102',
            'nombre' => 'Kevin Alexis',
            'rol' => 1,
        ]);
    }

    #[Test]
    public function puede_listar_detalles_de_ventas()
    {
        $response = $this->get('/detalleventas');

        $response->assertStatus(200);
        $response->assertViewIs('detalle_ventas.index');
        $response->assertViewHas('detalles');
        $response->assertViewHas('productos');
        $response->assertViewHas('ultimasVentas');
    }

    #[Test]
    public function puede_crear_detalle_de_venta()
    {
        // Resetear stock
        DB::table('Productos')->where('ID_Producto', 1)->update(['Stock_Minimo' => 100]);
        
        $producto = DB::table('Productos')->where('ID_Producto', 1)->first();
        $stockInicial = $producto->Stock_Minimo;

        $idVenta = DB::table('Ventas')->insertGetId([
            'Documento_Cliente' => '53103136',
            'Documento_Empleado' => '1013262102',
        ]);

        $response = $this->post('/detalleventas', [
            'ID_Producto' => 1,
            'Cantidad' => 2,
            'Fecha_Salida' => now()->toDateString(),
            'ID_Venta' => $idVenta,
        ]);

        $response->assertRedirect(); // ✅ FIX 1
        $response->assertSessionHas('mensaje', 'Detalle registrado correctamente.');

        $this->assertDatabaseHas('Detalle_Ventas', [
            'ID_Venta' => $idVenta,
            'ID_Producto' => 1,
            'Cantidad' => 2,
        ]);

        $this->assertDatabaseHas('Productos', [
            'ID_Producto' => 1,
            'Stock_Minimo' => $stockInicial - 2,
        ]);
    }

    #[Test]
    public function no_puede_crear_detalle_con_stock_insuficiente()
    {
        $producto = DB::table('Productos')->where('ID_Producto', 1)->first();

        $idVenta = DB::table('Ventas')->insertGetId([
            'Documento_Cliente' => '53103136',
            'Documento_Empleado' => '1013262102',
        ]);

        $response = $this->post('/detalleventas', [
            'ID_Producto' => 1,
            'Cantidad' => $producto->Stock_Minimo + 100,
            'Fecha_Salida' => now()->toDateString(),
            'ID_Venta' => $idVenta,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function no_puede_crear_detalle_duplicado()
    {
        // ✅ FIX 2: Resetear stock
        DB::table('Productos')->where('ID_Producto', 1)->update(['Stock_Minimo' => 100]);
        
        $idVenta = DB::table('Ventas')->insertGetId([
            'Documento_Cliente' => '53103136',
            'Documento_Empleado' => '1013262102',
        ]);

        DB::table('Detalle_Ventas')->insert([
            'ID_Venta' => $idVenta,
            'ID_Producto' => 1,
            'Cantidad' => 1,
            'Fecha_Salida' => now(),
        ]);

        $response = $this->post('/detalleventas', [
            'ID_Producto' => 1,
            'Cantidad' => 1,
            'Fecha_Salida' => now()->toDateString(),
            'ID_Venta' => $idVenta,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Ya existe un detalle para esta venta con ese producto.');
    }

    #[Test]
    public function puede_actualizar_cantidad_de_detalle()
    {
        // Resetear stock
        DB::table('Productos')->where('ID_Producto', 1)->update(['Stock_Minimo' => 100]);
        
        $producto = DB::table('Productos')->where('ID_Producto', 1)->first();
        $stockInicial = $producto->Stock_Minimo;

        $idVenta = DB::table('Ventas')->insertGetId([
            'Documento_Cliente' => '53103136',
            'Documento_Empleado' => '1013262102',
        ]);

        DB::table('Detalle_Ventas')->insert([
            'ID_Venta' => $idVenta,
            'ID_Producto' => 1,
            'Cantidad' => 2,
            'Fecha_Salida' => now(),
        ]);

        $response = $this->put('/detalleventas', [
            'ID_Producto' => 1,
            'ID_Venta' => $idVenta,
            'Cantidad' => 5,
        ]);

        $response->assertRedirect(); // ✅ FIX 3
        $response->assertSessionHas('mensaje', 'Detalle actualizado correctamente.');

        $this->assertDatabaseHas('Detalle_Ventas', [
            'ID_Venta' => $idVenta,
            'ID_Producto' => 1,
            'Cantidad' => 5,
        ]);

        $this->assertDatabaseHas('Productos', [
            'ID_Producto' => 1,
            'Stock_Minimo' => $stockInicial - 5,
        ]);
    }

    #[Test]
    public function puede_reducir_cantidad_de_detalle_y_devuelve_stock()
    {
        DB::table('Productos')->where('ID_Producto', 1)->update(['Stock_Minimo' => 100]);
        
        $producto = DB::table('Productos')->where('ID_Producto', 1)->first();
        $stockInicial = $producto->Stock_Minimo;

        $idVenta = DB::table('Ventas')->insertGetId([
            'Documento_Cliente' => '53103136',
            'Documento_Empleado' => '1013262102',
        ]);

        DB::table('Detalle_Ventas')->insert([
            'ID_Venta' => $idVenta,
            'ID_Producto' => 1,
            'Cantidad' => 5,
            'Fecha_Salida' => now(),
        ]);

        $response = $this->put('/detalleventas', [
            'ID_Producto' => 1,
            'ID_Venta' => $idVenta,
            'Cantidad' => 2,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('mensaje');

        $this->assertDatabaseHas('Detalle_Ventas', [
            'ID_Venta' => $idVenta,
            'ID_Producto' => 1,
            'Cantidad' => 2,
        ]);

        $this->assertDatabaseHas('Productos', [
            'ID_Producto' => 1,
            'Stock_Minimo' => $stockInicial - 2,
        ]);
    }

    #[Test]
    public function puede_eliminar_detalle_y_devuelve_stock()
    {
        DB::table('Productos')->where('ID_Producto', 1)->update(['Stock_Minimo' => 100]);
        
        $producto = DB::table('Productos')->where('ID_Producto', 1)->first();
        $stockInicial = $producto->Stock_Minimo;

        $idVenta = DB::table('Ventas')->insertGetId([
            'Documento_Cliente' => '53103136',
            'Documento_Empleado' => '1013262102',
        ]);

        DB::table('Detalle_Ventas')->insert([
            'ID_Venta' => $idVenta,
            'ID_Producto' => 1,
            'Cantidad' => 3,
            'Fecha_Salida' => now(),
        ]);

        $response = $this->delete('/detalleventas', [
            'ID_Producto' => 1,
            'ID_Venta' => $idVenta,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('mensaje', 'Detalle eliminado correctamente.');

        $this->assertDatabaseMissing('Detalle_Ventas', [
            'ID_Venta' => $idVenta,
            'ID_Producto' => 1,
        ]);

        $this->assertDatabaseHas('Productos', [
            'ID_Producto' => 1,
            'Stock_Minimo' => $stockInicial,
        ]);
    }

    #[Test]
    public function buscar_producto_por_nombre_devuelve_resultados()
    {
        $response = $this->get('/detalleventas/buscar-producto/Redmi');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'productos' => [
                '*' => ['ID_Producto', 'Nombre_Producto', 'Stock_Minimo']
            ]
        ]);
    }

    #[Test]
    public function buscar_producto_inexistente_devuelve_404()
    {
        $response = $this->get('/detalleventas/buscar-producto/ProductoQueNoExiste999');

        $response->assertStatus(404);
        $response->assertJson(['error' => 'No se encontraron productos']);
    }

    #[Test]
    public function puede_obtener_info_de_venta()
    {
        $idVenta = DB::table('Ventas')->insertGetId([
            'Documento_Cliente' => '53103136',
            'Documento_Empleado' => '1013262102',
        ]);

        DB::table('Detalle_Ventas')->insert([
            'ID_Venta' => $idVenta,
            'ID_Producto' => 1,
            'Cantidad' => 2,
            'Fecha_Salida' => now(),
        ]);

        $response = $this->get("/detalleventas/venta-info/{$idVenta}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['producto', 'cantidad', 'stock', 'id_producto']
        ]);
    }

    #[Test]
    public function venta_info_de_venta_inexistente_devuelve_404()
    {
        $response = $this->get('/detalleventas/venta-info/99999');

        $response->assertStatus(404);
        $response->assertJson(['error' => 'Venta no encontrada']);
    }

    #[Test]
    public function validacion_cantidad_minima_requerida()
    {
        $idVenta = DB::table('Ventas')->insertGetId([
            'Documento_Cliente' => '53103136',
            'Documento_Empleado' => '1013262102',
        ]);

        $response = $this->post('/detalleventas', [
            'ID_Producto' => 1,
            'Cantidad' => 0,
            'Fecha_Salida' => now()->toDateString(),
            'ID_Venta' => $idVenta,
        ]);

        $response->assertSessionHasErrors('Cantidad');
    }

    #[Test]
    public function validacion_producto_debe_existir()
    {
        $idVenta = DB::table('Ventas')->insertGetId([
            'Documento_Cliente' => '53103136',
            'Documento_Empleado' => '1013262102',
        ]);

        $response = $this->post('/detalleventas', [
            'ID_Producto' => 99999,
            'Cantidad' => 1,
            'Fecha_Salida' => now()->toDateString(),
            'ID_Venta' => $idVenta,
        ]);

        $response->assertSessionHasErrors('ID_Producto');
    }

    #[Test]
    public function validacion_venta_debe_existir()
    {
        $response = $this->post('/detalleventas', [
            'ID_Producto' => 1,
            'Cantidad' => 1,
            'Fecha_Salida' => now()->toDateString(),
            'ID_Venta' => 99999,
        ]);

        $response->assertSessionHasErrors('ID_Venta');
    }
}