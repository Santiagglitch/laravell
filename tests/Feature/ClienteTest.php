<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;

class ClienteTest extends TestCase
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
    public function puede_listar_clientes()
    {
        $response = $this->get('/clientes');

        $response->assertStatus(200);
        $response->assertViewIs('clientes.index');
        $response->assertViewHas('clientes');
    }

    #[Test]
    public function puede_crear_cliente()
    {
        $response = $this->post('/clientes', [
            'Documento_Cliente' => '9999999999',
            'Nombre_Cliente' => 'Carlos',
            'Apellido_Cliente' => 'Martínez',
            'ID_Estado' => 1,
        ]);

        $response->assertRedirect('/clientes');
        $response->assertSessionHas('mensaje', 'Cliente registrado correctamente.');

        $this->assertDatabaseHas('Clientes', [
            'Documento_Cliente' => '9999999999',
            'Nombre_Cliente' => 'Carlos',
            'Apellido_Cliente' => 'Martínez',
            'ID_Estado' => 1,
        ]);
    }

    #[Test]
    public function no_puede_crear_cliente_con_documento_duplicado()
    {
        DB::table('Clientes')->insert([
            'Documento_Cliente' => '1111111111',
            'Nombre_Cliente' => 'Ana',
            'Apellido_Cliente' => 'López',
            'ID_Estado' => 1,
        ]);

        $response = $this->post('/clientes', [
            'Documento_Cliente' => '1111111111',
            'Nombre_Cliente' => 'Pedro',
            'Apellido_Cliente' => 'Gómez',
            'ID_Estado' => 1,
        ]);

        $response->assertRedirect('/clientes');
        $response->assertSessionHas('error', 'El cliente con este documento ya está registrado.');
    }

    #[Test]
    public function puede_actualizar_cliente()
    {
        DB::table('Clientes')->insert([
            'Documento_Cliente' => '2222222222',
            'Nombre_Cliente' => 'Luis',
            'Apellido_Cliente' => 'Pérez',
            'ID_Estado' => 1,
        ]);

        $response = $this->put('/clientes', [
            'Documento_Cliente' => '2222222222',
            'Nombre_Cliente' => 'Luis Alberto',
            'Apellido_Cliente' => 'Pérez García',
            'ID_Estado' => 2,
        ]);

        $response->assertRedirect('/clientes');
        $response->assertSessionHas('mensaje', 'Cliente actualizado correctamente.');

        $this->assertDatabaseHas('Clientes', [
            'Documento_Cliente' => '2222222222',
            'Nombre_Cliente' => 'Luis Alberto',
            'Apellido_Cliente' => 'Pérez García',
            'ID_Estado' => 2,
        ]);
    }

    #[Test]
    public function puede_actualizar_solo_nombre_de_cliente()
    {
        DB::table('Clientes')->insert([
            'Documento_Cliente' => '3333333333',
            'Nombre_Cliente' => 'María',
            'Apellido_Cliente' => 'Rodríguez',
            'ID_Estado' => 1,
        ]);

        $response = $this->put('/clientes', [
            'Documento_Cliente' => '3333333333',
            'Nombre_Cliente' => 'María José',
        ]);

        $response->assertRedirect('/clientes');
        $response->assertSessionHas('mensaje');

        $this->assertDatabaseHas('Clientes', [
            'Documento_Cliente' => '3333333333',
            'Nombre_Cliente' => 'María José',
            'Apellido_Cliente' => 'Rodríguez',
            'ID_Estado' => 1,
        ]);
    }

    #[Test]
    public function puede_actualizar_solo_estado_de_cliente()
    {
        DB::table('Clientes')->insert([
            'Documento_Cliente' => '4444444444',
            'Nombre_Cliente' => 'Jorge',
            'Apellido_Cliente' => 'Silva',
            'ID_Estado' => 1,
        ]);

        $response = $this->put('/clientes', [
            'Documento_Cliente' => '4444444444',
            'ID_Estado' => 2,
        ]);

        $response->assertRedirect('/clientes');

        $this->assertDatabaseHas('Clientes', [
            'Documento_Cliente' => '4444444444',
            'Nombre_Cliente' => 'Jorge',
            'Apellido_Cliente' => 'Silva',
            'ID_Estado' => 2,
        ]);
    }

    #[Test]
    public function puede_eliminar_cliente()
    {
        DB::table('Clientes')->insert([
            'Documento_Cliente' => '5555555555',
            'Nombre_Cliente' => 'Andrea',
            'Apellido_Cliente' => 'Torres',
            'ID_Estado' => 1,
        ]);

        $response = $this->delete('/clientes', [
            'Documento_Cliente' => '5555555555',
        ]);

        $response->assertRedirect('/clientes');
        $response->assertSessionHas('mensaje', 'Cliente eliminado correctamente.');

        $this->assertDatabaseMissing('Clientes', [
            'Documento_Cliente' => '5555555555',
        ]);
    }

    #[Test]
    public function no_puede_eliminar_cliente_inexistente()
    {
        $response = $this->delete('/clientes', [
            'Documento_Cliente' => '9999999999',
        ]);

        $response->assertRedirect(); // ✅ FIX 1
        $response->assertSessionHasErrors('Documento_Cliente'); // ✅ FIX 1
    }

    #[Test]
    public function validacion_nombre_requerido()
    {
        $response = $this->post('/clientes', [
            'Documento_Cliente' => '6666666666',
            'Apellido_Cliente' => 'Ramírez',
            'ID_Estado' => 1,
        ]);

        $response->assertSessionHasErrors('Nombre_Cliente');
    }

    #[Test]
    public function validacion_apellido_requerido()
    {
        $response = $this->post('/clientes', [
            'Documento_Cliente' => '7777777777',
            'Nombre_Cliente' => 'Sofía',
            'ID_Estado' => 1,
        ]);

        $response->assertSessionHasErrors('Apellido_Cliente');
    }

    #[Test]
    public function validacion_estado_requerido()
    {
        $response = $this->post('/clientes', [
            'Documento_Cliente' => '8888888888',
            'Nombre_Cliente' => 'Daniel',
            'Apellido_Cliente' => 'Castro',
        ]);

        $response->assertSessionHasErrors('ID_Estado');
    }

    #[Test]
    public function validacion_estado_debe_ser_1_o_2()
    {
        $response = $this->post('/clientes', [
            'Documento_Cliente' => '9999999998',
            'Nombre_Cliente' => 'Laura',
            'Apellido_Cliente' => 'Moreno',
            'ID_Estado' => 99,
        ]);

        $response->assertSessionHasErrors('ID_Estado');
    }

    #[Test]
    public function validacion_nombre_maximo_20_caracteres()
    {
        $response = $this->post('/clientes', [
            'Documento_Cliente' => '1010101010',
            'Nombre_Cliente' => 'NombreMuyLargoQueExcedeLosVeinteCaracteres',
            'Apellido_Cliente' => 'Díaz',
            'ID_Estado' => 1,
        ]);

        $response->assertSessionHasErrors('Nombre_Cliente');
    }

    #[Test]
    public function validacion_apellido_maximo_20_caracteres()
    {
        $response = $this->post('/clientes', [
            'Documento_Cliente' => '2020202020',
            'Nombre_Cliente' => 'Elena',
            'Apellido_Cliente' => 'ApellidoMuyLargoQueExcedeLosVeinteCaracteres',
            'ID_Estado' => 1,
        ]);

        $response->assertSessionHasErrors('Apellido_Cliente');
    }

    #[Test]
    public function no_puede_actualizar_cliente_inexistente()
    {
        $response = $this->put('/clientes', [
            'Documento_Cliente' => '9999999997',
            'Nombre_Cliente' => 'Fantasma',
        ]);

        $response->assertRedirect(); // ✅ FIX 4
        $response->assertSessionHasErrors('Documento_Cliente'); // ✅ FIX 4
    }
}