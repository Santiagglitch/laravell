<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="<?php echo e(asset('Imagenes/Logo.webp')); ?>" type="image/webp">
    <title>Detalle de Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('css/menu.css')); ?>">
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
                <a href="<?php echo e(route('admin.inicio')); ?>" class="elemento-menu">
                    <i class="fa-solid fa-tachometer-alt"></i><span>Dashboard</span>
                </a>
                <a href="<?php echo e(route('compras.index')); ?>" class="elemento-menu activo">
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
                    <a class="elemento-menu d-flex align-items-center text-white text-decoration-none dropdown-toggle"
                       data-bs-toggle="dropdown">
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

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div></div>
                <div class="d-flex align-items-center gap-3">
                    <img src="<?php echo e(asset('Imagenes/Logo.webp')); ?>" style="height:48px;">
                    <h1>Detalle de Compras</h1>
                </div>
                <a href="<?php echo e(route('compras.index')); ?>" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Volver a Compras
                </a>
            </div>

            <?php if(session('mensaje')): ?>
                <div id="alertaMensaje" class="alert alert-success text-center mt-3">
                    <?php echo e(session('mensaje')); ?>

                </div>
                <script>
                    setTimeout(() => {
                        let alerta = document.getElementById('alertaMensaje');
                        if (alerta) {
                            alerta.style.transition = "opacity 0.5s";
                            alerta.style.opacity = 0;
                            setTimeout(() => alerta.remove(), 500);
                        }
                    }, 2000);
                </script>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div id="alertaError" class="alert alert-danger text-center mt-3">
                    <?php echo e(session('error')); ?>

                </div>
                <script>
                    setTimeout(() => {
                        let alerta = document.getElementById('alertaError');
                        if (alerta) {
                            alerta.style.transition = "opacity 0.5s";
                            alerta.style.opacity = 0;
                            setTimeout(() => alerta.remove(), 500);
                        }
                    }, 2000);
                </script>
            <?php endif; ?>

            <div class="text-end mt-4">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fa fa-plus"></i> Añadir Detalle
                </button>
            </div>

            <div class="table-responsive mt-4">
                <table class="table table-bordered table-striped table-hover text-center">
                    <thead class="table-dark">
                    <tr>
                        <th>ID Entrada (Compra)</th>
                        <th>Fecha Entrada</th>
                        <th>Cantidad</th>
                        <th>Proveedor</th>
                        <th>Producto</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $detalles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detalle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($detalle->ID_Entrada); ?></td>
                            <td><?php echo e($detalle->Fecha_Entrada); ?></td>
                            <td><?php echo e($detalle->Cantidad); ?></td>
                            <td><?php echo e($detalle->proveedor->Nombre_Proveedor ?? 'N/A'); ?></td>
                            <td><?php echo e($detalle->compra->nombre_producto ?? 'N/A'); ?></td>

                            <td>
                                <!-- Botón editar -->
                                <button class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editarModal<?php echo e($detalle->ID_Proveedor); ?><?php echo e($detalle->ID_Entrada); ?>">
                                    <i class="fa fa-edit"></i>
                                </button>

                                <!-- Botón eliminar -->
                                <button class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#eliminarModal<?php echo e($detalle->ID_Proveedor); ?><?php echo e($detalle->ID_Entrada); ?>">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Editar -->
                        <div class="modal fade" id="editarModal<?php echo e($detalle->ID_Proveedor); ?><?php echo e($detalle->ID_Entrada); ?>">
                            <div class="modal-dialog">
                                <form method="POST" action="<?php echo e(route('detallecompras.update')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PUT'); ?>

                                    <input type="hidden" name="ID_Proveedor" value="<?php echo e($detalle->ID_Proveedor); ?>">
                                    <input type="hidden" name="ID_Entrada" value="<?php echo e($detalle->ID_Entrada); ?>">

                                    <div class="modal-content">

                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">
                                                <i class="fa fa-edit"></i> Editar Detalle
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">

                                            <div class="alert alert-info">
                                                <strong>ID Entrada:</strong> <?php echo e($detalle->ID_Entrada); ?> 
                                                <br>
                                                <strong>Proveedor:</strong> <?php echo e($detalle->proveedor->Nombre_Proveedor ?? 'N/A'); ?>

                                                <br>
                                                <strong>Producto:</strong> <?php echo e($detalle->compra->nombre_producto ?? 'N/A'); ?>

                                            </div>

                                            <label>Fecha Entrada</label>
                                            <input type="date" name="Fecha_Entrada" class="form-control mb-3"
                                                   value="<?php echo e($detalle->Fecha_Entrada); ?>"
                                                   min="<?php echo e(date('Y-m-d')); ?>" 
                                                   max="<?php echo e(date('Y-m-d')); ?>" required>

                                            <label>Cantidad</label>
                                            <input type="number" name="Cantidad" class="form-control" min="1"
                                                   value="<?php echo e($detalle->Cantidad); ?>" required>

                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-warning">
                                                <i class="fa fa-save"></i> Actualizar
                                            </button>
                                        </div>

                                    </div>

                                </form>
                            </div>
                        </div>

                        <!-- Modal Eliminar -->
                        <div class="modal fade" id="eliminarModal<?php echo e($detalle->ID_Proveedor); ?><?php echo e($detalle->ID_Entrada); ?>">
                            <div class="modal-dialog">
                                <form method="POST" action="<?php echo e(route('detallecompras.destroy')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>

                                    <input type="hidden" name="ID_Proveedor" value="<?php echo e($detalle->ID_Proveedor); ?>">
                                    <input type="hidden" name="ID_Entrada" value="<?php echo e($detalle->ID_Entrada); ?>">

                                    <div class="modal-content">

                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">
                                                <i class="fa fa-trash"></i> Eliminar Detalle
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <p>¿Seguro que deseas eliminar este detalle?</p>
                                            <div class="alert alert-warning">
                                                <strong>Fecha:</strong> <?php echo e($detalle->Fecha_Entrada); ?><br>
                                                <strong>Cantidad:</strong> <?php echo e($detalle->Cantidad); ?><br>
                                                <strong>Proveedor:</strong> <?php echo e($detalle->proveedor->Nombre_Proveedor ?? 'N/A'); ?><br>
                                                <strong>Producto:</strong> <?php echo e($detalle->compra->nombre_producto ?? 'N/A'); ?>

                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fa fa-trash"></i> Eliminar
                                            </button>
                                        </div>

                                    </div>

                                </form>
                            </div>
                        </div>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-muted">No hay detalles registrados.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>

                </table>
            </div>

            <!-- Modal Crear -->
            <div class="modal fade" id="crearModal">
                <div class="modal-dialog">
                    <form method="POST" action="<?php echo e(route('detallecompras.store')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="modal-content">

                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">
                                    <i class="fa fa-plus"></i> Añadir Detalle
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">

                                <label>Compra (ID Entrada)</label>
                                <select name="ID_Entrada" class="form-control mb-3" required>
                                    <option value="">Seleccione una compra</option>
                                    <?php $__currentLoopData = $compras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compra): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($compra->ID_Entrada); ?>">
                                            ID: <?php echo e($compra->ID_Entrada); ?> - <?php echo e($compra->nombre_producto); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>

                                <label>Proveedor</label>
                                <select name="ID_Proveedor" class="form-control mb-3" required>
                                    <option value="">Seleccione un proveedor</option>
                                    <?php $__currentLoopData = $proveedores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($prov->ID_Proveedor); ?>">
                                            <?php echo e($prov->Nombre_Proveedor); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>

                                <label>Fecha Entrada</label>
                                <input type="date" 
                                       name="Fecha_Entrada" 
                                       class="form-control mb-3" 
                                       value="<?php echo e(date('Y-m-d')); ?>"
                                       min="<?php echo e(date('Y-m-d')); ?>" 
                                       max="<?php echo e(date('Y-m-d')); ?>"
                                       readonly
                                       required>

                                <label>Cantidad</label>
                                <input type="number" name="Cantidad" class="form-control" min="1" required>

                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-save"></i> Guardar
                                </button>
                            </div>

                        </div>

                    </form>
                </div>
            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<div style="position: fixed; bottom: 10px; left: 0; width: 100%; text-align: center; margin-left: 115px;">
    <p style="color: #aaaaaa; font-size: 13px; margin: 0;">Copyright © 2026 Fonrio</p>
</div>
</body>
</html><?php /**PATH C:\Users\rnico\laraveeeeee\miapp\resources\views/detalle_compras/index.blade.php ENDPATH**/ ?>