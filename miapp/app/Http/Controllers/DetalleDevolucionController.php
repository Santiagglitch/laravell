<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
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

        return view('detalle_devoluciones.index', compact(
            'detalles',
            'devoluciones'
        ));
    }

    /* ======================
       CREAR
    ====================== */
    public function post(Request $request)
    {
        $validated = $request->validate([
            'ID_Devolucion'     => 'required|exists:devoluciones,ID_Devolucion',
            'Cantidad_Devuelta' => 'required|integer|min:1',
            'ID_Venta'          => 'required|exists:ventas,ID_Venta',
        ]);

        DetalleDevolucion::create($validated);

        return redirect()
            ->back()
            ->with('mensaje', 'Detalle registrado correctamente.');
    }

    /* ======================
       ACTUALIZAR
    ====================== */
    public function update(Request $request, $ID_Devolucion)
    {
        $request->validate([
            'Cantidad_Devuelta' => 'required|integer|min:1',
        ]);

        DetalleDevolucion::where('ID_Devolucion', $ID_Devolucion)
            ->update([
                'Cantidad_Devuelta' => $request->Cantidad_Devuelta
            ]);

        return redirect()
            ->back()
            ->with('mensaje', 'Detalle actualizado correctamente.');
    }

    /* ======================
       ELIMINAR
    ====================== */
    public function destroy($ID_Devolucion)
    {
        DetalleDevolucion::where('ID_Devolucion', $ID_Devolucion)->delete();

        return redirect()
            ->back()
            ->with('mensaje', 'Detalle eliminado correctamente.');
    }

    /* ======================
       BUSCAR VENTA POR DOCUMENTO (AJAX)
    ====================== */
    public function ventaPorDocumento($documento)
    {
        $venta = DB::table('ventas')
            ->where('Documento_Cliente', $documento)
            ->orderByDesc('ID_Venta')
            ->select('ID_Venta')
            ->first();

        return response()->json($venta);
    }

    /* ======================
       EMPLEADO
    ====================== */
    public function indexEmpleado()
    {
        $detalles = DetalleDevolucion::all();
        $devoluciones = Devolucion::select('ID_Devolucion')->get();

        return view('detalle_devoluciones.indexEm', compact(
            'detalles',
            'devoluciones'
        ));
    }

    public function storeEmpleado(Request $request)
    {
        return $this->post($request);
    }

    public function updateEmpleado(Request $request, $ID_Devolucion)
    {
        return $this->update($request, $ID_Devolucion);
    }

    public function destroyEmpleado($ID_Devolucion)
    {
        return $this->destroy($ID_Devolucion);
    }
}