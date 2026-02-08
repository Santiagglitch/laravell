<?php

namespace App\Http\Controllers;

use App\Models\Detalle_Compras;
use App\Models\Proveedor;
use App\Models\Compras;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetalleComprasController
{
    public function get()
    {
        // Cargar detalles con la información de la compra y proveedor
        $detalles = Detalle_Compras::with(['compra.empleado', 'proveedor'])->get();
        
        // Obtener todas las compras y proveedores
        $compras = Compras::all();
        $proveedores = Proveedor::all();
        
        return view('detalle_compras.index', compact('detalles', 'compras', 'proveedores'));
    }

    public function post(Request $request)
    {
        $validated = $request->validate([
            'Fecha_Entrada' => 'required|date',
            'Cantidad'      => 'required|integer|min:1',
            'ID_Proveedor'  => 'required|exists:proveedores,ID_Proveedor',
            'ID_Entrada'    => 'required|exists:compras,ID_Entrada',
        ]);

        // Registrar el cambio
        $this->registrarCambio('crear', null, $validated);

        Detalle_Compras::create($validated);

        return redirect()
            ->route('detallecompras.index')
            ->with('mensaje', 'Detalle de compra registrado correctamente.');
    }

    public function put(Request $request)
    {
        $validated = $request->validate([
            'ID_Proveedor'  => 'required|exists:detalle_compras,ID_Proveedor',
            'ID_Entrada'    => 'required|exists:detalle_compras,ID_Entrada',
            'Fecha_Entrada' => 'nullable|date',
            'Cantidad'      => 'nullable|integer|min:1',
        ]);

        // Obtener datos anteriores para el registro
        $detalleAnterior = DB::table('detalle_compras')
            ->where('ID_Proveedor', $validated['ID_Proveedor'])
            ->where('ID_Entrada', $validated['ID_Entrada'])
            ->first();

        $data = $validated;
        unset($data['ID_Proveedor'], $data['ID_Entrada']);

        $data = array_filter($data, fn($v) => !is_null($v) && $v !== '');

        // Registrar el cambio
        $this->registrarCambio('editar', $detalleAnterior, $data);

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
            'ID_Proveedor' => 'required|exists:detalle_compras,ID_Proveedor',
            'ID_Entrada'   => 'required|exists:detalle_compras,ID_Entrada',
        ]);

        // Obtener datos antes de eliminar para el registro
        $detalleAnterior = DB::table('detalle_compras')
            ->where('ID_Proveedor', $validated['ID_Proveedor'])
            ->where('ID_Entrada', $validated['ID_Entrada'])
            ->first();

        // Registrar el cambio
        $this->registrarCambio('eliminar', $detalleAnterior, null);

        DB::table('detalle_compras')
            ->where('ID_Proveedor', $validated['ID_Proveedor'])
            ->where('ID_Entrada', $validated['ID_Entrada'])
            ->delete();

        return redirect()
            ->route('detallecompras.index')
            ->with('mensaje', 'Detalle de compra eliminado correctamente.');
    }

    /**
     * Registrar cambios en una tabla de auditoría
     */
    private function registrarCambio($accion, $datosAnteriores, $datosNuevos)
    {
        // Verificar si la tabla existe antes de insertar
        try {
            DB::table('auditoria_cambios')->insert([
                'tabla' => 'detalle_compras',
                'accion' => $accion,
                'usuario' => session('documento'),
                'datos_anteriores' => json_encode($datosAnteriores),
                'datos_nuevos' => json_encode($datosNuevos),
                'fecha_cambio' => now(),
            ]);
        } catch (\Exception $e) {
            // Si la tabla no existe, solo registrar en log
            \Log::info("Cambio en detalle_compras: $accion", [
                'usuario' => session('documento'),
                'anteriores' => $datosAnteriores,
                'nuevos' => $datosNuevos
            ]);
        }
    }
}