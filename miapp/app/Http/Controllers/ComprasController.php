<?php

namespace App\Http\Controllers;

use App\Models\Compras;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class ComprasController extends BaseController
{
    public function get()
    {
        $compras = Compras::with(['empleado'])->get();
        
        // Obtener productos desde la tabla
        $productos = \DB::table('productos')->get();
        
        return view('compras.index', compact('compras', 'productos'));
    }

    public function post(Request $request)
    {
        $validated = $request->validate([
            'Precio_Compra'      => 'nullable|numeric',
            'ID_Producto'        => 'required|exists:productos,ID_Producto',
            'Documento_Empleado' => 'required|exists:empleados,Documento_Empleado',
        ]);

        Compras::create($validated);

        return redirect()
            ->route('compras.index')
            ->with('mensaje', 'Compra agregada correctamente.');
    }

    public function put(Request $request, $ID_Entrada)
    {
        $validated = $request->validate([
            'Precio_Compra' => 'nullable|numeric',
            'ID_Producto'   => 'nullable|exists:productos,ID_Producto',
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

    public function getDetalles($ID_Entrada)
    {
        $compra = Compras::with(['detalles.proveedor'])
            ->findOrFail($ID_Entrada);
        
        // Obtener producto manualmente
        $producto = \DB::table('productos')
            ->where('ID_Producto', $compra->ID_Producto)
            ->first();
        
        $compra->producto_info = $producto;
        
        // Obtener empleado SIEMPRE manualmente
        $empleado = \DB::table('empleados')
            ->where('Documento_Empleado', $compra->Documento_Empleado)
            ->first();
        
        $compra->empleado = $empleado;
        
        return response()->json([
            'compra' => $compra
        ]);
    }
}