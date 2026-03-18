<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;

class ProveedorTest extends TestCase
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

    // ===== LISTAR =====

    #[Test]
    public function puede_listar_proveedores()
    {
        $response = $this->get('/proveedores');

        $response->assertStatus(200);
        $response->assertViewHas('proveedores');
    }

    // ===== CREAR =====

    #[Test]
    public function puede_registrar_proveedor()
    {
        $response = $this->post('/proveedores', [
            'Nombre_Proveedor'   => 'Proveedor Test',
            'Correo_Electronico' => 'proveedor@test.com',
            'Telefono'           => '3001234567',
            'ID_Estado'          => 1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('mensaje', 'Proveedor registrado correctamente.');

        $this->assertDatabaseHas('Proveedores', [
            'Nombre_Proveedor' => 'Proveedor Test',
        ]);
    }

    #[Test]
    public function no_puede_registrar_proveedor_sin_nombre()
    {
        $response = $this->post('/proveedores', [
            'Nombre_Proveedor'   => '',
            'Correo_Electronico' => 'proveedor@test.com',
            'Telefono'           => '3001234567',
            'ID_Estado'          => 1,
        ]);

        $response->assertSessionHasErrors('Nombre_Proveedor');
    }

    #[Test]
    public function no_puede_registrar_proveedor_sin_correo()
    {
        $response = $this->post('/proveedores', [
            'Nombre_Proveedor'   => 'Proveedor Test',
            'Correo_Electronico' => '',
            'Telefono'           => '3001234567',
            'ID_Estado'          => 1,
        ]);

        $response->assertSessionHasErrors('Correo_Electronico');
    }

    #[Test]
    public function no_puede_registrar_proveedor_sin_telefono()
    {
        $response = $this->post('/proveedores', [
            'Nombre_Proveedor'   => 'Proveedor Test',
            'Correo_Electronico' => 'proveedor@test.com',
            'Telefono'           => '',
            'ID_Estado'          => 1,
        ]);

        $response->assertSessionHasErrors('Telefono');
    }

    #[Test]
    public function no_puede_registrar_proveedor_sin_estado()
    {
        $response = $this->post('/proveedores', [
            'Nombre_Proveedor'   => 'Proveedor Test',
            'Correo_Electronico' => 'proveedor@test.com',
            'Telefono'           => '3001234567',
            'ID_Estado'          => '',
        ]);

        $response->assertSessionHasErrors('ID_Estado');
    }

    #[Test]
    public function no_puede_registrar_proveedor_con_estado_invalido()
    {
        $response = $this->post('/proveedores', [
            'Nombre_Proveedor'   => 'Proveedor Test',
            'Correo_Electronico' => 'proveedor@test.com',
            'Telefono'           => '3001234567',
            'ID_Estado'          => 99,
        ]);

        $response->assertSessionHasErrors('ID_Estado');
    }

    #[Test]
    public function no_puede_registrar_proveedor_con_correo_invalido()
    {
        $response = $this->post('/proveedores', [
            'Nombre_Proveedor'   => 'Proveedor Test',
            'Correo_Electronico' => 'correo-no-valido',
            'Telefono'           => '3001234567',
            'ID_Estado'          => 1,
        ]);

        $response->assertSessionHasErrors('Correo_Electronico');
    }

    #[Test]
    public function no_puede_registrar_proveedor_con_nombre_duplicado()
    {
        DB::table('Proveedores')->insert([
            'Nombre_Proveedor'   => 'Proveedor Duplicado',
            'Correo_Electronico' => 'original@test.com',
            'Telefono'           => '3000000001',
            'ID_Estado'          => 1,
        ]);

        $response = $this->post('/proveedores', [
            'Nombre_Proveedor'   => 'Proveedor Duplicado',
            'Correo_Electronico' => 'otro@test.com',
            'Telefono'           => '3000000002',
            'ID_Estado'          => 1,
        ]);

        $response->assertSessionHasErrors('Nombre_Proveedor');
    }

    #[Test]
    public function no_puede_registrar_proveedor_con_correo_duplicado()
    {
        DB::table('Proveedores')->insert([
            'Nombre_Proveedor'   => 'Proveedor Original',
            'Correo_Electronico' => 'duplicado@test.com',
            'Telefono'           => '3000000003',
            'ID_Estado'          => 1,
        ]);

        $response = $this->post('/proveedores', [
            'Nombre_Proveedor'   => 'Proveedor Nuevo',
            'Correo_Electronico' => 'duplicado@test.com',
            'Telefono'           => '3000000004',
            'ID_Estado'          => 1,
        ]);

        $response->assertSessionHasErrors('Correo_Electronico');
    }

    #[Test]
    public function no_puede_registrar_proveedor_con_telefono_duplicado()
    {
        DB::table('Proveedores')->insert([
            'Nombre_Proveedor'   => 'Proveedor Original',
            'Correo_Electronico' => 'original2@test.com',
            'Telefono'           => '3000000005',
            'ID_Estado'          => 1,
        ]);

        $response = $this->post('/proveedores', [
            'Nombre_Proveedor'   => 'Proveedor Nuevo',
            'Correo_Electronico' => 'nuevo@test.com',
            'Telefono'           => '3000000005',
            'ID_Estado'          => 1,
        ]);

        $response->assertSessionHasErrors('Telefono');
    }

    // ===== ACTUALIZAR =====

    #[Test]
    public function puede_actualizar_proveedor()
    {
        $id = DB::table('Proveedores')->insertGetId([
            'Nombre_Proveedor'   => 'Proveedor Original',
            'Correo_Electronico' => 'original@update.com',
            'Telefono'           => '3100000001',
            'ID_Estado'          => 1,
        ]);

        $response = $this->put('/proveedores', [
            'ID_Proveedor'       => $id,
            'Nombre_Proveedor'   => 'Proveedor Actualizado',
            'Correo_Electronico' => 'actualizado@update.com',
            'Telefono'           => '3100000002',
            'ID_Estado'          => 2,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('mensaje', 'Proveedor actualizado correctamente.');

        $this->assertDatabaseHas('Proveedores', [
            'ID_Proveedor'     => $id,
            'Nombre_Proveedor' => 'Proveedor Actualizado',
        ]);
    }

    #[Test]
    public function puede_actualizar_proveedor_con_campos_parciales()
    {
        $id = DB::table('Proveedores')->insertGetId([
            'Nombre_Proveedor'   => 'Proveedor Parcial',
            'Correo_Electronico' => 'parcial@update.com',
            'Telefono'           => '3100000003',
            'ID_Estado'          => 1,
        ]);

        $response = $this->put('/proveedores', [
            'ID_Proveedor'     => $id,
            'Nombre_Proveedor' => 'Proveedor Parcial Editado',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('mensaje', 'Proveedor actualizado correctamente.');

        $this->assertDatabaseHas('Proveedores', [
            'ID_Proveedor'     => $id,
            'Nombre_Proveedor' => 'Proveedor Parcial Editado',
        ]);
    }

    #[Test]
    public function no_puede_actualizar_proveedor_inexistente()
    {
        $response = $this->put('/proveedores', [
            'ID_Proveedor'     => 999999,
            'Nombre_Proveedor' => 'No existe',
        ]);

        $response->assertSessionHasErrors('ID_Proveedor');
    }

    #[Test]
    public function no_puede_actualizar_proveedor_con_nombre_duplicado()
    {
        DB::table('Proveedores')->insert([
            'Nombre_Proveedor'   => 'Nombre Ocupado',
            'Correo_Electronico' => 'ocupado@test.com',
            'Telefono'           => '3200000001',
            'ID_Estado'          => 1,
        ]);

        $id = DB::table('Proveedores')->insertGetId([
            'Nombre_Proveedor'   => 'Mi Proveedor',
            'Correo_Electronico' => 'miproveedor@test.com',
            'Telefono'           => '3200000002',
            'ID_Estado'          => 1,
        ]);

        $response = $this->put('/proveedores', [
            'ID_Proveedor'     => $id,
            'Nombre_Proveedor' => 'Nombre Ocupado',
        ]);

        $response->assertSessionHasErrors('Nombre_Proveedor');
    }

    #[Test]
    public function no_puede_actualizar_proveedor_con_correo_duplicado()
    {
        DB::table('Proveedores')->insert([
            'Nombre_Proveedor'   => 'Proveedor A',
            'Correo_Electronico' => 'correo.ocupado@test.com',
            'Telefono'           => '3200000003',
            'ID_Estado'          => 1,
        ]);

        $id = DB::table('Proveedores')->insertGetId([
            'Nombre_Proveedor'   => 'Proveedor B',
            'Correo_Electronico' => 'correo.b@test.com',
            'Telefono'           => '3200000004',
            'ID_Estado'          => 1,
        ]);

        $response = $this->put('/proveedores', [
            'ID_Proveedor'       => $id,
            'Correo_Electronico' => 'correo.ocupado@test.com',
        ]);

        $response->assertSessionHasErrors('Correo_Electronico');
    }

    #[Test]
    public function puede_actualizar_proveedor_con_sus_propios_datos_unicos()
    {
        // Rule::unique()->ignore() permite guardar el mismo valor que ya tenía
        $id = DB::table('Proveedores')->insertGetId([
            'Nombre_Proveedor'   => 'Mismo Nombre',
            'Correo_Electronico' => 'mismo@test.com',
            'Telefono'           => '3200000005',
            'ID_Estado'          => 1,
        ]);

        $response = $this->put('/proveedores', [
            'ID_Proveedor'       => $id,
            'Nombre_Proveedor'   => 'Mismo Nombre',
            'Correo_Electronico' => 'mismo@test.com',
            'Telefono'           => '3200000005',
            'ID_Estado'          => 2,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('mensaje', 'Proveedor actualizado correctamente.');
    }

    // ===== ELIMINAR =====

    #[Test]
    public function puede_eliminar_proveedor_sin_compras()
    {
        $id = DB::table('Proveedores')->insertGetId([
            'Nombre_Proveedor'   => 'Proveedor A Eliminar',
            'Correo_Electronico' => 'eliminar@test.com',
            'Telefono'           => '3300000001',
            'ID_Estado'          => 1,
        ]);

        $response = $this->delete('/proveedores', [
            'ID_Proveedor' => $id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('mensaje', 'Proveedor eliminado correctamente.');

        $this->assertDatabaseMissing('Proveedores', [
            'ID_Proveedor' => $id,
        ]);
    }

    #[Test]
    public function no_puede_eliminar_proveedor_con_detalles_compras()
    {
        $idProveedor = DB::table('Proveedores')->insertGetId([
            'Nombre_Proveedor'   => 'Proveedor Con Compras',
            'Correo_Electronico' => 'concompras@test.com',
            'Telefono'           => '3300000002',
            'ID_Estado'          => 1,
        ]);

        // Tomar una Compra ya existente en la BD para respetar la FK
        $idEntrada = DB::table('Compras')->value('ID_Entrada');

        DB::table('Detalle_Compras')->insert([
            'ID_Proveedor'  => $idProveedor,
            'ID_Entrada'    => $idEntrada,
            'Fecha_Entrada' => '2025-01-15',
            'Cantidad'      => 5,
        ]);

        $response = $this->delete('/proveedores', [
            'ID_Proveedor' => $idProveedor,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'No se puede eliminar este proveedor porque tiene detalles de compras asociados. Primero elimine los detalles de compras relacionados.');

        $this->assertDatabaseHas('Proveedores', [
            'ID_Proveedor' => $idProveedor,
        ]);
    }

    #[Test]
    public function no_puede_eliminar_proveedor_inexistente()
    {
        $response = $this->delete('/proveedores', [
            'ID_Proveedor' => 999999,
        ]);

        $response->assertSessionHasErrors('ID_Proveedor');
    }
}