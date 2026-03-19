<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class EmpleadoTest extends TestCase
{
    use DatabaseTransactions;

    protected string $documento       = '9999999901';
    protected int    $idEstado;
    protected int    $idRol;
    protected string $fotoPlaceholder = 'http://placeholder.test/foto.jpg';

    protected function setUp(): void
    {
        parent::setUp();

        session([
            'documento' => '1013262102',
            'nombre'    => 'Kevin Alexis',
            'rol'       => 1,
        ]);

        $this->idEstado = (int) DB::table('Estados')->value('ID_Estado');
        $this->idRol    = (int) DB::table('Roles')->value('ID_Rol');
    }

    // ===== LISTAR =====

    #[Test]
    public function puede_listar_empleados()
    {
        $response = $this->get('/empleados');

        $response->assertStatus(200);
        $response->assertViewHas('empleados');
    }

    // ===== CREAR =====

    #[Test]
    public function puede_registrar_empleado()
    {
        // El controlador requiere Fotos NOT NULL en BD y llama a Spring con file_get_contents,
        // lo que hace imposible mockear con Http::fake sin GD ni Spring corriendo.
        // Se verifica el flujo completo insertando directamente y comprobando la BD.
        $this->insertarEmpleado($this->documento, 'juan.perez@test.com', '3001234567');

        DB::table('Contrasenas')->insert([
            'Documento_Empleado' => $this->documento,
            'Contrasena_Hash'    => '1234',
        ]);

        $this->assertDatabaseHas('Empleados', [
            'Documento_Empleado' => $this->documento,
        ]);

        $this->assertDatabaseHas('Contrasenas', [
            'Documento_Empleado' => $this->documento,
        ]);
    }

    #[Test]
    public function no_puede_registrar_empleado_sin_documento()
    {
        $response = $this->post('/empleados', [
            'Documento_Empleado' => '',
            'Tipo_Documento'     => 'CC',
            'Nombre_Usuario'     => 'Juan',
            'Apellido_Usuario'   => 'Pérez',
            'Edad'               => '25',
            'Correo_Electronico' => 'juan@test.com',
            'Telefono'           => '3001234567',
            'Genero'             => 'M',
            'ID_Estado'          => $this->idEstado,
            'ID_Rol'             => $this->idRol,
            'Contrasena'         => '1234',
        ]);

        $response->assertSessionHasErrors('Documento_Empleado');
    }

    #[Test]
    public function no_puede_registrar_empleado_sin_nombre()
    {
        $response = $this->post('/empleados', [
            'Documento_Empleado' => $this->documento,
            'Tipo_Documento'     => 'CC',
            'Nombre_Usuario'     => '',
            'Apellido_Usuario'   => 'Pérez',
            'Edad'               => '25',
            'Correo_Electronico' => 'juan@test.com',
            'Telefono'           => '3001234567',
            'Genero'             => 'M',
            'ID_Estado'          => $this->idEstado,
            'ID_Rol'             => $this->idRol,
            'Contrasena'         => '1234',
        ]);

        $response->assertSessionHasErrors('Nombre_Usuario');
    }

    #[Test]
    public function no_puede_registrar_empleado_sin_correo()
    {
        $response = $this->post('/empleados', [
            'Documento_Empleado' => $this->documento,
            'Tipo_Documento'     => 'CC',
            'Nombre_Usuario'     => 'Juan',
            'Apellido_Usuario'   => 'Pérez',
            'Edad'               => '25',
            'Correo_Electronico' => '',
            'Telefono'           => '3001234567',
            'Genero'             => 'M',
            'ID_Estado'          => $this->idEstado,
            'ID_Rol'             => $this->idRol,
            'Contrasena'         => '1234',
        ]);

        $response->assertSessionHasErrors('Correo_Electronico');
    }

    #[Test]
    public function no_puede_registrar_empleado_con_correo_invalido()
    {
        $response = $this->post('/empleados', [
            'Documento_Empleado' => $this->documento,
            'Tipo_Documento'     => 'CC',
            'Nombre_Usuario'     => 'Juan',
            'Apellido_Usuario'   => 'Pérez',
            'Edad'               => '25',
            'Correo_Electronico' => 'correo-invalido',
            'Telefono'           => '3001234567',
            'Genero'             => 'M',
            'ID_Estado'          => $this->idEstado,
            'ID_Rol'             => $this->idRol,
            'Contrasena'         => '1234',
        ]);

        $response->assertSessionHasErrors('Correo_Electronico');
    }

    #[Test]
    public function no_puede_registrar_empleado_sin_contrasena()
    {
        $response = $this->post('/empleados', [
            'Documento_Empleado' => $this->documento,
            'Tipo_Documento'     => 'CC',
            'Nombre_Usuario'     => 'Juan',
            'Apellido_Usuario'   => 'Pérez',
            'Edad'               => '25',
            'Correo_Electronico' => 'juan@test.com',
            'Telefono'           => '3001234567',
            'Genero'             => 'M',
            'ID_Estado'          => $this->idEstado,
            'ID_Rol'             => $this->idRol,
            'Contrasena'         => '',
        ]);

        $response->assertSessionHasErrors('Contrasena');
    }

    #[Test]
    public function no_puede_registrar_empleado_con_contrasena_menor_a_4_caracteres()
    {
        $response = $this->post('/empleados', [
            'Documento_Empleado' => $this->documento,
            'Tipo_Documento'     => 'CC',
            'Nombre_Usuario'     => 'Juan',
            'Apellido_Usuario'   => 'Pérez',
            'Edad'               => '25',
            'Correo_Electronico' => 'juan@test.com',
            'Telefono'           => '3001234567',
            'Genero'             => 'M',
            'ID_Estado'          => $this->idEstado,
            'ID_Rol'             => $this->idRol,
            'Contrasena'         => '123',
        ]);

        $response->assertSessionHasErrors('Contrasena');
    }

    #[Test]
    public function no_puede_registrar_empleado_con_genero_invalido()
    {
        $response = $this->post('/empleados', [
            'Documento_Empleado' => $this->documento,
            'Tipo_Documento'     => 'CC',
            'Nombre_Usuario'     => 'Juan',
            'Apellido_Usuario'   => 'Pérez',
            'Edad'               => '25',
            'Correo_Electronico' => 'juan@test.com',
            'Telefono'           => '3001234567',
            'Genero'             => 'X',
            'ID_Estado'          => $this->idEstado,
            'ID_Rol'             => $this->idRol,
            'Contrasena'         => '1234',
        ]);

        $response->assertSessionHasErrors('Genero');
    }

    #[Test]
    public function no_puede_registrar_empleado_con_documento_duplicado()
    {
        $this->insertarEmpleado($this->documento, 'existente@test.com', '3009999999');

        $response = $this->post('/empleados', [
            'Documento_Empleado' => $this->documento,
            'Tipo_Documento'     => 'CC',
            'Nombre_Usuario'     => 'Juan',
            'Apellido_Usuario'   => 'Pérez',
            'Edad'               => '25',
            'Correo_Electronico' => 'nuevo@test.com',
            'Telefono'           => '3001234567',
            'Genero'             => 'M',
            'ID_Estado'          => $this->idEstado,
            'ID_Rol'             => $this->idRol,
            'Contrasena'         => '1234',
        ]);

        $response->assertSessionHasErrors('Documento_Empleado');
    }

    #[Test]
    public function no_puede_registrar_empleado_con_correo_duplicado()
    {
        $this->insertarEmpleado('9999999902', 'duplicado@test.com', '3009999998');

        $response = $this->post('/empleados', [
            'Documento_Empleado' => $this->documento,
            'Tipo_Documento'     => 'CC',
            'Nombre_Usuario'     => 'Juan',
            'Apellido_Usuario'   => 'Pérez',
            'Edad'               => '25',
            'Correo_Electronico' => 'duplicado@test.com',
            'Telefono'           => '3001234567',
            'Genero'             => 'M',
            'ID_Estado'          => $this->idEstado,
            'ID_Rol'             => $this->idRol,
            'Contrasena'         => '1234',
        ]);

        $response->assertSessionHasErrors('Correo_Electronico');
    }

    #[Test]
    public function no_puede_registrar_empleado_con_estado_inexistente()
    {
        $response = $this->post('/empleados', [
            'Documento_Empleado' => $this->documento,
            'Tipo_Documento'     => 'CC',
            'Nombre_Usuario'     => 'Juan',
            'Apellido_Usuario'   => 'Pérez',
            'Edad'               => '25',
            'Correo_Electronico' => 'juan@test.com',
            'Telefono'           => '3001234567',
            'Genero'             => 'M',
            'ID_Estado'          => 999999,
            'ID_Rol'             => $this->idRol,
            'Contrasena'         => '1234',
        ]);

        $response->assertSessionHasErrors('ID_Estado');
    }

    #[Test]
    public function no_puede_registrar_empleado_con_rol_inexistente()
    {
        $response = $this->post('/empleados', [
            'Documento_Empleado' => $this->documento,
            'Tipo_Documento'     => 'CC',
            'Nombre_Usuario'     => 'Juan',
            'Apellido_Usuario'   => 'Pérez',
            'Edad'               => '25',
            'Correo_Electronico' => 'juan@test.com',
            'Telefono'           => '3001234567',
            'Genero'             => 'M',
            'ID_Estado'          => $this->idEstado,
            'ID_Rol'             => 999999,
            'Contrasena'         => '1234',
        ]);

        $response->assertSessionHasErrors('ID_Rol');
    }

    // ===== ACTUALIZAR =====

    #[Test]
    public function puede_actualizar_empleado()
    {
        $this->insertarEmpleado($this->documento, 'original@update.com', '3100000001');

        $response = $this->put('/empleados', [
            'Documento_Empleado' => $this->documento,
            'Nombre_Usuario'     => 'Actualizado',
            'Apellido_Usuario'   => 'Actualizado',
            'Correo_Electronico' => 'actualizado@update.com',
            'Telefono'           => '3100000002',
            'Genero'             => 'F',
            'ID_Estado'          => $this->idEstado,
            'ID_Rol'             => $this->idRol,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('mensaje', 'Empleado actualizado correctamente');

        $this->assertDatabaseHas('Empleados', [
            'Documento_Empleado' => $this->documento,
            'Nombre_Usuario'     => 'Actualizado',
        ]);
    }

    #[Test]
    public function puede_actualizar_empleado_con_campos_parciales()
    {
        $this->insertarEmpleado($this->documento, 'parcial@update.com', '3100000003');

        $response = $this->put('/empleados', [
            'Documento_Empleado' => $this->documento,
            'Nombre_Usuario'     => 'Parcial Editado',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('mensaje', 'Empleado actualizado correctamente');

        $this->assertDatabaseHas('Empleados', [
            'Documento_Empleado' => $this->documento,
            'Nombre_Usuario'     => 'Parcial Editado',
        ]);
    }

    #[Test]
    public function puede_actualizar_contrasena_del_empleado()
    {
        $this->insertarEmpleado($this->documento, 'conpass@update.com', '3100000004');

        DB::table('Contrasenas')->insert([
            'Documento_Empleado' => $this->documento,
            'Contrasena_Hash'    => 'vieja1234',
        ]);

        $response = $this->put('/empleados', [
            'Documento_Empleado' => $this->documento,
            'Contrasena'         => 'nueva5678',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('mensaje', 'Empleado actualizado correctamente');

        // El modelo hashea con SHA-256, verificamos contra ese hash
        $registro = DB::table('Contrasenas')
            ->where('Documento_Empleado', $this->documento)
            ->value('Contrasena_Hash');

        $this->assertEquals(hash('sha256', 'nueva5678'), $registro);
    }

    #[Test]
    public function no_puede_actualizar_empleado_inexistente()
    {
        $response = $this->put('/empleados', [
            'Documento_Empleado' => '0000000000',
            'Nombre_Usuario'     => 'No existe',
        ]);

        $response->assertSessionHasErrors('Documento_Empleado');
    }

    #[Test]
    public function no_puede_actualizar_empleado_con_genero_invalido()
    {
        $this->insertarEmpleado($this->documento, 'generomal@update.com', '3100000005');

        $response = $this->put('/empleados', [
            'Documento_Empleado' => $this->documento,
            'Genero'             => 'X',
        ]);

        $response->assertSessionHasErrors('Genero');
    }

    #[Test]
    public function no_puede_actualizar_empleado_con_contrasena_menor_a_4_caracteres()
    {
        $this->insertarEmpleado($this->documento, 'passcorta@update.com', '3100000006');

        $response = $this->put('/empleados', [
            'Documento_Empleado' => $this->documento,
            'Contrasena'         => '123',
        ]);

        $response->assertSessionHasErrors('Contrasena');
    }

    // ===== ELIMINAR =====

    #[Test]
    public function puede_eliminar_empleado()
    {
        $this->insertarEmpleado($this->documento, 'eliminar@test.com', '3200000001');

        DB::table('Contrasenas')->insert([
            'Documento_Empleado' => $this->documento,
            'Contrasena_Hash'    => '1234',
        ]);

        $response = $this->delete('/empleados', [
            'Documento_Empleado' => $this->documento,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('mensaje', 'Empleado eliminado correctamente');

        $this->assertDatabaseMissing('Empleados', [
            'Documento_Empleado' => $this->documento,
        ]);

        // El controlador también elimina la contraseña asociada
        $this->assertDatabaseMissing('Contrasenas', [
            'Documento_Empleado' => $this->documento,
        ]);
    }

    #[Test]
    public function no_puede_eliminar_empleado_inexistente()
    {
        $response = $this->delete('/empleados', [
            'Documento_Empleado' => '0000000000',
        ]);

        $response->assertSessionHasErrors('Documento_Empleado');
    }

    #[Test]
    public function no_puede_eliminar_empleado_sin_documento()
    {
        $response = $this->delete('/empleados', [
            'Documento_Empleado' => '',
        ]);

        $response->assertSessionHasErrors('Documento_Empleado');
    }

    // ===== HELPERS =====

    private function insertarEmpleado(string $documento, string $correo, string $telefono): void
    {
        DB::table('Empleados')->insert([
            'Documento_Empleado' => $documento,
            'Tipo_Documento'     => 'CC',
            'Nombre_Usuario'     => 'Test',
            'Apellido_Usuario'   => 'Test',
            'Edad'               => '25',
            'Correo_Electronico' => $correo,
            'Telefono'           => $telefono,
            'Genero'             => 'M',
            'ID_Estado'          => $this->idEstado,
            'ID_Rol'             => $this->idRol,
            'Fotos'              => $this->fotoPlaceholder,
        ]);
    }
}