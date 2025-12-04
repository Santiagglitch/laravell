<?php

namespace App\Http\Controllers;

use App\Models\Compras;
use Illuminate\Http\Request;

class ComprasController
{
    public function get()
    {
        $compras = Compras::all();
        return view('compras.index', compact('compras'));
    }

   public function post(Request $request)
{
    $validated = $request->validate([
        'ID_Entrada'        => 'required|string|max:20|unique:compras,ID_Entrada',
        'Precio_Compra'     => 'nullable|numeric',
        'ID_Producto'   => 'required|string|max:20|exists:productos,ID_Producto',
        'Documento_Empleado' => 'required|string|max:20|exists:empleados,Documento_Empleado',
    ]);

    Compras::create($validated);

    return redirect()
        ->route('compras.index')
        ->with('mensaje', 'Compra agregada correctamente.');
}

    public function put(Request $request, $ID_Entrada)
{
    $validated = $request->validate([
        'Precio_Compra'     => 'nullable|numeric',
        'ID_Producto'       => 'nullable|string|max:20|exists:productos,ID_Producto',
        'Documento_Empleado'=> 'nullable|string|max:20|exists:empleados,Documento_Empleado',
    ]);

    $compra = Compras::findOrFail($ID_Entrada);

    $datosActualizar = array_filter($validated, fn($v) => $v !== null && $v !== "");

    if (!empty($datosActualizar)) {
        $compra->update($datosActualizar);
    }

    return redirect()
        ->route('compras.index')
        ->with('mensaje', 'Compra actualizada correctamente.');
}


public function delete($ID_Entrada)
{
    $compra = Compras::findOrFail($ID_Entrada);
    $compra->delete();

    return redirect()
        ->route('compras.index')
        ->with('mensaje', 'Compra eliminada correctamente.');
}

}

