<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;

class DetalleDevolucionTest extends TestCase
{
    use DatabaseTransactions;

    protected string $idDevolucion;
    protected string $idVenta;

    protected function setUp(): void
    {
        parent::setUp();

        session([
            'documento' => '1013262102',
            'nombre'    => 'Kevin Alexis',
            'rol'       => 1,
        ]);

        $this->idDevolucion = (string) DB::table('devoluciones')->insertGetId([
            'Fecha_Devolucion' => '2025-01-15',
            'Motivo'           => 'Motivo base para detalle',
        ]);

        // Necesita una venta que tenga registro en Detalle_Ventas (el controlador lo consulta)
        $this->idVenta = (string) DB::table('Detalle_Ventas')->value('ID_Venta');
    }

    // ===== ADMIN: LISTAR =====

    #[Test]
    public function puede_listar_detalles_devolucion()
    {
        $response = $this->get('/detalledevolucion');

        $response->assertStatus(200);
        $response->assertViewHas('detalles');
    }

    // ===== ADMIN: CREAR =====

    #[Test]
    public function puede_registrar_detalle_devolucion()
    {
        $response = $this->post('/detalledevolucion', [
            'ID_Devolucion'     => $this->idDevolucion,
            'ID_Venta'          => $this->idVenta,
            'Cantidad_Devuelta' => 1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('mensaje', 'Detalle registrado correctamente.');

        $this->assertDatabaseHas('Detalle_Devoluciones', [
            'ID_Devolucion'     => $this->idDevolucion,
            'ID_Venta'          => $this->idVenta,
            'Cantidad_Devuelta' => 1,
        ]);
    }

    #[Test]
    public function no_puede_registrar_detalle_sin_id_devolucion()
    {
        $response = $this->post('/detalledevolucion', [
            'ID_Devolucion'     => '',
            'ID_Venta'          => $this->idVenta,
            'Cantidad_Devuelta' => 1,
        ]);

        $response->assertSessionHasErrors('ID_Devolucion');
    }

    #[Test]
    public function no_puede_registrar_detalle_sin_id_venta()
    {
        $response = $this->post('/detalledevolucion', [
            'ID_Devolucion'     => $this->idDevolucion,
            'ID_Venta'          => '',
            'Cantidad_Devuelta' => 1,
        ]);

        $response->assertSessionHasErrors('ID_Venta');
    }

    #[Test]
    public function no_puede_registrar_detalle_sin_cantidad_devuelta()
    {
        $response = $this->post('/detalledevolucion', [
            'ID_Devolucion'     => $this->idDevolucion,
            'ID_Venta'          => $this->idVenta,
            'Cantidad_Devuelta' => '',
        ]);

        $response->assertSessionHasErrors('Cantidad_Devuelta');
    }

    #[Test]
    public function no_puede_registrar_detalle_con_devolucion_inexistente()
    {
        $response = $this->post('/detalledevolucion', [
            'ID_Devolucion'     => '999999',
            'ID_Venta'          => $this->idVenta,
            'Cantidad_Devuelta' => 1,
        ]);

        $response->assertSessionHasErrors('ID_Devolucion');
    }

    #[Test]
    public function no_puede_registrar_detalle_con_venta_inexistente()
    {
        $response = $this->post('/detalledevolucion', [
            'ID_Devolucion'     => $this->idDevolucion,
            'ID_Venta'          => '999999',
            'Cantidad_Devuelta' => 1,
        ]);

        $response->assertSessionHasErrors('ID_Venta');
    }

    #[Test]
    public function no_puede_registrar_detalle_duplicado_para_misma_devolucion()
    {
        DB::table('Detalle_Devoluciones')->insert([
            'ID_Devolucion'     => $this->idDevolucion,
            'ID_Venta'          => $this->idVenta,
            'Cantidad_Devuelta' => 1,
        ]);

        $response = $this->post('/detalledevolucion', [
            'ID_Devolucion'     => $this->idDevolucion,
            'ID_Venta'          => $this->idVenta,
            'Cantidad_Devuelta' => 1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Esta devolución ya tiene un detalle registrado. Solo se permite un detalle por devolución.');
    }

    #[Test]
    public function no_puede_registrar_detalle_con_cantidad_mayor_a_la_comprada()
    {
        $cantidadComprada = DB::table('Detalle_Ventas')
            ->where('ID_Venta', $this->idVenta)
            ->value('Cantidad');

        $response = $this->post('/detalledevolucion', [
            'ID_Devolucion'     => $this->idDevolucion,
            'ID_Venta'          => $this->idVenta,
            'Cantidad_Devuelta' => $cantidadComprada + 999,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    // ===== ADMIN: ACTUALIZAR =====

    #[Test]
    public function puede_actualizar_detalle_devolucion()
    {
        DB::table('Detalle_Devoluciones')->insert([
            'ID_Devolucion'     => $this->idDevolucion,
            'ID_Venta'          => $this->idVenta,
            'Cantidad_Devuelta' => 1,
        ]);

        $response = $this->put("/detalledevolucion/{$this->idDevolucion}", [
            'ID_Venta'          => $this->idVenta,
            'Cantidad_Devuelta' => 1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('mensaje', 'Detalle actualizado correctamente.');

        $this->assertDatabaseHas('Detalle_Devoluciones', [
            'ID_Devolucion'     => $this->idDevolucion,
            'Cantidad_Devuelta' => 1,
        ]);
    }

    #[Test]
    public function no_puede_actualizar_detalle_inexistente()
    {
        // firstOrFail() lanza ModelNotFoundException → respuesta 404
        $response = $this->put('/detalledevolucion/999999', [
            'ID_Venta'          => $this->idVenta,
            'Cantidad_Devuelta' => 1,
        ]);

        $response->assertStatus(404);
    }

    #[Test]
    public function no_puede_actualizar_con_cantidad_mayor_a_la_comprada()
    {
        DB::table('Detalle_Devoluciones')->insert([
            'ID_Devolucion'     => $this->idDevolucion,
            'ID_Venta'          => $this->idVenta,
            'Cantidad_Devuelta' => 1,
        ]);

        $cantidadComprada = DB::table('Detalle_Ventas')
            ->where('ID_Venta', $this->idVenta)
            ->value('Cantidad');

        $response = $this->put("/detalledevolucion/{$this->idDevolucion}", [
            'ID_Venta'          => $this->idVenta,
            'Cantidad_Devuelta' => $cantidadComprada + 999,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    // ===== ADMIN: ELIMINAR =====

    #[Test]
    public function puede_eliminar_detalle_devolucion()
    {
        DB::table('Detalle_Devoluciones')->insert([
            'ID_Devolucion'     => $this->idDevolucion,
            'ID_Venta'          => $this->idVenta,
            'Cantidad_Devuelta' => 1,
        ]);

        $response = $this->delete("/detalledevolucion/{$this->idDevolucion}");

        $response->assertRedirect();
        $response->assertSessionHas('mensaje', 'Detalle eliminado correctamente.');

        $this->assertDatabaseMissing('Detalle_Devoluciones', [
            'ID_Devolucion' => $this->idDevolucion,
        ]);
    }

    #[Test]
    public function eliminar_detalle_inexistente_no_falla()
    {
        // El controlador usa ->delete() directo sin firstOrFail(),
        // por lo que simplemente no borra nada y retorna el mensaje igual
        $response = $this->delete('/detalledevolucion/999999');

        $response->assertRedirect();
        $response->assertSessionHas('mensaje', 'Detalle eliminado correctamente.');
    }

    // ===== EMPLEADO: LISTAR =====

    #[Test]
    public function empleado_puede_listar_detalles_devolucion()
    {
        $response = $this->get('/empleado/detalledevolucion');

        $response->assertStatus(200);
        $response->assertViewHas('detalles');
    }

    // ===== EMPLEADO: CREAR =====

    #[Test]
    public function empleado_puede_registrar_detalle_devolucion()
    {
        $response = $this->post('/empleado/detalledevolucion/store', [
            'ID_Devolucion'     => $this->idDevolucion,
            'ID_Venta'          => $this->idVenta,
            'Cantidad_Devuelta' => 1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('mensaje', 'Detalle registrado correctamente.');

        $this->assertDatabaseHas('Detalle_Devoluciones', [
            'ID_Devolucion'     => $this->idDevolucion,
            'Cantidad_Devuelta' => 1,
        ]);
    }

    #[Test]
    public function empleado_no_puede_registrar_detalle_sin_cantidad()
    {
        $response = $this->post('/empleado/detalledevolucion/store', [
            'ID_Devolucion'     => $this->idDevolucion,
            'ID_Venta'          => $this->idVenta,
            'Cantidad_Devuelta' => '',
        ]);

        $response->assertSessionHasErrors('Cantidad_Devuelta');
    }

    #[Test]
    public function empleado_no_puede_registrar_detalle_duplicado_para_misma_devolucion()
    {
        DB::table('Detalle_Devoluciones')->insert([
            'ID_Devolucion'     => $this->idDevolucion,
            'ID_Venta'          => $this->idVenta,
            'Cantidad_Devuelta' => 1,
        ]);

        $response = $this->post('/empleado/detalledevolucion/store', [
            'ID_Devolucion'     => $this->idDevolucion,
            'ID_Venta'          => $this->idVenta,
            'Cantidad_Devuelta' => 1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Esta devolución ya tiene un detalle registrado. Solo se permite un detalle por devolución.');
    }

    #[Test]
    public function empleado_no_puede_registrar_con_cantidad_mayor_a_la_comprada()
    {
        $cantidadComprada = DB::table('Detalle_Ventas')
            ->where('ID_Venta', $this->idVenta)
            ->value('Cantidad');

        $response = $this->post('/empleado/detalledevolucion/store', [
            'ID_Devolucion'     => $this->idDevolucion,
            'ID_Venta'          => $this->idVenta,
            'Cantidad_Devuelta' => $cantidadComprada + 999,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    // ===== EMPLEADO: ACTUALIZAR =====

    #[Test]
    public function empleado_puede_actualizar_cantidad_devuelta()
    {
        DB::table('Detalle_Devoluciones')->insert([
            'ID_Devolucion'     => $this->idDevolucion,
            'ID_Venta'          => $this->idVenta,
            'Cantidad_Devuelta' => 1,
        ]);

        $response = $this->put("/empleado/detalledevolucion/update/{$this->idDevolucion}", [
            'ID_Venta'          => $this->idVenta,
            'Cantidad_Devuelta' => 1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('mensaje', 'Detalle actualizado correctamente.');

        $this->assertDatabaseHas('Detalle_Devoluciones', [
            'ID_Devolucion'     => $this->idDevolucion,
            'Cantidad_Devuelta' => 1,
        ]);
    }

    #[Test]
    public function empleado_no_puede_actualizar_detalle_inexistente()
    {
        // firstOrFail() lanza ModelNotFoundException → respuesta 404
        $response = $this->put('/empleado/detalledevolucion/update/999999', [
            'ID_Venta'          => $this->idVenta,
            'Cantidad_Devuelta' => 1,
        ]);

        $response->assertStatus(404);
    }

    // ===== EMPLEADO: ELIMINAR =====

    #[Test]
    public function empleado_puede_eliminar_detalle_devolucion()
    {
        DB::table('Detalle_Devoluciones')->insert([
            'ID_Devolucion'     => $this->idDevolucion,
            'ID_Venta'          => $this->idVenta,
            'Cantidad_Devuelta' => 1,
        ]);

        $response = $this->delete("/empleado/detalledevolucion/delete/{$this->idDevolucion}");

        $response->assertRedirect();
        $response->assertSessionHas('mensaje', 'Detalle eliminado correctamente.');

        $this->assertDatabaseMissing('Detalle_Devoluciones', [
            'ID_Devolucion' => $this->idDevolucion,
        ]);
    }

    #[Test]
    public function empleado_eliminar_detalle_inexistente_no_falla()
    {
        // El controlador usa ->delete() directo sin firstOrFail()
        $response = $this->delete('/empleado/detalledevolucion/delete/999999');

        $response->assertRedirect();
        $response->assertSessionHas('mensaje', 'Detalle eliminado correctamente.');
    }
}