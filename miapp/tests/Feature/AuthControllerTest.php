<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class AuthControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // Evita llamadas HTTP reales
        Http::preventStrayRequests();
        Http::fake(['*' => Http::response([], 200)]);

        // Asegura FKs mínimas (Estados/Roles) SIN borrar tablas
        $this->seedLookups();
    }

    private function seedLookups(): void
    {
        // Estados (IDs 1..)
        DB::table('Estados')->updateOrInsert(
            ['ID_Estado' => 1],
            ['Nombre_Estado' => 'Activo']
        );

        DB::table('Estados')->updateOrInsert(
            ['ID_Estado' => 2],
            ['Nombre_Estado' => 'Inactivo']
        );

        // Roles (IDs 1..)
        DB::table('Roles')->updateOrInsert(
            ['ID_Rol' => 1],
            ['Nombre' => 'Administrador']
        );

        DB::table('Roles')->updateOrInsert(
            ['ID_Rol' => 2],
            ['Nombre' => 'Empleado']
        );
    }

    private function insertEmpleado(array $overrides = []): array
    {
        $base = [
            'Documento_Empleado' => '9999999999',
            'Tipo_Documento'     => 'CC',
            'Nombre_Usuario'     => 'Test',
            'Apellido_Usuario'   => 'User',
            'Edad'               => '20',
            'Correo_Electronico' => 'test' . uniqid() . '@mail.com',
            'Telefono'           => '3000000000',
            'Genero'             => 'M',
            'ID_Estado'          => 1,
            'ID_Rol'             => 2,
            'Fotos'              => 'fotos', // NOT NULL en tu BD
        ];

        $data = array_merge($base, $overrides);
        DB::table('Empleados')->insert($data);

        return $data;
    }

    private function insertContrasena(string $documento, string $contrasenaPlana): void
    {
        // IMPORTANTE: en tu BD hay trigger que hace SHA2( ,256) antes de insertar,
        // así que aquí insertamos la contraseña PLANA.
        DB::table('Contrasenas')->insert([
            'Documento_Empleado' => $documento,
            'Contrasena_Hash'    => $contrasenaPlana,
        ]);
    }

    public function test_login_falla_si_faltan_campos()
    {
        $response = $this->from('/login')->post('/login', []);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['usuario', 'contrasena']);
    }

    public function test_login_falla_si_credenciales_son_incorrectas()
    {
        $response = $this->from('/login')->post('/login', [
            'usuario' => '123',
            'contrasena' => 'mala',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['login']);
        $this->assertGuestLike();
    }

    public function test_login_exitoso_setea_session_y_redirige_a_inicio_empleado_rol_2()
    {
        $documento = '1001';
        $clave     = '1234';

        $this->insertEmpleado([
            'Documento_Empleado' => $documento,
            'Nombre_Usuario'     => 'Kata',
            'Apellido_Usuario'   => 'Test',
            'ID_Rol'             => 2,
            'Fotos'              => 'fotos',
        ]);

        $this->insertContrasena($documento, $clave);

        $response = $this->post('/login', [
            'usuario'    => $documento,
            'contrasena' => $clave,
        ]);

        $response->assertRedirect(route('InicioE.index'));

        $response->assertSessionHas('documento', $documento);
        $response->assertSessionHas('nombre', 'Kata');
        $response->assertSessionHas('rol', 2);

        $response->assertSessionMissing('jwt_token');
    }

    public function test_login_exitoso_con_rol_distinto_redirige_a_admin()
    {
        $documento = '2002';
        $clave     = 'abcd';

        $this->insertEmpleado([
            'Documento_Empleado' => $documento,
            'Nombre_Usuario'     => 'Admin',
            'Apellido_Usuario'   => 'Test',
            'ID_Rol'             => 1,
            'Fotos'              => 'fotos',
        ]);

        $this->insertContrasena($documento, $clave);

        $response = $this->post('/login', [
            'usuario'    => $documento,
            'contrasena' => $clave,
        ]);

        $response->assertRedirect(route('admin.inicio'));
    }

    public function test_login_setea_foto_si_es_url_http()
    {
        $documento = '3003';
        $clave     = 'pass';

        $this->insertEmpleado([
            'Documento_Empleado' => $documento,
            'Nombre_Usuario'     => 'FotoUser',
            'Apellido_Usuario'   => 'Test',
            'ID_Rol'             => 2,
            'Fotos'              => 'http://site.com/foto.jpg',
        ]);

        $this->insertContrasena($documento, $clave);

        $response = $this->post('/login', [
            'usuario'    => $documento,
            'contrasena' => $clave,
        ]);

        $response->assertSessionHas('foto', 'http://site.com/foto.jpg');
    }

    public function test_login_setea_foto_si_empieza_con_uploads_con_base_spring()
    {
        config()->set('services.spring.base_url', 'http://192.168.80.18:8080');

        $documento = '4004';
        $clave     = 'pass';

        $this->insertEmpleado([
            'Documento_Empleado' => $documento,
            'Nombre_Usuario'     => 'FotoUser2',
            'Apellido_Usuario'   => 'Test',
            'ID_Rol'             => 2,
            'Fotos'              => 'uploads/foto.png',
        ]);

        $this->insertContrasena($documento, $clave);

        $response = $this->post('/login', [
            'usuario'    => $documento,
            'contrasena' => $clave,
        ]);

        $response->assertSessionHas('foto', 'http://192.168.80.18:8080/uploads/foto.png');
    }

    public function test_logout_limpia_session_y_redirige_a_inicio()
    {
        Session::put('documento', '999');
        Session::put('nombre', 'X');
        Session::put('rol', 1);
        Session::put('foto', 'x');
        Session::put('jwt_token', 't');
        Session::put('rol_api', 'r');

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('inicio'));
        $response->assertSessionMissing('documento');
        $response->assertSessionMissing('nombre');
        $response->assertSessionMissing('rol');
        $response->assertSessionMissing('foto');
        $response->assertSessionMissing('jwt_token');
        $response->assertSessionMissing('rol_api');
    }

    private function assertGuestLike(): void
    {
        $this->assertFalse(Session::has('documento'));
        $this->assertFalse(Session::has('rol'));
    }
}