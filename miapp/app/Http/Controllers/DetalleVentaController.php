<?php

namespace App\Http\Controllers;

use App\Models\DetalleVenta;
use Illuminate\Http\Request;

class DetalleVentaController
{
    public function get()
    {
        $detalles = DetalleVenta::all();
        return view('detalle_ventas.index', compact('detalles'));
    }

    public function post(Request $request)
    {
        $validated = $request->validate([
            'Cantidad'     => 'required|integer|min:1',
            'Fecha_Salida' => 'required|date',
            'ID_Producto'  => 'required|string|max:20|exists:productos,ID_Producto',
            'ID_Venta'     => 'required|string|max:20|exists:ventas,ID_Venta',
        ]);

        DetalleVenta::create($validated);

        return redirect()
            ->route('detalleventas.index')
            ->with('mensaje', 'Detalle registrado correctamente.');
    }

    public function put(Request $request)
    {
        $validated = $request->validate([
            'ID_Producto'  => 'required|string|max:20|exists:detalle_ventas,ID_Producto',
            'ID_Venta'     => 'required|string|max:20|exists:detalle_ventas,ID_Venta',
            'Cantidad'     => 'nullable|integer|min:1',
            'Fecha_Salida' => 'nullable|date',
        ]);

        $data = $validated;
        unset($data['ID_Producto'], $data['ID_Venta']);

        $data = array_filter($data, fn($v) => !is_null($v) && $v !== '');

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
