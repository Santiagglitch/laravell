<?php

namespace App\Http\Controllers;

use App\Models\Devolucion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DevolucionController
{
    public function get()
    {
        $devolucion = Devolucion::all();
        return view('devolucion.index', compact('devolucion'));
    }

    public function post(Request $request)
    {
        $validated = $request->validate([
            'Fecha_Devolucion' => 'required|date',
            'Motivo'           => 'required|string|max:45',
        ]);

        Devolucion::create($validated);

        return redirect()
            ->route('devolucion.index')
            ->with('mensaje', 'Devolución registrada correctamente.');
    }

    public function put(Request $request)
    {
        $validated = $request->validate([
            'ID_Devolucion'    => 'required|string|max:20|exists:devoluciones,ID_Devolucion',
            'Fecha_Devolucion' => 'required|date',
            'Motivo'           => 'required|string|max:45',
        ]);

        $devolucion = Devolucion::findOrFail($validated['ID_Devolucion']);

        $datosActualizar = $validated;
        unset($datosActualizar['ID_Devolucion']);

        $datosActualizar = array_filter(
            $datosActualizar,
            fn($value) => !is_null($value) && $value !== ''
        );

        if (!empty($datosActualizar)) {
            $devolucion->update($datosActualizar);
        }

        return redirect()
            ->route('devolucion.index')
            ->with('mensaje', 'Devolución actualizada correctamente.');
    }

    public function delete(Request $request)
    {
        $validated = $request->validate([
            'ID_Devolucion' => 'required|string|max:20|exists:devoluciones,ID_Devolucion',
        ]);

        $tieneDetalles = DB::table('Detalle_Devoluciones')
            ->where('ID_Devolucion', $validated['ID_Devolucion'])
            ->exists();

        if ($tieneDetalles) {
            return redirect()
                ->route('devolucion.index')
                ->with('error', 'No se puede eliminar la devolución porque tiene detalles asociados. Por favor, elimina primero el detalle de la devolución.');
        }

        $devolucion = Devolucion::findOrFail($validated['ID_Devolucion']);
        $devolucion->delete();

        return redirect()
            ->route('devolucion.index')
            ->with('mensaje', 'Devolución eliminada correctamente.');
    }

    public function obtenerDetalles($id)
    {
        try {
            $devolucion = Devolucion::with(['detalles'])->findOrFail($id);

            return response()->json([
                'devolucion' => [
                    'ID_Devolucion'    => $devolucion->ID_Devolucion,
                    'Fecha_Devolucion' => $devolucion->Fecha_Devolucion,
                    'Motivo'           => $devolucion->Motivo,
                    'detalles'         => $devolucion->detalles->map(function ($detalle) {
                        return [
                            'ID_Devolucion'    => $detalle->ID_Devolucion,
                            'Cantidad_Devuelta' => $detalle->Cantidad_Devuelta,
                            'ID_Venta'         => $detalle->ID_Venta,
                        ];
                    })
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo cargar la información de la devolución'
            ], 500);
        }
    }

    public function indexEmpleado()
    {
        $devolucion = Devolucion::all();
        return view('devolucion.indexEm', compact('devolucion'));
    }

    // ✅ FIX: lógica propia, redirect a ruta Em
    public function storeEmpleado(Request $request)
    {
        $validated = $request->validate([
            'Fecha_Devolucion' => 'required|date',
            'Motivo'           => 'required|string|max:45',
        ]);

        Devolucion::create($validated);

        return redirect()
            ->route('devolucion.indexEm')
            ->with('mensaje', 'Devolución registrada correctamente.');
    }

    // ✅ FIX: lógica propia, redirect a ruta Em
    public function updateEmpleado(Request $request)
    {
        $validated = $request->validate([
            'ID_Devolucion' => 'required|string|max:20|exists:devoluciones,ID_Devolucion',
            'Motivo'        => 'required|string|max:45',
        ]);

        $devolucion = Devolucion::findOrFail($validated['ID_Devolucion']);
        $devolucion->update(['Motivo' => $validated['Motivo']]);

        return redirect()
            ->route('devolucion.indexEm')
            ->with('mensaje', 'Devolución actualizada correctamente.');
    }

    public function destroyEmpleado(Request $request)
    {
        $validated = $request->validate([
            'ID_Devolucion' => 'required|string|max:20|exists:devoluciones,ID_Devolucion',
        ]);

        $tieneDetalles = DB::table('Detalle_Devoluciones')
            ->where('ID_Devolucion', $validated['ID_Devolucion'])
            ->exists();

        if ($tieneDetalles) {
            return redirect()
                ->route('devolucion.indexEm')
                ->with('error', 'No se puede eliminar la devolución porque tiene detalles asociados. Por favor, elimina primero el detalle de la devolución.');
        }

        $devolucion = Devolucion::findOrFail($validated['ID_Devolucion']);
        $devolucion->delete();

        return redirect()
            ->route('devolucion.indexEm')
            ->with('mensaje', 'Devolución eliminada correctamente.');
    }
}
