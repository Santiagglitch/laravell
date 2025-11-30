<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index()
    {
        // Trae todos los proveedores
        $proveedores = Proveedor::all();
        return view('proveedor.index', compact('proveedores'));
    }

    // GUARDAR PROVEEDOR NUEVO
    public function store(Request $request)
    {
        // Validar datos del formulario
        $validated = $request->validate([
            'ID_Proveedor'      => 'required|string|max:20|unique:proveedores,ID_Proveedor',
            'Nombre_Proveedor'  => 'required|string|max:45',
            'Correo_Electronico'=> 'required|string|email|max:30|unique:proveedores,Correo_Electronico',
            'Telefono'          => 'required|string|max:15',
            'ID_Estado'         => 'required|in:EST001,EST002,EST003',
        ]);

        // Crear proveedor en la BD
        Proveedor::create($validated);

        return redirect()
            ->route('proveedor.index')
            ->with('mensaje', 'Proveedor registrado correctamente.');
    }

    // ACTUALIZAR PROVEEDOR
    public function update(Request $request)
    {
        // Validación
        $validated = $request->validate([
            'ID_Proveedor'      => 'required|string|max:20|exists:proveedores,ID_Proveedor',
            'Nombre_Proveedor'  => 'nullable|string|max:45',
            'Correo_Electronico'=> 'nullable|string|email|max:30',
            'Telefono'          => 'nullable|string|max:15',
            'ID_Estado'         => 'nullable|in:EST001,EST002,EST003',
        ]);

        // Buscar proveedor por PK
        $proveedor = Proveedor::findOrFail($validated['ID_Proveedor']);

        // Preparar los datos a actualizar (sin la PK)
        $datosActualizar = $validated;
        unset($datosActualizar['ID_Proveedor']);

        // Evitar actualizar con valores nulos o vacíos
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

    // ELIMINAR PROVEEDOR
    public function destroy(Request $request)
    {
        // Validar PK
        $validated = $request->validate([
            'ID_Proveedor' => 'required|string|max:20|exists:proveedores,ID_Proveedor',
        ]);

        // Buscar y eliminar
        $proveedor = Proveedor::findOrFail($validated['ID_Proveedor']);
        $proveedor->delete();

        return redirect()
            ->route('proveedor.index')
            ->with('mensaje', 'Proveedor eliminado correctamente.');
    }
}