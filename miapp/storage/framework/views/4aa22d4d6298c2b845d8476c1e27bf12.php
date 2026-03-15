<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <link rel="icon" href="<?php echo e(asset('Imagenes/Logo.webp')); ?>" type="image/webp">
    <title>Administrador</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link rel="stylesheet" href="<?php echo e(asset('css/Inicio.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/menu.css')); ?>">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>

<div class="d-flex" style="min-height:100vh">

    <div class="barra-lateral d-flex flex-column flex-shrink-0 p-3 bg-primary text-white">
        <a class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            TECNICELL RM <img src="<?php echo e(asset('Imagenes/Logo.webp')); ?>" style="height:48px;">
        </a>
        <hr>

        <div class="menu-barra-lateral">
            <div class="seccion-menu">
                <a href="<?php echo e(route('admin.inicio')); ?>" class="elemento-menu activo">
                    <i class="fa-solid fa-tachometer-alt"></i><span>Dashboard</span>
                </a>
                <a href="<?php echo e(route('compras.index')); ?>" class="elemento-menu">
                    <i class="ri-shopping-cart-2-line"></i><span>Compras</span>
                </a>
                <a href="<?php echo e(route('devolucion.index')); ?>" class="elemento-menu">
                    <i class="ri-arrow-go-back-line"></i><span>Devoluciones</span>
                </a>
                <a href="<?php echo e(route('ventas.index')); ?>" class="elemento-menu">
                    <i class="ri-price-tag-3-line"></i><span>Ventas</span>
                </a>
            </div>

            <hr>

            <div class="seccion-menu">
                <a href="<?php echo e(route('productos.index')); ?>" class="elemento-menu">
                    <i class="ri-box-3-line"></i><span>Productos</span>
                </a>

                <a href="<?php echo e(route('proveedor.index')); ?>" class="elemento-menu">
                    <i class="ri-truck-line"></i><span>Proveedores</span>
                </a>

                <a href="<?php echo e(route('migracion.historial')); ?>" class="elemento-menu">
                    <i class="fa fa-history"></i><span>Historial de Migraciones</span>
                </a>

                <div class="dropdown">
                    <a class="elemento-menu d-flex align-items-center text-white text-decoration-none dropdown-toggle"
                       href="#" data-bs-toggle="dropdown">
                        <i class="ri-user-line"></i><span>Usuarios</span>
                    </a>
                
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?php echo e(route('clientes.index')); ?>">Cliente</a></li>
                        <li><a class="dropdown-item" href="<?php echo e(route('empleados.index')); ?>">Empleado</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="contenido-principal flex-grow-1">

        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <a class="navbar-brand">Sistema gestión de inventarios</a>
                <div class="dropdown ms-auto">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle"
                       data-bs-toggle="dropdown">
                        <img src="<?php echo e(asset('fotos_empleados/686fe89fe865f_Foto Kevin.jpeg')); ?>"
                             width="32" height="32" class="rounded-circle me-2">
                        <strong><?php echo e(session('nombre') ?? 'Perfil'); ?></strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item">Mi perfil</a></li>
                        <li><a class="dropdown-item">Editar perfil</a></li>
                        <li><hr></li>
                        <li>
                            <form action="<?php echo e(route('logout')); ?>" method="POST"><?php echo csrf_field(); ?>
                                <button type="submit" class="dropdown-item">Cerrar sesión</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container py-5">
            <div class="d-flex justify-content-center align-items-center gap-3 mb-4">
                <img src="<?php echo e(asset('Imagenes/Logo.webp')); ?>" style="height:48px;">
                <h1>Historial de Migraciones</h1>
            </div>
            
            <div class="card shadow">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Módulo</th>
                                    <th>Tipo</th>
                                    <th>Total Registros</th>
                                    <th>Migrados</th>
                                    <th>Estado</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Duración</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $migraciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mig): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($mig->id); ?></td>

                                    <td>
                                        <span class="badge bg-primary">
                                            <?php echo e(strtoupper($mig->modulo)); ?>

                                        </span>
                                    </td>

                                    <td>
                                        <?php if($mig->tipo == 'importacion'): ?>
                                            <span class="badge bg-info">
                                                <i class="fa fa-file-import"></i> Importación
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-dark">
                                                <i class="fa fa-file-export"></i> Exportación
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td><?php echo e(number_format($mig->total_registros)); ?></td>
                                    <td><?php echo e(number_format($mig->registros_migrados)); ?></td>

                                    <td>
                                        <?php if($mig->estado == 'completado'): ?>
                                            <span class="badge bg-success">
                                                <i class="fa fa-check"></i> Completado
                                            </span>
                                        <?php elseif($mig->estado == 'error'): ?>
                                            <span class="badge bg-danger">
                                                <i class="fa fa-times"></i> Error
                                            </span>
                                        <?php elseif($mig->estado == 'en_proceso'): ?>
                                            <span class="badge bg-warning text-dark">
                                                <i class="fa fa-spinner fa-spin"></i> En proceso
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">
                                                <?php echo e(ucfirst($mig->estado)); ?>

                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if($mig->fecha_inicio): ?>
                                            <?php echo e(\Carbon\Carbon::parse($mig->fecha_inicio)->format('d/m/Y H:i:s')); ?>

                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if($mig->fecha_fin): ?>
                                            <?php echo e(\Carbon\Carbon::parse($mig->fecha_fin)->format('d/m/Y H:i:s')); ?>

                                        <?php else: ?>
                                            <span class="text-muted">En proceso</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if($mig->fecha_inicio && $mig->fecha_fin): ?>
                                            <?php echo e(\Carbon\Carbon::parse($mig->fecha_inicio)
                                                ->diffInSeconds(\Carbon\Carbon::parse($mig->fecha_fin))); ?> seg
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="fa fa-info-circle fa-2x mb-2"></i>
                                        <p class="mb-0">No hay historial de migraciones</p>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH C:\Users\rnico\laraveeeeee\miapp\resources\views/admin/migracion.blade.php ENDPATH**/ ?>