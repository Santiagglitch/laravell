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
    // IMPORTACIÓN UNIFICADA (clientes, proveedores, devoluciones, compras)
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

        switch ($modulo) {
            case 'devoluciones':
                return $this->importarDevoluciones($datos);
            case 'compras':
                return $this->importarCompras($datos);
            case 'proveedores':
                return $this->importarProveedores($datos);
            case 'clientes':
                return $this->importarClientes($datos);
            default:
                return response()->json([
                    'success' => false,
                    'mensaje' => 'Módulo no válido'
                ]);
        }
    }

    private function importarDevoluciones($datos)
    {
        $importados = 0;
        DB::beginTransaction();

        try {
            foreach ($datos as $fila) {
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

    private function importarProveedores($datos)
    {
        $importados = 0;
        $errores    = [];

        DB::beginTransaction();

        try {
            foreach ($datos as $index => $fila) {
                $nombre   = $fila['Nombre_Proveedor']   ?? null;
                $correo   = $fila['Correo_Electronico'] ?? null;
                $telefono = $fila['Telefono']            ?? null;
                $estado   = $this->resolverEstado($fila['ID_Estado'] ?? $fila['Estado'] ?? 1);

                if (empty($nombre)) {
                    $errores[] = "Fila " . ($index + 2) . ": Falta el nombre del proveedor";
                    continue;
                }

                if (DB::table('Proveedores')->where('Nombre_Proveedor', $nombre)->exists()) {
                    $errores[] = "Fila " . ($index + 2) . ": El proveedor '{$nombre}' ya existe";
                    continue;
                }

                if ($correo && DB::table('Proveedores')->where('Correo_Electronico', $correo)->exists()) {
                    $errores[] = "Fila " . ($index + 2) . ": El correo '{$correo}' ya está registrado";
                    continue;
                }

                if ($telefono && DB::table('Proveedores')->where('Telefono', $telefono)->exists()) {
                    $errores[] = "Fila " . ($index + 2) . ": El teléfono '{$telefono}' ya está registrado";
                    continue;
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

    private function importarClientes($datos)
    {
        $importados = 0;
        $errores    = [];

        DB::beginTransaction();

        try {
            foreach ($datos as $index => $fila) {
                $documento = $fila['Documento_Cliente'] ?? null;
                $nombre    = $fila['Nombre_Cliente']    ?? null;
                $apellido  = $fila['Apellido_Cliente']  ?? null;
                $estado    = $this->convertirEstadoCliente($fila['ID_Estado'] ?? $fila['Estado'] ?? 1);

                if (empty($documento)) {
                    $errores[] = "Fila " . ($index + 2) . ": Falta el documento del cliente";
                    continue;
                }

                if (DB::table('clientes')->where('Documento_Cliente', $documento)->exists()) {
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
    // ✅ IMPORTAR PRODUCTOS - ÚNICO MÉTODO EDITADO
    // ===============================
    public function importarProductos(Request $request)
    {
        try {
            Log::info('=== INICIO importarProductos ===');
            
            $modulo = $request->input('modulo');
            $datos  = $request->input('datos', []);

            if ($modulo !== 'productos') {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'Módulo incorrecto'
                ], 400);
            }

            if (empty($datos)) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No se recibieron datos'
                ], 400);
            }

            $importados = 0;
            $errores = [];

            DB::beginTransaction();

            foreach ($datos as $index => $fila) {
                try {
                    $filaNum = $index + 2;
                    
                    $categoria = $fila['Categoria'] ?? null;
                    $estado = $fila['Estado'] ?? null;
                    $gama = $fila['Gama'] ?? null;

                    $idCategoria = $this->resolverCategoria($categoria);
                    $idEstado = $this->resolverEstadoProducto($estado);
                    $idGama = $this->resolverGama($gama);

                    if (!$idCategoria) {
                        $errores[] = "Fila {$filaNum}: Categoría '{$categoria}' no encontrada";
                        continue;
                    }

                    if (!$idEstado) {
                        $errores[] = "Fila {$filaNum}: Estado '{$estado}' no encontrado";
                        continue;
                    }

                    if (!$idGama) {
                        $errores[] = "Fila {$filaNum}: Gama '{$gama}' no encontrada";
                        continue;
                    }

                    $nombreProducto = $fila['Nombre_Producto'] ?? 'Sin nombre';
                    
                    if (DB::table('productos')->where('Nombre_Producto', $nombreProducto)->exists()) {
                        $errores[] = "Fila {$filaNum}: Producto '{$nombreProducto}' ya existe";
                        continue;
                    }

                    DB::table('productos')->insert([
                        'Nombre_Producto' => $nombreProducto,
                        'Descripcion'     => $fila['Descripcion'] ?? 'Sin descripción',
                        'Precio_Venta'    => floatval($fila['Precio_Venta'] ?? 0),
                        'Stock_Minimo'    => intval($fila['Stock_Minimo'] ?? 0),
                        'ID_Categoria'    => $idCategoria,
                        'ID_Estado'       => $idEstado,
                        'ID_Gama'         => $idGama,
                        'Fotos'           => $fila['Fotos'] ?? '',
                    ]);

                    $importados++;

                } catch (\Exception $e) {
                    Log::error("Error en fila {$filaNum}: " . $e->getMessage());
                    $errores[] = "Fila {$filaNum}: " . $e->getMessage();
                }
            }

            DB::commit();

            $mensaje = "Se importaron {$importados} productos.";
            if (!empty($errores)) {
                $mensaje .= " " . count($errores) . " filas omitidas.";
            }

            return response()->json([
                'success'    => true,
                'importados' => $importados,
                'errores'    => $errores,
                'mensaje'    => $mensaje,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en importarProductos: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'mensaje' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function importarEmpleados(Request $request)
    {
        $modulo = $request->input('modulo');
        $datos  = $request->input('datos', []);

        if ($modulo !== 'empleados' || empty($datos)) {
            return response()->json([
                'success' => false,
                'mensaje' => 'No se recibió información válida'
            ]);
        }

        $importados = 0;
        $errores    = [];

        DB::beginTransaction();

        try {
            foreach ($datos as $index => $fila) {
                $documento  = $fila['Documento_Empleado'] ?? null;
                $tipoDoc    = $fila['Tipo_Documento']     ?? 'CC';
                $nombre     = $fila['Nombre_Usuario']     ?? null;
                $apellido   = $fila['Apellido_Usuario']   ?? null;
                $edad       = $fila['Edad']               ?? null;
                $correo     = $fila['Correo_Electronico'] ?? null;
                $telefono   = $fila['Telefono']           ?? null;
                $generoRaw  = $fila['Genero']             ?? 'M';
                $fotos      = $fila['Fotos']              ?? '';
                $contrasena = $fila['Contrasena']         ?? null;

                $idEstado = $this->resolverEstado($fila['Estado'] ?? $fila['ID_Estado'] ?? 1);
                $idRol = $this->resolverRol($fila['Rol'] ?? $fila['ID_Rol'] ?? 2);
                $genero = $this->resolverGenero($generoRaw);

                if (empty($documento)) {
                    $errores[] = "Fila " . ($index + 2) . ": Falta el documento del empleado";
                    continue;
                }

                if (DB::table('empleados')->where('Documento_Empleado', $documento)->exists()) {
                    $errores[] = "Fila " . ($index + 2) . ": El empleado con documento '{$documento}' ya existe";
                    continue;
                }

                if ($correo && DB::table('empleados')->where('Correo_Electronico', $correo)->exists()) {
                    $errores[] = "Fila " . ($index + 2) . ": El correo '{$correo}' ya está registrado";
                    continue;
                }

                if ($telefono && DB::table('empleados')->where('Telefono', $telefono)->exists()) {
                    $errores[] = "Fila " . ($index + 2) . ": El teléfono '{$telefono}' ya está registrado";
                    continue;
                }

                DB::table('Empleados')->insert([
                    'Documento_Empleado' => $documento,
                    'Tipo_Documento'     => $tipoDoc,
                    'Nombre_Usuario'     => $nombre,
                    'Apellido_Usuario'   => $apellido,
                    'Edad'               => $edad,
                    'Correo_Electronico' => $correo,
                    'Telefono'           => $telefono,
                    'Genero'             => $genero,
                    'ID_Estado'          => $idEstado,
                    'ID_Rol'             => $idRol,
                    'Fotos'              => $fotos,
                ]);

                $claveValida = (!empty($contrasena) && strtolower(trim($contrasena)) !== 'fotos')
                    ? $contrasena
                    : '1234';

                DB::table('Contrasenas')->insert([
                    'Documento_Empleado' => $documento,
                    'Contrasena_Hash'    => hash('sha256', $claveValida),
                ]);

                $importados++;
            }

            DB::commit();

            $mensaje = "Se importaron {$importados} empleados correctamente.";
            if (!empty($errores)) {
                $mensaje .= " Advertencias: " . implode(" | ", $errores);
            }

            return response()->json([
                'success'    => true,
                'importados' => $importados,
                'errores'    => $errores,
                'mensaje'    => $mensaje,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al importar: ' . $e->getMessage()
            ]);
        }
    }

    public function iniciar(Request $request)
    {
        $modulo   = $request->input('modulo');
        $loteSize = $request->input('loteSize', 20);

        $ids = [];

        switch ($modulo) {
            case 'devoluciones':
                $ids = Devolucion::pluck('ID_Devolucion')->toArray();
                break;
            case 'compras':
                $ids = Compras::pluck('ID_Entrada')->toArray();
                break;
            case 'proveedores':
                $ids = DB::table('Proveedores')->pluck('ID_Proveedor')->toArray();
                break;
            case 'clientes':
                $ids = DB::table('clientes')->pluck('Documento_Cliente')->toArray();
                break;
            default:
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

    // ✅ MÉTODOS DE PRODUCTOS EDITADOS
    public function iniciarProductos(Request $request)
    {
        try {
            $ids = DB::table('productos')->pluck('ID_Producto')->toArray();

            session([
                'export_productos_ids'  => $ids,
                'lote_productos_actual' => 0,
                'lote_productos_size'   => 20
            ]);

            return response()->json([
                'success'         => true,
                'total_registros' => count($ids),
                'lote_size'       => 20
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function iniciarEmpleados(Request $request)
    {
        $modulo   = $request->input('modulo');
        $loteSize = $request->input('loteSize', 20);

        if ($modulo !== 'empleados') {
            return response()->json([
                'success' => false,
                'mensaje' => 'Módulo incorrecto'
            ]);
        }

        $ids = DB::table('empleados')->pluck('Documento_Empleado')->toArray();

        session([
            'export_empleados_ids'  => $ids,
            'lote_empleados_actual' => 0,
            'lote_empleados_size'   => $loteSize,
        ]);

        return response()->json([
            'success'         => true,
            'total_registros' => count($ids),
            'lote_size'       => $loteSize,
        ]);
    }

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

        $datos = [];

        switch ($modulo) {
            case 'devoluciones':
                $datos = $this->exportarDevoluciones($idsLote);
                break;
            case 'compras':
                $datos = $this->exportarCompras($idsLote);
                break;
            case 'proveedores':
                $datos = $this->exportarProveedores($idsLote);
                break;
            case 'clientes':
                $datos = $this->exportarClientes($idsLote);
                break;
            default:
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

    // ✅ MÉTODO DE PRODUCTOS EDITADO
    public function loteProductos(Request $request)
    {
        try {
            $ids        = session('export_productos_ids', []);
            $loteActual = session('lote_productos_actual', 0);
            $loteSize   = session('lote_productos_size', 20);
            $totalIds   = count($ids);

            if ($totalIds === 0) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No hay datos'
                ], 400);
            }

            $inicio  = $loteActual * $loteSize;
            $idsLote = array_slice($ids, $inicio, $loteSize);

            $productos = DB::table('productos')
                ->whereIn('ID_Producto', $idsLote)
                ->get();

            $datos = $productos->map(function ($pro) {
                $categoria = DB::table('categorias')->where('ID_Categoria', $pro->ID_Categoria)->value('Nombre_Categoria');
                $estado = DB::table('estados')->where('ID_Estado', $pro->ID_Estado)->value('Nombre_Estado');
                $gama = DB::table('gamas')->where('ID_Gama', $pro->ID_Gama)->value('Nombre_Gama');

                return [
                    'Nombre_Producto' => $pro->Nombre_Producto,
                    'Descripcion'     => $pro->Descripcion,
                    'Precio_Venta'    => $pro->Precio_Venta,
                    'Stock_Minimo'    => $pro->Stock_Minimo,
                    'Categoria'       => $categoria ?? 'N/A',
                    'Estado'          => $estado ?? 'N/A',
                    'Gama'            => $gama ?? 'N/A',
                    'Fotos'           => $pro->Fotos ?? '',
                ];
            });

            $registrosMigrados = $inicio + count($productos);
            $completado        = $registrosMigrados >= $totalIds;

            session(['lote_productos_actual' => $loteActual + 1]);

            return response()->json([
                'success'            => true,
                'datos'              => $datos,
                'progreso'           => intval(($registrosMigrados / $totalIds) * 100),
                'registros_migrados' => $registrosMigrados,
                'total_registros'    => $totalIds,
                'lote_actual'        => $loteActual + 1,
                'completado'         => $completado
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function loteEmpleados(Request $request)
    {
        $ids        = session('export_empleados_ids', []);
        $loteActual = session('lote_empleados_actual', 0);
        $loteSize   = session('lote_empleados_size', 20);
        $totalIds   = count($ids);

        if ($totalIds === 0) {
            return response()->json([
                'success' => false,
                'mensaje' => 'No hay IDs en sesión. Inicie la exportación primero.'
            ]);
        }

        $inicio  = $loteActual * $loteSize;
        $idsLote = array_slice($ids, $inicio, $loteSize);

        $empleados = DB::table('empleados')
            ->whereIn('Documento_Empleado', $idsLote)
            ->get();

        $datos = $empleados->map(function ($emp) {
            $estado = DB::table('estados')->where('ID_Estado', $emp->ID_Estado)->value('Nombre_Estado');
            $rol    = DB::table('roles')->where('ID_Rol', $emp->ID_Rol)->value('Nombre');

            return [
                'Documento_Empleado' => $emp->Documento_Empleado,
                'Tipo_Documento'     => $emp->Tipo_Documento,
                'Nombre_Usuario'     => $emp->Nombre_Usuario,
                'Apellido_Usuario'   => $emp->Apellido_Usuario,
                'Edad'               => $emp->Edad,
                'Correo_Electronico' => $emp->Correo_Electronico,
                'Telefono'           => $emp->Telefono,
                'Genero'             => $emp->Genero,
                'Estado'             => $estado ?? 'N/A',
                'Rol'                => $rol    ?? 'N/A',
                'Fotos'              => $emp->Fotos ?? '',
            ];
        });

        $registrosMigrados = $inicio + count($empleados);
        $completado        = $registrosMigrados >= $totalIds;

        session(['lote_empleados_actual' => $loteActual + 1]);

        return response()->json([
            'success'            => true,
            'datos'              => $datos,
            'progreso'           => intval(($registrosMigrados / $totalIds) * 100),
            'registros_migrados' => $registrosMigrados,
            'total_registros'    => $totalIds,
            'lote_actual'        => $loteActual + 1,
            'completado'         => $completado,
        ]);
    }

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
    // ✅ HELPERS PARA PRODUCTOS (EDITADOS)
    // ===============================
    
    private function resolverCategoria($valor): ?int
    {
        if (empty($valor)) return null;
        
        if (is_numeric($valor)) {
            $existe = DB::table('categorias')->where('ID_Categoria', (int) $valor)->exists();
            return $existe ? (int) $valor : null;
        }

        $nombre = trim((string) $valor);
        
        $id = DB::table('categorias')
            ->where('Nombre_Categoria', $nombre)
            ->value('ID_Categoria');
        
        if ($id) return $id;

        $id = DB::table('categorias')
            ->whereRaw('LOWER(Nombre_Categoria) = ?', [strtolower($nombre)])
            ->value('ID_Categoria');
        
        if ($id) return $id;

        return DB::table('categorias')
            ->where('Nombre_Categoria', 'LIKE', "%{$nombre}%")
            ->value('ID_Categoria');
    }

    private function resolverEstadoProducto($valor): ?int
    {
        if (empty($valor)) return null;
        
        if (is_numeric($valor)) {
            $existe = DB::table('estados')->where('ID_Estado', (int) $valor)->exists();
            return $existe ? (int) $valor : null;
        }

        $nombre = mb_strtolower(trim((string) $valor));
        
        $mapa = [
            'activo'     => 1,
            'inactivo'   => 2,
            'en proceso' => 3,
        ];

        if (isset($mapa[$nombre])) {
            return $mapa[$nombre];
        }

        $id = DB::table('estados')
            ->whereRaw('LOWER(Nombre_Estado) = ?', [$nombre])
            ->value('ID_Estado');
        
        if ($id) return $id;

        return DB::table('estados')
            ->where('Nombre_Estado', 'LIKE', "%{$nombre}%")
            ->value('ID_Estado');
    }

    private function resolverGama($valor): ?int
    {
        if (empty($valor)) return null;
        
        if (is_numeric($valor)) {
            $existe = DB::table('gamas')->where('ID_Gama', (int) $valor)->exists();
            return $existe ? (int) $valor : null;
        }

        $nombre = trim((string) $valor);
        
        $id = DB::table('gamas')
            ->where('Nombre_Gama', $nombre)
            ->value('ID_Gama');
        
        if ($id) return $id;

        $id = DB::table('gamas')
            ->whereRaw('LOWER(Nombre_Gama) = ?', [strtolower($nombre)])
            ->value('ID_Gama');
        
        if ($id) return $id;

        return DB::table('gamas')
            ->where('Nombre_Gama', 'LIKE', "%{$nombre}%")
            ->value('ID_Gama');
    }

    // ===============================
    // OTROS HELPERS (SIN CAMBIOS)
    // ===============================

    private function convertirEstadoCliente($valor): int
    {
        if (is_numeric($valor)) {
            return (int) $valor;
        }

        $valorLower = mb_strtolower(trim((string) $valor));
        
        $mapa = [
            'activo'   => 1,
            'inactivo' => 2,
        ];

        return $mapa[$valorLower] ?? 1;
    }

    private function resolverEstado($valor): int
    {
        if (is_numeric($valor)) {
            return (int) $valor;
        }

        $valorLower = mb_strtolower(trim((string) $valor));
        
        $mapa = [
            'activo'     => 1,
            'inactivo'   => 2,
            'en proceso' => 3,
        ];

        return $mapa[$valorLower] ?? 1;
    }

    private function resolverRol($valor): int
    {
        if (is_numeric($valor)) {
            return (int) $valor;
        }

        $valorLower = mb_strtolower(trim((string) $valor));
        
        $mapa = [
            'administrador' => 1,
            'empleado'      => 2,
        ];

        return $mapa[$valorLower] ?? 2;
    }

    private function resolverGenero($valor): string
    {
        if (empty($valor)) return 'M';
        
        $valorUpper = strtoupper(trim((string) $valor));
        
        if (in_array($valorUpper, ['M', 'F'])) {
            return $valorUpper;
        }

        $valorLower = mb_strtolower(trim((string) $valor));
        
        if (str_starts_with($valorLower, 'm') || str_starts_with($valorLower, 'mas')) return 'M';
        if (str_starts_with($valorLower, 'f') || str_starts_with($valorLower, 'fem')) return 'F';

        return 'M';
    }
}
