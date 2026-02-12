<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Services\VentasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class VentaController
{
    protected $ventasService;

    public function __construct(VentasService $ventasService)
    {
        $this->ventasService = $ventasService;
    }

    public function get()
    {
        $ventas = Venta::all();
        $empleados = \DB::table('Empleados')->select('Documento_Empleado', 'Nombre_Usuario', 'Apellido_Usuario')->get();
        return view('ventas.index', compact('ventas', 'empleados'));
    }

public function delete(Request $request)
{
    $validated = $request->validate([
        'ID_Venta' => 'required|integer|exists:ventas,ID_Venta',
    ]);

    $tieneDetalles = \DB::table('Detalle_Ventas')
        ->where('ID_Venta', $validated['ID_Venta'])
        ->exists();

    if ($tieneDetalles) {
        return redirect()
            ->route('ventas.index')
            ->with('error', 'No se puede eliminar la venta porque tiene detalles asociados. Por favor, elimina primero el detalle de la venta.');
    }

    $venta = Venta::findOrFail($validated['ID_Venta']);
    $venta->delete();

    return redirect()
        ->route('ventas.index')
        ->with('mensaje', 'Venta eliminada correctamente.');
}

    

    public function post(Request $request)
    {
        try {
            $validated = $request->validate([
                'Documento_Cliente'  => 'required|string|max:20',
                'Documento_Empleado' => 'required|string|max:20',
                'cliente_nuevo'      => 'nullable|string',
                'Nombre_Cliente'     => 'required_if:cliente_nuevo,1|nullable|string|max:100',
                'Apellido_Cliente'   => 'required_if:cliente_nuevo,1|nullable|string|max:100',
                'Estado_Cliente'     => 'nullable|string|in:activo,inactivo',
            ]);

            if ($request->cliente_nuevo == '1') {
                $clienteCreado = $this->ventasService->crearCliente([
                    'Documento_Cliente' => $request->Documento_Cliente,
                    'Nombre_Cliente'    => $request->Nombre_Cliente,
                    'Apellido_Cliente'  => $request->Apellido_Cliente,
                    'ID_Estado'         => $request->Estado_Cliente == 'activo' ? '1' : '2'
                ]);

                if (!$clienteCreado) {
                    return back()->with('error', 'Error al registrar el cliente en la API');
                }
            }

            $ventaCreada = $this->ventasService->crearVenta([
                'Documento_Cliente'  => $request->Documento_Cliente,
                'Documento_Empleado' => $request->Documento_Empleado
            ]);

            if (!$ventaCreada) {
            return back()->with('error', 'Error al registrar la venta en la API');
            }

            return redirect()
                ->route('ventas.index')
                ->with('mensaje', 'Venta registrada correctamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('error', 'Error de validación: ' . json_encode($e->errors()));

        } catch (\Exception $e) {
            \Log::error('Error en post de venta: ' . $e->getMessage());
            return back()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }

    public function buscarClienteAjax($documento)
    {
        try {
            $cliente = $this->ventasService->buscarCliente($documento);

            if ($cliente) {
                return response()->json([
                    'encontrado' => true,
                    'cliente'    => $cliente
                ]);
            }

            return response()->json(['encontrado' => false]);

        } catch (\Exception $e) {
            Log::error('Error buscando cliente: ' . $e->getMessage());
            return response()->json(['encontrado' => false]);
        }
    }

    public function obtenerDetalles($id)
    {
        try {
            $venta = Venta::findOrFail($id);

            $detalles = \DB::table('Detalle_Ventas as dv')
                ->join('Productos as p', 'dv.ID_Producto', '=', 'p.ID_Producto')
                ->where('dv.ID_Venta', $id)
                ->select(
                    'dv.ID_Venta',
                    'p.Nombre_Producto',
                    'dv.Cantidad',
                    'dv.Fecha_Salida'
                )
                ->get();

            return response()->json([
                'venta' => [
                    'ID_Venta'           => $venta->ID_Venta,
                    'Documento_Cliente'  => $venta->Documento_Cliente,
                    'Documento_Empleado' => $venta->Documento_Empleado,
                    'detalles'           => $detalles
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en obtenerDetalles: ' . $e->getMessage());
            return response()->json([
                'error' => 'No se pudo cargar la información: ' . $e->getMessage()
            ], 500);
        }
    }

    public function indexEmpleado()
    {
        $ventas = Venta::all();
        return view('ventas.indexEm', compact('ventas'));
    }

    public function storeEmpleado(Request $request)
    {
        return $this->post($request);
    }

    public function updateEmpleado(Request $request)
    {
        return $this->put($request);
    }

    public function destroyEmpleado(Request $request)
    {
        $validated = $request->validate([
            'ID_Venta' => 'required|integer|exists:ventas,ID_Venta',
        ]);

        $venta = Venta::findOrFail($validated['ID_Venta']);
        $venta->delete();

        return redirect()
            ->route('ventas.indexEm')
            ->with('mensaje', 'Venta eliminada correctamente.');
    }
}