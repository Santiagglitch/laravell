<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController
{
    // ========== MÉTODO GET (INDEX) ==========
    public function get()
    {
        $ventas = Venta::all();
        return view('ventas.index', compact('ventas'));
    }
    
    // ========== MÉTODO POST (CREATE) - MODIFICADO PARA API ==========
    public function post(Request $request)
    {
        try {
            $rules = [
                'Documento_Empleado' => 'required|string|max:20',
                'cliente_nuevo' => 'nullable|string',
            ];

            if ($request->cliente_nuevo === '1') {
                $rules['Documento_Cliente'] = 'required|string|max:20|unique:clientes,Documento_Cliente';
                $rules['Nombre_Cliente'] = 'required|string|max:100';
                $rules['Apellido_Cliente'] = 'required|string|max:100';
                $rules['Estado_Cliente'] = 'required|in:activo,inactivo';
            } else {
                $rules['Documento_Cliente'] = 'required|string|max:20|exists:clientes,Documento_Cliente';
            }

            $validated = $request->validate($rules);
            
            // Crear cliente si es nuevo
            if ($request->cliente_nuevo === '1') {
                DB::table('clientes')->insert([
                    'Documento_Cliente' => $request->Documento_Cliente,
                    'Nombre_Cliente' => $request->Nombre_Cliente,
                    'Apellido_Cliente' => $request->Apellido_Cliente,
                    'ID_Estado' => $request->Estado_Cliente === 'activo' ? 1 : 2,
                ]);
            }

            // Crear venta
            Venta::create([
                'Documento_Cliente' => $request->Documento_Cliente,
                'Documento_Empleado' => $request->Documento_Empleado,
            ]);

            return redirect()
                ->route('ventas.index')
                ->with('mensaje', 'Venta registrada correctamente');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar la venta: ' . $e->getMessage()]);
        }
    }

    // ========== BÚSQUEDA DE CLIENTE VÍA AJAX ==========
    public function buscarClienteAjax($documento)
    {
        $cliente = \DB::table('clientes')
            ->where('Documento_Cliente', $documento)
            ->first();

        if ($cliente) {
            return response()->json([
                'encontrado' => true,
                'cliente' => [
                    'Documento_Cliente' => $cliente->Documento_Cliente,
                    'Nombre_Cliente'    => $cliente->Nombre_Cliente,
                    'Apellido_Cliente'  => $cliente->Apellido_Cliente,
                    'ID_Estado'         => $cliente->ID_Estado,
                ]
            ]);
        }

        return response()->json(['encontrado' => false]);
    }

    // ========== MÉTODO PUT (UPDATE) ==========
    public function put(Request $request)
    {
        try {
            $validated = $request->validate([
                'ID_Venta'           => 'required|integer|exists:ventas,ID_Venta',
                'Documento_Cliente'  => 'nullable|string|max:20|exists:clientes,Documento_Cliente',
                'Documento_Empleado' => 'nullable|string|max:20|exists:empleados,Documento_Empleado',
            ]);

            $venta = Venta::findOrFail($validated['ID_Venta']);

            $datosActualizar = array_filter([
                'Documento_Cliente'  => $request->Documento_Cliente,
                'Documento_Empleado' => $request->Documento_Empleado,
            ], function ($value) {
                return !is_null($value) && $value !== '';
            });

            if (!empty($datosActualizar)) {
                $venta->update($datosActualizar);
            }

            return redirect()
                ->route('ventas.index')
                ->with('mensaje', 'Venta actualizada correctamente.');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al actualizar la venta: ' . $e->getMessage()]);
        }
    }

    // ========== MÉTODO DELETE ==========
    public function delete(Request $request)
    {
        try {
            $validated = $request->validate([
                'ID_Venta' => 'required|integer|exists:ventas,ID_Venta',
            ]);

            $venta = Venta::findOrFail($validated['ID_Venta']);
            $venta->delete();

            return redirect()
                ->route('ventas.index')
                ->with('mensaje', 'Venta eliminada correctamente.');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar la venta: ' . $e->getMessage()]);
        }
    }

    // ========== MÉTODOS PARA EMPLEADO ==========
    public function indexEmpleado()
    {
        $ventas = Venta::all();
        return view('ventas.indexEm', compact('ventas'));
    }

    public function storeEmpleado(Request $request)
    {
        $this->post($request);
        return redirect()->route('ventas.indexEm')->with('mensaje', 'Venta registrada correctamente.');
    }

    public function updateEmpleado(Request $request)
    {
        $this->put($request);
        return redirect()->route('ventas.indexEm')->with('mensaje', 'Venta actualizada correctamente.');
    }

    public function destroyEmpleado(Request $request)
    {
        $this->delete($request);
        return redirect()->route('ventas.indexEm')->with('mensaje', 'Venta eliminada correctamente.');
    }
}