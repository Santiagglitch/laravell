<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Proveedores</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
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
                    <i class="ri-shopping-cart-2-line"></i><span>Compras</span>
                </a>
                <a href="#" class="elemento-menu">
                    <i class="ri-arrow-go-back-line"></i><span>Devoluciones</span>
                </a>
                <a href="#" class="elemento-menu">
                    <i class="ri-price-tag-3-line"></i><span>Ventas</span>
                </a>
            </div>
            <hr>
            <div class="seccion-menu">

                <a href="{{ route('productos.index') }}" class="elemento-menu">
                   <i class="ri-box-3-line"></i>
                    <span>Productos</span>
                </a>

                <a href="{{ route('proveedor.index') }}" class="elemento-menu activo">
                <i class="ri-truck-line"></i>
                    <span>Proveedores</span>
                </a>

                <div class="dropdown">
                    <a class="elemento-menu d-flex align-items-center text-white text-decoration-none dropdown-toggle"
                       href="#" id="rolesMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-user-line"></i><span>Usuarios</span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="rolesMenu">
                        <li><a class="dropdown-item" href="{{ route('clientes.index') }}">Cliente</a></li>
                        <li><a class="dropdown-item" href="{{ route('empleados.index') }}">Empleado</a></li>
                    </ul>
                </div>
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
                       id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ asset('fotos_empleados/686fe89fe865f_Foto Kevin.jpeg') }}"
                             alt="Perfil" width="32" height="32" class="rounded-circle me-2">
                        <strong>{{ session('nombre') ?? 'Perfil' }}</strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
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

            {{-- TÍTULO --}}
            <div class="d-flex justify-content-center align-items-center gap-3">
                <img src="{{ asset('Imagenes/Logo.webp') }}" style="height:48px;">
                <h1>Registro de Proveedores</h1>
            </div>

            {{-- MENSAJE --}}
            @if(session('mensaje'))
                <div class="alert alert-success text-center mt-3">{{ session('mensaje') }}</div>
            @endif

            {{-- BOTÓN CREAR --}}
            <div class="text-end mt-4">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fa fa-plus"></i> Añadir Proveedor
                </button>
            </div>

            {{-- TABLA --}}
            <div class="table-responsive mt-4">
                <table class="table table-bordered table-hover table-striped align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Telefono</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($proveedores as $prov)
                        <tr>
                            <td>{{ $prov->ID_Proveedor }}</td>
                            <td>{{ $prov->Nombre_Proveedor }}</td>
                            <td>{{ $prov->Correo_Electronico }}</td>
                            <td>{{ $prov->Telefono }}</td>
                            <td>{{ $prov->ID_Estado }}</td>

                            <td>
                                {{-- EDITAR --}}
                                <button class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editar{{ $prov->ID_Proveedor }}">
                                    <i class="fa fa-edit"></i>
                                </button>

                                {{-- ELIMINAR --}}
                                <button class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#eliminar{{ $prov->ID_Proveedor }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- MODAL EDITAR --}}
                        <div class="modal fade" id="editar{{ $prov->ID_Proveedor }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('proveedor.update') }}">
                                    @csrf
                                    @method('PUT')

                                    <input type="hidden" name="ID_Proveedor" value="{{ $prov->ID_Proveedor }}">

                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Editar Proveedor</h5>
                                            <button class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">

                                            <label>Nombre</label>
                                            <input class="form-control" name="Nombre_Proveedor"
                                                   value="{{ $prov->Nombre_Proveedor }}" required>

                                            <label>Correo</label>
                                            <input class="form-control" name="Correo_Electronico"
                                                   value="{{ $prov->Correo_Electronico }}" required>

                                            <label>Telefono</label>
                                            <input class="form-control" name="Telefono"
                                                   value="{{ $prov->Telefono }}" required>

                                            <label>Estado</label>
                                            <select name="ID_Estado" class="form-control">
                                                <option value="EST001" {{ $prov->ID_Estado=='EST001'?'selected':'' }}>Activo</option>
                                                <option value="EST002" {{ $prov->ID_Estado=='EST002'?'selected':'' }}>Inactivo</option>
                                            </select>

                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-warning">Actualizar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- MODAL ELIMINAR --}}
                        <div class="modal fade" id="eliminar{{ $prov->ID_Proveedor }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('proveedor.destroy') }}">
                                    @csrf
                                    @method('DELETE')

                                    <input type="hidden" name="ID_Proveedor" value="{{ $prov->ID_Proveedor }}">

                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">¿Eliminar proveedor?</h5>
                                            <button class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            Esta acción no se puede deshacer.
                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-danger">Eliminar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    @empty
                        <tr><td colspan="6" class="text-center text-muted">No hay proveedores registrados.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MODAL CREAR --}}
            <div class="modal fade" id="crearModal">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('proveedor.store') }}">
                        @csrf

                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">Añadir Proveedor</h5>
                                <button class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">

                                <label>ID</label>
                                <input class="form-control" name="ID_Proveedor" required>

                                <label>Nombre</label>
                                <input class="form-control" name="Nombre_Proveedor" required>

                                <label>Correo</label>
                                <input type="email" class="form-control" name="Correo_Electronico" required>

                                <label>Telefono</label>
                                <input class="form-control" name="Telefono" required>

                                <label>Estado</label>
                                <select class="form-control" name="ID_Estado">
                                    <option value="">--Seleccione--</option>
                                    <option value="EST001">Activo</option>
                                    <option value="EST002">Inactivo</option>
                                </select>

                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-success">Guardar</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
