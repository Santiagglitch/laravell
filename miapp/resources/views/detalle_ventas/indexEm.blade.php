<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <link rel="icon" href="{{ asset('Imagenes/Logo.webp') }}" type="image/webp">
    <title>Detalle de Ventas</title>

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


                 <a href="{{ route('productos.indexEm') }}"class="elemento-menu">
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

                <div class="dropdown ms-auto">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle"
                       data-bs-toggle="dropdown">

                        <img src="{{ asset('fotos_empleados/686fe89fe865f_Foto Kevin.jpeg') }}"
                             width="32" height="32" class="rounded-circle me-2">

                        <strong>{{ session('nombre') ?? 'Perfil' }}</strong>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                        <li><a class="dropdown-item">Mi perfil</a></li>
                        <li><a class="dropdown-item">Editar perfil</a></li>
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
                <h1>Detalle de Ventas</h1>
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
                    <i class="fa fa-plus"></i> Añadir Detalle
                </button>
            </div>

        
            <div class="table-responsive mt-4">
                <table class="table table-bordered table-striped table-hover text-center">
                    <thead class="table-dark">
                    <tr>
                        <th>Cantidad</th>
                        <th>Fecha Salida</th>
                        <th>ID Producto</th>
                        <th>ID Venta</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($detalles as $detalle)
                        <tr>
                            <td>{{ $detalle->Cantidad }}</td>
                            <td>{{ $detalle->Fecha_Salida }}</td>
                            <td>{{ $detalle->ID_Producto }}</td>
                            <td>{{ $detalle->ID_Venta }}</td>

                            <td>

                             
                                <button class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editarModal{{ $detalle->ID_Producto }}{{ $detalle->ID_Venta }}">
                                    <i class="fa fa-edit"></i>
                                </button>

                             
                                <button class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#eliminarModal{{ $detalle->ID_Producto }}{{ $detalle->ID_Venta }}">
                                    <i class="fa fa-trash"></i>
                                </button>

                            </td>
                        </tr>

                        <div class="modal fade"
                             id="editarModal{{ $detalle->ID_Producto }}{{ $detalle->ID_Venta }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('detalleventas.updateEm') }}">
                                    @csrf
                                    @method('PUT')

                                    <input type="hidden" name="ID_Producto" value="{{ $detalle->ID_Producto }}">
                                    <input type="hidden" name="ID_Venta" value="{{ $detalle->ID_Venta }}">

                                    <div class="modal-content">

                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Editar Detalle</h5>
                                            <button class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">

                                            <label>Cantidad</label>
                                            <input name="Cantidad" class="form-control"
                                                   value="{{ $detalle->Cantidad }}">

                                            <label class="mt-3">Fecha de Salida</label>
                                            <input type="date" name="Fecha_Salida" class="form-control"
                                                   value="{{ $detalle->Fecha_Salida }}">

                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-warning">Actualizar</button>
                                        </div>

                                    </div>

                                </form>
                            </div>
                        </div>

                        <div class="modal fade"
                             id="eliminarModal{{ $detalle->ID_Producto }}{{ $detalle->ID_Venta }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('detalleventas.destroyEm') }}">
                                    @csrf
                                    @method('DELETE')

                                    <input type="hidden" name="ID_Producto" value="{{ $detalle->ID_Producto }}">
                                    <input type="hidden" name="ID_Venta" value="{{ $detalle->ID_Venta }}">

                                    <div class="modal-content">

                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Eliminar Detalle</h5>
                                            <button class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            ¿Seguro que quieres eliminar este detalle?
                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-danger">Eliminar</button>
                                        </div>

                                    </div>

                                </form>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="5" class="text-muted">No hay detalles registrados.</td>
                        </tr>
                    @endforelse
                    </tbody>

                </table>
            </div>

            <div class="modal fade" id="crearModal">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('detalleventas.storeEm') }}">
                        @csrf

                        <div class="modal-content">

                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">Añadir Detalle</h5>
                                <button class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">

                                <label>Cantidad</label>
                                <input name="Cantidad" class="form-control" required>

                                <label class="mt-3">Fecha de Salida</label>
                                <input type="date" name="Fecha_Salida" class="form-control" required>

                                <label class="mt-3">ID Producto</label>
                                <input name="ID_Producto" class="form-control" required>

                                <label class="mt-3">ID Venta</label>
                                <input name="ID_Venta" class="form-control" required>

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
