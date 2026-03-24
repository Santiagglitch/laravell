<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('Imagenes/Logo.webp') }}" type="image/webp">
    <title>Empleado</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link rel="stylesheet" href="{{ asset('css/Inicio.css') }}">
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>

<!-- Overlay oscuro al abrir sidebar -->
<div class="overlay-sidebar" id="overlay"></div>

<div class="d-flex" style="min-height:100vh">

    <!-- ===================== SIDEBAR ===================== -->
    <div class="barra-lateral d-flex flex-column flex-shrink-0 p-3 bg-primary text-white" id="sidebar">
        <a class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            TECNICELL RM <img src="{{ asset('Imagenes/Logo.webp') }}" style="height:48px;">
        </a>
        <hr>
        <div class="menu-barra-lateral">
            <div class="seccion-menu">
                <a href="{{ route('InicioE.index') }}"
                   class="elemento-menu {{ request()->routeIs('InicioE.*') ? 'activo' : '' }}">
                    <i class="fa-solid fa-tachometer-alt"></i><span>Dashboard</span>
                </a>
                <a href="{{ route('ventas.indexEm') }}"
                   class="elemento-menu {{ request()->routeIs('ventas.indexEm') ? 'activo' : '' }}">
                    <i class="ri-price-tag-3-line"></i><span>Ventas</span>
                </a>
                <a href="{{ route('devolucion.indexEm') }}"
                   class="elemento-menu {{ request()->routeIs('devolucion.indexEm') ? 'activo' : '' }}">
                    <i class="ri-arrow-go-back-line"></i><span>Devoluciones</span>
                </a>
            </div>
            <hr>
            <div class="seccion-menu">
                <a href="{{ route('productos.indexEm') }}"
                   class="elemento-menu {{ request()->routeIs('productos.indexEm') ? 'activo' : '' }}">
                    <i class="ri-box-3-line"></i><span>Productos</span>
                </a>
                <a href="{{ route('clientes.indexEm') }}"
                   class="elemento-menu {{ request()->routeIs('clientes.indexEm') ? 'activo' : '' }}">
                    <i class="ri-user-line"></i><span>Cliente</span>
                </a>
            </div>
        </div>
    </div>
    <!-- ===================== FIN SIDEBAR ===================== -->

    <!-- ===================== CONTENIDO PRINCIPAL ===================== -->
    <div class="contenido-principal flex-grow-1">

        <!-- NAVBAR SUPERIOR -->
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">

                <button class="btn-sidebar-toggle" id="btnToggleSidebar">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <a class="navbar-brand">Sistema gestión de inventarios</a>

                <div class="dropdown ms-auto">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle"
                       id="dropdownUser1" data-bs-toggle="dropdown">
                        <img src="{{ session('foto') ?? asset('Imagenes/default-user.png') }}"
                             width="32" height="32" class="rounded-circle me-2">
                        <strong>{{ session('nombre') ?? 'Perfil' }}</strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                        <li><a class="dropdown-item" href="{{ route('perfilEm') }}">Mi perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
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

                <div class="col-md-4">
                    <a href="{{ route('devolucion.indexEm') }}" style="text-decoration:none; color:inherit;">
                        <div class="card tarjeta-dashboard verde-azul h-100 text-center">
                            <div class="icono-tarjeta-dashboard mx-auto mt-3">
                                <i class="ri-arrow-go-back-line fw-bold"></i>
                            </div>
                            <div class="card-body">
                                <div class="titulo-tarjeta-dashboard">Devoluciones</div>
                                <div class="numero-tarjeta-dashboard">{{ $devoluciones }}</div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4">
                    <a href="{{ route('ventas.indexEm') }}" style="text-decoration:none; color:inherit;">
                        <div class="card tarjeta-dashboard naranja h-100 text-center">
                            <div class="icono-tarjeta-dashboard mx-auto mt-3">
                                <i class="ri-price-tag-3-line fw-bold"></i>
                            </div>
                            <div class="card-body">
                                <div class="titulo-tarjeta-dashboard">Ventas</div>
                                <div class="numero-tarjeta-dashboard">{{ $ventas }}</div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4">
                    <a href="{{ route('productos.indexEm') }}" style="text-decoration:none; color:inherit;">
                        <div class="card tarjeta-dashboard naranja-alternativo h-100 text-center">
                            <div class="icono-tarjeta-dashboard mx-auto mt-3">
                                <i class="ri-box-3-line fw-bold"></i>
                            </div>
                            <div class="card-body">
                                <div class="titulo-tarjeta-dashboard">Productos</div>
                                <div class="numero-tarjeta-dashboard">{{ $productos }}</div>
                            </div>
                        </div>
                    </a>
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
                    </a>
                </div>

            </div>
        </div>

    </div>
    <!-- ===================== FIN CONTENIDO PRINCIPAL ===================== -->

</div>

<div style="position: fixed; bottom: 10px; left: 0; width: 100%; text-align: center; margin-left: 115px;">
    <p style="color: #aaaaaa; font-size: 13px; margin: 0;">Copyright © 2026 Fonrio</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

<script>
    const btnToggle = document.getElementById('btnToggleSidebar');
    const sidebar   = document.getElementById('sidebar');
    const overlay   = document.getElementById('overlay');

    btnToggle.addEventListener('click', function () {
        sidebar.classList.toggle('abierto');
        overlay.classList.toggle('activo');
    });

    overlay.addEventListener('click', function () {
        sidebar.classList.remove('abierto');
        overlay.classList.remove('activo');
    });
</script>

</body>
</html>