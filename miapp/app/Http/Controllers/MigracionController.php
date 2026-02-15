<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Devolucion;
use App\Models\Compras;
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

        if (empty($datos)) {
            return response()->json([
                'success' => false,
                'mensaje' => 'No se recibió información válida'
            ]);
        }

        // Determinar qué módulo se está importando
        if ($modulo === 'devoluciones') {
            return $this->importarDevoluciones($datos);
        } elseif ($modulo === 'compras') {
            return $this->importarCompras($datos);
        } elseif ($modulo === 'proveedores') {
            return $this->importarProveedores($datos);
        } elseif ($modulo === 'clientes') {
            return $this->importarClientes($datos);
        }

        return response()->json([
            'success' => false,
            'mensaje' => 'Módulo no válido'
        ]);
    }

    // ===============================
    // IMPORTAR DEVOLUCIONES
    // ===============================
    private function importarDevoluciones($datos)
    {
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

                DB::table('devoluciones')->insert([
                    'Fecha_Devolucion' => $fecha,
                    'Motivo'           => $motivo,
                ]);

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
    // IMPORTAR COMPRAS
    // ===============================
    private function importarCompras($datos)
    {
        $importados = 0;
        DB::beginTransaction();

        try {
            foreach ($datos as $fila) {
                // Validar precio
                $precio = floatval($fila['Precio_Compra'] ?? 0);
                
                // Buscar producto por nombre
                $nombreProducto = $fila['Nombre_Producto'] ?? null;
                if (!$nombreProducto) {
                    throw new \Exception("Falta el nombre del producto en la fila.");
                }
                
                $producto = DB::table('productos')
                    ->where('Nombre_Producto', $nombreProducto)
                    ->first();
                
                if (!$producto) {
                    throw new \Exception("Producto '{$nombreProducto}' no encontrado en la base de datos.");
                }

                $documentoEmpleado = session('documento');
                if (!$documentoEmpleado) {
                    throw new \Exception("No se encontró el documento del empleado en sesión.");
                }

                // Insertar compra
                DB::table('compras')->insert([
                    'Precio_Compra'      => $precio,
                    'ID_Producto'        => $producto->ID_Producto,
                    'Documento_Empleado' => $documentoEmpleado,
                ]);

                $idEntrada = DB::getPdo()->lastInsertId();

                if (empty($idEntrada)) {
                    throw new \Exception("No se pudo obtener el ID de la compra recién insertada.");
                }

                // Insertar detalles
                $detalles = (isset($fila['detalles']) && is_array($fila['detalles']))
                    ? $fila['detalles']
                    : [];

                foreach ($detalles as $det) {
                    if (empty($det['Nombre_Proveedor']) || !isset($det['Cantidad'])) {
                        continue;
                    }

                    // Buscar proveedor por nombre
                    $proveedor = DB::table('Proveedores')
                        ->where('Nombre_Proveedor', $det['Nombre_Proveedor'])
                        ->first();
                    
                    if (!$proveedor) {
                        throw new \Exception("Proveedor '{$det['Nombre_Proveedor']}' no encontrado.");
                    }

                    DB::table('detalle_compras')->insert([
                        'ID_Entrada'    => $idEntrada,
                        'ID_Proveedor'  => $proveedor->ID_Proveedor,
                        'Fecha_Entrada' => $det['Fecha_Entrada'] ?? date('Y-m-d'),
                        'Cantidad'      => $det['Cantidad'],
                    ]);
                }

                $importados++;
            }

            DB::commit();

            $comprasRecientes = Compras::orderBy('ID_Entrada', 'desc')
                ->take($importados)
                ->get()
                ->map(function ($compra) {
                    $compra->detalles = DB::table('detalle_compras')
                        ->where('ID_Entrada', $compra->ID_Entrada)
                        ->get();
                    return $compra;
                });

            return response()->json([
                'success'   => true,
                'importados' => $importados,
                'compras'   => $comprasRecientes
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

        if ($modulo === 'devoluciones') {
            $ids = Devolucion::pluck('ID_Devolucion')->toArray();
        } elseif ($modulo === 'compras') {
            $ids = Compras::pluck('ID_Entrada')->toArray();
        } elseif ($modulo === 'proveedores') {
            $ids = DB::table('Proveedores')->pluck('ID_Proveedor')->toArray();
        } elseif ($modulo === 'clientes') {
            $ids = DB::table('clientes')->pluck('Documento_Cliente')->toArray();
        } else {
            return response()->json([
                'success' => false,
                'mensaje' => 'Módulo incorrecto'
            ]);
        }

        session([
            'export_modulo' => $modulo,
            'export_ids'    => $ids,
            'lote_actual'   => 0,
            'lote_size'     => $loteSize
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
    // LOTE DE EXPORTACIÓN
    // ===============================
    public function lote(Request $request)
    {
        $modulo     = session('export_modulo');
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

        if ($modulo === 'devoluciones') {
            $datos = $this->exportarDevoluciones($idsLote);
        } elseif ($modulo === 'compras') {
            $datos = $this->exportarCompras($idsLote);
        } elseif ($modulo === 'proveedores') {
            $datos = $this->exportarProveedores($idsLote);
        } elseif ($modulo === 'clientes') {
            $datos = $this->exportarClientes($idsLote);
        } else {
            return response()->json([
                'success' => false,
                'mensaje' => 'Módulo no válido'
            ]);
        }

        $registrosMigrados = $inicio + count($idsLote);
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
    // EXPORTAR DEVOLUCIONES
    // ===============================
    private function exportarDevoluciones($idsLote)
    {
        $devoluciones     = Devolucion::whereIn('ID_Devolucion', $idsLote)->get();
        $todosLosDetalles = DB::table('Detalle_Devoluciones')
            ->whereIn('ID_Devolucion', $idsLote)
            ->get()
            ->groupBy('ID_Devolucion');

        return $devoluciones->map(function ($dev) use ($todosLosDetalles) {
            $detalles = isset($todosLosDetalles[$dev->ID_Devolucion])
                ? $todosLosDetalles[$dev->ID_Devolucion]->map(function ($det) {
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
    }

    // ===============================
    // EXPORTAR COMPRAS
    // ===============================
    private function exportarCompras($idsLote)
    {
        $compras = Compras::whereIn('ID_Entrada', $idsLote)->get();
        $todosLosDetalles = DB::table('detalle_compras')
            ->whereIn('ID_Entrada', $idsLote)
            ->get()
            ->groupBy('ID_Entrada');

        return $compras->map(function ($compra) use ($todosLosDetalles) {
            $producto = DB::table('productos')
                ->where('ID_Producto', $compra->ID_Producto)
                ->first();

            $detalles = isset($todosLosDetalles[$compra->ID_Entrada])
                ? $todosLosDetalles[$compra->ID_Entrada]->map(function ($det) {
                    $proveedor = DB::table('Proveedores')
                        ->where('ID_Proveedor', $det->ID_Proveedor)
                        ->first();

                    return [
                        'Nombre_Proveedor' => $proveedor->Nombre_Proveedor ?? 'N/A',
                        'Fecha_Entrada'    => $det->Fecha_Entrada,
                        'Cantidad'         => $det->Cantidad,
                    ];
                })->values()->toArray()
                : [];

            return [
                'ID_Entrada'       => $compra->ID_Entrada,
                'Precio_Compra'    => $compra->Precio_Compra,
                'Nombre_Producto'  => $producto->Nombre_Producto ?? 'N/A',
                'detalles'         => $detalles,
            ];
        });
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
    // BUSCAR PRODUCTO POR NOMBRE
    // ===============================
    public function buscarProducto(Request $request)
    {
        $nombre = $request->input('Nombre_Producto');

        $producto = DB::table('productos')
            ->where('Nombre_Producto', $nombre)
            ->first();

        if (!$producto) {
            return response()->json([
                'success' => false,
                'mensaje' => 'No se encontró ningún producto con el nombre: ' . $nombre
            ]);
        }

        return response()->json([
            'success'         => true,
            'ID_Producto'     => $producto->ID_Producto,
            'Nombre_Producto' => $producto->Nombre_Producto
        ]);
    }

    // ===============================
    // BUSCAR PROVEEDOR POR NOMBRE
    // ===============================
    public function buscarProveedor(Request $request)
    {
        $nombre = $request->input('Nombre_Proveedor');

        $proveedor = DB::table('Proveedores')
            ->where('Nombre_Proveedor', $nombre)
            ->first();

        if (!$proveedor) {
            return response()->json([
                'success' => false,
                'mensaje' => 'No se encontró ningún proveedor con el nombre: ' . $nombre
            ]);
        }

        return response()->json([
            'success'          => true,
            'ID_Proveedor'     => $proveedor->ID_Proveedor,
            'Nombre_Proveedor' => $proveedor->Nombre_Proveedor
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

    // ===============================
    // IMPORTAR PROVEEDORES
    // ===============================
    private function importarProveedores($datos)
    {
        $importados = 0;
        $errores = [];
        
        DB::beginTransaction();

        try {
            foreach ($datos as $index => $fila) {
                $nombre = $fila['Nombre_Proveedor'] ?? null;
                $correo = $fila['Correo_Electronico'] ?? null;
                $telefono = $fila['Telefono'] ?? null;
                $estado = $fila['ID_Estado'] ?? 1;

                if (empty($nombre)) {
                    $errores[] = "Fila " . ($index + 2) . ": Falta el nombre del proveedor";
                    continue;
                }

                // Verificar duplicados
                $nombreExiste = DB::table('Proveedores')
                    ->where('Nombre_Proveedor', $nombre)
                    ->exists();
                
                if ($nombreExiste) {
                    $errores[] = "Fila " . ($index + 2) . ": El proveedor '{$nombre}' ya existe";
                    continue;
                }

                $correoExiste = DB::table('Proveedores')
                    ->where('Correo_Electronico', $correo)
                    ->exists();
                
                if ($correoExiste) {
                    $errores[] = "Fila " . ($index + 2) . ": El correo '{$correo}' ya está registrado";
                    continue;
                }

                $telefonoExiste = DB::table('Proveedores')
                    ->where('Telefono', $telefono)
                    ->exists();
                
                if ($telefonoExiste) {
                    $errores[] = "Fila " . ($index + 2) . ": El teléfono '{$telefono}' ya está registrado";
                    continue;
                }

                // Insertar si no hay duplicados
                DB::table('Proveedores')->insert([
                    'Nombre_Proveedor'   => $nombre,
                    'Correo_Electronico' => $correo,
                    'Telefono'           => $telefono,
                    'ID_Estado'          => $estado,
                ]);
                
                $importados++;
            }

            DB::commit();

            $mensaje = "Se importaron {$importados} proveedores correctamente.";
            if (!empty($errores)) {
                $mensaje .= " Errores encontrados: " . implode(", ", $errores);
            }

            return response()->json([
                'success'    => true,
                'importados' => $importados,
                'errores'    => $errores,
                'mensaje'    => $mensaje
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
    // EXPORTAR PROVEEDORES
    // ===============================
    private function exportarProveedores($idsLote)
    {
        $proveedores = DB::table('Proveedores')
            ->whereIn('ID_Proveedor', $idsLote)
            ->get();

        return $proveedores->map(function ($prov) {
            return [
                'ID_Proveedor'       => $prov->ID_Proveedor,
                'Nombre_Proveedor'   => $prov->Nombre_Proveedor,
                'Correo_Electronico' => $prov->Correo_Electronico,
                'Telefono'           => $prov->Telefono,
                'Estado'             => $prov->ID_Estado == 1 ? 'Activo' : ($prov->ID_Estado == 2 ? 'Inactivo' : 'En proceso'),
            ];
        });
    }

    // ===============================
    // IMPORTAR CLIENTES
    // ===============================
    private function importarClientes($datos)
    {
        $importados = 0;
        $errores = [];
        
        DB::beginTransaction();

        try {
            foreach ($datos as $index => $fila) {
                $documento = $fila['Documento_Cliente'] ?? null;
                $nombre = $fila['Nombre_Cliente'] ?? null;
                $apellido = $fila['Apellido_Cliente'] ?? null;
                $estado = $fila['ID_Estado'] ?? 1;

                if (empty($documento)) {
                    $errores[] = "Fila " . ($index + 2) . ": Falta el documento del cliente";
                    continue;
                }

                // Verificar duplicados
                $documentoExiste = DB::table('clientes')
                    ->where('Documento_Cliente', $documento)
                    ->exists();
                
                if ($documentoExiste) {
                    $errores[] = "Fila " . ($index + 2) . ": El cliente con documento '{$documento}' ya existe";
                    continue;
                }

                // Insertar si no hay duplicados
                DB::table('clientes')->insert([
                    'Documento_Cliente' => $documento,
                    'Nombre_Cliente'    => $nombre,
                    'Apellido_Cliente'  => $apellido,
                    'ID_Estado'         => $estado,
                ]);
                
                $importados++;
            }

            DB::commit();

            $mensaje = "Se importaron {$importados} clientes correctamente.";
            if (!empty($errores)) {
                $mensaje .= " Errores encontrados: " . implode(", ", $errores);
            }

            return response()->json([
                'success'    => true,
                'importados' => $importados,
                'errores'    => $errores,
                'mensaje'    => $mensaje
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
    // EXPORTAR CLIENTES
    // ===============================
    private function exportarClientes($idsLote)
    {
        $clientes = DB::table('clientes')
            ->whereIn('Documento_Cliente', $idsLote)
            ->get();

        return $clientes->map(function ($cli) {
            return [
                'Documento_Cliente' => $cli->Documento_Cliente,
                'Nombre_Cliente'    => $cli->Nombre_Cliente,
                'Apellido_Cliente'  => $cli->Apellido_Cliente,
                'Estado'            => $cli->ID_Estado == 1 ? 'Activo' : 'Inactivo',
            ];
        });
    }
}