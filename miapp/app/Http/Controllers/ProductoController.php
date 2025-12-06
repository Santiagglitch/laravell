<?php

namespace App\Http\Controllers;

use App\Services\ProductosService;
use Illuminate\Http\Request;

class ProductoController
{
    private ProductosService $productosService;

    public function __construct(ProductosService $productosService)
    {
        $this->productosService = $productosService;
    }

    // ==========================
    // LISTAR PRODUCTOS
    // ==========================
    public function get()
    {
        $productos = $this->productosService->obtenerProductos();

        if ($productos === null) {
            $mensaje   = 'No se pudieron obtener los productos. Revisa el token o la API.';
            $productos = [];
        } else {
            $mensaje = session('mensaje'); // por si venimos de store/update/destroy
        }

        return view('productos.index', compact('productos', 'mensaje'));
    }

    // ==========================
    // GUARDAR (POST) - productos.store
    // ==========================
    public function post(Request $request)
    {
        // Validar datos que vienen del formulario "Añadir Producto"
        $data = $request->validate([
            'ID_Producto'     => 'required|string|max:20',
            'Nombre_Producto' => 'required|string|max:50',
            'Descripcion'     => 'nullable|string',
            'Precio_Venta'    => 'nullable|numeric',
            'Stock_Minimo'    => 'nullable|integer',
            'ID_Categoria'    => 'nullable|string|max:20',
            'ID_Estado'       => 'nullable|string|max:20',
            'ID_Gama'         => 'nullable|string|max:20',
            'Fotos'           => 'nullable|string|max:255',
        ]);

        // Llamar a la API a través del service
        $respuesta = $this->productosService->agregarProducto($data);

        $mensaje = $respuesta['success']
            ? 'Producto agregado correctamente.'
            : 'Error al agregar el producto.';

        return redirect()
            ->route('productos.index')
            ->with('mensaje', $mensaje);
    }

    // ==========================
    // ACTUALIZAR (PUT) - productos.update
    // ==========================
    public function put(Request $request)
    {
        // Validamos que venga el ID del producto
        $request->validate([
            'ID_Producto' => 'required|string|max:20',
        ]);

        $id = $request->input('ID_Producto');

        // Tomamos todos los campos excepto los de control
        $data = $request->except(['_token', '_method', 'ID_Producto']);

        // Quitamos los campos vacíos para no sobrescribir con ""
        $data = array_filter($data, function ($valor) {
            return $valor !== null && $valor !== '';
        });

        $respuesta = $this->productosService->actualizarProducto($id, $data);

        $mensaje = $respuesta['success']
            ? 'Producto actualizado correctamente.'
            : 'Error al actualizar el producto.';

        return redirect()
            ->route('productos.index')
            ->with('mensaje', $mensaje);
    }

    // ==========================
    // ELIMINAR (DELETE) - productos.destroy
    // ==========================
    public function delete(Request $request)
    {
        $request->validate([
            'ID_Producto' => 'required|string|max:20',
        ]);

        $id = $request->input('ID_Producto');

        $respuesta = $this->productosService->eliminarProducto($id);

        $mensaje = $respuesta['success']
            ? 'Producto eliminado correctamente.'
            : 'Error al eliminar el producto.';

        return redirect()
            ->route('productos.index')
            ->with('mensaje', $mensaje);
    }




public function indexEmpleado()
{
    $productos = $this->productosService->obtenerProductos();
    if (!$productos) {
        $productos = [];
    }
    return view('productos.indexEm', compact('productos'));
}


public function storeEmpleado(Request $request)
{
    $this->post($request); 
    return redirect()->route('productos.indexEm')->with('mensaje', 'Producto creado correctamente.');
}


public function updateEmpleado(Request $request)
{
    $this->put($request); 
    return redirect()->route('productos.indexEm')->with('mensaje', 'Producto actualizado correctamente.');
}


public function destroyEmpleado(Request $request)
{
    $this->delete($request); 
    return redirect()->route('productos.indexEm')->with('mensaje', 'Producto eliminado correctamente.');
}


}

