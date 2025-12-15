<?php

namespace App\Http\Controllers;

use App\Models\Detalle_Compras;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetalleComprasController
{
    public function get()
    {
        $detalles = Detalle_Compras::all();
        return view('detalle_compras.index', compact('detalles'));
    }

    public function post(Request $request)
    {
        $validated = $request->validate([
            'Fecha_Entrada' => 'required|date',
            'Cantidad'      => 'required|integer|min:1',
            'ID_Proveedor'  => 'required|string|max:20|exists:proveedores,ID_Proveedor',
            'ID_Entrada'    => 'required|string|max:20|exists:compras,ID_Entrada',
        ]);

        Detalle_Compras::create($validated);

        return redirect()
            ->route('detallecompras.index')
            ->with('mensaje', 'Detalle de compra registrado correctamente.');
    }

    public function put(Request $request)
    {
        $validated = $request->validate([
            'ID_Proveedor'  => 'required|string|max:20|exists:detalle_compras,ID_Proveedor',
            'ID_Entrada'    => 'required|string|max:20|exists:detalle_compras,ID_Entrada',
            'Fecha_Entrada' => 'nullable|date',
            'Cantidad'      => 'nullable|integer|min:1',
        ]);

        $data = $validated;
        unset($data['ID_Proveedor'], $data['ID_Entrada']);

        $data = array_filter($data, fn($v) => !is_null($v) && $v !== '');

        DB::table('detalle_compras')
            ->where('ID_Proveedor', $validated['ID_Proveedor'])
            ->where('ID_Entrada', $validated['ID_Entrada'])
            ->update($data);

        return redirect()
            ->route('detallecompras.index')
            ->with('mensaje', 'Detalle de compra actualizado correctamente.');
    }

    public function delete(Request $request)
    {
        $validated = $request->validate([
            'ID_Proveedor' => 'required|string|max:20|exists:detalle_compras,ID_Proveedor',
            'ID_Entrada'   => 'required|string|max:20|exists:detalle_compras,ID_Entrada',
        ]);

        DB::table('detalle_compras')
            ->where('ID_Proveedor', $validated['ID_Proveedor'])
            ->where('ID_Entrada', $validated['ID_Entrada'])
            ->delete();

        return redirect()
            ->route('detallecompras.index')
            ->with('mensaje', 'Detalle de compra eliminado correctamente.');
    }
}
