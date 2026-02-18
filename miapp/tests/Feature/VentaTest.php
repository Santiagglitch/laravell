<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions; // ✅ Cambio aquí
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test; // ✅ Para quitar los warnings

class VentaTest extends TestCase
{
    use DatabaseTransactions; // ✅ Cambio aquí

    protected function setUp(): void
    {
        parent::setUp();

        session([
            'documento' => '1013262102',
            'nombre' => 'Kevin Alexis',
            'rol' => 1,
        ]);

        Http::fake([
            '*/BuscarPorDocumento/*' => Http::response([
                'Documento_Cliente' => '53103136',
                'Nombre_Cliente' => 'Juan',
                'Apellido_Cliente' => 'Pérez',
                'ID_Estado' => '1',
            ], 200),
            '*/VentaRegistro' => Http::response('Venta registrada correctamente', 200),
        ]);
    }

    #[Test] // ✅ Nuevo formato (quita warnings)
    public function puede_listar_ventas()
    {
        $response = $this->get('/ventas');

        $response->assertStatus(200);
        $response->assertViewIs('ventas.index');
        $response->assertViewHas('ventas');
        $response->assertViewHas('empleados');
    }

    #[Test]
    public function puede_crear_venta_con_cliente_existente()
    {
        $response = $this->post('/ventas', [
            'Documento_Cliente' => '53103136',
            'Documento_Empleado' => '1013262102',
            'cliente_nuevo' => '0',
        ]);

        $response->assertRedirect('/ventas');
        $response->assertSessionHas('mensaje', 'Venta registrada correctamente.');

        $this->assertDatabaseHas('Ventas', [
            'Documento_Cliente' => '53103136',
            'Documento_Empleado' => '1013262102',
        ]);
    }

    #[Test]
    public function puede_crear_venta_con_cliente_nuevo()
    {
        Http::fake([
            '*/BuscarPorDocumento/*' => Http::response([], 404),
            '*/RegistraC' => Http::response('Cliente registrado', 200),
            '*/VentaRegistro' => Http::response('Venta registrada correctamente', 200),
        ]);

        $response = $this->post('/ventas', [
            'Documento_Cliente' => '9999999999',
            'Documento_Empleado' => '1013262102',
            'cliente_nuevo' => '1',
            'Nombre_Cliente' => 'Pedro',
            'Apellido_Cliente' => 'García',
            'Estado_Cliente' => 'activo',
        ]);

        $response->assertRedirect('/ventas');
        $response->assertSessionHas('mensaje');
    }

    #[Test]
    public function no_puede_crear_venta_sin_documento_cliente()
    {
        $response = $this->post('/ventas', [
            'Documento_Cliente' => '',
            'Documento_Empleado' => '1013262102',
        ]);

        $response->assertSessionHasErrors('Documento_Cliente');
    }

    #[Test]
    public function no_puede_crear_venta_sin_documento_empleado()
    {
        $response = $this->post('/ventas', [
            'Documento_Cliente' => '53103136',
            'Documento_Empleado' => '',
        ]);

        $response->assertSessionHasErrors('Documento_Empleado');
    }

    #[Test]
    public function puede_eliminar_venta_sin_detalles()
    {
        $idVenta = DB::table('Ventas')->insertGetId([
            'Documento_Cliente' => '53103136',
            'Documento_Empleado' => '1013262102',
        ]);

        $response = $this->delete('/ventas', [
            'ID_Venta' => $idVenta,
        ]);

        $response->assertRedirect('/ventas');
        $response->assertSessionHas('mensaje', 'Venta eliminada correctamente.');

        $this->assertDatabaseMissing('Ventas', [
            'ID_Venta' => $idVenta,
        ]);
    }

    #[Test]
    public function no_puede_eliminar_venta_con_detalles()
    {
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

        $response = $this->delete('/ventas', [
            'ID_Venta' => $idVenta,
        ]);

        $response->assertRedirect('/ventas');
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('Ventas', [
            'ID_Venta' => $idVenta,
        ]);
    }

    #[Test]
    public function puede_obtener_detalles_de_venta()
    {
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

        $response = $this->get("/ventas/{$idVenta}/detalles");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'venta' => [
                'ID_Venta',
                'Documento_Cliente',
                'Documento_Empleado',
                'detalles',
            ],
        ]);
    }

    #[Test]
    public function buscar_cliente_devuelve_datos_correctos()
    {
        $response = $this->get('/api/buscar-cliente/53103136');

        $response->assertStatus(200);
        $response->assertJson([
            'encontrado' => true,
            'cliente' => [
                'Documento_Cliente' => '53103136',
                'Nombre_Cliente' => 'Juan',
                'Apellido_Cliente' => 'Pérez',
            ],
        ]);
    }

    #[Test]
    public function buscar_cliente_inexistente_devuelve_404()
    {
        Http::fake([
            '*/BuscarPorDocumento/9999999999' => Http::response([], 404),
        ]);

        $response = $this->get('/api/buscar-cliente/9999999999');

        $response->assertStatus(200);
        $response->assertJson(['encontrado' => false]);
    }
}