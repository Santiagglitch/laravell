<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="{{ asset('Imagenes/Logo.webp') }}" type="image/webp">
    <title>Detalle de Compras</title>
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
                <a href="{{ route('compras.index') }}" class="elemento-menu activo">
                    <i class="ri-shopping-cart-2-line"></i><span>Compras</span>
                </a>
                <a href="{{ route('devolucion.index') }}" class="elemento-menu">
                    <i class="ri-arrow-go-back-line"></i><span>Devoluciones</span>
                </a>
                <a href="{{ route('ventas.index') }}" class="elemento-menu">
                    <i class="ri-price-tag-3-line"></i><span>Ventas</span>
                </a>
                  <a href="{{ route('auditoria.index') }}"
                   class="elemento-menu {{ request()->routeIs('auditoria.*') ? 'activo' : '' }}">
                    <i class="ri-shield-check-line"></i>
                    <span>Auditoría</span>
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
                       data-bs-toggle="dropdown">
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
                        <img src="{{ asset('fotos_empleados/686fe89fe865f_Foto Kevin.jpeg') }}"
                             width="32" height="32" class="rounded-circle me-2">
                        <strong>{{ session('nombre') ?? 'Perfil' }}</strong>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
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

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div></div>
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('Imagenes/Logo.webp') }}" style="height:48px;">
                    <h1>Detalle de Compras</h1>
                </div>
                <a href="{{ route('compras.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Volver a Compras
                </a>
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

            @if(session('error'))
                <div id="alertaError" class="alert alert-danger text-center mt-3">
                    {{ session('error') }}
                </div>
                <script>
                    setTimeout(() => {
                        let alerta = document.getElementById('alertaError');
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
                        <th>ID Entrada (Compra)</th>
                        <th>Fecha Entrada</th>
                        <th>Cantidad</th>
                        <th>Proveedor</th>
                        <th>Producto</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($detalles as $detalle)
                        <tr>
                            <td>{{ $detalle->ID_Entrada }}</td>
                            <td>{{ $detalle->Fecha_Entrada }}</td>
                            <td>{{ $detalle->Cantidad }}</td>
                            <td>{{ $detalle->proveedor->Nombre_Proveedor ?? 'N/A' }}</td>
                            <td>{{ $detalle->compra->nombre_producto ?? 'N/A' }}</td>

                            <td>
                                <!-- Botón editar -->
                                <button class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editarModal{{ $detalle->ID_Proveedor }}{{ $detalle->ID_Entrada }}">
                                    <i class="fa fa-edit"></i>
                                </button>

                                <!-- Botón eliminar -->
                                <button class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#eliminarModal{{ $detalle->ID_Proveedor }}{{ $detalle->ID_Entrada }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Editar -->
                        <div class="modal fade" id="editarModal{{ $detalle->ID_Proveedor }}{{ $detalle->ID_Entrada }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('detallecompras.update') }}">
                                    @csrf
                                    @method('PUT')

                                    <input type="hidden" name="ID_Proveedor" value="{{ $detalle->ID_Proveedor }}">
                                    <input type="hidden" name="ID_Entrada" value="{{ $detalle->ID_Entrada }}">

                                    <div class="modal-content">

                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">
                                                <i class="fa fa-edit"></i> Editar Detalle
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">

                                            <div class="alert alert-info">
                                                <strong>ID Entrada:</strong> {{ $detalle->ID_Entrada }} 
                                                <br>
                                                <strong>Proveedor:</strong> {{ $detalle->proveedor->Nombre_Proveedor ?? 'N/A' }}
                                                <br>
                                                <strong>Producto:</strong> {{ $detalle->compra->nombre_producto ?? 'N/A' }}
                                            </div>

                                            <label>Fecha Entrada</label>
                                            <input type="date" name="Fecha_Entrada" class="form-control mb-3"
                                                   value="{{ $detalle->Fecha_Entrada }}"
                                                   min="{{ date('Y-m-d') }}" 
                                                   max="{{ date('Y-m-d') }}" required>

                                            <label>Cantidad</label>
                                            <input type="number" name="Cantidad" class="form-control" min="1"
                                                   value="{{ $detalle->Cantidad }}" required>

                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-warning">
                                                <i class="fa fa-save"></i> Actualizar
                                            </button>
                                        </div>

                                    </div>

                                </form>
                            </div>
                        </div>

                        <!-- Modal Eliminar -->
                        <div class="modal fade" id="eliminarModal{{ $detalle->ID_Proveedor }}{{ $detalle->ID_Entrada }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('detallecompras.destroy') }}">
                                    @csrf
                                    @method('DELETE')

                                    <input type="hidden" name="ID_Proveedor" value="{{ $detalle->ID_Proveedor }}">
                                    <input type="hidden" name="ID_Entrada" value="{{ $detalle->ID_Entrada }}">

                                    <div class="modal-content">

                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">
                                                <i class="fa fa-trash"></i> Eliminar Detalle
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <p>¿Seguro que deseas eliminar este detalle?</p>
                                            <div class="alert alert-warning">
                                                <strong>Fecha:</strong> {{ $detalle->Fecha_Entrada }}<br>
                                                <strong>Cantidad:</strong> {{ $detalle->Cantidad }}<br>
                                                <strong>Proveedor:</strong> {{ $detalle->proveedor->Nombre_Proveedor ?? 'N/A' }}<br>
                                                <strong>Producto:</strong> {{ $detalle->compra->nombre_producto ?? 'N/A' }}
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fa fa-trash"></i> Eliminar
                                            </button>
                                        </div>

                                    </div>

                                </form>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="6" class="text-muted">No hay detalles registrados.</td>
                        </tr>
                    @endforelse
                    </tbody>

                </table>
            </div>

            <!-- Modal Crear -->
            <div class="modal fade" id="crearModal">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('detallecompras.store') }}">
                        @csrf

                        <div class="modal-content">

                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">
                                    <i class="fa fa-plus"></i> Añadir Detalle
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">

                                <label>Compra (ID Entrada)</label>
                                <select name="ID_Entrada" class="form-control mb-3" required>
                                    <option value="">Seleccione una compra</option>
                                    @foreach($compras as $compra)
                                        <option value="{{ $compra->ID_Entrada }}">
                                            ID: {{ $compra->ID_Entrada }} - {{ $compra->nombre_producto }}
                                        </option>
                                    @endforeach
                                </select>

                                <label>Proveedor</label>
                                <select name="ID_Proveedor" class="form-control mb-3" required>
                                    <option value="">Seleccione un proveedor</option>
                                    @foreach($proveedores as $prov)
                                        <option value="{{ $prov->ID_Proveedor }}">
                                            {{ $prov->Nombre_Proveedor }}
                                        </option>
                                    @endforeach
                                </select>

                                <label>Fecha Entrada</label>
                                <input type="date" 
                                       name="Fecha_Entrada" 
                                       class="form-control mb-3" 
                                       value="{{ date('Y-m-d') }}"
                                       min="{{ date('Y-m-d') }}" 
                                       max="{{ date('Y-m-d') }}"
                                       readonly
                                       required>

                                <label>Cantidad</label>
                                <input type="number" name="Cantidad" class="form-control" min="1" required>

                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-save"></i> Guardar
                                </button>
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