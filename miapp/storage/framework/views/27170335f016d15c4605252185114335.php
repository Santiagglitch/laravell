<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <link rel="icon" href="<?php echo e(asset('Imagenes/Logo.webp')); ?>" type="image/webp">
    <title>Auditoría</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('css/Inicio.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/menu.css')); ?>">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        .badge-op { font-weight: 700; }
        .op-insert { background:#198754 !important; }
        .op-update { background:#0d6efd !important; }
        .op-delete { background:#dc3545 !important; }

        .audit-card {
            border:0;
            border-radius:16px;
            box-shadow: 0 8px 20px rgba(0,0,0,.06);
        }

        .audit-kpi {
            border-radius:16px;
            border:0;
            box-shadow: 0 8px 20px rgba(0,0,0,.06);
        }

        .mono { font-family: ui-monospace, Consolas, monospace; }

        .field-line { margin-bottom:4px; }
        .field-label { font-weight:600; color:#6c757d; }
    </style>
</head>

<body>

<div class="d-flex" style="min-height: 100vh;">

    <div class="barra-lateral d-flex flex-column flex-shrink-0 p-3 bg-primary text-white">
        <a class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            TECNICELL RM <img src="<?php echo e(asset('Imagenes/Logo.webp')); ?>" style="height:48px;">
        </a>
        <hr>
        <div class="menu-barra-lateral">
            <div class="seccion-menu">
                <a href="<?php echo e(route('admin.inicio')); ?>" class="elemento-menu">
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
                <a href="<?php echo e(route('auditoria.index')); ?>"
                   class="elemento-menu <?php echo e(request()->routeIs('auditoria.*') ? 'activo' : ''); ?>">
                    <i class="ri-shield-check-line"></i>
                    <span>Auditoría</span>
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
                <div class="dropdown">
                    <a class="elemento-menu d-flex align-items-center text-white text-decoration-none dropdown-toggle activo"
                       href="#" data-bs-toggle="dropdown">
                        <i class="ri-user-line"></i><span>Usuarios</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item active" href="<?php echo e(route('clientes.index')); ?>">Cliente</a></li>
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
                       id="dropdownUser1" data-bs-toggle="dropdown">
                        <img src="<?php echo e(session('foto') ?? asset('Imagenes/default-user.png')); ?>"
                             width="32" height="32" class="rounded-circle me-2">
                        <strong><?php echo e(session('nombre') ?? 'Perfil'); ?></strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                        <li><a class="dropdown-item" href="<?php echo e(route('perfil')); ?>">Mi perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="<?php echo e(route('logout')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="dropdown-item">Cerrar sesión</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container py-4">

            <h3 class="mb-4">Auditoría de movimientos</h3>

            <!-- FICHAS -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card audit-kpi p-3">
                        <div class="text-muted">Registros hoy</div>
                        <div class="fs-4 fw-bold"><?php echo e($stats['hoy'] ?? 0); ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card audit-kpi p-3">
                        <div class="text-muted">Insertar</div>
                        <div class="fs-4 fw-bold"><?php echo e($stats['insert'] ?? 0); ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card audit-kpi p-3">
                        <div class="text-muted">Actualizar</div>
                        <div class="fs-4 fw-bold"><?php echo e($stats['update'] ?? 0); ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card audit-kpi p-3">
                        <div class="text-muted">Eliminar</div>
                        <div class="fs-4 fw-bold"><?php echo e($stats['delete'] ?? 0); ?></div>
                    </div>
                </div>
            </div>

            <!-- FILTROS -->
            <div class="card audit-card p-3 mb-3">
                <form method="GET" action="<?php echo e(route('auditoria.index')); ?>" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Tabla</label>
                        <select class="form-select" name="tabla">
                            <option value="">Todas</option>
                            <?php $__currentLoopData = ($tablas ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($t); ?>" <?php if(request('tabla') === $t): echo 'selected'; endif; ?>><?php echo e($t); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Operación</label>
                        <select class="form-select" name="op">
                            <option value="">Todas</option>
                            <option value="INSERT" <?php if(request('op') === 'INSERT'): echo 'selected'; endif; ?>>INSERT</option>
                            <option value="UPDATE" <?php if(request('op') === 'UPDATE'): echo 'selected'; endif; ?>>UPDATE</option>
                            <option value="DELETE" <?php if(request('op') === 'DELETE'): echo 'selected'; endif; ?>>DELETE</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Desde</label>
                        <input class="form-control" type="date" name="desde" value="<?php echo e(request('desde')); ?>">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Hasta</label>
                        <input class="form-control" type="date" name="hasta" value="<?php echo e(request('hasta')); ?>">
                    </div>

                    <div class="col-12 d-flex gap-2 mt-2">
                        <button class="btn btn-primary" type="submit">
                            <i class="ri-filter-3-line"></i> Filtrar
                        </button>
                        <a class="btn btn-outline-secondary" href="<?php echo e(route('auditoria.index')); ?>">
                            <i class="ri-close-circle-line"></i> Limpiar
                        </a>
                    </div>
                </form>
            </div>

            <!-- TABLA -->
            <div class="card audit-card">
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Operación</th>
                            <th>Tabla</th>
                            <th>Registro</th>
                            <th>Fecha</th>
                            <th>Antes</th>
                            <th>Después</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $auditorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="mono">#<?php echo e($a->ID_Auditoria); ?></td>

                                <td>
                                    <?php
                                        $op = strtoupper($a->Operacion);
                                        $cls = $op === 'INSERT' ? 'op-insert' :
                                               ($op === 'UPDATE' ? 'op-update' : 'op-delete');
                                    ?>
                                    <span class="badge badge-op <?php echo e($cls); ?>"><?php echo e($op); ?></span>
                                </td>

                                <td><?php echo e($a->Tabla_Afectada); ?></td>
                                <td class="mono"><?php echo e($a->ID_Registro); ?></td>
                                <td class="mono"><?php echo e($a->Fecha); ?></td>

                                <td><?php echo $a->Datos_Antes; ?></td>
                                <td><?php echo $a->Datos_Despues; ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    No hay registros de auditoría con los filtros actuales.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- ✅ PAGINACIÓN 1,2,3... (5 por página en el controller) -->
                <!-- PAGINACIÓN SOLO NÚMEROS -->
<div class="card-footer bg-white text-center">

    <div class="text-muted mb-2">
        Mostrando <?php echo e($auditorias->count()); ?> de <?php echo e($auditorias->total()); ?> registros.
    </div>

    <?php if($auditorias->lastPage() > 1): ?>
        <nav>
            <ul class="pagination justify-content-center mb-0">

                <?php for($i = 1; $i <= $auditorias->lastPage(); $i++): ?>
                    <li class="page-item <?php echo e($auditorias->currentPage() == $i ? 'active' : ''); ?>">
                        <a class="page-link"
                           href="<?php echo e($auditorias->url($i)); ?>">
                            <?php echo e($i); ?>

                        </a>
                    </li>
                <?php endfor; ?>

            </ul>
        </nav>
    <?php endif; ?>

</div>
            </div>

        </div>
    </div>
</div>
<div style="position: fixed; bottom: 10px; left: 0; width: 100%; text-align: center; margin-left: 115px;">
    <p style="color: #aaaaaa; font-size: 13px; margin: 0;">Copyright © 2026 Fonrio</p>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html><?php /**PATH C:\Users\rnico\laraveeeeee\miapp\resources\views/auditoria/index.blade.php ENDPATH**/ ?>