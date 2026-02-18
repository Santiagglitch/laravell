<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;

class DevolucionTest extends TestCase
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

    // ===== ADMIN: LISTAR =====

    #[Test]
    public function puede_listar_devoluciones()
    {
        $response = $this->get('/devolucion');

        $response->assertStatus(200);
        $response->assertViewHas('devolucion');
    }

    // ===== ADMIN: CREAR =====

    #[Test]
    public function puede_registrar_devolucion()
    {
        $response = $this->post('/devolucion', [
            'Fecha_Devolucion' => '2025-01-15',
            'Motivo'           => 'Producto dañado',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('mensaje', 'Devolución registrada correctamente.');

        $this->assertDatabaseHas('devoluciones', [
            'Motivo' => 'Producto dañado',
        ]);
    }

    #[Test]
    public function no_puede_registrar_devolucion_sin_fecha()
    {
        $response = $this->post('/devolucion', [
            'Fecha_Devolucion' => '',
            'Motivo'           => 'Producto dañado',
        ]);

        $response->assertSessionHasErrors('Fecha_Devolucion');
    }

    #[Test]
    public function no_puede_registrar_devolucion_sin_motivo()
    {
        $response = $this->post('/devolucion', [
            'Fecha_Devolucion' => '2025-01-15',
            'Motivo'           => '',
        ]);

        $response->assertSessionHasErrors('Motivo');
    }

    // ===== ADMIN: ACTUALIZAR =====

    #[Test]
    public function puede_actualizar_devolucion()
    {
        $id = (string) DB::table('devoluciones')->insertGetId([
            'Fecha_Devolucion' => '2025-01-15',
            'Motivo'           => 'Motivo original',
        ]);

        $response = $this->put('/devolucion', [
            'ID_Devolucion'    => $id,
            'Fecha_Devolucion' => '2025-02-01',
            'Motivo'           => 'Motivo actualizado',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('mensaje', 'Devolución actualizada correctamente.');

        $this->assertDatabaseHas('devoluciones', [
            'ID_Devolucion' => $id,
            'Motivo'        => 'Motivo actualizado',
        ]);
    }

    #[Test]
    public function no_puede_actualizar_devolucion_inexistente()
    {
        $response = $this->put('/devolucion', [
            'ID_Devolucion'    => '999999',
            'Fecha_Devolucion' => '2025-02-01',
            'Motivo'           => 'No existe',
        ]);

        $response->assertSessionHasErrors('ID_Devolucion');
    }

    // ===== ADMIN: ELIMINAR =====

    #[Test]
    public function puede_eliminar_devolucion_sin_detalles()
    {
        $id = (string) DB::table('devoluciones')->insertGetId([
            'Fecha_Devolucion' => '2025-01-15',
            'Motivo'           => 'Para eliminar',
        ]);

        $response = $this->delete('/devolucion', [
            'ID_Devolucion' => $id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('mensaje', 'Devolución eliminada correctamente.');

        $this->assertDatabaseMissing('devoluciones', [
            'ID_Devolucion' => $id,
        ]);
    }

    #[Test]
    public function no_puede_eliminar_devolucion_con_detalles()
    {
        $id = (string) DB::table('devoluciones')->insertGetId([
            'Fecha_Devolucion' => '2025-01-15',
            'Motivo'           => 'Con detalles',
        ]);

        DB::table('Detalle_Devoluciones')->insert([
            'ID_Devolucion'     => $id,
            'Cantidad_Devuelta' => 2,
            'ID_Venta'          => 1,
        ]);

        $response = $this->delete('/devolucion', [
            'ID_Devolucion' => $id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('devoluciones', [
            'ID_Devolucion' => $id,
        ]);
    }

    // ===== ADMIN: DETALLES JSON =====

    #[Test]
    public function puede_obtener_detalles_de_devolucion()
    {
        $id = (string) DB::table('devoluciones')->insertGetId([
            'Fecha_Devolucion' => '2025-01-15',
            'Motivo'           => 'Para ver detalles',
        ]);

        DB::table('Detalle_Devoluciones')->insert([
            'ID_Devolucion'     => $id,
            'Cantidad_Devuelta' => 3,
            'ID_Venta'          => 1,
        ]);

        $response = $this->get("/devolucion/{$id}/detalles");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'devolucion' => [
                'ID_Devolucion',
                'Fecha_Devolucion',
                'Motivo',
                'detalles',
            ],
        ]);
    }

    // ===== EMPLEADO: LISTAR =====

    #[Test]
    public function empleado_puede_listar_devoluciones()
    {
        $response = $this->get('/empleado/devolucion');

        $response->assertStatus(200);
        $response->assertViewHas('devolucion');
    }

    // ===== EMPLEADO: CREAR =====

    #[Test]
    public function empleado_puede_registrar_devolucion()
    {
        $response = $this->post('/empleado/devolucion/store', [
            'Fecha_Devolucion' => '2025-03-10',
            'Motivo'           => 'Producto en mal estado',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('mensaje', 'Devolución registrada correctamente.');

        $this->assertDatabaseHas('devoluciones', [
            'Motivo' => 'Producto en mal estado',
        ]);
    }

    #[Test]
    public function empleado_no_puede_registrar_devolucion_sin_motivo()
    {
        $response = $this->post('/empleado/devolucion/store', [
            'Fecha_Devolucion' => '2025-03-10',
            'Motivo'           => '',
        ]);

        $response->assertSessionHasErrors('Motivo');
    }

    // ===== EMPLEADO: ACTUALIZAR =====

    #[Test]
    public function empleado_puede_actualizar_motivo()
    {
        $id = (string) DB::table('devoluciones')->insertGetId([
            'Fecha_Devolucion' => '2025-03-10',
            'Motivo'           => 'Motivo viejo',
        ]);

        $response = $this->put('/empleado/devolucion/update', [
            'ID_Devolucion' => $id,
            'Motivo'        => 'Motivo corregido',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('mensaje', 'Devolución actualizada correctamente.');

        $this->assertDatabaseHas('devoluciones', [
            'ID_Devolucion' => $id,
            'Motivo'        => 'Motivo corregido',
        ]);
    }

    // ===== EMPLEADO: ELIMINAR =====

    #[Test]
    public function empleado_puede_eliminar_devolucion_sin_detalles()
    {
        $id = (string) DB::table('devoluciones')->insertGetId([
            'Fecha_Devolucion' => '2025-03-10',
            'Motivo'           => 'A eliminar por empleado',
        ]);

        $response = $this->delete('/empleado/devolucion/destroy', [
            'ID_Devolucion' => $id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('mensaje', 'Devolución eliminada correctamente.');

        $this->assertDatabaseMissing('devoluciones', [
            'ID_Devolucion' => $id,
        ]);
    }

    #[Test]
    public function empleado_no_puede_eliminar_devolucion_con_detalles()
    {
        $id = (string) DB::table('devoluciones')->insertGetId([
            'Fecha_Devolucion' => '2025-03-10',
            'Motivo'           => 'Con detalles empleado',
        ]);

        DB::table('Detalle_Devoluciones')->insert([
            'ID_Devolucion'     => $id,
            'Cantidad_Devuelta' => 1,
            'ID_Venta'          => 1,
        ]);

        $response = $this->delete('/empleado/devolucion/destroy', [
            'ID_Devolucion' => $id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('devoluciones', [
            'ID_Devolucion' => $id,
        ]);
    }
}