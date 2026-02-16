<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <link rel="icon" href="{{ asset('Imagenes/Logo.webp') }}" type="image/webp">
    <title>Administrador</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link rel="stylesheet" href="{{ asset('css/Inicio.css') }}">
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>

<div class="d-flex" style="min-height:100vh">

    <!-- ===================== SIDEBAR ===================== -->
    <div class="barra-lateral d-flex flex-column flex-shrink-0 p-3 bg-primary text-white">
        <a class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            TECNICELL RM
            <img src="{{ asset('Imagenes/Logo.webp') }}" style="height:48px;">
        </a>
        <hr>

        <div class="menu-barra-lateral">

            <!-- SECCIÓN PRINCIPAL -->
            <div class="seccion-menu">

                <a href="{{ route('admin.inicio') }}"
                   class="elemento-menu {{ request()->routeIs('admin.inicio') ? 'activo' : '' }}">
                    <i class="fa-solid fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('compras.index') }}"
                   class="elemento-menu {{ request()->routeIs('compras.*') ? 'activo' : '' }}">
                    <i class="ri-shopping-cart-2-line"></i>
                    <span>Compras</span>
                </a>

                <a href="{{ route('devolucion.index') }}"
                   class="elemento-menu {{ request()->routeIs('devolucion.*') ? 'activo' : '' }}">
                    <i class="ri-arrow-go-back-line"></i>
                    <span>Devoluciones</span>
                </a>

                <a href="{{ route('ventas.index') }}"
                   class="elemento-menu {{ request()->routeIs('ventas.*') ? 'activo' : '' }}">
                    <i class="ri-price-tag-3-line"></i>
                    <span>Ventas</span>
                </a>

                <!-- NUEVO MÓDULO AUDITORÍA -->
                <a href="{{ route('auditoria.index') }}"
                   class="elemento-menu {{ request()->routeIs('auditoria.*') ? 'activo' : '' }}">
                    <i class="ri-shield-check-line"></i>
                    <span>Auditoría</span>
                </a>

            </div>

            <hr>

            <!-- SECCIÓN ADMINISTRACIÓN -->
            <div class="seccion-menu">

                <a href="{{ route('productos.index') }}"
                   class="elemento-menu {{ request()->routeIs('productos.*') ? 'activo' : '' }}">
                    <i class="ri-box-3-line"></i>
                    <span>Productos</span>
                </a>

                <a href="{{ route('proveedor.index') }}"
                   class="elemento-menu {{ request()->routeIs('proveedor.*') ? 'activo' : '' }}">
                    <i class="ri-truck-line"></i>
                    <span>Proveedores</span>
                </a>

                <div class="dropdown">
                    <a class="elemento-menu d-flex align-items-center text-white text-decoration-none dropdown-toggle 
                       {{ request()->routeIs('clientes.*') || request()->routeIs('empleados.*') ? 'activo' : '' }}"
                       href="#" data-bs-toggle="dropdown">
                        <i class="ri-user-line"></i>
                        <span>Usuarios</span>
                    </a>

                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('clientes.index') }}">
                                Cliente
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('empleados.index') }}">
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
                       data-bs-toggle="dropdown">
                        <img src="{{ session('foto') ?? asset('Imagenes/default-user.png') }}"
                             width="32"
                             height="32"
                             class="rounded-circle me-2"
                             alt="Perfil">
                        <strong>{{ session('nombre') ?? 'Perfil' }}</strong>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                        <li>
                            <a class="dropdown-item" href="{{ route('perfil') }}">
                                Mi perfil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    Cerrar sesión
                                </button>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
