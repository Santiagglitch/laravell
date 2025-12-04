<?php

namespace App\Http\Controllers;

use App\Models\DetalleDevolucion;
use Illuminate\Http\Request;

class DetalleDevolucionController
{
    // LISTAR DETALLES
    public function get()
    {
        $detalles = DetalleDevolucion::all();
        return view('detalle_devoluciones.index', compact('detalles'));
    }

    // GUARDAR DETALLE NUEVO
    public function post(Request $request)
    {
        $validated = $request->validate([
            'ID_DetalleDev'      => 'required|string|unique:detalle_devoluciones,ID_DetalleDev',
            'ID_Devolucion'      => 'required|string|max:20|exists:devoluciones,ID_Devolucion',
            'Cantidad_Devuelta'  => 'required|integer|min:1',
            'ID_Venta'           => 'required|string|max:20|exists:ventas,ID_Venta',
        ]);

        DetalleDevolucion::create($validated);

        return redirect()
            ->route('detalledevolucion.index')
            ->with('mensaje', 'Detalle registrado correctamente.');
    }

    // ACTUALIZAR DETALLE (NO hay PK, se usa combinación de campos)
   
    public function put(Request $request)
{
    $validated = $request->validate([
        'ID_DetalleDev'      => 'required|string|exists:detalle_devoluciones,ID_DetalleDev',
        'ID_Devolucion'      => 'required|string|max:20|exists:devoluciones,ID_Devolucion',
        'Cantidad_Devuelta'  => 'required|integer|min:1',
        'ID_Venta'           => 'required|string|max:20|exists:ventas,ID_Venta',
    ]);

    // Quitar los campos de llave
    $data = $validated;
    unset($data['ID_DetalleDev'], $data['ID_Devolucion'], $data['ID_Venta']);

    // Eliminar valores vacíos
    $data = array_filter($data, fn($v) => !is_null($v) && $v !== '');

    // Hacer UPDATE sin usar Eloquent (para no actualizar toda la tabla)
    \DB::table('detalle_devoluciones')
        ->where('ID_DetalleDev', $validated['ID_DetalleDev'])
        ->where('ID_Devolucion', $validated['ID_Devolucion'])
        ->where('ID_Venta', $validated['ID_Venta'])
        ->update($data);

    return redirect()
        ->route('detalledevolucion.index')
        ->with('mensaje', 'Detalle actualizado correctamente.');
}


  public function delete(Request $request)
{
    $validated = $request->validate([
        'ID_DetalleDev'      => 'required|string|exists:detalle_devoluciones,ID_DetalleDev',
        'ID_Devolucion'      => 'required|string|max:20|exists:devoluciones,ID_Devolucion',
        'ID_Venta'           => 'required|string|max:20|exists:ventas,ID_Venta',
    ]);

    // Eliminar usando Query Builder (elimina aunque no haya primary key)
    \DB::table('detalle_devoluciones')
        ->where('ID_DetalleDev', $validated['ID_DetalleDev'])
        ->where('ID_Devolucion', $validated['ID_Devolucion'])
        ->where('ID_Venta', $validated['ID_Venta'])
        ->delete();

    return redirect()
        ->route('detalledevolucion.index')
        ->with('mensaje', 'Detalle eliminado correctamente.');
}

}