<?php

namespace App\Http\Controllers;

use App\Models\Devolucion;
use Illuminate\Http\Request;

class DevolucionController
{
    // LISTAR DEVOLUCIONES
    public function get()
    {
        // Trae todas las DEVOLUCIONES
        $devolucion = Devolucion::all();
        return view('devolucion.index', compact('devolucion'));
    }

    // GUARDAR
    public function post(Request $request)
    {
        // Validar datos
        $validated = $request->validate([
            'ID_Devolucion'     => 'required|string|max:20|unique:devoluciones,ID_Devolucion',
            'Fecha_Devolucion'  => 'required|date|max:20',
            'Motivo'            => 'required|string|max:45',
        ]);

        // Crear
        Devolucion::create($validated);

        return redirect()
            ->route('devolucion.index')
            ->with('mensaje', 'Devolución registrada correctamente.');
    }

    // ACTUALIZAR
    public function put(Request $request)
    {
        // Validar datos
        $validated = $request->validate([
            'ID_Devolucion'     => 'required|string|max:20|exists:devoluciones,ID_Devolucion',
            'Fecha_Devolucion'  => 'required|date|max:20',
            'Motivo'            => 'required|string|max:45',
        ]);

        // Buscar la Devolucion por su ID
        $devolucion = Devolucion::findOrFail($validated['ID_Devolucion']);

        // Quitar la PK de los datos a actualizar
        $datosActualizar = $validated;
        unset($datosActualizar['ID_Devolucion']);

        // Eliminar null o vacío para evitar sobrescribir campos
        $datosActualizar = array_filter(
            $datosActualizar,
            fn($value) => !is_null($value) && $value !== ''
        );

        if (!empty($datosActualizar)) {
            $devolucion->update($datosActualizar);
        }

        return redirect()
            ->route('devolucion.index')
            ->with('mensaje', 'Devolución actualizada correctamente.');
    }

    // ELIMINAR VENTA
    public function delete(Request $request)
    {
        // Validar ID que exista
        $validated = $request->validate([
            'ID_Devolucion'     => 'required|string|max:20|exists:devoluciones,ID_Devolucion',
        ]);

        // Buscar y eliminar
        $venta = Devolucion::findOrFail($validated['ID_Devolucion']);
        $venta->delete();

        return redirect()
            ->route('devolucion.index')
            ->with('mensaje', 'Devolución eliminada correctamente.');
    }

    
}
