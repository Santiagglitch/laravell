<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <link rel="icon" href="{{ asset('Imagenes/Logo.webp') }}" type="image/webp">
    <title>Clientes</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
</head>

<body>

<div class="d-flex" style="min-height:100vh">


    <div class="barra-lateral d-flex flex-column flex-shrink-0 p-3 bg-primary text-white">
        <a class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
            TECNICELL RM <img src="{{ asset('Imagenes/Logo.webp') }}" style="height:48px;">
        </a>

        <hr>

        <div class="menu-barra-lateral">

            <div class="seccion-menu">

                
                <a href="{{ route('InicioE.index') }}" class="elemento-menu">
                    <i class="fa-solid fa-tachometer-alt"></i><span>Dashboard</span>
                </a>

             
                <a href="{{ route('ventas.indexEm') }}" class="elemento-menu activo">
                    <i class="ri-price-tag-3-line"></i><span>Ventas</span>
                </a>

                <a href="{{ route('devolucion.indexEm') }}" class="elemento-menu">
                    <i class="ri-arrow-go-back-line"></i><span>Devoluciones</span>
                </a>

            </div>

            <hr>

            <div class="seccion-menu">

                 <a href="{{ route('productos.indexEm') }}" class="elemento-menu">
                    <i class="ri-box-3-line"></i>
                    <span>Productos</span>
                </a>

           
               <a href="{{ route('clientes.indexEm') }}" class="elemento-menu">
                    <i class="ri-user-line"></i>
                    <span>Cliente</span>
                </a>

            </div>

        </div>
    </div>

    <div class="contenido-principal flex-grow-1">

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

            <div class="d-flex justify-content-center align-items-center gap-3">
                <img src="{{ asset('Imagenes/Logo.webp') }}" style="height:48px;">
                <h1>Registro de Clientes</h1>
            </div>

        
               
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

            <div class="text-end mt-4">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fa fa-plus"></i> Añadir Cliente
                </button>
            </div>

         
            <div class="table-responsive mt-4">
                <table class="table table-bordered table-striped table-hover align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Documento</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Teléfono</th>
                            <th>Fecha Nacimiento</th>
                            <th>Género</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($clientes as $cli)
                        <tr>
                            <td>{{ $cli->Documento_Cliente }}</td>
                            <td>{{ $cli->Nombre_Cliente }}</td>
                            <td>{{ $cli->Apellido_Cliente }}</td>
                            <td>{{ $cli->Telefono }}</td>
                            <td>{{ $cli->Fecha_Nacimiento }}</td>
                            <td>{{ $cli->Genero }}</td>
                            <td>{{ $cli->ID_Estado }}</td>

                            <td>
                              
                                <button class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editarModal{{ $cli->Documento_Cliente }}">
                                    <i class="fa fa-edit"></i>
                                </button>

                            
                                <button class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#eliminarModal{{ $cli->Documento_Cliente }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                  
                        <div class="modal fade" id="editarModal{{ $cli->Documento_Cliente }}">
                            <div class="modal-dialog modal-lg">
                                <form method="POST" action="{{ route('clientes.updateEm') }}">
                                    @csrf
                                    @method('PUT')

                                    <input type="hidden" name="Documento_Cliente" value="{{ $cli->Documento_Cliente }}">

                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Editar Cliente</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body row g-3">

                                            <div class="col-md-6">
                                                <label>Nombre</label>
                                                <input class="form-control" name="Nombre_Cliente" value="{{ $cli->Nombre_Cliente }}" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Apellido</label>
                                                <input class="form-control" name="Apellido_Cliente" value="{{ $cli->Apellido_Cliente }}" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Teléfono</label>
                                                <input class="form-control" name="Telefono" value="{{ $cli->Telefono }}" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Fecha Nacimiento</label>
                                                <input type="date" class="form-control" name="Fecha_Nacimiento" value="{{ $cli->Fecha_Nacimiento }}" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Género</label>
                                                <select class="form-control" name="Genero" required>
                                                    <option value="F" {{ $cli->Genero == 'F' ? 'selected' : '' }}>Femenino</option>
                                                    <option value="M" {{ $cli->Genero == 'M' ? 'selected' : '' }}>Masculino</option>
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Estado</label>
                                                <select name="ID_Estado" class="form-control">
                                                    <option value="EST001" {{ $cli->ID_Estado=='EST001'?'selected':'' }}>Activo</option>
                                                    <option value="EST002" {{ $cli->ID_Estado=='EST002'?'selected':'' }}>Inactivo</option>
                                                    <option value="EST003" {{ $cli->ID_Estado=='EST003'?'selected':'' }}>Suspendido</option>
                                                </select>
                                            </div>

                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-warning" type="submit">Actualizar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    
                        <div class="modal fade" id="eliminarModal{{ $cli->Documento_Cliente }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('clientes.destroyEm') }}">
                                    @csrf
                                    @method('DELETE')

                                    <input type="hidden" name="Documento_Cliente" value="{{ $cli->Documento_Cliente }}">

                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Eliminar Cliente</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            ¿Seguro que deseas eliminar este cliente?
                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-danger" type="submit">Eliminar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    @empty
                        <tr><td colspan="8" class="text-center text-muted">No hay clientes registrados.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

        
            <div class="modal fade" id="crearModal">
                <div class="modal-dialog modal-lg">
                    <form method="POST" action="{{ route('clientes.storeEm') }}">
                        @csrf

                        <div class="modal-content">

                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">Añadir Cliente</h5>
                                <button class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body row g-3">

                                <div class="col-md-6">
                                    <label>Documento</label>
                                    <input name="Documento_Cliente" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label>Nombre</label>
                                    <input name="Nombre_Cliente" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label>Apellido</label>
                                    <input name="Apellido_Cliente" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label>Teléfono</label>
                                    <input name="Telefono" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label>Fecha Nacimiento</label>
                                    <input type="date" name="Fecha_Nacimiento" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label>Género</label>
                                    <select name="Genero" class="form-control" required>
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
                                        <option value="EST003">Suspendido</option>
                                    </select>
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
</body>
</html>

