<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Devolucion;
use App\Models\Compras;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MigracionController extends Controller
{
    // ===============================
    // IMPORTACIÓN GENERAL
    // ===============================
    public function importar(Request $request)
    {
        try {
            $modulo = $request->input('modulo');
            $datos  = $request->input('datos', []);

            if (empty($datos)) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No se recibió información válida'
                ]);
            }

            if ($modulo === 'devoluciones') {
                return $this->importarDevoluciones($datos);
            } elseif ($modulo === 'compras') {
                return $this->importarCompras($datos);
            } elseif ($modulo === 'proveedores') {
                return $this->importarProveedores($datos);
            } elseif ($modulo === 'clientes') {
                return $this->importarClientes($datos);
            } elseif ($modulo === 'ventas') {
                return $this->importarVentas($datos);
            } elseif ($modulo === 'empleados') {
                return $this->procesarImportarEmpleados($datos);
            } elseif ($modulo === 'productos') {
                return $this->procesarImportarProductos($datos);
            }

            return response()->json([
                'success' => false,
                'mensaje' => 'Módulo no válido'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Error general: ' . $e->getMessage()
            ], 500);
        }
    }

    // ===============================
    // MÉTODOS PÚBLICOS PARA EMPLEADOS
    // ===============================
    public function importarEmpleados(Request $request)
    {
        try {
            $datos = $request->input('datos', []);
            
            if (empty($datos)) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No se recibieron datos para importar'
                ]);
            }

            return $this->procesarImportarEmpleados($datos);
        } catch (\Exception $e) {
            Log::error('Error en importarEmpleados: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al importar empleados: ' . $e->getMessage()
            ], 500);
        }
    }

    public function iniciarEmpleados(Request $request)
    {
        try {
            $ids = DB::table('Empleados')->pluck('Documento_Empleado')->toArray();
            $loteSize = $request->input('loteSize', 20);

            session([
                'export_modulo' => 'empleados',
                'export_ids'    => $ids,
                'lote_actual'   => 0,
                'lote_size'     => $loteSize
            ]);

            return response()->json([
                'success'         => true,
                'total_registros' => count($ids),
                'lote_size'       => $loteSize
            ]);
        } catch (\Exception $e) {
            Log::error('Error en iniciarEmpleados: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al iniciar exportación: ' . $e->getMessage()
            ], 500);
        }
    }

    public function loteEmpleados(Request $request)
    {
        try {
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

            $datos = $this->exportarEmpleados($idsLote);

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

        } catch (\Exception $e) {
            Log::error('Error en loteEmpleados: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'mensaje' => 'Error en exportación: ' . $e->getMessage()
            ], 500);
        }
    }

    // ===============================
    // MÉTODOS PÚBLICOS PARA PRODUCTOS
    // ===============================
    public function importarProductos(Request $request)
    {
        try {
            $datos = $request->input('datos', []);
            
            if (empty($datos)) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No se recibieron datos para importar'
                ]);
            }

            return $this->procesarImportarProductos($datos);
        } catch (\Exception $e) {
            Log::error('Error en importarProductos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al importar productos: ' . $e->getMessage()
            ], 500);
        }
    }

    public function iniciarProductos(Request $request)
    {
        try {
            // Obtener productos desde la API Spring usando el servicio
            $productosService = app(\App\Services\ProductosService::class);
            $productos = $productosService->obtenerProductos();

            if ($productos === null || !is_array($productos)) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No se pudieron obtener los productos de la API'
                ]);
            }

            // Extraer los IDs
            $ids = array_map(function($p) {
                return $p['ID_Producto'] ?? null;
            }, $productos);
            
            $ids = array_filter($ids); // Eliminar nulls
            $loteSize = $request->input('loteSize', 20);

            session([
                'export_modulo_productos' => 'productos',
                'export_ids_productos'    => $ids,
                'lote_actual_productos'   => 0,
                'lote_size_productos'     => $loteSize
            ]);

            return response()->json([
                'success'         => true,
                'total_registros' => count($ids),
                'lote_size'       => $loteSize
            ]);
        } catch (\Exception $e) {
            Log::error('Error en iniciarProductos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al iniciar exportación: ' . $e->getMessage()
            ], 500);
        }
    }

    public function loteProductos(Request $request)
    {
        try {
            $ids        = session('export_ids_productos', []);
            $loteActual = session('lote_actual_productos', 0);
            $loteSize   = session('lote_size_productos', 20);
            $totalIds   = count($ids);

            if ($totalIds === 0) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No hay IDs en sesión. Inicie la exportación primero.'
                ]);
            }

            $inicio  = $loteActual * $loteSize;
            $idsLote = array_slice($ids, $inicio, $loteSize);

            $datos = $this->exportarProductos($idsLote);

            $registrosMigrados = $inicio + count($idsLote);
            $completado        = $registrosMigrados >= $totalIds;

            session(['lote_actual_productos' => $loteActual + 1]);

            return response()->json([
                'success'            => true,
                'datos'              => $datos,
                'progreso'           => intval(($registrosMigrados / $totalIds) * 100),
                'registros_migrados' => $registrosMigrados,
                'total_registros'    => $totalIds,
                'lote_actual'        => $loteActual + 1,
                'completado'         => $completado
            ]);

        } catch (\Exception $e) {
            Log::error('Error en loteProductos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'mensaje' => 'Error en exportación: ' . $e->getMessage()
            ], 500);
        }
    }

    // ===============================
    // INICIAR EXPORTACIÓN GENERAL
    // ===============================
    public function iniciar(Request $request)
    {
        try {
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
            } elseif ($modulo === 'ventas') {
                $ids = DB::table('ventas')->pluck('ID_Venta')->toArray();
            } elseif ($modulo === 'empleados') {
                $ids = DB::table('Empleados')->pluck('Documento_Empleado')->toArray();
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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al iniciar exportación: ' . $e->getMessage()
            ], 500);
        }
    }

    // ===============================
    // LOTE DE EXPORTACIÓN GENERAL
    // ===============================
    public function lote(Request $request)
    {
        try {
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

            $datos = null;

            if ($modulo === 'empleados') {
                $datos = $this->exportarEmpleados($idsLote);
            } elseif ($modulo === 'devoluciones') {
                $datos = $this->exportarDevoluciones($idsLote);
            } elseif ($modulo === 'compras') {
                $datos = $this->exportarCompras($idsLote);
            } elseif ($modulo === 'proveedores') {
                $datos = $this->exportarProveedores($idsLote);
            } elseif ($modulo === 'clientes') {
                $datos = $this->exportarClientes($idsLote);
            } elseif ($modulo === 'ventas') {
                $datos = $this->exportarVentas($idsLote);
            } else {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'Módulo no válido: ' . $modulo
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

        } catch (\Exception $e) {
            Log::error('Error en lote(): ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'mensaje' => 'Error en exportación: ' . $e->getMessage()
            ], 500);
        }
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
                $fecha = date('Y-m-d');
                if (!empty($fila['Fecha_Devolucion'])) {
                    if (is_numeric($fila['Fecha_Devolucion'])) {
                        $excelEpoch = 25569;
                        $unixTimestamp = ($fila['Fecha_Devolucion'] - $excelEpoch) * 86400;
                        $fecha = date('Y-m-d', $unixTimestamp);
                    } else {
                        $timestamp = strtotime($fila['Fecha_Devolucion']);
                        $fecha = ($timestamp !== false) ? date('Y-m-d', $timestamp) : date('Y-m-d');
                    }
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
                $precio = floatval($fila['Precio_Compra'] ?? 0);
                
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

                DB::table('compras')->insert([
                    'Precio_Compra'      => $precio,
                    'ID_Producto'        => $producto->ID_Producto,
                    'Documento_Empleado' => $documentoEmpleado,
                ]);

                $idEntrada = DB::getPdo()->lastInsertId();

                if (empty($idEntrada)) {
                    throw new \Exception("No se pudo obtener el ID de la compra recién insertada.");
                }

                $detalles = (isset($fila['detalles']) && is_array($fila['detalles']))
                    ? $fila['detalles']
                    : [];

                foreach ($detalles as $det) {
                    if (empty($det['Nombre_Proveedor']) || !isset($det['Cantidad'])) {
                        continue;
                    }

                    $proveedor = DB::table('Proveedores')
                        ->where('Nombre_Proveedor', $det['Nombre_Proveedor'])
                        ->first();
                    
                    if (!$proveedor) {
                        throw new \Exception("Proveedor '{$det['Nombre_Proveedor']}' no encontrado.");
                    }

                    $fechaEntrada = date('Y-m-d');
                    if (!empty($det['Fecha_Entrada'])) {
                        if (is_numeric($det['Fecha_Entrada'])) {
                            $excelEpoch = 25569;
                            $unixTimestamp = ($det['Fecha_Entrada'] - $excelEpoch) * 86400;
                            $fechaEntrada = date('Y-m-d', $unixTimestamp);
                        } else {
                            $timestamp = strtotime($det['Fecha_Entrada']);
                            $fechaEntrada = ($timestamp !== false) ? date('Y-m-d', $timestamp) : date('Y-m-d');
                        }
                    }

                    DB::table('detalle_compras')->insert([
                        'ID_Entrada'    => $idEntrada,
                        'ID_Proveedor'  => $proveedor->ID_Proveedor,
                        'Fecha_Entrada' => $fechaEntrada,
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
                
                $estadoTexto = $fila['Estado'] ?? $fila['ID_Estado'] ?? 'Activo';
                $estado = $this->convertirEstadoANumero($estadoTexto);

                if (empty($nombre)) {
                    $errores[] = "Fila " . ($index + 2) . ": Falta el nombre del proveedor";
                    continue;
                }

                $nombreExiste = DB::table('Proveedores')
                    ->where('Nombre_Proveedor', $nombre)
                    ->exists();
                
                if ($nombreExiste) {
                    $errores[] = "Fila " . ($index + 2) . ": El proveedor '{$nombre}' ya existe";
                    continue;
                }

                if (!empty($correo)) {
                    $correoExiste = DB::table('Proveedores')
                        ->where('Correo_Electronico', $correo)
                        ->exists();
                    
                    if ($correoExiste) {
                        $errores[] = "Fila " . ($index + 2) . ": El correo '{$correo}' ya está registrado";
                        continue;
                    }
                }

                if (!empty($telefono)) {
                    $telefonoExiste = DB::table('Proveedores')
                        ->where('Telefono', $telefono)
                        ->exists();
                    
                    if ($telefonoExiste) {
                        $errores[] = "Fila " . ($index + 2) . ": El teléfono '{$telefono}' ya está registrado";
                        continue;
                    }
                }

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
                
                $estadoTexto = $fila['Estado'] ?? $fila['ID_Estado'] ?? 'Activo';
                $estado = $this->convertirEstadoANumero($estadoTexto);

                if (empty($documento)) {
                    $errores[] = "Fila " . ($index + 2) . ": Falta el documento del cliente";
                    continue;
                }

                $documentoExiste = DB::table('clientes')
                    ->where('Documento_Cliente', $documento)
                    ->exists();
                
                if ($documentoExiste) {
                    $errores[] = "Fila " . ($index + 2) . ": El cliente con documento '{$documento}' ya existe";
                    continue;
                }

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
    // IMPORTAR VENTAS
    // ===============================
    private function importarVentas($datos)
    {
        $importados = 0;
        $errores = [];
        $clientesCreados = 0;
        $detallesCreados = 0;
        
        DB::beginTransaction();

        try {
            foreach ($datos as $index => $fila) {
                $documentoCliente = $fila['Documento_Cliente'] ?? null;
                $documentoEmpleado = $fila['Documento_Empleado'] ?? session('documento');

                if (empty($documentoCliente)) {
                    $errores[] = "Fila " . ($index + 2) . ": Falta el documento del cliente";
                    continue;
                }

                if (empty($documentoEmpleado)) {
                    $errores[] = "Fila " . ($index + 2) . ": Falta el documento del empleado";
                    continue;
                }

                $clienteExiste = DB::table('clientes')
                    ->where('Documento_Cliente', $documentoCliente)
                    ->exists();

                if (!$clienteExiste) {
                    $nombreCliente = $fila['Nombre_Cliente'] ?? 'Cliente';
                    $apellidoCliente = $fila['Apellido_Cliente'] ?? 'Importado';
                    $estadoTexto = $fila['Estado_Cliente'] ?? 'Activo';
                    $estadoCliente = $this->convertirEstadoANumero($estadoTexto);

                    DB::table('clientes')->insert([
                        'Documento_Cliente' => $documentoCliente,
                        'Nombre_Cliente'    => $nombreCliente,
                        'Apellido_Cliente'  => $apellidoCliente,
                        'ID_Estado'         => $estadoCliente,
                    ]);

                    $clientesCreados++;
                }

                DB::table('ventas')->insert([
                    'Documento_Cliente'  => $documentoCliente,
                    'Documento_Empleado' => $documentoEmpleado,
                ]);

                $idVenta = DB::getPdo()->lastInsertId();

                if (empty($idVenta)) {
                    throw new \Exception("No se pudo obtener el ID de la venta recién insertada.");
                }

                $detalles = (isset($fila['detalles']) && is_array($fila['detalles']))
                    ? $fila['detalles']
                    : [];

                foreach ($detalles as $det) {
                    if (empty($det['Nombre_Producto']) || !isset($det['Cantidad'])) {
                        continue;
                    }

                    $producto = DB::table('productos')
                        ->where('Nombre_Producto', $det['Nombre_Producto'])
                        ->first();
                    
                    if (!$producto) {
                        $errores[] = "Producto '{$det['Nombre_Producto']}' no encontrado";
                        continue;
                    }

                    $fechaSalida = date('Y-m-d');
                    if (!empty($det['Fecha_Salida'])) {
                        if (is_numeric($det['Fecha_Salida'])) {
                            $excelEpoch = 25569;
                            $unixTimestamp = ($det['Fecha_Salida'] - $excelEpoch) * 86400;
                            $fechaSalida = date('Y-m-d', $unixTimestamp);
                        } else {
                            $timestamp = strtotime($det['Fecha_Salida']);
                            $fechaSalida = ($timestamp !== false) ? date('Y-m-d', $timestamp) : date('Y-m-d');
                        }
                    }

                    DB::table('Detalle_Ventas')->insert([
                        'ID_Venta'     => $idVenta,
                        'ID_Producto'  => $producto->ID_Producto,
                        'Cantidad'     => $det['Cantidad'],
                        'Fecha_Salida' => $fechaSalida,
                    ]);

                    $detallesCreados++;
                }

                $importados++;
            }

            DB::commit();

            $mensaje = "Se importaron {$importados} ventas correctamente.";
            if ($clientesCreados > 0) {
                $mensaje .= " Se crearon {$clientesCreados} clientes nuevos.";
            }
            if ($detallesCreados > 0) {
                $mensaje .= " Se crearon {$detallesCreados} detalles de venta.";
            }
            if (!empty($errores)) {
                $mensaje .= " Errores encontrados: " . implode(", ", $errores);
            }

            return response()->json([
                'success'          => true,
                'importados'       => $importados,
                'clientesCreados'  => $clientesCreados,
                'detallesCreados'  => $detallesCreados,
                'errores'          => $errores,
                'mensaje'          => $mensaje
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
    // IMPORTAR EMPLEADOS (PROCESADOR INTERNO)
    // ===============================
    private function procesarImportarEmpleados($datos)
    {
        $importados = 0;
        $errores = [];
        
        DB::beginTransaction();

        try {
            foreach ($datos as $index => $fila) {
                $documento = $fila['Documento_Empleado'] ?? null;
                $tipoDocumento = $fila['Tipo_Documento'] ?? 'CC';
                $nombre = $fila['Nombre_Usuario'] ?? null;
                $apellido = $fila['Apellido_Usuario'] ?? null;
                $edad = $fila['Edad'] ?? '';
                $correo = $fila['Correo_Electronico'] ?? null;
                $telefono = $fila['Telefono'] ?? '';
                $genero = $fila['Genero'] ?? '';
                $fotos = $fila['Fotos'] ?? '';
                
                $rolTexto = $fila['Rol'] ?? $fila['ID_Rol'] ?? 'Empleado';
                $idRol = $this->convertirRolANumero($rolTexto);
                
                $estadoTexto = $fila['Estado'] ?? $fila['ID_Estado'] ?? 'Activo';
                $estado = $this->convertirEstadoANumero($estadoTexto);

                if (empty($documento)) {
                    $errores[] = "Fila " . ($index + 2) . ": Falta el documento del empleado";
                    continue;
                }

                $documentoExiste = DB::table('Empleados')
                    ->where('Documento_Empleado', $documento)
                    ->exists();
                
                if ($documentoExiste) {
                    $errores[] = "Fila " . ($index + 2) . ": El empleado con documento '{$documento}' ya existe";
                    continue;
                }

                if (!empty($correo)) {
                    $correoExiste = DB::table('Empleados')
                        ->where('Correo_Electronico', $correo)
                        ->exists();
                    
                    if ($correoExiste) {
                        $errores[] = "Fila " . ($index + 2) . ": El correo '{$correo}' ya está registrado";
                        continue;
                    }
                }

                DB::table('Empleados')->insert([
                    'Documento_Empleado' => $documento,
                    'Tipo_Documento'     => $tipoDocumento,
                    'Nombre_Usuario'     => $nombre,
                    'Apellido_Usuario'   => $apellido,
                    'Edad'               => $edad,
                    'Correo_Electronico' => $correo,
                    'Telefono'           => $telefono,
                    'Genero'             => $genero,
                    'ID_Rol'             => $idRol,
                    'ID_Estado'          => $estado,
                    'Fotos'              => $fotos,
                ]);
                
                $importados++;
            }

            DB::commit();

            $mensaje = "Se importaron {$importados} empleados correctamente.";
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
    // IMPORTAR PRODUCTOS (PROCESADOR INTERNO)
    // ===============================
    private function procesarImportarProductos($datos)
    {
        $importados = 0;
        $errores = [];
        
        try {
            $productosService = app(\App\Services\ProductosService::class);

            foreach ($datos as $index => $fila) {
                $nombreProducto = $fila['Nombre_Producto'] ?? null;
                
                if (empty($nombreProducto)) {
                    $errores[] = "Fila " . ($index + 2) . ": Falta el nombre del producto";
                    continue;
                }

                // Preparar datos para la API
                $dataProducto = [
                    'Nombre_Producto' => $nombreProducto,
                    'Descripcion'     => $fila['Descripcion'] ?? 'Sin descripción',
                    'Precio_Venta'    => floatval($fila['Precio_Venta'] ?? 0),
                    'Stock_Minimo'    => intval($fila['Stock_Minimo'] ?? 0),
                    'Fotos'           => $fila['Fotos'] ?? '',
                ];

                // Mapear categoría por nombre
                if (!empty($fila['Categoria'])) {
                    $catalogos = $productosService->obtenerCatalogos();
                    $categorias = $catalogos['categorias'] ?? [];
                    $idCategoria = array_search($fila['Categoria'], $categorias);
                    if ($idCategoria !== false) {
                        $dataProducto['ID_Categoria'] = intval($idCategoria);
                    }
                }

                // Mapear estado por nombre
                if (!empty($fila['Estado'])) {
                    $catalogos = $productosService->obtenerCatalogos();
                    $estados = $catalogos['estados'] ?? [];
                    $idEstado = array_search($fila['Estado'], $estados);
                    if ($idEstado !== false) {
                        $dataProducto['ID_Estado'] = intval($idEstado);
                    }
                }

                // Mapear gama por nombre
                if (!empty($fila['Gama'])) {
                    $catalogos = $productosService->obtenerCatalogos();
                    $gamas = $catalogos['gamas'] ?? [];
                    $idGama = array_search($fila['Gama'], $gamas);
                    if ($idGama !== false) {
                        $dataProducto['ID_Gama'] = intval($idGama);
                    }
                }

                // Enviar a la API
                $respuesta = $productosService->agregarProducto($dataProducto);

                if ($respuesta['success']) {
                    $importados++;
                } else {
                    $errores[] = "Fila " . ($index + 2) . ": Error al guardar en la API - " . ($respuesta['body'] ?? 'Error desconocido');
                }
            }

            $mensaje = "Se importaron {$importados} productos correctamente.";
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
            Log::error('Error al importar productos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al importar: ' . $e->getMessage()
            ]);
        }
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
                'Estado'             => $this->convertirEstadoATexto($prov->ID_Estado),
            ];
        });
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
                'Estado'            => $this->convertirEstadoATexto($cli->ID_Estado),
            ];
        });
    }

    // ===============================
    // EXPORTAR VENTAS
    // ===============================
    private function exportarVentas($idsLote)
    {
        $ventas = DB::table('ventas')
            ->whereIn('ID_Venta', $idsLote)
            ->get();

        return $ventas->map(function ($venta) {
            $cliente = DB::table('clientes')
                ->where('Documento_Cliente', $venta->Documento_Cliente)
                ->first();

            $empleado = DB::table('Empleados')
                ->where('Documento_Empleado', $venta->Documento_Empleado)
                ->first();

            $detalles = DB::table('Detalle_Ventas as dv')
                ->join('Productos as p', 'dv.ID_Producto', '=', 'p.ID_Producto')
                ->where('dv.ID_Venta', $venta->ID_Venta)
                ->select('p.Nombre_Producto', 'dv.Cantidad', 'dv.Fecha_Salida')
                ->get();

            return [
                'ID_Venta'           => $venta->ID_Venta,
                'Documento_Cliente'  => $venta->Documento_Cliente,
                'Nombre_Cliente'     => $cliente ? ($cliente->Nombre_Cliente . ' ' . $cliente->Apellido_Cliente) : 'N/A',
                'Documento_Empleado' => $venta->Documento_Empleado,
                'Nombre_Empleado'    => $empleado ? ($empleado->Nombre_Usuario . ' ' . $empleado->Apellido_Usuario) : 'N/A',
                'Total_Productos'    => $detalles->sum('Cantidad'),
                'detalles'           => $detalles->map(function($det) {
                    return [
                        'Producto'     => $det->Nombre_Producto,
                        'Cantidad'     => $det->Cantidad,
                        'Fecha_Salida' => $det->Fecha_Salida,
                    ];
                })->toArray()
            ];
        });
    }

    // ===============================
    // EXPORTAR EMPLEADOS
    // ===============================
    private function exportarEmpleados($idsLote)
    {
        try {
            $empleados = DB::table('Empleados')
                ->whereIn('Documento_Empleado', $idsLote)
                ->get();

            if ($empleados->isEmpty()) {
                return [];
            }

            $resultado = [];
            
            foreach ($empleados as $emp) {
                $resultado[] = [
                    'Documento_Empleado' => $emp->Documento_Empleado ?? '',
                    'Tipo_Documento'     => $emp->Tipo_Documento ?? 'CC',
                    'Nombre_Usuario'     => $emp->Nombre_Usuario ?? '',
                    'Apellido_Usuario'   => $emp->Apellido_Usuario ?? '',
                    'Edad'               => $emp->Edad ?? '',
                    'Correo_Electronico' => $emp->Correo_Electronico ?? '',
                    'Telefono'           => $emp->Telefono ?? '',
                    'Genero'             => $emp->Genero ?? '',
                    'Estado'             => $this->convertirEstadoATexto($emp->ID_Estado ?? 1),
                    'Rol'                => $this->convertirRolATexto($emp->ID_Rol ?? 2),
                    'Fotos'              => $emp->Fotos ?? '',
                ];
            }
            
            return $resultado;

        } catch (\Exception $e) {
            Log::error('Error en exportarEmpleados: ' . $e->getMessage());
            return [];
        }
    }

    // ===============================
    // EXPORTAR PRODUCTOS
    // ===============================
    private function exportarProductos($idsLote)
    {
        try {
            $productosService = app(\App\Services\ProductosService::class);
            $todosLosProductos = $productosService->obtenerProductos();

            if ($todosLosProductos === null || !is_array($todosLosProductos)) {
                Log::error('exportarProductos: No se pudieron obtener productos de la API');
                return [];
            }

            // Filtrar solo los productos del lote
            $productosFiltrados = array_filter($todosLosProductos, function($p) use ($idsLote) {
                return in_array($p['ID_Producto'], $idsLote);
            });

            $resultado = [];
            
            foreach ($productosFiltrados as $prod) {
                $resultado[] = [
                    'Nombre_Producto' => $prod['Nombre_Producto'] ?? '',
                    'Descripcion'     => $prod['Descripcion'] ?? '',
                    'Precio_Venta'    => $prod['Precio_Venta'] ?? '',
                    'Stock_Minimo'    => $prod['Stock_Minimo'] ?? '',
                    'Categoria'       => $prod['Categoria'] ?? '',
                    'Estado'          => $prod['Estado'] ?? '',
                    'Gama'            => $prod['Gama'] ?? '',
                    'Fotos'           => $prod['Fotos'] ?? '',
                ];
            }
            
            Log::info('exportarProductos: Exportados ' . count($resultado) . ' productos');
            return $resultado;

        } catch (\Exception $e) {
            Log::error('Error en exportarProductos: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return [];
        }
    }

    // ===============================
    // MÉTODOS DE BÚSQUEDA
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

    public function historial()
    {
        $migraciones = DB::table('migraciones')
            ->where('tipo', 'importacion')
            ->orderBy('id', 'desc')
            ->get();

        return view('migracion.historial', compact('migraciones'));
    }

    // ===============================
    // FUNCIONES AUXILIARES
    // ===============================
    private function convertirEstadoANumero($estadoTexto)
    {
        if (is_numeric($estadoTexto)) {
            return intval($estadoTexto);
        }

        $estadoTexto = strtolower(trim($estadoTexto));
        
        switch ($estadoTexto) {
            case 'activo':
            case 'active':
                return 1;
            case 'inactivo':
            case 'inactive':
                return 2;
            case 'en proceso':
            case 'pending':
                return 3;
            default:
                return 1;
        }
    }

    private function convertirEstadoATexto($estadoNumero)
    {
        switch (intval($estadoNumero)) {
            case 1:
                return 'Activo';
            case 2:
                return 'Inactivo';
            case 3:
                return 'En proceso';
            default:
                return 'Activo';
        }
    }

    private function convertirRolANumero($rolTexto)
    {
        if (is_numeric($rolTexto)) {
            return intval($rolTexto);
        }

        $rolTexto = strtolower(trim($rolTexto));
        
        switch ($rolTexto) {
            case 'administrador':
            case 'admin':
            case 'administrator':
                return 1;
            case 'empleado':
            case 'employee':
            case 'trabajador':
                return 2;
            case 'supervisor':
                return 3;
            default:
                return 2;
        }
    }

    private function convertirRolATexto($rolNumero)
    {
        switch (intval($rolNumero)) {
            case 1:
                return 'Administrador';
            case 2:
                return 'Empleado';
            case 3:
                return 'Supervisor';
            default:
                return 'Empleado';
        }
    }
}