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

        // ✅ catálogos dinámicos desde API Spring
        $catalogos  = $this->productosService->obtenerCatalogos();
        $categorias = $catalogos['categorias'] ?? [];
        $estados    = $catalogos['estados'] ?? [];
        $gamas      = $catalogos['gamas'] ?? [];

        if ($productos === null) {
            $mensaje   = 'No se pudieron obtener los productos. Revisa el token o la API.';
            $productos = [];
        } else {
            $mensaje = session('mensaje');
        }

        return view('productos.index', compact('productos', 'mensaje', 'categorias', 'estados', 'gamas'));
    }

    public function post(Request $request)
    {
        // ✅ ID_Producto ya NO se envía (AUTO_INCREMENT)
        $data = $request->validate([
            'Nombre_Producto' => 'required|string|max:50',
            'Descripcion'     => 'nullable|string',
            'Precio_Venta'    => 'nullable|numeric',
            'Stock_Minimo'    => 'nullable|integer',

            'ID_Categoria'    => 'nullable|integer',
            'ID_Estado'       => 'nullable|integer',
            'ID_Gama'         => 'nullable|integer',

            'Fotos'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        // ✅ tipado (recomendado)
        if (isset($data['Precio_Venta']) && $data['Precio_Venta'] !== '') $data['Precio_Venta'] = (float) $data['Precio_Venta'];
        if (isset($data['Stock_Minimo']) && $data['Stock_Minimo'] !== '') $data['Stock_Minimo'] = (int) $data['Stock_Minimo'];

        if (isset($data['ID_Categoria']) && $data['ID_Categoria'] !== '') $data['ID_Categoria'] = (int) $data['ID_Categoria'];
        if (isset($data['ID_Estado'])    && $data['ID_Estado']    !== '') $data['ID_Estado']    = (int) $data['ID_Estado'];
        if (isset($data['ID_Gama'])      && $data['ID_Gama']      !== '') $data['ID_Gama']      = (int) $data['ID_Gama'];

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
        // ✅ ID_Producto es INT
        $request->validate([
            'ID_Producto' => 'required|integer',
            'Fotos'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',

            'Precio_Venta' => 'nullable|numeric',
            'Stock_Minimo' => 'nullable|integer',

            'ID_Categoria' => 'nullable|integer',
            'ID_Estado'    => 'nullable|integer',
            'ID_Gama'      => 'nullable|integer',
        ]);

        $id = (int) $request->input('ID_Producto');

        $data = $request->except(['_token', '_method', 'ID_Producto', 'Fotos']);

        // ✅ limpiar vacíos
        $data = array_filter($data, function ($valor) {
            return $valor !== null && $valor !== '';
        });

        // ✅ tipado (recomendado)
        if (isset($data['Precio_Venta']) && $data['Precio_Venta'] !== '') $data['Precio_Venta'] = (float) $data['Precio_Venta'];
        if (isset($data['Stock_Minimo']) && $data['Stock_Minimo'] !== '') $data['Stock_Minimo'] = (int) $data['Stock_Minimo'];

        if (isset($data['ID_Categoria']) && $data['ID_Categoria'] !== '') $data['ID_Categoria'] = (int) $data['ID_Categoria'];
        if (isset($data['ID_Estado'])    && $data['ID_Estado']    !== '') $data['ID_Estado']    = (int) $data['ID_Estado'];
        if (isset($data['ID_Gama'])      && $data['ID_Gama']      !== '') $data['ID_Gama']      = (int) $data['ID_Gama'];

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
        // ✅ ID_Producto es INT
        $request->validate([
            'ID_Producto' => 'required|integer',
        ]);

        $id = (int) $request->input('ID_Producto');

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
        if (!$productos) $productos = [];

        $catalogos  = $this->productosService->obtenerCatalogos();
        $categorias = $catalogos['categorias'] ?? [];
        $estados    = $catalogos['estados'] ?? [];
        $gamas      = $catalogos['gamas'] ?? [];

        return view('productos.indexEm', compact('productos', 'categorias', 'estados', 'gamas'));
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
