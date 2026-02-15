<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Devolucion;
use Illuminate\Support\Facades\DB;

class MigracionController extends Controller
{
    // ===============================
    // IMPORTACIÓN DE DEVOLUCIONES + DETALLES
    // ===============================
    public function importar(Request $request)
    {
        $modulo = $request->input('modulo');
        $datos  = $request->input('datos', []);

        if ($modulo !== 'devoluciones' || empty($datos)) {
            return response()->json([
                'success' => false,
                'mensaje' => 'No se recibió información válida'
            ]);
        }

        $importados = 0;

        DB::beginTransaction();

        try {
            foreach ($datos as $fila) {

                // Validar fecha
                $fecha = null;
                if (!empty($fila['Fecha_Devolucion'])) {
                    $ts    = strtotime($fila['Fecha_Devolucion']);
                    $fecha = ($ts !== false) ? date('Y-m-d', $ts) : date('Y-m-d');
                } else {
                    $fecha = date('Y-m-d');
                }

                $motivo = $fila['Motivo'] ?? 'Sin motivo';

                // ✅ FIX PRINCIPAL: usar DB::table directamente
                // Devolucion tiene $incrementing=false y $keyType='string'
                // lo que hace que Eloquent::create() no retorne el ID auto-generado.
                // Con DB::table + getPdo()->lastInsertId() obtenemos el ID real.
                DB::table('devoluciones')->insert([
                    'Fecha_Devolucion' => $fecha,
                    'Motivo'           => $motivo,
                ]);

                // Obtener el ID recién insertado directamente del PDO
                $idDevolucion = DB::getPdo()->lastInsertId();

                if (empty($idDevolucion)) {
                    throw new \Exception("No se pudo obtener el ID de la devolución recién insertada.");
                }

                // Insertar detalles
                $detalles = (isset($fila['detalles']) && is_array($fila['detalles']))
                    ? $fila['detalles']
                    : [];

                foreach ($detalles as $det) {
                    if (empty($det['ID_Venta']) || !isset($det['Cantidad_Devuelta'])) {
                        continue;
                    }

                    DB::table('Detalle_Devoluciones')->insert([
                        'ID_Devolucion'     => $idDevolucion,
                        'ID_Venta'          => $det['ID_Venta'],
                        'Cantidad_Devuelta' => $det['Cantidad_Devuelta'],
                    ]);
                }

                $importados++;
            }

            DB::commit();

            // Retornar devoluciones importadas con sus detalles
            $devolucionesRecientes = Devolucion::orderBy('ID_Devolucion', 'desc')
                ->take($importados)
                ->get()
                ->map(function ($dev) {
                    $dev->detalles = DB::table('Detalle_Devoluciones')
                        ->where('ID_Devolucion', $dev->ID_Devolucion)
                        ->get();
                    return $dev;
                });

            return response()->json([
                'success'      => true,
                'importados'   => $importados,
                'devoluciones' => $devolucionesRecientes
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al importar: ' . $e->getMessage()
            ]);
        }
    }

    // ===============================
    // INICIAR EXPORTACIÓN
    // ===============================
    public function iniciar(Request $request)
    {
        $modulo   = $request->input('modulo');
        $loteSize = $request->input('loteSize', 20);

        if ($modulo !== 'devoluciones') {
            return response()->json([
                'success' => false,
                'mensaje' => 'Módulo incorrecto'
            ]);
        }

        $ids = Devolucion::pluck('ID_Devolucion')->toArray();

        session([
            'export_ids'  => $ids,
            'lote_actual' => 0,
            'lote_size'   => $loteSize
        ]);

        return response()->json([
            'success'         => true,
            'total_registros' => count($ids),
            'lote_size'       => $loteSize
        ]);
    }

    // ===============================
    // LOTE DE EXPORTACIÓN
    // ===============================
    public function lote(Request $request)
    {
        $ids        = session('export_ids', []);
        $loteActual = session('lote_actual', 0);
        $loteSize   = session('lote_size', 20);
        $totalIds   = count($ids);

        if ($totalIds === 0) {
            return response()->json([
                'success' => false,
                'mensaje' => 'No hay IDs en sesión. Inicie la exportación primero.'
            ]);
        }

        $inicio  = $loteActual * $loteSize;
        $idsLote = array_slice($ids, $inicio, $loteSize);

        $devoluciones     = Devolucion::whereIn('ID_Devolucion', $idsLote)->get();
        $todosLosDetalles = DB::table('Detalle_Devoluciones')
            ->whereIn('ID_Devolucion', $idsLote)
            ->get()
            ->groupBy('ID_Devolucion');

        $datos = $devoluciones->map(function ($dev) use ($todosLosDetalles) {
            $detalles = isset($todosLosDetalles[$dev->ID_Devolucion])
                ? $todosLosDetalles[$dev->ID_Devolucion]->map(function ($det) {
                    // Buscar el Documento_Cliente y cantidad máxima para exportar
                    $venta = DB::table('ventas')
                        ->where('ID_Venta', $det->ID_Venta)
                        ->first();

                    $cantMax = DB::table('Detalle_Ventas')
                        ->where('ID_Venta', $det->ID_Venta)
                        ->sum('Cantidad');

                    return [
                        'Documento_Cliente' => $venta->Documento_Cliente ?? 'N/A',
                        'Cantidad_Devuelta' => $det->Cantidad_Devuelta,
                        'Cantidad_Maxima'   => $cantMax,
                    ];
                })->values()->toArray()
                : [];

            return [
                'ID_Devolucion'    => $dev->ID_Devolucion,
                'Fecha_Devolucion' => $dev->Fecha_Devolucion,
                'Motivo'           => $dev->Motivo,
                'detalles'         => $detalles,
            ];
        });

        $registrosMigrados = $inicio + count($devoluciones);
        $completado        = $registrosMigrados >= $totalIds;

        session(['lote_actual' => $loteActual + 1]);

        return response()->json([
            'success'            => true,
            'datos'              => $datos,
            'progreso'           => intval(($registrosMigrados / $totalIds) * 100),
            'registros_migrados' => $registrosMigrados,
            'total_registros'    => $totalIds,
            'lote_actual'        => $loteActual + 1,
            'completado'         => $completado
        ]);
    }

    // ===============================
    // BUSCAR VENTA POR DOCUMENTO CLIENTE
    // ===============================
    public function buscarVenta(Request $request)
    {
        $doc = $request->input('Documento_Cliente');

        $venta = DB::table('ventas')
            ->where('Documento_Cliente', $doc)
            ->orderBy('ID_Venta', 'desc')
            ->first();

        if (!$venta) {
            return response()->json([
                'success' => false,
                'mensaje' => 'No se encontró ninguna venta para el cliente: ' . $doc
            ]);
        }

        $cantMax = DB::table('Detalle_Ventas')
            ->where('ID_Venta', $venta->ID_Venta)
            ->sum('Cantidad');

        return response()->json([
            'success'         => true,
            'ID_Venta'        => $venta->ID_Venta,
            'cantidad_maxima' => $cantMax
        ]);
    }

    // ===============================
    // HISTORIAL DE IMPORTACIONES
    // ===============================
    public function historial()
    {
        $migraciones = DB::table('migraciones')
            ->where('tipo', 'importacion')
            ->orderBy('id', 'desc')
            ->get();

        return view('migracion.historial', compact('migraciones'));
    }
}
