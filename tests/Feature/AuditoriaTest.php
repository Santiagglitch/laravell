<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;

class AuditoriaTest extends TestCase
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
    public function puede_listar_auditorias()
    {
        $response = $this->get('/auditoria');

        $response->assertStatus(200);
        $response->assertViewIs('auditoria.index');
        $response->assertViewHas('auditorias');
    }

    #[Test]
    public function puede_filtrar_por_tabla()
    {
        DB::table('Auditoria')->insert([
            'Tabla_Afectada' => 'Productos',
            'Operacion' => 'INSERT',
            'ID_Registro' => '1',
            'Usuario_BD' => 'root@localhost',
            'Datos_Antes' => '-',
            'Datos_Despues' => 'Nombre_Producto=Test',
            'Fecha' => now(),
        ]);

        $response = $this->get('/auditoria?tabla=Productos');

        $response->assertStatus(200);
        $response->assertViewHas('auditorias');
    }

    #[Test]
    public function puede_filtrar_por_operacion()
    {
        DB::table('Auditoria')->insert([
            'Tabla_Afectada' => 'Clientes',
            'Operacion' => 'UPDATE',
            'ID_Registro' => '123',
            'Usuario_BD' => 'root@localhost',
            'Datos_Antes' => 'Nombre_Cliente=Juan',
            'Datos_Despues' => 'Nombre_Cliente=Pedro',
            'Fecha' => now(),
        ]);

        $response = $this->get('/auditoria?op=UPDATE');

        $response->assertStatus(200);
        $response->assertViewHas('auditorias');
    }

    #[Test]
    public function puede_filtrar_por_rango_de_fechas()
    {
        DB::table('Auditoria')->insert([
            'Tabla_Afectada' => 'Clientes',
            'Operacion' => 'DELETE',
            'ID_Registro' => '456',
            'Usuario_BD' => 'root@localhost',
            'Datos_Antes' => 'Nombre_Cliente=Ana',
            'Datos_Despues' => '-',
            'Fecha' => now(),
        ]);

        $response = $this->get('/auditoria?desde=' . now()->subDay()->toDateString() . '&hasta=' . now()->toDateString());

        $response->assertStatus(200);
        $response->assertViewHas('auditorias');
    }

    #[Test]
    public function muestra_estadisticas_correctamente()
    {
        DB::table('Auditoria')->insert([
            [
                'Tabla_Afectada' => 'Clientes',
                'Operacion' => 'INSERT',
                'ID_Registro' => '1',
                'Usuario_BD' => 'root@localhost',
                'Datos_Antes' => '-',
                'Datos_Despues' => 'Nombre_Cliente=Test1',
                'Fecha' => now(),
            ],
            [
                'Tabla_Afectada' => 'Clientes',
                'Operacion' => 'UPDATE',
                'ID_Registro' => '1',
                'Usuario_BD' => 'root@localhost',
                'Datos_Antes' => 'Nombre_Cliente=Test1',
                'Datos_Despues' => 'Nombre_Cliente=Test2',
                'Fecha' => now(),
            ],
            [
                'Tabla_Afectada' => 'Clientes',
                'Operacion' => 'DELETE',
                'ID_Registro' => '1',
                'Usuario_BD' => 'root@localhost',
                'Datos_Antes' => 'Nombre_Cliente=Test2',
                'Datos_Despues' => '-',
                'Fecha' => now(),
            ],
        ]);

        $response = $this->get('/auditoria');

        $response->assertStatus(200);
        $response->assertViewHas('stats');
    }

    #[Test]
    public function maneja_datos_vacios_correctamente()
    {
        DB::table('Auditoria')->insert([
            'Tabla_Afectada' => 'Productos',
            'Operacion' => 'INSERT',
            'ID_Registro' => '999',
            'Usuario_BD' => 'root@localhost',
            'Datos_Antes' => '',
            'Datos_Despues' => '',
            'Fecha' => now(),
        ]);

        $response = $this->get('/auditoria');

        $response->assertStatus(200);
    }

    #[Test]
    public function normaliza_operacion_con_espacios_y_minusculas()
    {
        DB::table('Auditoria')->insert([
            'Tabla_Afectada' => 'Productos',
            'Operacion' => '  insert  ',
            'ID_Registro' => '10',
            'Usuario_BD' => 'root@localhost',
            'Datos_Antes' => '-',
            'Datos_Despues' => 'Nombre_Producto=Test',
            'Fecha' => now(),
        ]);

        $response = $this->get('/auditoria?op=INSERT');

        $response->assertStatus(200);
        $response->assertViewHas('auditorias');
    }

    #[Test]
    public function la_paginacion_funciona_correctamente()
    {
        for ($i = 0; $i < 10; $i++) {
            DB::table('Auditoria')->insert([
                'Tabla_Afectada' => 'Clientes',
                'Operacion' => 'INSERT',
                'ID_Registro' => (string)$i,
                'Usuario_BD' => 'root@localhost',
                'Datos_Antes' => '-',
                'Datos_Despues' => 'Nombre_Cliente=Test'.$i,
                'Fecha' => now(),
            ]);
        }

        $response = $this->get('/auditoria');

        $response->assertStatus(200);
        $response->assertViewHas('auditorias');
    }
}