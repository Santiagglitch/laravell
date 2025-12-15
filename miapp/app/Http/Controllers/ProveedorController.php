<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController
{
    public function get()
    {
        $proveedores = Proveedor::all();
        return view('proveedor.index', compact('proveedores'));
    }

    public function post(Request $request)
    {
        $validated = $request->validate([
            'ID_Proveedor'       => 'required|string|max:20|unique:proveedores,ID_Proveedor',
            'Nombre_Proveedor'   => 'required|string|max:45',
            'Correo_Electronico' => 'required|string|email|max:30|unique:proveedores,Correo_Electronico',
            'Telefono'           => 'required|string|max:15',
            'ID_Estado'          => 'required|in:EST001,EST002,EST003',
        ]);

        Proveedor::create($validated);

        return redirect()
            ->route('proveedor.index')
            ->with('mensaje', 'Proveedor registrado correctamente.');
    }

    public function put(Request $request)
    {
        $validated = $request->validate([
            'ID_Proveedor'       => 'required|string|max:20|exists:proveedores,ID_Proveedor',
            'Nombre_Proveedor'   => 'nullable|string|max:45',
            'Correo_Electronico' => 'nullable|string|email|max:30',
            'Telefono'           => 'nullable|string|max:15',
            'ID_Estado'          => 'nullable|in:EST001,EST002,EST003',
        ]);

        $proveedor = Proveedor::findOrFail($validated['ID_Proveedor']);

        $datosActualizar = $validated;
        unset($datosActualizar['ID_Proveedor']);

        $datosActualizar = array_filter(
            $datosActualizar,
            fn($value) => !is_null($value) && $value !== ''
        );

        if (!empty($datosActualizar)) {
            $proveedor->update($datosActualizar);
        }

        return redirect()
            ->route('proveedor.index')
            ->with('mensaje', 'Proveedor actualizado correctamente.');
    }

    public function delete(Request $request)
    {
        $validated = $request->validate([
            'ID_Proveedor' => 'required|string|max:20|exists:proveedores,ID_Proveedor',
        ]);

        $proveedor = Proveedor::findOrFail($validated['ID_Proveedor']);
        $proveedor->delete();

        return redirect()
            ->route('proveedor.index')
            ->with('mensaje', 'Proveedor eliminado correctamente.');
    }
}
