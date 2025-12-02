<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <link rel="icon" href="{{ asset('Imagenes/Logo.webp') }}" type="image/webp">
    <title>Empleados</title>

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
            <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
            TECNICELL RM <img src="{{ asset('Imagenes/Logo.webp') }}" style="height:48px;">
        </a>
        <hr>
        <div class="menu-barra-lateral">
            <div class="seccion-menu">
                <a href="{{ route('admin.inicio') }}" class="elemento-menu">
                    <i class="fa-solid fa-tachometer-alt"></i><span>Dashboard</span>
                </a>
                <a href="" class="elemento-menu">
                    <i class="ri-shopping-cart-2-line"></i><span>Compras</span>
                </a>
                <a href="#" class="elemento-menu">
                    <i class="ri-arrow-go-back-line"></i><span>Devoluciones</span>
                </a>
                <a href="{{ route('ventas.index') }}" class="elemento-menu">
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

            {{-- TÍTULO --}}
            <div class="d-flex justify-content-center align-items-center gap-3">
                <img src="{{ asset('Imagenes/Logo.webp') }}" style="height:48px;">
                <h1>Registro de Empleados</h1>
            </div>

            {{-- MENSAJE --}}
               
            @if(session('mensaje'))
    <div id="alertaMensaje" class="alert alert-success text-center mt-3">
        {{ session('mensaje') }}
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
@endif

            {{-- BOTÓN CREAR --}}
            <div class="text-end mt-4">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fa fa-plus"></i> Añadir Empleado
                </button>
            </div>

            {{-- TABLA --}}
            <div class="table-responsive mt-4">
                <table class="table table-bordered table-hover table-striped align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Documento</th>
                            <th>Tipo Doc</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Edad</th>
                            <th>Correo</th>
                            <th>Telefono</th>
                            <th>Genero</th>
                            <th>Estado</th>
                            <th>Rol</th>
                            <th>Foto</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($empleados as $emp)
                        <tr>
                            <td>{{ $emp->Documento_Empleado }}</td>
                            <td>{{ $emp->Tipo_Documento }}</td>
                            <td>{{ $emp->Nombre_Usuario }}</td>
                            <td>{{ $emp->Apellido_Usuario }}</td>
                            <td>{{ $emp->Edad }}</td>
                            <td>{{ $emp->Correo_Electronico }}</td>
                            <td>{{ $emp->Telefono }}</td>
                            <td>{{ $emp->Genero }}</td>
                            <td>{{ $emp->ID_Estado }}</td>
                            <td>{{ $emp->ID_Rol }}</td>
                            <td><img src="{{ asset($emp->Fotos) }}" width="50" class="rounded"></td>

                            <td class="text-center">

                                {{-- BOTÓN EDITAR --}}
                                <button class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editarModal{{ $emp->Documento_Empleado }}">
                                    <i class="fa fa-edit"></i>
                                </button>

                                {{-- BOTÓN ELIMINAR --}}
                                <button class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#eliminarModal{{ $emp->Documento_Empleado }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- MODAL EDITAR --}}
                        <div class="modal fade" id="editarModal{{ $emp->Documento_Empleado }}">
                            <div class="modal-dialog modal-lg">
                                <form method="POST" action="{{ route('empleados.update') }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <input type="hidden" name="Documento_Empleado" value="{{ $emp->Documento_Empleado }}">

                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Editar Empleado</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body row g-3">

                                            <div class="col-md-6">
                                                <label>Tipo Documento</label>
                                                <input class="form-control" name="Tipo_Documento" value="{{ $emp->Tipo_Documento }}" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Nombre</label>
                                                <input class="form-control" name="Nombre_Usuario" value="{{ $emp->Nombre_Usuario }}" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Apellido</label>
                                                <input class="form-control" name="Apellido_Usuario" value="{{ $emp->Apellido_Usuario }}" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Edad</label>
                                                <input class="form-control" name="Edad" value="{{ $emp->Edad }}" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Correo</label>
                                                <input type="email" class="form-control" name="Correo_Electronico" value="{{ $emp->Correo_Electronico }}" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Teléfono</label>
                                                <input class="form-control" name="Telefono" value="{{ $emp->Telefono }}" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Género</label>
                                                <select name="Genero" class="form-control">
                                                    <option value="F" {{ $emp->Genero=='F'?'selected':'' }}>Femenino</option>
                                                    <option value="M" {{ $emp->Genero=='M'?'selected':'' }}>Masculino</option>
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Estado</label>
                                                <select name="ID_Estado" class="form-control">
                                                    <option value="EST001" {{ $emp->ID_Estado=='EST001'?'selected':'' }}>Activo</option>
                                                    <option value="EST002" {{ $emp->ID_Estado=='EST002'?'selected':'' }}>Inactivo</option>
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Rol</label>
                                                <select name="ID_Rol" class="form-control">
                                                    <option value="ROL001 " {{ $emp->ID_Rol=='ROL001'?'selected':'' }}>Administrador</option>
                                                    <option value="ROL002 " {{ $emp->ID_Rol=='ROL002'?'selected':'' }}>Empleado</option>
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Foto</label>
                                                <input type="file" name="Fotos" class="form-control">
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-warning" type="submit">Actualizar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- MODAL ELIMINAR --}}
                        <div class="modal fade" id="eliminarModal{{ $emp->Documento_Empleado }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('empleados.destroy') }}">
                                    @csrf
                                    @method('DELETE')

                                    <input type="hidden" name="Documento_Empleado" value="{{ $emp->Documento_Empleado }}">

                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Eliminar Empleado</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            ¿Seguro que deseas eliminar este empleado?
                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-danger" type="submit">Eliminar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    @empty
                        <tr><td colspan="12" class="text-center text-muted">No hay empleados registrados.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>


            {{-- MODAL CREAR --}}
            <div class="modal fade" id="crearModal">
                <div class="modal-dialog modal-lg">
                    <form method="POST" action="{{ route('empleados.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">Añadir Empleado</h5>
                                <button class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body row g-3">

                                <div class="col-md-6">
                                    <label>Documento</label>
                                    <input name="Documento_Empleado" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label>Tipo Documento</label>
                                    <input name="Tipo_Documento" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label>Nombre</label>
                                    <input name="Nombre_Usuario" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label>Apellido</label>
                                    <input name="Apellido_Usuario" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label>Edad</label>
                                    <input name="Edad" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label>Correo</label>
                                    <input type="email" name="Correo_Electronico" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label>Teléfono</label>
                                    <input name="Telefono" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label>Género</label>
                                    <select name="Genero" class="form-control">
                                        <option value="">--Seleccione--</option>
                                        <option value="F">Femenino</option>
                                        <option value="M">Masculino</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label>Estado</label>
                                    <select name="ID_Estado" class="form-control">
                                        <option value="">--Seleccione--</option>
                                        <option value="EST001">Activo</option>
                                        <option value="EST002">Inactivo</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label>Rol</label>
                                    <select name="ID_Rol" class="form-control">
                                        <option value="">--Seleccione--</option>
                                        <option value="ROL001">Administrador</option>
                                        <option value="ROL002">Empleado</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label>Foto</label>
                                    <input type="file" name="Fotos" class="form-control">
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-success" type="submit">Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div> {{-- cierre container --}}
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
