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

<div class="d-flex" style="min-height:100vh">

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
                    <i class="ri-box-3-line"></i>
                    <span>Productos</span>
                </a>

                <a href="{{ route('proveedor.index') }}" class="elemento-menu">
                    <i class="ri-truck-line"></i>
                    <span>Proveedores</span>
                </a>

                <div class="dropdown">
                    <a class="elemento-menu d-flex align-items-center text-white text-decoration-none dropdown-toggle activo"
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

    <div class="contenido-principal flex-grow-1">
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <a class="navbar-brand">Sistema gestión de inventarios</a>

                <div class="dropdown ms-auto">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle"
                       id="dropdownUser1" data-bs-toggle="dropdown">
                        <img src="{{ asset('fotos_empleados/686fe89fe865f_Foto Kevin.jpeg') }}"
                             width="32" height="32" class="rounded-circle me-2">
                        <strong>{{ session('nombre') ?? 'Perfil' }}</strong>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                        <li><a class="dropdown-item" href="#">Mi perfil</a></li>
                        <li><a class="dropdown-item" href="#">Editar perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="dropdown-item">Cerrar sesión</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container py-4">
            <div class="d-flex justify-content-center align-items-center gap-3">
                <img src="{{ asset('Imagenes/Logo.webp') }}" style="height:48px;">
                <h1>Registro de Empleado</h1>
            </div>

            @if(session('mensaje'))
                <div id="alertaMensaje" class="alert alert-success text-center">
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

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="text-end mb-3">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fa fa-plus"></i> Añadir Empleado
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Documento</th>
                            <th>Tipo Doc</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Edad</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Género</th>
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
                            <td>{{ $emp->estado->Nombre_Estado ?? 'Sin estado' }}</td>
                            <td>{{ $emp->rol->Nombre ?? 'Sin rol' }}</td>

                            <td>
                                @if($emp->Fotos)
                                    @php
                                        $springBase = rtrim(config('services.spring.base_url', 'http://192.168.80.13:8080'), '/');
                                        $foto = trim($emp->Fotos);
                                        $fotoUrl = str_starts_with($foto, 'http')
                                            ? $foto
                                            : (str_starts_with($foto, 'uploads/') ? $springBase.'/'.$foto : asset($foto));
                                    @endphp
                                    <img src="{{ $fotoUrl }}" width="50" class="rounded">
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editarModal{{ $emp->Documento_Empleado }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#eliminarModal{{ $emp->Documento_Empleado }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- MODAL EDITAR -->
                        <div class="modal fade" id="editarModal{{ $emp->Documento_Empleado }}">
                            <div class="modal-dialog modal-lg">
                                <form method="POST" action="{{ route('empleados.update') }}" enctype="multipart/form-data" autocomplete="off">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="Documento_Empleado" value="{{ $emp->Documento_Empleado }}">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning text-white">
                                            <h5 class="modal-title">Editar Empleado</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body row g-3">
                                            
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
                                                <label>Tipo de documento</label> 
                                                <select name="Tipo_Documento" class="form-control" required>
                                                    <option value="CC" {{ $emp->Tipo_Documento == 'CC' ? 'selected' : '' }}>Cédula de ciudadanía</option>
                                                    <option value="TI" {{ $emp->Tipo_Documento == 'TI' ? 'selected' : '' }}>Tarjeta de identidad</option>
                                                    <option value="CE" {{ $emp->Tipo_Documento == 'CE' ? 'selected' : '' }}>Cédula de extranjería</option>
                                                    <option value="PA" {{ $emp->Tipo_Documento == 'PA' ? 'selected' : '' }}>Pasaporte</option>
                                                    <option value="NIT" {{ $emp->Tipo_Documento == 'NIT' ? 'selected' : '' }}>NIT</option>
                                                </select>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label>Género</label>
                                                <select name="Genero" class="form-control" required>
                                                    <option value="F" {{ $emp->Genero=='F'?'selected':'' }}>Femenino</option>
                                                    <option value="M" {{ $emp->Genero=='M'?'selected':'' }}>Masculino</option>
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Estado</label>
                                                <select name="ID_Estado" class="form-control" required>
                                                    <option value="1" {{ (int)$emp->ID_Estado===1?'selected':'' }}>Activo</option>
                                                    <option value="2" {{ (int)$emp->ID_Estado===2?'selected':'' }}>Inactivo</option>
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Rol</label>
                                                <select name="ID_Rol" class="form-control" required>
                                                    <option value="1" {{ (int)$emp->ID_Rol===1?'selected':'' }}>Administrador</option>
                                                    <option value="2" {{ (int)$emp->ID_Rol===2?'selected':'' }}>Empleado</option>
                                                </select>
                                            </div>

                                            <div class="col-md-6 position-relative">
                                                <label>Nueva Contraseña</label>
                                                <input type="password" name="Contrasena" id="editarContrasena{{ $emp->Documento_Empleado }}" 
                                                       class="form-control" placeholder="Dejar en blanco para mantener la actual" 
                                                       autocomplete="new-password" value="" minlength="4">
                                                <i class="fa fa-eye position-absolute" style="top:38px; right:10px; cursor:pointer;"
                                                   onclick="togglePassword('editarContrasena{{ $emp->Documento_Empleado }}', this)"></i>
                                                <small class="text-muted">Mínimo 4 caracteres (dejar vacío para no cambiar)</small>
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

                        <!-- MODAL ELIMINAR -->
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
                                        <div class="modal-body">¿Seguro que deseas eliminar este empleado?</div>
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

            <!-- MODAL CREAR -->
            <div class="modal fade" id="crearModal">
                <div class="modal-dialog modal-lg">
                    <form method="POST" action="{{ route('empleados.store') }}" enctype="multipart/form-data" autocomplete="off">
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
                                    <label>Tipo de documento</label>
                                    <select name="Tipo_Documento" class="form-control" required>
                                        <option value="" selected disabled>--Seleccione--</option>
                                        <option value="CC">Cédula de ciudadanía</option>
                                        <option value="TI">Tarjeta de identidad</option>
                                        <option value="CE">Cédula de extranjería</option>
                                        <option value="PA">Pasaporte</option>
                                        <option value="NIT">NIT</option>
                                    </select>
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
                                    <input type="number" name="Edad" class="form-control" required>
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
                                    <select name="Genero" class="form-control" required>
                                        <option value="" selected disabled>--Seleccione--</option>
                                        <option value="F">Femenino</option>
                                        <option value="M">Masculino</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label>Estado</label>
                                    <select name="ID_Estado" class="form-control" required>
                                        <option value="" selected disabled>--Seleccione--</option>
                                        <option value="1">Activo</option>
                                        <option value="2">Inactivo</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label>Rol</label>
                                    <select name="ID_Rol" class="form-control" required>
                                        <option value="" selected disabled>--Seleccione--</option>
                                        <option value="1">Administrador</option>
                                        <option value="2">Empleado</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label>Foto</label>
                                    <input type="file" name="Fotos" class="form-control">
                                </div>
                                
                                <div class="col-md-6 position-relative">
                                    <label>Contraseña</label>
                                    <input type="password" name="Contrasena" id="contrasenaInput" 
                                           class="form-control" required minlength="4" 
                                           placeholder="Mínimo 4 caracteres">
                                    <i class="fa fa-eye position-absolute" id="toggleContrasena" style="top:38px; right:10px; cursor:pointer;"></i>
                                    <small class="text-muted">Debe tener al menos 4 caracteres</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-success" type="submit">Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const toggleContrasena = document.getElementById('toggleContrasena');
    const contrasenaInput = document.getElementById('contrasenaInput');

    if (toggleContrasena && contrasenaInput) {
        toggleContrasena.addEventListener('click', () => {
            contrasenaInput.type = contrasenaInput.type === 'password' ? 'text' : 'password';
            toggleContrasena.classList.toggle('fa-eye-slash');
        });
    }

    function togglePassword(idInput, icon) {
        const input = document.getElementById(idInput);
        if (input) {
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('fa-eye-slash');
        }
    }
</script>
</body>
</html>
