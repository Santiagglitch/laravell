<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Http\Request;

class VentaController
{
    public function get()
    {
        $ventas = Venta::all();
        return view('ventas.index', compact('ventas'));
    }

    public function post(Request $request)
    {
        $validated = $request->validate([
            'ID_Venta'           => 'required|string|max:20|unique:ventas,ID_Venta',
            'Documento_Cliente'  => 'required|string|max:20|exists:clientes,Documento_Cliente',
            'Documento_Empleado' => 'required|string|max:20|exists:empleados,Documento_Empleado',
        ]);

        Venta::create($validated);

        return redirect()
            ->route('ventas.index')
            ->with('mensaje', 'Venta registrada correctamente.');
    }

    public function put(Request $request)
    {
        $validated = $request->validate([
            'ID_Venta'           => 'required|string|max:20|exists:ventas,ID_Venta',
            'Documento_Cliente'  => 'nullable|string|max:20|exists:clientes,Documento_Cliente',
            'Documento_Empleado' => 'nullable|string|max:20|exists:empleados,Documento_Empleado',
        ]);

        $venta = Venta::findOrFail($validated['ID_Venta']);

        $datosActualizar = $validated;
        unset($datosActualizar['ID_Venta']);

        $datosActualizar = array_filter(
            $datosActualizar,
            fn($value) => !is_null($value) && $value !== ''
        );

        if (!empty($datosActualizar)) {
            $venta->update($datosActualizar);
        }

        return redirect()
            ->route('ventas.index')
            ->with('mensaje', 'Venta actualizada correctamente.');
    }

    public function delete(Request $request)
    {
        $validated = $request->validate([
            'ID_Venta' => 'required|string|max:20|exists:ventas,ID_Venta',
        ]);

        $venta = Venta::findOrFail($validated['ID_Venta']);
        $venta->delete();

        return redirect()
            ->route('ventas.index')
            ->with('mensaje', 'Venta eliminada correctamente.');
    }

    public function indexEmpleado()
    {
        $ventas = Venta::all();
        return view('ventas.indexEm', compact('ventas'));
    }

    public function storeEmpleado(Request $request)
    {
        $this->post($request);
        return redirect()->route('ventas.indexEm')->with('mensaje', 'Venta registrada correctamente.');
    }

    public function updateEmpleado(Request $request)
    {
        $this->put($request);
        return redirect()->route('ventas.indexEm')->with('mensaje', 'Venta actualizada correctamente.');
    }

    public function destroyEmpleado(Request $request)
    {
        $this->delete($request);
        return redirect()->route('ventas.indexEm')->with('mensaje', 'Venta eliminada correctamente.');
    }
}
