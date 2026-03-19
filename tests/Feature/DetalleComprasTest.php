<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;

class DetalleComprasTest extends TestCase
{
    use DatabaseTransactions;

    protected string $idProveedor;
    protected string $idEntrada;

    protected function setUp(): void
    {
        parent::setUp();

        session([
            'documento' => '1013262102',
            'nombre'    => 'Kevin Alexis',
            'rol'       => 1,
        ]);

        // Tomar un proveedor y una compra ya existentes en la BD
        $this->idProveedor = (string) DB::table('Proveedores')->value('ID_Proveedor');
        $this->idEntrada   = (string) DB::table('Compras')->value('ID_Entrada');
    }

    // ===== LISTAR =====

    #[Test]
    public function puede_listar_detalles_compras()
    {
        $response = $this->get('/detallecompras');

        $response->assertStatus(200);
        $response->assertViewHas('detalles');
    }

    // ===== CREAR =====

    #[Test]
    public function puede_registrar_detalle_compra()
    {
        $response = $this->post('/detallecompras', [
            'Fecha_Entrada' => '2025-01-15',
            'Cantidad'      => 5,
            'ID_Proveedor'  => $this->idProveedor,
            'ID_Entrada'    => $this->idEntrada,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('mensaje', 'Detalle de compra registrado correctamente.');

        $this->assertDatabaseHas('Detalle_Compras', [
            'ID_Proveedor' => $this->idProveedor,
            'ID_Entrada'   => $this->idEntrada,
            'Cantidad'     => 5,
        ]);
    }

    #[Test]
    public function no_puede_registrar_detalle_sin_fecha_entrada()
    {
        $response = $this->post('/detallecompras', [
            'Fecha_Entrada' => '',
            'Cantidad'      => 5,
            'ID_Proveedor'  => $this->idProveedor,
            'ID_Entrada'    => $this->idEntrada,
        ]);

        $response->assertSessionHasErrors('Fecha_Entrada');
    }

    #[Test]
    public function no_puede_registrar_detalle_sin_cantidad()
    {
        $response = $this->post('/detallecompras', [
            'Fecha_Entrada' => '2025-01-15',
            'Cantidad'      => '',
            'ID_Proveedor'  => $this->idProveedor,
            'ID_Entrada'    => $this->idEntrada,
        ]);

        $response->assertSessionHasErrors('Cantidad');
    }

    #[Test]
    public function no_puede_registrar_detalle_sin_proveedor()
    {
        $response = $this->post('/detallecompras', [
            'Fecha_Entrada' => '2025-01-15',
            'Cantidad'      => 5,
            'ID_Proveedor'  => '',
            'ID_Entrada'    => $this->idEntrada,
        ]);

        $response->assertSessionHasErrors('ID_Proveedor');
    }

    #[Test]
    public function no_puede_registrar_detalle_sin_entrada()
    {
        $response = $this->post('/detallecompras', [
            'Fecha_Entrada' => '2025-01-15',
            'Cantidad'      => 5,
            'ID_Proveedor'  => $this->idProveedor,
            'ID_Entrada'    => '',
        ]);

        $response->assertSessionHasErrors('ID_Entrada');
    }

    #[Test]
    public function no_puede_registrar_detalle_con_cantidad_menor_a_uno()
    {
        $response = $this->post('/detallecompras', [
            'Fecha_Entrada' => '2025-01-15',
            'Cantidad'      => 0,
            'ID_Proveedor'  => $this->idProveedor,
            'ID_Entrada'    => $this->idEntrada,
        ]);

        $response->assertSessionHasErrors('Cantidad');
    }

    #[Test]
    public function no_puede_registrar_detalle_con_fecha_invalida()
    {
        $response = $this->post('/detallecompras', [
            'Fecha_Entrada' => 'no-es-una-fecha',
            'Cantidad'      => 5,
            'ID_Proveedor'  => $this->idProveedor,
            'ID_Entrada'    => $this->idEntrada,
        ]);

        $response->assertSessionHasErrors('Fecha_Entrada');
    }

    #[Test]
    public function no_puede_registrar_detalle_con_proveedor_inexistente()
    {
        $response = $this->post('/detallecompras', [
            'Fecha_Entrada' => '2025-01-15',
            'Cantidad'      => 5,
            'ID_Proveedor'  => '999999',
            'ID_Entrada'    => $this->idEntrada,
        ]);

        $response->assertSessionHasErrors('ID_Proveedor');
    }

    #[Test]
    public function no_puede_registrar_detalle_con_entrada_inexistente()
    {
        $response = $this->post('/detallecompras', [
            'Fecha_Entrada' => '2025-01-15',
            'Cantidad'      => 5,
            'ID_Proveedor'  => $this->idProveedor,
            'ID_Entrada'    => '999999',
        ]);

        $response->assertSessionHasErrors('ID_Entrada');
    }

    // ===== ACTUALIZAR =====

    #[Test]
    public function puede_actualizar_detalle_compra()
    {
        // Insertar detalle base para actualizar
        DB::table('Detalle_Compras')->insert([
            'ID_Proveedor'  => $this->idProveedor,
            'ID_Entrada'    => $this->idEntrada,
            'Fecha_Entrada' => '2025-01-15',
            'Cantidad'      => 3,
        ]);

        $response = $this->put('/detallecompras', [
            'ID_Proveedor'  => $this->idProveedor,
            'ID_Entrada'    => $this->idEntrada,
            'Fecha_Entrada' => '2025-06-01',
            'Cantidad'      => 10,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('mensaje', 'Detalle de compra actualizado correctamente.');

        $this->assertDatabaseHas('Detalle_Compras', [
            'ID_Proveedor' => $this->idProveedor,
            'ID_Entrada'   => $this->idEntrada,
            'Cantidad'     => 10,
        ]);
    }

    #[Test]
    public function puede_actualizar_detalle_compra_con_campos_parciales()
    {
        // El controlador filtra nulls y actualiza solo lo enviado
        DB::table('Detalle_Compras')->insert([
            'ID_Proveedor'  => $this->idProveedor,
            'ID_Entrada'    => $this->idEntrada,
            'Fecha_Entrada' => '2025-01-15',
            'Cantidad'      => 3,
        ]);

        $response = $this->put('/detallecompras', [
            'ID_Proveedor' => $this->idProveedor,
            'ID_Entrada'   => $this->idEntrada,
            'Cantidad'     => 7,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('mensaje', 'Detalle de compra actualizado correctamente.');

        $this->assertDatabaseHas('Detalle_Compras', [
            'ID_Proveedor' => $this->idProveedor,
            'ID_Entrada'   => $this->idEntrada,
            'Cantidad'     => 7,
        ]);
    }

    #[Test]
    public function no_puede_actualizar_detalle_con_proveedor_inexistente()
    {
        $response = $this->put('/detallecompras', [
            'ID_Proveedor' => '999999',
            'ID_Entrada'   => $this->idEntrada,
            'Cantidad'     => 5,
        ]);

        $response->assertSessionHasErrors('ID_Proveedor');
    }

    #[Test]
    public function no_puede_actualizar_detalle_con_entrada_inexistente()
    {
        $response = $this->put('/detallecompras', [
            'ID_Proveedor' => $this->idProveedor,
            'ID_Entrada'   => '999999',
            'Cantidad'     => 5,
        ]);

        $response->assertSessionHasErrors('ID_Entrada');
    }

    #[Test]
    public function no_puede_actualizar_detalle_con_cantidad_menor_a_uno()
    {
        DB::table('Detalle_Compras')->insert([
            'ID_Proveedor'  => $this->idProveedor,
            'ID_Entrada'    => $this->idEntrada,
            'Fecha_Entrada' => '2025-01-15',
            'Cantidad'      => 3,
        ]);

        $response = $this->put('/detallecompras', [
            'ID_Proveedor' => $this->idProveedor,
            'ID_Entrada'   => $this->idEntrada,
            'Cantidad'     => 0,
        ]);

        $response->assertSessionHasErrors('Cantidad');
    }

    // ===== ELIMINAR =====

    #[Test]
    public function puede_eliminar_detalle_compra()
    {
        DB::table('Detalle_Compras')->insert([
            'ID_Proveedor'  => $this->idProveedor,
            'ID_Entrada'    => $this->idEntrada,
            'Fecha_Entrada' => '2025-01-15',
            'Cantidad'      => 2,
        ]);

        $response = $this->delete('/detallecompras', [
            'ID_Proveedor' => $this->idProveedor,
            'ID_Entrada'   => $this->idEntrada,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('mensaje', 'Detalle de compra eliminado correctamente.');

        $this->assertDatabaseMissing('Detalle_Compras', [
            'ID_Proveedor' => $this->idProveedor,
            'ID_Entrada'   => $this->idEntrada,
        ]);
    }

    #[Test]
    public function no_puede_eliminar_detalle_con_proveedor_inexistente()
    {
        $response = $this->delete('/detallecompras', [
            'ID_Proveedor' => '999999',
            'ID_Entrada'   => $this->idEntrada,
        ]);

        $response->assertSessionHasErrors('ID_Proveedor');
    }

    #[Test]
    public function no_puede_eliminar_detalle_con_entrada_inexistente()
    {
        $response = $this->delete('/detallecompras', [
            'ID_Proveedor' => $this->idProveedor,
            'ID_Entrada'   => '999999',
        ]);

        $response->assertSessionHasErrors('ID_Entrada');
    }
}