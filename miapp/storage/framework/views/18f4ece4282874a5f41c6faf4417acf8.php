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

    <!-- ===================== SIDEBAR ===================== -->
    <div class="barra-lateral d-flex flex-column flex-shrink-0 p-3 bg-primary text-white">
        <a class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            TECNICELL RM
            <img src="<?php echo e(asset('Imagenes/Logo.webp')); ?>" style="height:48px;">
        </a>
        <hr>

        <div class="menu-barra-lateral">

            <!-- SECCIÓN PRINCIPAL -->
            <div class="seccion-menu">

                <a href="<?php echo e(route('admin.inicio')); ?>"
                   class="elemento-menu <?php echo e(request()->routeIs('admin.inicio') ? 'activo' : ''); ?>">
                    <i class="fa-solid fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>

                <a href="<?php echo e(route('compras.index')); ?>"
                   class="elemento-menu <?php echo e(request()->routeIs('compras.*') ? 'activo' : ''); ?>">
                    <i class="ri-shopping-cart-2-line"></i>
                    <span>Compras</span>
                </a>

                <a href="<?php echo e(route('devolucion.index')); ?>"
                   class="elemento-menu <?php echo e(request()->routeIs('devolucion.*') ? 'activo' : ''); ?>">
                    <i class="ri-arrow-go-back-line"></i>
                    <span>Devoluciones</span>
                </a>

                <a href="<?php echo e(route('ventas.index')); ?>"
                   class="elemento-menu <?php echo e(request()->routeIs('ventas.*') ? 'activo' : ''); ?>">
                    <i class="ri-price-tag-3-line"></i>
                    <span>Ventas</span>
                </a>

                <!-- NUEVO MÓDULO AUDITORÍA -->
                <a href="<?php echo e(route('auditoria.index')); ?>"
                   class="elemento-menu <?php echo e(request()->routeIs('auditoria.*') ? 'activo' : ''); ?>">
                    <i class="ri-shield-check-line"></i>
                    <span>Auditoría</span>
                </a>

            </div>

            <hr>

            <!-- SECCIÓN ADMINISTRACIÓN -->
            <div class="seccion-menu">

                <a href="<?php echo e(route('productos.index')); ?>"
                   class="elemento-menu <?php echo e(request()->routeIs('productos.*') ? 'activo' : ''); ?>">
                    <i class="ri-box-3-line"></i>
                    <span>Productos</span>
                </a>

                <a href="<?php echo e(route('proveedor.index')); ?>"
                   class="elemento-menu <?php echo e(request()->routeIs('proveedor.*') ? 'activo' : ''); ?>">
                    <i class="ri-truck-line"></i>
                    <span>Proveedores</span>
                </a>

                <div class="dropdown">
                    <a class="elemento-menu d-flex align-items-center text-white text-decoration-none dropdown-toggle 
                       <?php echo e(request()->routeIs('clientes.*') || request()->routeIs('empleados.*') ? 'activo' : ''); ?>"
                       href="#" data-bs-toggle="dropdown">
                        <i class="ri-user-line"></i>
                        <span>Usuarios</span>
                    </a>

                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="<?php echo e(route('clientes.index')); ?>">
                                Cliente
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php echo e(route('empleados.index')); ?>">
                                Empleado
                            </a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
    <!-- ===================== FIN SIDEBAR ===================== -->


    <!-- ===================== CONTENIDO PRINCIPAL ===================== -->
    <div class="contenido-principal flex-grow-1">

        <!-- NAVBAR SUPERIOR -->
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

        <!-- DASHBOARD -->
        <div class="container py-4">
            <div class="row g-4">

                <!-- TARJETAS -->
                <div class="col-md-4">
                    <div class="card tarjeta-dashboard azul h-100 text-center">
                        <div class="icono-tarjeta-dashboard mx-auto mt-3">
                            <i class="ri-shopping-cart-2-line fw-bold"></i>
                        </div>
                        <div class="card-body">
                            <div class="titulo-tarjeta-dashboard">Compras</div>
                            <div class="numero-tarjeta-dashboard">3</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card tarjeta-dashboard verde-azul h-100 text-center">
                        <div class="icono-tarjeta-dashboard mx-auto mt-3">
                            <i class="ri-arrow-go-back-line fw-bold"></i>
                        </div>
                        <div class="card-body">
                            <div class="titulo-tarjeta-dashboard">Devoluciones</div>
                            <div class="numero-tarjeta-dashboard">2</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card tarjeta-dashboard naranja h-100 text-center">
                        <div class="icono-tarjeta-dashboard mx-auto mt-3">
                            <i class="ri-price-tag-3-line fw-bold"></i>
                        </div>
                        <div class="card-body">
                            <div class="titulo-tarjeta-dashboard">Ventas</div>
                            <div class="numero-tarjeta-dashboard">1</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card tarjeta-dashboard azul-oscuro h-100 text-center">
                        <div class="icono-tarjeta-dashboard mx-auto mt-3">
                            <i class="ri-truck-line fw-bold"></i>
                        </div>
                        <div class="card-body">
                            <div class="titulo-tarjeta-dashboard">Proveedores</div>
                            <div class="numero-tarjeta-dashboard">1</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card tarjeta-dashboard naranja-alternativo h-100 text-center">
                        <div class="icono-tarjeta-dashboard mx-auto mt-3">
                            <i class="ri-box-3-line fw-bold"></i>
                        </div>
                        <div class="card-body">
                            <div class="titulo-tarjeta-dashboard">Productos</div>
                            <div class="numero-tarjeta-dashboard">1</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card tarjeta-dashboard morado h-100 text-center">
                        <div class="icono-tarjeta-dashboard mx-auto mt-3">
                            <i class="ri-user-line fw-bold"></i>
                        </div>
                        <div class="card-body">
                            <div class="titulo-tarjeta-dashboard">Usuarios</div>
                            <div class="numero-tarjeta-dashboard">2</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
<div style="position: fixed; bottom: 10px; left: 0; width: 100%; text-align: center; margin-left: 115px;">
    <p style="color: #aaaaaa; font-size: 13px; margin: 0;">Copyright © 2026 Fonrio</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH C:\Users\rnico\laraveeeeee\miapp\resources\views/admin/inicio.blade.php ENDPATH**/ ?>