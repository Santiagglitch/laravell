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

  
    public function get()
    {
        $productos = $this->productosService->obtenerProductos();

        if ($productos === null) {
            $mensaje   = 'No se pudieron obtener los productos. Revisa el token o la API.';
            $productos = [];
        } else {
            $mensaje = session('mensaje');
        }

        return view('productos.index', compact('productos', 'mensaje'));
    }

    public function post(Request $request)
    {
    
        $data = $request->validate([
            'ID_Producto'     => 'required|string|max:20',
            'Nombre_Producto' => 'required|string|max:50',
            'Descripcion'     => 'nullable|string',
            'Precio_Venta'    => 'nullable|numeric',
            'Stock_Minimo'    => 'nullable|integer',
            'ID_Categoria'    => 'nullable|string|max:20',
            'ID_Estado'       => 'nullable|string|max:20',
            'ID_Gama'         => 'nullable|string|max:20',
            'Fotos'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        if ($request->hasFile('Fotos')) {
            $respuesta = $this->productosService->agregarProductoMultipart($data, $request->file('Fotos'));
        } else {
       
            $data['Fotos'] = ''; 
            $respuesta = $this->productosService->agregarProducto($data);
        }

        $mensaje = $respuesta['success']
            ? 'Producto agregado correctamente.'
            : 'Error al agregar el producto.';

        return redirect()
            ->route('productos.index')
            ->with('mensaje', $mensaje);
    }

    public function put(Request $request)
    {
        $request->validate([
            'ID_Producto' => 'required|string|max:20',
            'Fotos'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $id = $request->input('ID_Producto');

        $data = $request->except(['_token', '_method', 'ID_Producto', 'Fotos']);

   
        $data = array_filter($data, function ($valor) {
            return $valor !== null && $valor !== '';
        });

       
        if ($request->hasFile('Fotos')) {
            $respuesta = $this->productosService->actualizarProductoMultipart($id, $data, $request->file('Fotos'));
        } else {
            
            $respuesta = $this->productosService->actualizarProducto($id, $data);
        }

        $mensaje = $respuesta['success']
            ? 'Producto actualizado correctamente.'
            : 'Error al actualizar el producto.';

        return redirect()
            ->route('productos.index')
            ->with('mensaje', $mensaje);
    }

   
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