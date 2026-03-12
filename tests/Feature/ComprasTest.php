<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;

class ComprasTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        session([
            'documento' => '1013262102',
            'nombre'    => 'Kevin Alexis',
            'rol'       => 1,
        ]);
    }

    // ==========================================
    // GET - Listar compras
    // ==========================================

    #[Test]
    public function puede_listar_compras()
    {
        $response = $this->get('/compras');

        $response->assertStatus(200);
        $response->assertViewIs('compras.index');
        $response->assertViewHas('compras');
        $response->assertViewHas('productos');
    }

    // ==========================================
    // POST - Crear compra
    // ==========================================

    #[Test]
    public function puede_crear_compra_con_datos_validos()
    {
        $response = $this->post('/compras', [
            'Precio_Compra'      => 500000,
            'ID_Producto'        => 1,
            'Documento_Empleado' => '1013262102',
        ]);

        $response->assertRedirect('/compras');
        $response->assertSessionHas('mensaje', 'Compra agregada correctamente.');

        $this->assertDatabaseHas('Compras', [
            'Precio_Compra'      => 500000,
            'ID_Producto'        => 1,
            'Documento_Empleado' => '1013262102',
        ]);
    }

    #[Test]
    public function puede_crear_compra_con_precio_cero()
    {
        // La BD no acepta NULL en Precio_Compra, pero si acepta 0
        $response = $this->post('/compras', [
            'Precio_Compra'      => 0,
            'ID_Producto'        => 1,
            'Documento_Empleado' => '1013262102',
        ]);

        $response->assertRedirect('/compras');
        $response->assertSessionHas('mensaje', 'Compra agregada correctamente.');

        $this->assertDatabaseHas('Compras', [
            'Precio_Compra'      => 0,
            'ID_Producto'        => 1,
            'Documento_Empleado' => '1013262102',
        ]);
    }

    #[Test]
    public function no_puede_crear_compra_sin_producto()
    {
        $response = $this->post('/compras', [
            'Precio_Compra'      => 500000,
            'Documento_Empleado' => '1013262102',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('ID_Producto');
    }

    #[Test]
    public function no_puede_crear_compra_con_producto_inexistente()
    {
        $response = $this->post('/compras', [
            'Precio_Compra'      => 500000,
            'ID_Producto'        => 99999,
            'Documento_Empleado' => '1013262102',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('ID_Producto');
    }

    #[Test]
    public function no_puede_crear_compra_sin_empleado()
    {
        $response = $this->post('/compras', [
            'Precio_Compra' => 500000,
            'ID_Producto'   => 1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('Documento_Empleado');
    }

    #[Test]
    public function no_puede_crear_compra_con_empleado_inexistente()
    {
        $response = $this->post('/compras', [
            'Precio_Compra'      => 500000,
            'ID_Producto'        => 1,
            'Documento_Empleado' => '9999999999',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('Documento_Empleado');
    }

    #[Test]
    public function no_puede_crear_compra_con_precio_no_numerico()
    {
        $response = $this->post('/compras', [
            'Precio_Compra'      => 'precio_invalido',
            'ID_Producto'        => 1,
            'Documento_Empleado' => '1013262102',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('Precio_Compra');
    }

    // ==========================================
    // PUT - Actualizar compra
    // ==========================================

    #[Test]
    public function puede_actualizar_precio_de_compra()
    {
        $idEntrada = DB::table('Compras')->insertGetId([
            'Precio_Compra'      => 100000,
            'ID_Producto'        => 1,
            'Documento_Empleado' => '1013262102',
        ]);

        $response = $this->put("/compras/{$idEntrada}", [
            'Precio_Compra' => 200000,
        ]);

        $response->assertRedirect('/compras');
        $response->assertSessionHas('mensaje', 'Compra actualizada correctamente.');

        $this->assertDatabaseHas('Compras', [
            'ID_Entrada'    => $idEntrada,
            'Precio_Compra' => 200000,
        ]);
    }

    #[Test]
    public function puede_actualizar_producto_de_compra()
    {
        $idEntrada = DB::table('Compras')->insertGetId([
            'Precio_Compra'      => 100000,
            'ID_Producto'        => 1,
            'Documento_Empleado' => '1013262102',
        ]);

        $response = $this->put("/compras/{$idEntrada}", [
            'ID_Producto' => 2,
        ]);

        $response->assertRedirect('/compras');
        $response->assertSessionHas('mensaje', 'Compra actualizada correctamente.');

        $this->assertDatabaseHas('Compras', [
            'ID_Entrada'  => $idEntrada,
            'ID_Producto' => 2,
        ]);
    }

    #[Test]
    public function no_actualiza_compra_con_producto_inexistente()
    {
        $idEntrada = DB::table('Compras')->insertGetId([
            'Precio_Compra'      => 100000,
            'ID_Producto'        => 1,
            'Documento_Empleado' => '1013262102',
        ]);

        $response = $this->put("/compras/{$idEntrada}", [
            'ID_Producto' => 99999,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('ID_Producto');

        $this->assertDatabaseHas('Compras', [
            'ID_Entrada'  => $idEntrada,
            'ID_Producto' => 1,
        ]);
    }

    #[Test]
    public function actualizar_compra_inexistente_retorna_404()
    {
        $response = $this->put('/compras/99999', [
            'Precio_Compra' => 200000,
        ]);

        $response->assertStatus(404);
    }

    // ==========================================
    // DELETE - Eliminar compra
    // ==========================================

    #[Test]
    public function puede_eliminar_compra_sin_detalles()
    {
        $idEntrada = DB::table('Compras')->insertGetId([
            'Precio_Compra'      => 100000,
            'ID_Producto'        => 1,
            'Documento_Empleado' => '1013262102',
        ]);

        $response = $this->delete("/compras/{$idEntrada}");

        $response->assertRedirect('/compras');
        $response->assertSessionHas('mensaje', 'Compra eliminada correctamente.');

        $this->assertDatabaseMissing('Compras', [
            'ID_Entrada' => $idEntrada,
        ]);
    }

    #[Test]
    public function no_puede_eliminar_compra_con_detalles_asociados()
    {
        $idEntrada = DB::table('Compras')->insertGetId([
            'Precio_Compra'      => 100000,
            'ID_Producto'        => 1,
            'Documento_Empleado' => '1013262102',
        ]);

        DB::table('Detalle_Compras')->insert([
            'Fecha_Entrada' => now()->toDateString(),
            'Cantidad'      => 10,
            'ID_Proveedor'  => 1,
            'ID_Entrada'    => $idEntrada,
        ]);

        $response = $this->delete("/compras/{$idEntrada}");

        $response->assertRedirect('/compras');
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('Compras', [
            'ID_Entrada' => $idEntrada,
        ]);
    }

    #[Test]
    public function eliminar_compra_inexistente_retorna_404()
    {
        $response = $this->delete('/compras/99999');

        $response->assertStatus(404);
    }

    // ==========================================
    // GET Detalles - JSON
    // ==========================================

    #[Test]
    public function puede_obtener_detalles_de_compra()
    {
        $idEntrada = DB::table('Compras')->insertGetId([
            'Precio_Compra'      => 100000,
            'ID_Producto'        => 1,
            'Documento_Empleado' => '1013262102',
        ]);

        $response = $this->get("/compras/{$idEntrada}/detalles");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'compra' => [
                'ID_Entrada',
                'Precio_Compra',
                'ID_Producto',
                'Documento_Empleado',
            ],
        ]);
    }

    #[Test]
    public function detalles_de_compra_inexistente_retorna_404()
    {
        $response = $this->get('/compras/99999/detalles');

        $response->assertStatus(404);
    }

    #[Test]
    public function detalles_incluye_informacion_de_producto_y_empleado()
    {
        $idEntrada = DB::table('Compras')->insertGetId([
            'Precio_Compra'      => 100000,
            'ID_Producto'        => 1,
            'Documento_Empleado' => '1013262102',
        ]);

        $response = $this->get("/compras/{$idEntrada}/detalles");

        $response->assertStatus(200);

        $data = $response->json('compra');
        $this->assertArrayHasKey('producto_info', $data);
        $this->assertArrayHasKey('empleado', $data);
    }
}