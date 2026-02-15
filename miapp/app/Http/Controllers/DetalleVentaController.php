<?php

namespace App\Http\Controllers;

use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetalleVentaController
{
    public function get()
    {
        $detalles = DB::table('Detalle_Ventas as dv')
            ->join('Productos as p', 'dv.ID_Producto', '=', 'p.ID_Producto')
            ->join('Ventas as v', 'dv.ID_Venta', '=', 'v.ID_Venta')
            ->select('dv.ID_Venta','dv.ID_Producto','dv.Cantidad','dv.Fecha_Salida','p.Nombre_Producto','p.Stock_Minimo')->get();

        $productos = DB::table('Productos')->where('ID_Estado', 1)->select('ID_Producto', 'Nombre_Producto', 'Stock_Minimo')->get();
        $ultimasVentas = DB::table('Ventas')->select('ID_Venta', 'Documento_Cliente')->orderByDesc('ID_Venta')->limit(5)->get();
        return view('detalle_ventas.index', compact('detalles', 'productos', 'ultimasVentas'));
    }

    public function post(Request $request)
    {
        $validated = $request->validate(['ID_Producto'=>'required|integer|exists:Productos,ID_Producto',
        'Cantidad'=>'required|integer|min:1','Fecha_Salida'=>'required|date','ID_Venta'=> 'required|integer|exists:Ventas,ID_Venta',
        ]);

        $producto = DB::table('Productos')
            ->where('ID_Producto', $validated['ID_Producto'])
            ->first();

        if (!$producto) {
            return redirect()->back()
                ->with('error', 'Producto no encontrado.');
        }

        if ($validated['Cantidad'] > $producto->Stock_Minimo) {
            return redirect()->back()
                ->with('error', "Stock insuficiente. Solo hay {$producto->Stock_Minimo} unidades disponibles de '{$producto->Nombre_Producto}'.");
        }
        $existente = DB::table('Detalle_Ventas')
            ->where('ID_Venta', $validated['ID_Venta'])
            ->where('ID_Producto', $validated['ID_Producto'])
            ->first();

        if ($existente) {
            return redirect()->back()
                ->with('error', 'Ya existe un detalle para esta venta con ese producto.');
        }

        DB::table('Detalle_Ventas')->insert([
            'ID_Venta'     => $validated['ID_Venta'],
            'ID_Producto'  => $validated['ID_Producto'],
            'Cantidad'     => $validated['Cantidad'],
            'Fecha_Salida' => $validated['Fecha_Salida'],
        ]);
        DB::table('Productos')
            ->where('ID_Producto', $validated['ID_Producto'])
            ->decrement('Stock_Minimo', $validated['Cantidad']);

        return redirect()
            ->route('detalleventas.index')
            ->with('mensaje', 'Detalle registrado correctamente.');
    }

    public function put(Request $request)
    {
        $validated = $request->validate([
            'ID_Producto'      => 'required|integer|exists:Productos,ID_Producto',
            'ID_Venta'         => 'required|integer|exists:Ventas,ID_Venta',
            'Cantidad'         => 'required|integer|min:1',
        ]);

        $detalleActual = DB::table('Detalle_Ventas')
            ->where('ID_Venta', $validated['ID_Venta'])
            ->where('ID_Producto', $validated['ID_Producto'])
            ->first();

        if (!$detalleActual) {
            return redirect()->back()
                ->with('error', 'No se encontró el detalle a actualizar.');
        }

        $producto = DB::table('Productos')
            ->where('ID_Producto', $validated['ID_Producto'])
            ->first();

        $cantidadAnterior = $detalleActual->Cantidad;
        $cantidadNueva    = $validated['Cantidad'];
        $diferencia       = $cantidadNueva - $cantidadAnterior;

        // Verificar que haya stock suficiente para la diferencia
        if ($diferencia > 0 && $diferencia > $producto->Stock_Minimo) {
            return redirect()->back()
                ->with('error', "Stock insuficiente. Solo hay {$producto->Stock_Minimo} unidades adicionales disponibles.");
        }

        // Actualizar detalle
        DB::table('Detalle_Ventas')
            ->where('ID_Venta', $validated['ID_Venta'])
            ->where('ID_Producto', $validated['ID_Producto'])
            ->update(['Cantidad' => $cantidadNueva]);

        // Ajustar stock según diferencia
        if ($diferencia > 0) {
            // Se vendió más → restar del stock
            DB::table('Productos')
                ->where('ID_Producto', $validated['ID_Producto'])
                ->decrement('Stock_Minimo', $diferencia);
        } elseif ($diferencia < 0) {
            // Se vendió menos → devolver al stock
            DB::table('Productos')
                ->where('ID_Producto', $validated['ID_Producto'])
                ->increment('Stock_Minimo', abs($diferencia));
        }

        return redirect()
            ->route('detalleventas.index')
            ->with('mensaje', 'Detalle actualizado correctamente.');
    }

    /* ======================
       ELIMINAR (ADMIN)
       Devuelve stock al eliminar
    ====================== */
    public function delete(Request $request)
    {
        $validated = $request->validate([
            'ID_Producto' => 'required|integer|exists:Detalle_Ventas,ID_Producto',
            'ID_Venta'    => 'required|integer|exists:Detalle_Ventas,ID_Venta',
        ]);

        $detalle = DB::table('Detalle_Ventas')
            ->where('ID_Venta', $validated['ID_Venta'])
            ->where('ID_Producto', $validated['ID_Producto'])
            ->first();

        if (!$detalle) {
            return redirect()->back()->with('error', 'Detalle no encontrado.');
        }

        // Devolver stock al eliminar
        DB::table('Productos')
            ->where('ID_Producto', $validated['ID_Producto'])
            ->increment('Stock_Minimo', $detalle->Cantidad);

        DB::table('Detalle_Ventas')
            ->where('ID_Venta', $validated['ID_Venta'])
            ->where('ID_Producto', $validated['ID_Producto'])
            ->delete();

        return redirect()
            ->route('detalleventas.index')
            ->with('mensaje', 'Detalle eliminado correctamente.');
    }

    public function buscarProducto($nombre)
    {
        try {
            $productos = DB::table('Productos')
                ->where('Nombre_Producto', 'LIKE', "%{$nombre}%")
                ->where('ID_Estado', 1)
                ->select('ID_Producto', 'Nombre_Producto', 'Stock_Minimo')
                ->limit(10)
                ->get();

            if ($productos->isEmpty()) {
                return response()->json(['error' => 'No se encontraron productos'], 404);
            }

            return response()->json(['productos' => $productos]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al buscar productos'], 500);
        }
    }
    public function ventaInfo($idVenta)
    {
        try {
            $info = DB::table('Detalle_Ventas as dv')
                ->join('Productos as p', 'dv.ID_Producto', '=', 'p.ID_Producto')
                ->where('dv.ID_Venta', $idVenta)
                ->select(
                    'p.Nombre_Producto as producto',
                    'dv.Cantidad as cantidad',
                    'p.Stock_Minimo as stock',
                    'p.ID_Producto as id_producto'
                )
                ->first();

            if (!$info) {
                return response()->json(['error' => 'Venta no encontrada'], 404);
            }

            return response()->json([
                'producto'    => $info->producto,
                'cantidad'    => $info->cantidad,
                'stock'       => $info->stock,
                'id_producto' => $info->id_producto,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al buscar la venta'], 500);
        }
    }
}