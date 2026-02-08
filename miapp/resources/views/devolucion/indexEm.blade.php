<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="{{ asset('Imagenes/Logo.webp') }}" type="image/webp">
    <title>Devoluciones</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
</head>
<body>

<div class="d-flex" style="min-height:100vh">

    <!-- BARRA LATERAL -->
    <div class="barra-lateral d-flex flex-column flex-shrink-0 p-3 bg-primary text-white">
        <a class="d-flex align-items-center mb-3 text-white text-decoration-none">
            TECNICELL RM <img src="{{ asset('Imagenes/Logo.webp') }}" style="height:48px;">
        </a>

        <hr>

        <div class="menu-barra-lateral">
            <div class="seccion-menu">
                <a href="{{ route('InicioE.index') }}" class="elemento-menu">
                    <i class="fa-solid fa-tachometer-alt"></i><span>Dashboard</span>
                </a>

                <a href="{{ route('ventas.indexEm') }}" class="elemento-menu">
                    <i class="ri-price-tag-3-line"></i><span>Ventas</span>
                </a>

                <a href="{{ route('devolucion.indexEm') }}" class="elemento-menu activo">
                    <i class="ri-arrow-go-back-line"></i><span>Devoluciones</span>
                </a>
            </div>

            <hr>

            <div class="seccion-menu">
                <a href="{{ route('productos.indexEm') }}" class="elemento-menu">
                    <i class="ri-box-3-line"></i><span>Productos</span>
                </a>

                <a href="{{ route('clientes.indexEm') }}" class="elemento-menu">
                    <i class="ri-user-line"></i><span>Cliente</span>
                </a>
            </div>
        </div>
    </div>

    <!-- CONTENIDO -->
    <div class="contenido-principal flex-grow-1">

        <!-- NAV -->
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <a class="navbar-brand">Sistema gestión de inventarios</a>

                <div class="dropdown ms-auto">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle"
                       data-bs-toggle="dropdown">
                        <img src="{{ asset('fotos_empleados/686fe89fe865f_Foto Kevin.jpeg') }}"
                             width="32" height="32" class="rounded-circle me-2">
                        <strong>{{ session('nombre') ?? 'Perfil' }}</strong>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-dark">
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

        <!-- CONTENIDO -->
        <div class="container py-4">

            <div class="d-flex justify-content-center align-items-center gap-3">
                <img src="{{ asset('Imagenes/Logo.webp') }}" style="height:48px;">
                <h1>Registro de Devolución</h1>
            </div>

            @if(session('mensaje'))
                <div class="alert alert-success text-center mt-3">
                    {{ session('mensaje') }}
                </div>
            @endif

            <div class="d-flex justify-content-end mt-4 gap-2">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fa fa-plus"></i> Añadir Devolución
                </button>
                <a href="{{ route('detalledevolucion.indexEm') }}" class="btn btn-primary">
                    <i class="fa fa-list"></i> Detalle Devolución
                </a>
               

            </div>


            <!-- TABLA -->
            <div class="table-responsive mt-4">
                <table class="table table-bordered table-striped table-hover text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>ID Devolución</th>
                            <th>Fecha Devolución</th>
                            <th>Motivo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($devolucion as $dev)
                        <tr>
                            <td>{{ $dev->ID_Devolucion }}</td>
                            <td>{{ $dev->Fecha_Devolucion }}</td>
                            <td>{{ $dev->Motivo }}</td>
                            
                               <td>
    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
            data-bs-target="#editarModal{{ $dev->ID_Devolucion }}">
        <i class="fa fa-edit"></i>
    </button>

    <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
            data-bs-target="#eliminarModal{{ $dev->ID_Devolucion }}">
        <i class="fa fa-trash"></i>
    </button>
</td>

                            
                        </tr>

                        <!-- MODAL EDITAR (SIN FECHA) -->
                        <div class="modal fade" id="editarModal{{ $dev->ID_Devolucion }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('devolucion.updateEm') }}">
                                    @csrf
                                    @method('PUT')

                                    <input type="hidden" name="ID_Devolucion" value="{{ $dev->ID_Devolucion }}">
                                    <input type="hidden" name="Fecha_Devolucion" value="{{ $dev->Fecha_Devolucion }}">

                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Editar Devolución</h5>
                                            <button class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <label>Motivo</label>
                                            <input type="text"
                                                   name="Motivo"
                                                   class="form-control"
                                                   value="{{ $dev->Motivo }}"
                                                   required>
                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-warning">Actualizar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="4" class="text-muted">No hay devoluciones.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
<!-- MODAL ELIMINAR -->
<div class="modal fade" id="eliminarModal{{ $dev->ID_Devolucion }}">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('devolucion.destroy') }}">
            @csrf
            @method('DELETE')

            <input type="hidden" name="ID_Devolucion" value="{{ $dev->ID_Devolucion }}">

            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Eliminar Devolución</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    ¿Seguro que deseas eliminar esta Devolución?
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">
                        Eliminar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

            <!-- MODAL CREAR -->
            <div class="modal fade" id="crearModal">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('devolucion.storeEm') }}">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">Añadir Devolución</h5>
                                <button class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="date"
                                       name="Fecha_Devolucion"
                                       class="form-control"
                                       min="{{ date('Y-m-d', strtotime('-5 days')) }}"
                                       max="{{ date('Y-m-d') }}"
                                       required>

                                <label class="mt-3">Motivo</label>
                                <input name="Motivo" class="form-control" required>
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
