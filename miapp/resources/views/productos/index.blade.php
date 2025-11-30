<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos</title>

    {{-- Bootstrap y Font Awesome --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    {{-- Tu CSS --}}
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
</head>
<body>
<div class="d-flex" style="min-height: 100vh;">

    {{-- BARRA LATERAL --}}
    <div class="barra-lateral d-flex flex-column flex-shrink-0 p-3 bg-primary text-white">
        <a class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            TECNICELL RM
        </a>
        <hr>
        <div class="menu-barra-lateral">
            <div class="seccion-menu">
                <a href="{{ route('admin.inicio') }}" class="elemento-menu">
                    <i class="fa-solid fa-tachometer-alt"></i><span>Dashboard</span>
                </a>
                <a href="#" class="elemento-menu">
                    <i class="fa-solid fa-shopping-cart"></i><span>Compras</span>
                </a>
                <a href="#" class="elemento-menu">
                    <i class="fa-solid fa-undo"></i><span>Devoluciones</span>
                </a>
                <a href="#" class="elemento-menu">
                    <i class="fa-solid fa-chart-line"></i><span>Ventas</span>
                </a>
            </div>
            <hr>
            <div class="seccion-menu">
                <a href="#" class="elemento-menu">
                    <i class="fa-solid fa-users"></i><span>Proveedores</span>
                </a>
                <a href="{{ route('productos.index') }}" class="elemento-menu active">
                    <i class="fa-solid fa-boxes"></i><span>Productos</span>
                </a>
            </div>
        </div>
    </div>

    {{-- CONTENIDO PRINCIPAL --}}
    <div class="contenido-principal flex-grow-1">

        {{-- NAVBAR --}}
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <a class="navbar-brand">Sistema gestión de inventarios</a>

                <div class="dropdown ms-auto">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle"
                       data-bs-toggle="dropdown">
                        <img src="{{ asset('php/fotos_empleados/686fe89fe865f_Foto Kevin.jpeg') }}"
                             width="32" height="32" class="rounded-circle me-2">
                        <strong>Perfil</strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                        <li><a class="dropdown-item" href="#">Mi perfil</a></li>
                        <li><a class="dropdown-item" href="#">Editar perfil</a></li>
                        <li><a class="dropdown-item" href="#">Registrarse</a></li>
                        <li><a class="dropdown-item" href="{{ route('logout') }}">Cerrar Sesión</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container py-4">
            <div class="d-flex align-items-center justify-content-center gap-3">
                <img src="{{ asset('Imagenes/Logo.webp') }}" style="height:48px;">
                <h1 class="m-0">Gestión de Productos</h1>
            </div>

            {{-- MENSAJE --}}
            @if (!empty($mensaje ?? null))
                <div class="alert alert-info mt-3">{{ $mensaje }}</div>
            @endif

            {{-- TABLA DE PRODUCTOS --}}
            @if (!empty($productos) && is_array($productos))
                <div class="table-responsive mt-4">
                    <table class="table table-bordered table-striped align-middle text-center">
                        <thead class="table-dark">
                        <tr>
                            <th>ID Producto</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Precio Venta</th>
                            <th>Stock Mínimo</th>
                            <th>Categoría</th>
                            <th>Estado</th>
                            <th>Gama</th>
                            <th>Foto</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($productos as $producto)
                            <tr>
                                <td>{{ $producto['ID_Producto'] }}</td>
                                <td>{{ $producto['Nombre_Producto'] }}</td>
                                <td>{{ $producto['Descripcion'] }}</td>
                                <td>${{ $producto['Precio_Venta'] }}</td>
                                <td>{{ $producto['Stock_Minimo'] }}</td>
                                <td>{{ $producto['ID_Categoria'] }}</td>
                                <td>{{ $producto['ID_Estado'] }}</td>
                                <td>{{ $producto['ID_Gama'] }}</td>

                                <td>
                                    @php
                                        $nombre = $producto['Nombre_Producto'] ?? 'Producto';
                                        $foto   = trim($producto['Fotos'] ?? '');

                                        if ($foto === '') {
                                            $src = 'https://via.placeholder.com/80x50?text='.urlencode($nombre);
                                        } elseif (str_starts_with($foto, 'data:image')) {
                                            $src = $foto;
                                        } elseif (preg_match('~^https?://~i', $foto)) {
                                            $src = $foto;
                                        } else {
                                            $src = asset(ltrim($foto, '/'));
                                        }
                                    @endphp

                                    <img src="{{ $src }}" style="width:80px; border-radius:4px;">
                                </td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>
            @else
                <p class="text-center text-muted mt-4">No hay productos disponibles.</p>
            @endif

            {{-- FORMULARIOS --}}
            <div class="row mt-5 g-4">

                {{-- AÑADIR --}}
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h2 class="h4 mb-3">Añadir Producto</h2>

                            <form method="POST" action="{{ route('productos.store') }}" class="row g-3">
                                @csrf

                                <input type="text" name="ID_Producto" placeholder="ID Producto" class="form-control" required>
                                <input type="text" name="Nombre_Producto" placeholder="Nombre" class="form-control" required>
                                <input type="text" name="Descripcion" placeholder="Descripción" class="form-control">
                                <input type="number" step="0.01" name="Precio_Venta" placeholder="Precio Venta" class="form-control">
                                <input type="number" name="Stock_Minimo" placeholder="Stock Mínimo" class="form-control">
                                <input type="text" name="ID_Categoria" placeholder="ID Categoría" class="form-control">
                                <input type="text" name="ID_Estado" placeholder="ID Estado" class="form-control">
                                <input type="text" name="ID_Gama" placeholder="ID Gama" class="form-control">
                                <input type="text" name="Fotos" placeholder="URL Foto" class="form-control">

                                <div class="col-12 text-center">
                                    <button class="btn btn-success">Guardar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- ACTUALIZAR --}}
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h2 class="h4 mb-3">Actualizar Producto</h2>

                            <form method="POST" action="{{ route('productos.update') }}" class="row g-3">
                                @csrf
                                @method('PUT')

                                <input type="text" name="ID_Producto" placeholder="ID Producto a actualizar" class="form-control" required>
                                <input type="text" name="Nombre_Producto" placeholder="Nuevo nombre" class="form-control">
                                <input type="text" name="Descripcion" placeholder="Nueva descripción" class="form-control">
                                <input type="number" step="0.01" name="Precio_Venta" placeholder="Nuevo precio" class="form-control">
                                <input type="number" name="Stock_Minimo" placeholder="Nuevo stock mínimo" class="form-control">
                                <input type="text" name="ID_Categoria" placeholder="Nueva categoría" class="form-control">
                                <input type="text" name="ID_Estado" placeholder="Nuevo estado" class="form-control">
                                <input type="text" name="ID_Gama" placeholder="Nueva gama" class="form-control">
                                <input type="text" name="Fotos" placeholder="Nueva foto" class="form-control">

                                <div class="col-12 text-center">
                                    <button class="btn btn-warning">Actualizar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- ELIMINAR --}}
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h2 class="h4 mb-3">Eliminar Producto</h2>

                            <form method="POST" action="{{ route('productos.destroy') }}" class="row g-3">
                                @csrf
                                @method('DELETE')

                                <input type="text" name="ID_Producto" placeholder="ID Producto a eliminar" class="form-control" required>

                                <div class="col-12 text-center">
                                    <button class="btn btn-danger">Eliminar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>

            <footer class="footer mt-5 text-center text-muted">
                <p>Copyright © 2025 Fonrio</p>
            </footer>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
