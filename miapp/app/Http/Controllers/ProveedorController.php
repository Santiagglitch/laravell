<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
            'Nombre_Proveedor'   => 'required|string|max:45|unique:Proveedores,Nombre_Proveedor',
            'Correo_Electronico' => 'required|string|email|max:30|unique:Proveedores,Correo_Electronico',
            'Telefono'           => 'required|string|max:15|unique:Proveedores,Telefono',
            'ID_Estado'          => 'required|in:1,2,3',
        ], [
            'Nombre_Proveedor.unique' => 'El nombre del proveedor ya está registrado.',
            'Correo_Electronico.unique' => 'El correo electrónico ya está registrado.',
            'Telefono.unique' => 'El teléfono ya está registrado.',
        ]);

        Proveedor::create($validated);

        return redirect()
            ->route('proveedor.index')
            ->with('mensaje', 'Proveedor registrado correctamente.');
    }

    public function put(Request $request)
    {
        $validated = $request->validate([
            'ID_Proveedor'       => 'required|int|exists:Proveedores,ID_Proveedor',
            'Nombre_Proveedor'   => [
                'nullable',
                'string',
                'max:45',
                Rule::unique('Proveedores', 'Nombre_Proveedor')->ignore($request->ID_Proveedor, 'ID_Proveedor')
            ],
            'Correo_Electronico' => [
                'nullable',
                'string',
                'email',
                'max:30',
                Rule::unique('Proveedores', 'Correo_Electronico')->ignore($request->ID_Proveedor, 'ID_Proveedor')
            ],
            'Telefono' => [
                'nullable',
                'string',
                'max:15',
                Rule::unique('Proveedores', 'Telefono')->ignore($request->ID_Proveedor, 'ID_Proveedor')
            ],
            'ID_Estado'          => 'nullable|in:1,2,3',
        ], [
            'Nombre_Proveedor.unique' => 'El nombre del proveedor ya está registrado.',
            'Correo_Electronico.unique' => 'El correo electrónico ya está registrado.',
            'Telefono.unique' => 'El teléfono ya está registrado.',
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
            'ID_Proveedor' => 'required|int|exists:Proveedores,ID_Proveedor',
        ]);

        $proveedor = Proveedor::findOrFail($validated['ID_Proveedor']);
        
        // Verificar si tiene detalles de compras asociados
        $tieneDetallesCompras = \DB::table('detalle_compras')
            ->where('ID_Proveedor', $validated['ID_Proveedor'])
            ->exists();
        
        if ($tieneDetallesCompras) {
            return redirect()
                ->route('proveedor.index')
                ->with('error', 'No se puede eliminar este proveedor porque tiene detalles de compras asociados. Primero elimine los detalles de compras relacionados.');
        }
        
        // Si no hay relaciones, proceder a eliminar
        $proveedor->delete();

        return redirect()
            ->route('proveedor.index')
            ->with('mensaje', 'Proveedor eliminado correctamente.');
    }
}
