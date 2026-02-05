<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="{{ asset('Imagenes/Logo.webp') }}" type="image/webp">
    <title>Mi Perfil</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
</head>
<body>

<div class="d-flex" style="min-height: 100vh;">

    <div class="barra-lateral d-flex flex-column flex-shrink-0 p-3 bg-primary text-white">
        <a class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            TECNICELL RM <img src="{{ asset('Imagenes/Logo.webp') }}" style="height:48px;">
        </a>
        <hr>

        <div class="menu-barra-lateral">
            <div class="seccion-menu">
                <a href="{{ route('admin.inicio') }}" class="elemento-menu">
                    <i class="fa-solid fa-tachometer-alt"></i><span>Dashboard</span>
                </a>
                <a href="{{ route('compras.index') }}" class="elemento-menu">
                    <i class="ri-shopping-cart-2-line"></i><span>Compras</span>
                </a>
                <a href="{{ route('devolucion.index') }}" class="elemento-menu">
                    <i class="ri-arrow-go-back-line"></i><span>Devoluciones</span>
                </a>
                <a href="{{ route('ventas.index') }}" class="elemento-menu">
                    <i class="ri-price-tag-3-line"></i><span>Ventas</span>
                </a>
            </div>

            <hr>

            <div class="seccion-menu">
                <a href="{{ route('productos.index') }}" class="elemento-menu">
                    <i class="ri-box-3-line"></i><span>Productos</span>
                </a>
                <a href="{{ route('proveedor.index') }}" class="elemento-menu">
                    <i class="ri-truck-line"></i><span>Proveedores</span>
                </a>

                <div class="dropdown">
                    <a class="elemento-menu d-flex align-items-center text-white text-decoration-none dropdown-toggle"
                       href="#" id="rolesMenu" role="button" data-bs-toggle="dropdown">
                        <i class="ri-user-line"></i><span>Usuarios</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('clientes.index') }}">Cliente</a></li>
                        <li><a class="dropdown-item" href="{{ route('empleados.index') }}">Empleado</a></li>
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

                        <img
                            src="{{ session('foto') ?? asset('Imagenes/default-user.png') }}"
                            alt="Perfil"
                            width="32"
                            height="32"
                            class="rounded-circle me-2"
                        >

                        <strong>{{ session('nombre') ?? 'Perfil' }}</strong>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item" href="{{ route('perfil') }}">Mi perfil</a></li>
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

        <div class="container py-4">

            <div class="d-flex justify-content-center align-items-center gap-3">
                <img src="{{ asset('Imagenes/Logo.webp') }}" style="height:48px;">
                <h1>Mi Perfil</h1>
            </div>

            <div class="mt-4 p-4 bg-light rounded shadow-sm">

                <h3>Información del usuario</h3>

                <p><strong>Nombre:</strong> {{ $empleado->Nombre_Usuario }} {{ $empleado->Apellido_Usuario }}</p>
                <p><strong>Correo:</strong> {{ $empleado->Correo_Electronico }}</p>
                <p><strong>Edad:</strong> {{ $empleado->Edad }}</p>
                <p><strong>Teléfono:</strong> {{ $empleado->Telefono }}</p>
                <p><strong>Género:</strong> {{ $empleado->Genero }}</p>
                <p><strong>Tipo documento:</strong> {{ $empleado->Tipo_Documento }}</p>
                <p><strong>Documento:</strong> {{ $empleado->Documento_Empleado }}</p>
                <p><strong>Estado:</strong> {{ $empleado->ID_Estado }}</p>
                <p><strong>Rol:</strong> {{ $empleado->ID_Rol }}</p>

                @php
                    
                    $fotoGrande = $fotoUrl ?? session('foto');
                @endphp

                @if(!empty($fotoGrande))
                    <div class="mt-3">
                        <img src="{{ $fotoGrande }}" width="150" class="rounded shadow-sm" alt="Foto de perfil">
                    </div>
                @else
                    <div class="mt-3">
                        <img src="{{ asset('Imagenes/default-user.png') }}" width="150" class="rounded shadow-sm" alt="Foto de perfil">
                    </div>
                @endif

                <div class="mt-4 p-4 bg-light rounded shadow-sm">
                    <h3>Contraseña</h3>

                    <form action="{{ route('perfil.actualizarContrasena') }}" method="POST">
                        @csrf

                        <label>Nueva contraseña:</label>
                        <input
                            type="password"
                            name="nueva_contrasena"
                            class="form-control"
                            placeholder="********"
                            required
                        >

                        <button class="btn btn-primary mt-2">Actualizar contraseña</button>
                    </form>
                </div>

            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
