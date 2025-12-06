<?php

namespace App\Http\Controllers;

use App\Models\DetalleVenta;
use Illuminate\Http\Request;

class DetalleVentaController
{
    // LISTAR DETALLES
    public function get()
    {
        $detalles = DetalleVenta::all();
        return view('detalle_ventas.index', compact('detalles'));
    }

    // GUARDAR DETALLE NUEVO
    public function post(Request $request)
    {
        $validated = $request->validate([
            'Cantidad'      => 'required|integer|min:1',
            'Fecha_Salida'  => 'required|date',
            'ID_Producto'   => 'required|string|max:20|exists:productos,ID_Producto',
            'ID_Venta'      => 'required|string|max:20|exists:ventas,ID_Venta',
        ]);

        DetalleVenta::create($validated);

        return redirect()
            ->route('detalleventas.index')
            ->with('mensaje', 'Detalle registrado correctamente.');
    }

    // ACTUALIZAR DETALLE (NO hay PK, se usa combinación de campos)
   
    public function put(Request $request)
{
    $validated = $request->validate([
        'ID_Producto'   => 'required|string|max:20|exists:detalle_ventas,ID_Producto',
        'ID_Venta'      => 'required|string|max:20|exists:detalle_ventas,ID_Venta',
        'Cantidad'      => 'nullable|integer|min:1',
        'Fecha_Salida'  => 'nullable|date',
    ]);

    // Quitar los campos de llave
    $data = $validated;
    unset($data['ID_Producto'], $data['ID_Venta']);

    // Eliminar valores vacíos
    $data = array_filter($data, fn($v) => !is_null($v) && $v !== '');

    // Hacer UPDATE sin usar Eloquent (para no actualizar toda la tabla)
    \DB::table('detalle_ventas')
        ->where('ID_Producto', $validated['ID_Producto'])
        ->where('ID_Venta', $validated['ID_Venta'])
        ->update($data);

    return redirect()
        ->route('detalleventas.index')
        ->with('mensaje', 'Detalle actualizado correctamente.');
}


  public function delete(Request $request)
{
    $validated = $request->validate([
        'ID_Producto' => 'required|string|max:20|exists:detalle_ventas,ID_Producto',
        'ID_Venta'    => 'required|string|max:20|exists:detalle_ventas,ID_Venta',
    ]);

    // Eliminar usando Query Builder (elimina aunque no haya primary key)
    \DB::table('detalle_ventas')
        ->where('ID_Producto', $validated['ID_Producto'])
        ->where('ID_Venta', $validated['ID_Venta'])
        ->delete();

    return redirect()
        ->route('detalleventas.index')
        ->with('mensaje', 'Detalle eliminado correctamente.');
}




public function indexEmpleado()
{
    $detalles = DetalleVenta::all();
    return view('detalle_ventas.indexEm', compact('detalles'));
}
public function storeEmpleado(Request $request)
{
    $this->post($request); 
    return redirect()->route('detalleventas.indexEm')
                     ->with('mensaje', 'Detalle registrado correctamente.');
}

public function updateEmpleado(Request $request)
{
    $this->put($request);
    return redirect()->route('detalleventas.indexEm')
                     ->with('mensaje', 'Detalle actualizado correctamente.');
}

public function destroyEmpleado(Request $request)
{
    $this->delete($request); 
    return redirect()->route('detalleventas.indexEm')
                     ->with('mensaje', 'Detalle eliminado correctamente.');
}


}
