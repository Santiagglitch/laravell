<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\ProductosService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class ProductoTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        session([
            'jwt_token' => 'fake-token',
            'nombre' => 'Kevin',
            'rol' => 1,
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function mockService($methods)
    {
        $mock = Mockery::mock(ProductosService::class)->makePartial();

        foreach ($methods as $method => $response) {
            $mock->shouldReceive($method)->andReturn($response);
        }

        $this->app->bind(ProductosService::class, fn() => $mock);

        return $mock;
    }

    #[Test]
    public function puede_listar_productos()
    {
        $mock = Mockery::mock(ProductosService::class)->makePartial();

        $mock->shouldReceive('obtenerProductos')->once()->andReturn([
            [
                'ID_Producto' => 1,
                'Nombre_Producto' => 'Mouse',
                'Descripcion' => 'Mouse gamer', // ✅ FIX
                'Precio_Venta' => 20000,
                'Stock_Minimo' => 5,
                'ID_Categoria' => 1,
                'ID_Estado' => 1,
                'ID_Gama' => 1,
                'Categoria' => 'Tecnología',
                'Estado' => 'Activo',
                'Gama' => 'Alta',
                'Fotos' => '' // ✅ FIX
            ]
        ]);

        $mock->shouldReceive('obtenerCatalogos')->once()->andReturn([
            'categorias' => [],
            'estados' => [],
            'gamas' => [],
        ]);

        $this->app->bind(ProductosService::class, fn() => $mock);

        $response = $this->get('/productos');

        $response->assertStatus(200);
        $response->assertViewIs('productos.index');
        $response->assertViewHas('productos');
    }

    #[Test]
    public function puede_crear_producto_con_todos_los_campos()
    {
        $mock = Mockery::mock(ProductosService::class)->makePartial();

        $mock->shouldReceive('agregarProducto')->once()->andReturn([
            'success' => true
        ]);

        $this->app->bind(ProductosService::class, fn() => $mock);

        $response = $this->post('/productos', [
            'Nombre_Producto' => 'Teclado',
            'Descripcion' => 'Teclado gamer',
            'Precio_Venta' => 50000,
            'Stock_Minimo' => 5,
            'ID_Categoria' => 1,
            'ID_Estado' => 1,
            'ID_Gama' => 1,
        ]);

        $response->assertRedirect(route('productos.index'));
        $response->assertSessionHas('mensaje', 'Producto agregado correctamente.');
    }

    #[Test]
    public function error_al_crear_producto()
    {
        $mock = Mockery::mock(ProductosService::class)->makePartial();

        $mock->shouldReceive('agregarProducto')->once()->andReturn([
            'success' => false
        ]);

        $this->app->bind(ProductosService::class, fn() => $mock);

        $response = $this->post('/productos', [
            'Nombre_Producto' => 'Teclado',
            'Descripcion' => 'Desc',
            'Precio_Venta' => 1000,
            'Stock_Minimo' => 2,
            'ID_Categoria' => 1,
            'ID_Estado' => 1,
            'ID_Gama' => 1,
        ]);

        $response->assertRedirect(route('productos.index'));
        $response->assertSessionHas('mensaje', 'Error al agregar el producto.');
    }

    #[Test]
    public function puede_actualizar_producto()
    {
        $mock = Mockery::mock(ProductosService::class)->makePartial();

        $mock->shouldReceive('actualizarProducto')->once()->andReturn([
            'success' => true
        ]);

        $this->app->bind(ProductosService::class, fn() => $mock);

        $response = $this->put('/productos', [
            'ID_Producto' => 1,
            'Nombre_Producto' => 'Mouse Pro',
            'Precio_Venta' => 30000
        ]);

        $response->assertRedirect(route('productos.index'));
        $response->assertSessionHas('mensaje', 'Producto actualizado correctamente.');
    }

    #[Test]
    public function error_al_actualizar_producto()
    {
        $mock = Mockery::mock(ProductosService::class)->makePartial();

        $mock->shouldReceive('actualizarProducto')->once()->andReturn([
            'success' => false
        ]);

        $this->app->bind(ProductosService::class, fn() => $mock);

        $response = $this->put('/productos', [
            'ID_Producto' => 1,
        ]);

        $response->assertRedirect(route('productos.index'));
        $response->assertSessionHas('mensaje', 'Error al actualizar el producto.');
    }

    #[Test]
    public function puede_eliminar_producto()
    {
        $mock = Mockery::mock(ProductosService::class)->makePartial();

        $mock->shouldReceive('eliminarProducto')->once()->andReturn([
            'success' => true
        ]);

        $this->app->bind(ProductosService::class, fn() => $mock);

        $response = $this->delete('/productos', [
            'ID_Producto' => 1,
        ]);

        $response->assertRedirect(route('productos.index'));
        $response->assertSessionHas('mensaje', 'Producto eliminado correctamente.');
    }

    #[Test]
    public function error_al_eliminar_producto()
    {
        $mock = Mockery::mock(ProductosService::class)->makePartial();

        $mock->shouldReceive('eliminarProducto')->once()->andReturn([
            'success' => false
        ]);

        $this->app->bind(ProductosService::class, fn() => $mock);

        $response = $this->delete('/productos', [
            'ID_Producto' => 1,
        ]);

        $response->assertRedirect(route('productos.index'));
        $response->assertSessionHas('mensaje', 'Error al eliminar el producto.');
    }

    #[Test]
    public function validacion_campos_obligatorios_producto()
    {
        $response = $this->post('/productos', []);

        $response->assertSessionHasErrors([
            'Nombre_Producto',
            'Descripcion',
            'Precio_Venta',
            'Stock_Minimo',
            'ID_Categoria',
            'ID_Estado',
            'ID_Gama',
        ]);
    }

    #[Test]
    public function precio_debe_ser_numerico()
    {
        $response = $this->post('/productos', [
            'Nombre_Producto' => 'Producto',
            'Descripcion' => 'Desc',
            'Precio_Venta' => 'texto',
            'Stock_Minimo' => 5,
            'ID_Categoria' => 1,
            'ID_Estado' => 1,
            'ID_Gama' => 1,
        ]);

        $response->assertSessionHasErrors('Precio_Venta');
    }

    #[Test]
    public function stock_debe_ser_entero()
    {
        $response = $this->post('/productos', [
            'Nombre_Producto' => 'Producto',
            'Descripcion' => 'Desc',
            'Precio_Venta' => 1000,
            'Stock_Minimo' => 'abc',
            'ID_Categoria' => 1,
            'ID_Estado' => 1,
            'ID_Gama' => 1,
        ]);

        $response->assertSessionHasErrors('Stock_Minimo');
    }

    #[Test]
    public function nombre_producto_no_supera_50_caracteres()
    {
        $response = $this->post('/productos', [
            'Nombre_Producto' => str_repeat('A', 60),
            'Descripcion' => 'Desc',
            'Precio_Venta' => 1000,
            'Stock_Minimo' => 5,
            'ID_Categoria' => 1,
            'ID_Estado' => 1,
            'ID_Gama' => 1,
        ]);

        $response->assertSessionHasErrors('Nombre_Producto');
    }
}