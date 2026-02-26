<?php

namespace App\Http\Controllers;

use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetalleVentaController extends Controller
{
    /* ======================
       LISTAR (ADMIN)
    ====================== */
    public function get()
    {
        $detalles = DB::table('Detalle_Ventas as dv')
            ->join('Productos as p', 'dv.ID_Producto', '=', 'p.ID_Producto')
            ->join('Ventas as v', 'dv.ID_Venta', '=', 'v.ID_Venta')
            ->select(
                'dv.ID_Venta',
                'dv.ID_Producto',
                'dv.Cantidad',
                'dv.Fecha_Salida',
                'p.Nombre_Producto',
                'p.Stock_Minimo'
            )->get();

        $productos     = DB::table('Productos')->where('ID_Estado', 1)->select('ID_Producto', 'Nombre_Producto', 'Stock_Minimo')->get();
        $ultimasVentas = DB::table('Ventas')->select('ID_Venta', 'Documento_Cliente')->orderByDesc('ID_Venta')->limit(5)->get();

        return view('detalle_ventas.index', compact('detalles', 'productos', 'ultimasVentas'));
    }

    /* ======================
       LISTAR (EMPLEADO)
    ====================== */
    public function indexEmpleado()
    {
        // Usamos DB::table para evitar problemas con la clave primaria compuesta del modelo
        $detalles = DB::table('Detalle_Ventas')->get();
        return view('detalle_ventas.indexEm', compact('detalles'));
    }

    /* ======================
       CREAR
    ====================== */
    public function post(Request $request)
    {
        $validated = $request->validate([
            'ID_Producto'  => 'required|integer|exists:Productos,ID_Producto',
            'Cantidad'     => 'required|integer|min:1',
            'Fecha_Salida' => 'required|date',
            'ID_Venta'     => 'required|integer|exists:Ventas,ID_Venta',
        ]);

        $producto = DB::table('Productos')
            ->where('ID_Producto', $validated['ID_Producto'])
            ->first();

        if (!$producto) {
            return redirect()->back()->with('error', 'Producto no encontrado.');
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
            return redirect()->back()->with('error', 'Ya existe un detalle para esta venta con ese producto.');
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

        return redirect()->route('detalleventas.index')->with('mensaje', 'Detalle registrado correctamente.');
    }

    /* ======================
       ACTUALIZAR
    ====================== */
    public function put(Request $request)
    {
        $validated = $request->validate([
            'ID_Producto'  => 'required|integer|exists:Productos,ID_Producto',
            'ID_Venta'     => 'required|integer|exists:Ventas,ID_Venta',
            'Cantidad'     => 'required|integer|min:1',
            'Fecha_Salida' => 'required|date',   // ← CORREGIDO: ahora se valida y actualiza
        ]);

        $detalleActual = DB::table('Detalle_Ventas')
            ->where('ID_Venta', $validated['ID_Venta'])
            ->where('ID_Producto', $validated['ID_Producto'])
            ->first();

        if (!$detalleActual) {
            return redirect()->back()->with('error', 'No se encontró el detalle a actualizar.');
        }

        $producto = DB::table('Productos')
            ->where('ID_Producto', $validated['ID_Producto'])
            ->first();

        $cantidadAnterior = $detalleActual->Cantidad;
        $cantidadNueva    = $validated['Cantidad'];
        $diferencia       = $cantidadNueva - $cantidadAnterior;

        if ($diferencia > 0 && $diferencia > $producto->Stock_Minimo) {
            return redirect()->back()
                ->with('error', "Stock insuficiente. Solo hay {$producto->Stock_Minimo} unidades adicionales disponibles.");
        }

        // Actualizar cantidad Y fecha
        DB::table('Detalle_Ventas')
            ->where('ID_Venta', $validated['ID_Venta'])
            ->where('ID_Producto', $validated['ID_Producto'])
            ->update([
                'Cantidad'     => $cantidadNueva,
                'Fecha_Salida' => $validated['Fecha_Salida'],  // ← CORREGIDO
            ]);

        // Ajustar stock según diferencia
        if ($diferencia > 0) {
            DB::table('Productos')
                ->where('ID_Producto', $validated['ID_Producto'])
                ->decrement('Stock_Minimo', $diferencia);
        } elseif ($diferencia < 0) {
            DB::table('Productos')
                ->where('ID_Producto', $validated['ID_Producto'])
                ->increment('Stock_Minimo', abs($diferencia));
        }

        return redirect()->route('detalleventas.index')->with('mensaje', 'Detalle actualizado correctamente.');
    }

    /* ======================
       ELIMINAR
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

        return redirect()->route('detalleventas.index')->with('mensaje', 'Detalle eliminado correctamente.');
    }

    /* ======================
       BUSCAR PRODUCTO (AJAX)
    ====================== */
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

    /* ======================
       INFO DE VENTA (AJAX)
       Devuelve TODOS los productos de una venta
    ====================== */
    public function ventaInfo($idVenta)
    {
        try {
            // CORREGIDO: ->get() en lugar de ->first() para traer todos los productos de la venta
            $info = DB::table('Detalle_Ventas as dv')
                ->join('Productos as p', 'dv.ID_Producto', '=', 'p.ID_Producto')
                ->where('dv.ID_Venta', $idVenta)
                ->select(
                    'p.Nombre_Producto as producto',
                    'dv.Cantidad       as cantidad',
                    'p.Stock_Minimo    as stock',
                    'p.ID_Producto     as id_producto'
                )
                ->get();

            if ($info->isEmpty()) {
                return response()->json(['error' => 'Venta no encontrada'], 404);
            }

            return response()->json($info);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al buscar la venta: ' . $e->getMessage()], 500);
        }
    }
}