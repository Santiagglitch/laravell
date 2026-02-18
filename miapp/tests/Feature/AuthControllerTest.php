<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Evita llamadas HTTP reales
        Http::preventStrayRequests();
        Http::fake(['*' => Http::response([], 200)]);

        // Tablas mínimas para pruebas
        DB::statement('
            CREATE TABLE IF NOT EXISTS Empleados (
                Documento_Empleado VARCHAR(30) PRIMARY KEY,
                Nombre_Usuario VARCHAR(80) NOT NULL,
                ID_Rol INT NOT NULL,
                Fotos VARCHAR(255) NULL
            )
        ');

        DB::statement('
            CREATE TABLE IF NOT EXISTS Contrasenas (
                ID_Contrasena INT AUTO_INCREMENT PRIMARY KEY,
                Documento_Empleado VARCHAR(30) NOT NULL,
                Contrasena_Hash VARCHAR(64) NOT NULL
            )
        ');
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
        $hashClave = hash('sha256', $clave);

        DB::table('Empleados')->insert([
            'Documento_Empleado' => $documento,
            'Nombre_Usuario'     => 'Kata',
            'ID_Rol'             => 2,
            'Fotos'              => null,
        ]);

        DB::table('Contrasenas')->insert([
            'Documento_Empleado' => $documento,
            'Contrasena_Hash'    => $hashClave,
        ]);

        $response = $this->post('/login', [
            'usuario'    => $documento,
            'contrasena' => $clave,
        ]);

        $response->assertRedirect(route('InicioE.index'));

        $response->assertSessionHas('documento', $documento);
        $response->assertSessionHas('nombre', 'Kata');
        $response->assertSessionHas('rol', 2);

        // ✅ NO validamos jwt_token aquí (eso es integración con API externa)
        $response->assertSessionMissing('jwt_token');
    }

    public function test_login_exitoso_con_rol_distinto_redirige_a_admin()
    {
        $documento = '2002';
        $clave     = 'abcd';
        $hashClave = hash('sha256', $clave);

        DB::table('Empleados')->insert([
            'Documento_Empleado' => $documento,
            'Nombre_Usuario'     => 'Admin',
            'ID_Rol'             => 1,
            'Fotos'              => null,
        ]);

        DB::table('Contrasenas')->insert([
            'Documento_Empleado' => $documento,
            'Contrasena_Hash'    => $hashClave,
        ]);

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
        $hashClave = hash('sha256', $clave);

        DB::table('Empleados')->insert([
            'Documento_Empleado' => $documento,
            'Nombre_Usuario'     => 'FotoUser',
            'ID_Rol'             => 2,
            'Fotos'              => 'http://site.com/foto.jpg',
        ]);

        DB::table('Contrasenas')->insert([
            'Documento_Empleado' => $documento,
            'Contrasena_Hash'    => $hashClave,
        ]);

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
        $hashClave = hash('sha256', $clave);

        DB::table('Empleados')->insert([
            'Documento_Empleado' => $documento,
            'Nombre_Usuario'     => 'FotoUser2',
            'ID_Rol'             => 2,
            'Fotos'              => 'uploads/foto.png',
        ]);

        DB::table('Contrasenas')->insert([
            'Documento_Empleado' => $documento,
            'Contrasena_Hash'    => $hashClave,
        ]);

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
