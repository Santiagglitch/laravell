<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <link rel="icon" href="{{ asset('Imagenes/Logo.webp') }}" type="image/webp">
    <title>Empleado</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link rel="stylesheet" href="{{ asset('css/Inicio.css') }}">
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
</head>

<body>

<div class="d-flex" style="min-height:100vh">

   
  {{-- BARRA LATERAL --}}
    <div class="barra-lateral d-flex flex-column flex-shrink-0 p-3 bg-primary text-white">
        <a class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
            TECNICELL RM <img src="{{ asset('Imagenes/Logo.webp') }}" style="height:48px;">
        </a>

        <hr>

        <div class="menu-barra-lateral">

            <div class="seccion-menu">

                <!-- ✔ Dashboard -->
                <a href="{{ route('InicioE.index') }}" class="elemento-menu">
                    <i class="fa-solid fa-tachometer-alt"></i><span>Dashboard</span>
                </a>

                <!-- ✔ Ventas -->
                <a href="{{ route('ventas.indexEm') }}" class="elemento-menu activo">
                    <i class="ri-price-tag-3-line"></i><span>Ventas</span>
                </a>

                <!-- ✔ Devoluciones -->
                <a href="{{ route('devolucion.indexEm') }}" class="elemento-menu">
                    <i class="ri-arrow-go-back-line"></i><span>Devoluciones</span>
                </a>

            </div>

            <hr>

            <div class="seccion-menu">

                <!-- ✔ Productos -->
                 <a href="{{ route('productos.indexEm') }}" class="elemento-menu">
                    <i class="ri-box-3-line"></i>
                    <span>Productos</span>
                </a>

                <!-- ✔ Cliente -->
               <a href="{{ route('clientes.indexEm') }}" class="elemento-menu">
                    <i class="ri-user-line"></i>
                    <span>Cliente</span>
                </a>

            </div>

        </div>
    </div>

    {{-- CONTENIDO PRINCIPAL --}}
    <div class="contenido-principal flex-grow-1">

        {{-- NAVBAR SUPERIOR --}}
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">

                <a class="navbar-brand">Sistema gestión de inventarios</a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarNav" aria-controls="navbarNav"
                        aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav"></div>

                <div class="dropdown ms-auto">

                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle"
                       id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">

                        <img src="{{ asset('fotos_empleados/686fe89fe865f_Foto Kevin.jpeg') }}"
                             alt="Perfil" width="32" height="32" class="rounded-circle me-2">

                        <strong>{{ session('nombre') ?? 'Perfil' }}</strong>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                        <li><a class="dropdown-item" href="#">Mi perfil</a></li>
                        <li><a class="dropdown-item" href="#">Editar perfil</a></li>
                        <li><hr class="dropdown-divider"></li>

                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item">Cerrar sesión</button>
                            </form>
                        </li>
                    </ul>

                </div>
            </div>
        </nav>

        <div class="container py-4">

            {{-- TARJETAS DASHBOARD --}}
            <div class="container mt-4">
                <div class="row g-4">

                    <!-- ✔ Devoluciones -->
                       <div class="col-12 col-md-6 col-lg-4">
                    <div class="card tarjeta-dashboard verde-azul h-100 text-center">
                        <div class="icono-tarjeta-dashboard mx-auto mt-3 mb-2">
                            <i class="ri-arrow-go-back-line fw-bold"></i>
                        </div>
                        <div class="card-body py-2">
                            <div class="titulo-tarjeta-dashboard">Devoluciones</div>
                            <div class="numero-tarjeta-dashboard">2</div>
                        </div>
                    </div>
                </div>

                    <!-- ✔ Ventas -->
                    
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card tarjeta-dashboard naranja h-100 text-center">
                        <div class="icono-tarjeta-dashboard mx-auto mt-3 mb-2">
                            <i class="ri-price-tag-3-line fw-bold"></i>
                        </div>
                        <div class="card-body py-2">
                            <div class="titulo-tarjeta-dashboard">Ventas</div>
                            <div class="numero-tarjeta-dashboard">1</div>
                        </div>
                    </div>
                </div>

                    <!-- ✔ Productos -->
                    <div class="col-12 col-md-6 col-lg-4">
                    <div class="card tarjeta-dashboard naranja-alternativo h-100 text-center">
                        <div class="icono-tarjeta-dashboard mx-auto mt-3 mb-2">
                            <i class="ri-box-3-line fw-bold"></i>
                        </div>
                        <div class="card-body py-2">
                            <div class="titulo-tarjeta-dashboard">Productos</div>
                            <div class="numero-tarjeta-dashboard">1</div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card tarjeta-dashboard morado h-100 text-center">
                        <div class="icono-tarjeta-dashboard mx-auto mt-3 mb-2">
                            <i class="ri-user-line fw-bold"></i>
                        </div>
                        <div class="card-body py-2">
                            <div class="titulo-tarjeta-dashboard">Usuarios</div>
                            <div class="numero-tarjeta-dashboard">2</div>
                        </div>
                    </div>
                </div>

        </div> {{-- fin contenido-principal --}}
    </div>

</div> {{-- fin d-flex --}}

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
