<?php

namespace App\Http\Controllers;

use App\Models\DetalleDevolucion;
use App\Models\Devolucion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetalleDevolucionController
{
    /* ======================
       LISTAR (ADMIN)
    ====================== */
    public function get()
    {
        $detalles = DetalleDevolucion::all();
        $devoluciones = Devolucion::select('ID_Devolucion')->get();

        return view('detalle_devoluciones.index', compact('detalles', 'devoluciones'));
    }

    /* ======================
       CREAR (ADMIN)
    ====================== */
    public function post(Request $request)
    {
        $validated = $request->validate([
            'ID_Devolucion'     => 'required|exists:devoluciones,ID_Devolucion',
            'Cantidad_Devuelta' => 'required|integer|min:1',
            'ID_Venta'          => 'required|exists:ventas,ID_Venta',
        ]);

        $detalleExistente = DetalleDevolucion::where('ID_Devolucion', $validated['ID_Devolucion'])->first();
        if ($detalleExistente) {
            return redirect()->back()
                ->with('error', 'Esta devolución ya tiene un detalle registrado. Solo se permite un detalle por devolución.');
        }

        $detalleVenta = DB::table('Detalle_Ventas')->where('ID_Venta', $validated['ID_Venta'])->first();
        if (!$detalleVenta) {
            return redirect()->back()->with('error', 'No se encontró información de la venta seleccionada.');
        }

        if ($validated['Cantidad_Devuelta'] > $detalleVenta->Cantidad) {
            return redirect()->back()
                ->with('error', "La cantidad a devolver ({$validated['Cantidad_Devuelta']}) no puede ser mayor a la cantidad comprada ({$detalleVenta->Cantidad}).");
        }

        DetalleDevolucion::create($validated);
        return redirect()->back()->with('mensaje', 'Detalle registrado correctamente.');
    }

    /* ======================
       ACTUALIZAR (ADMIN)
    ====================== */
    public function update(Request $request, $ID_Devolucion)
    {
        $validated = $request->validate([
            'Cantidad_Devuelta' => 'required|integer|min:1',
            'ID_Venta'          => 'required|exists:ventas,ID_Venta',
        ]);

        $detalleVenta = DB::table('Detalle_Ventas')->where('ID_Venta', $validated['ID_Venta'])->first();
        if (!$detalleVenta) {
            return redirect()->back()->with('error', 'No se encontró información de la venta seleccionada.');
        }

        if ($validated['Cantidad_Devuelta'] > $detalleVenta->Cantidad) {
            return redirect()->back()
                ->with('error', "La cantidad a devolver ({$validated['Cantidad_Devuelta']}) no puede ser mayor a la cantidad comprada ({$detalleVenta->Cantidad}).");
        }

        $detalle = DetalleDevolucion::where('ID_Devolucion', $ID_Devolucion)->firstOrFail();
        $detalle->update($validated);
        return redirect()->back()->with('mensaje', 'Detalle actualizado correctamente.');
    }

    /* ======================
       ELIMINAR (ADMIN)
    ====================== */
    public function destroy($ID_Devolucion)
    {
        DetalleDevolucion::where('ID_Devolucion', $ID_Devolucion)->delete();
        return redirect()->back()->with('mensaje', 'Detalle eliminado correctamente.');
    }

    /* ======================
       BUSCAR VENTA POR DOCUMENTO (AJAX)
    ====================== */
    public function ventaPorDocumento($documento)
    {
        try {
            $cliente = DB::table('Clientes')
                ->where('Documento_Cliente', $documento)
                ->select('Documento_Cliente', 'Nombre_Cliente', 'Apellido_Cliente')
                ->first();

            if (!$cliente) {
                return response()->json(['error' => 'Cliente no encontrado'], 404);
            }

            $ventas = DB::table('Ventas as v')
                ->join('Detalle_Ventas as dv', 'v.ID_Venta', '=', 'dv.ID_Venta')
                ->join('Productos as p', 'dv.ID_Producto', '=', 'p.ID_Producto')
                ->where('v.Documento_Cliente', $documento)
                ->select(
                    'v.ID_Venta as id_venta',
                    'p.Nombre_Producto as producto',
                    'dv.Cantidad as cantidad',
                    'dv.Fecha_Salida as fecha'
                )
                ->orderByDesc('dv.Fecha_Salida')
                ->get();

            if ($ventas->isEmpty()) {
                return response()->json(['error' => 'El cliente no tiene ventas registradas'], 404);
            }

            return response()->json([
                'cliente' => [
                    'documento' => $cliente->Documento_Cliente,
                    'nombre'    => $cliente->Nombre_Cliente . ' ' . $cliente->Apellido_Cliente,
                ],
                'ventas' => $ventas,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al buscar las ventas del cliente', 'mensaje' => $e->getMessage()], 500);
        }
    }

    /* ============================================================
       ✅ NUEVO: INFO DE VENTA PARA MODAL EDITAR (AJAX)
       Ruta: GET /empleado/venta-info/{idVenta}
    ============================================================ */
    public function ventaInfo($idVenta)
    {
        try {
            $info = DB::table('Detalle_Ventas as dv')
                ->join('Productos as p', 'dv.ID_Producto', '=', 'p.ID_Producto')
                ->where('dv.ID_Venta', $idVenta)
                ->select('p.Nombre_Producto as producto', 'dv.Cantidad as cantidad')
                ->first();

            if (!$info) {
                return response()->json(['error' => 'Venta no encontrada'], 404);
            }

            return response()->json([
                'producto' => $info->producto,
                'cantidad' => $info->cantidad,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al buscar la venta', 'mensaje' => $e->getMessage()], 500);
        }
    }

    /* ======================
       EMPLEADO - LISTAR
    ====================== */
    public function indexEmpleado()
    {
        $detalles = DetalleDevolucion::all();
        $devoluciones = Devolucion::select('ID_Devolucion')->get();

        return view('detalle_devoluciones.indexEm', compact('detalles', 'devoluciones'));
    }

    /* ======================
       EMPLEADO - CREAR
    ====================== */
    public function storeEmpleado(Request $request)
    {
        $validated = $request->validate([
            'ID_Devolucion'     => 'required|exists:devoluciones,ID_Devolucion',
            'Cantidad_Devuelta' => 'required|integer|min:1',
            'ID_Venta'          => 'required|exists:ventas,ID_Venta',
        ]);

        $detalleExistente = DetalleDevolucion::where('ID_Devolucion', $validated['ID_Devolucion'])->first();
        if ($detalleExistente) {
            return redirect()->route('detalledevolucion.indexEm')
                ->with('error', 'Esta devolución ya tiene un detalle registrado. Solo se permite un detalle por devolución.');
        }

        $detalleVenta = DB::table('Detalle_Ventas')->where('ID_Venta', $validated['ID_Venta'])->first();
        if (!$detalleVenta) {
            return redirect()->route('detalledevolucion.indexEm')
                ->with('error', 'No se encontró información de la venta seleccionada.');
        }

        if ($validated['Cantidad_Devuelta'] > $detalleVenta->Cantidad) {
            return redirect()->route('detalledevolucion.indexEm')
                ->with('error', "La cantidad a devolver ({$validated['Cantidad_Devuelta']}) no puede ser mayor a la cantidad comprada ({$detalleVenta->Cantidad}).");
        }

        DetalleDevolucion::create($validated);
        return redirect()->route('detalledevolucion.indexEm')->with('mensaje', 'Detalle registrado correctamente.');
    }

    /* ======================
       EMPLEADO - ACTUALIZAR
    ====================== */
    public function updateEmpleado(Request $request, $ID_Devolucion)
    {
        $validated = $request->validate([
            'Cantidad_Devuelta' => 'required|integer|min:1',
            'ID_Venta'          => 'required|exists:ventas,ID_Venta',
        ]);

        $detalleVenta = DB::table('Detalle_Ventas')->where('ID_Venta', $validated['ID_Venta'])->first();
        if (!$detalleVenta) {
            return redirect()->route('detalledevolucion.indexEm')
                ->with('error', 'No se encontró información de la venta seleccionada.');
        }

        if ($validated['Cantidad_Devuelta'] > $detalleVenta->Cantidad) {
            return redirect()->route('detalledevolucion.indexEm')
                ->with('error', "La cantidad a devolver ({$validated['Cantidad_Devuelta']}) no puede ser mayor a la cantidad comprada ({$detalleVenta->Cantidad}).");
        }

        $detalle = DetalleDevolucion::where('ID_Devolucion', $ID_Devolucion)->firstOrFail();
        $detalle->update($validated);
        return redirect()->route('detalledevolucion.indexEm')->with('mensaje', 'Detalle actualizado correctamente.');
    }

    /* ======================
       EMPLEADO - ELIMINAR
    ====================== */
    public function destroyEmpleado($ID_Devolucion)
    {
        DetalleDevolucion::where('ID_Devolucion', $ID_Devolucion)->delete();
        return redirect()->route('detalledevolucion.indexEm')->with('mensaje', 'Detalle eliminado correctamente.');
    }
}
