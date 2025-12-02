<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Http\Request;

class VentaController
{
    // LISTAR VENTAS
    public function get()
    {
        // Trae todas las ventas
        $ventas = Venta::all();
        return view('ventas.index', compact('ventas'));
    }

    // GUARDAR NUEVA VENTA
    public function post(Request $request)
    {
        // Validar datos
        $validated = $request->validate([
            'ID_Venta'           => 'required|string|max:20|unique:ventas,ID_Venta',
            'Documento_Cliente'  => 'required|string|max:20|exists:clientes,Documento_Cliente',
            'Documento_Empleado' => 'required|string|max:20|exists:empleados,Documento_Empleado',
        ]);

        // Crear la venta
        Venta::create($validated);

        return redirect()
            ->route('ventas.index')
            ->with('mensaje', 'Venta registrada correctamente.');
    }

    // ACTUALIZAR VENTA
    public function put(Request $request)
    {
        // Validar datos
        $validated = $request->validate([
            'ID_Venta'           => 'required|string|max:20|exists:ventas,ID_Venta',
            'Documento_Cliente'  => 'nullable|string|max:20|exists:clientes,Documento_Cliente',
            'Documento_Empleado' => 'nullable|string|max:20|exists:empleados,Documento_Empleado',
        ]);

        // Buscar la venta por su ID
        $venta = Venta::findOrFail($validated['ID_Venta']);

        // Quitar la PK de los datos a actualizar
        $datosActualizar = $validated;
        unset($datosActualizar['ID_Venta']);

        // Eliminar null o vacío para evitar sobrescribir campos
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

    // ELIMINAR VENTA
    public function delete(Request $request)
    {
        // Validar ID que exista
        $validated = $request->validate([
            'ID_Venta' => 'required|string|max:20|exists:ventas,ID_Venta',
        ]);

        // Buscar y eliminar
        $venta = Venta::findOrFail($validated['ID_Venta']);
        $venta->delete();

        return redirect()
            ->route('ventas.index')
            ->with('mensaje', 'Venta eliminada correctamente.');
    }

    
}
