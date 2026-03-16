<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('Imagenes/Logo.webp') }}" type="image/webp">
    <title>Detalle de Compras</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/Inicio.css') }}">
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
</head>

<body>

<!-- Overlay oscuro al abrir sidebar -->
<div class="overlay-sidebar" id="overlay"></div>

<div class="d-flex" style="min-height:100vh">

    <!-- ===================== SIDEBAR ===================== -->
    <div class="barra-lateral d-flex flex-column flex-shrink-0 p-3 bg-primary text-white" id="sidebar">
        <a class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            TECNICELL RM <img src="{{ asset('Imagenes/Logo.webp') }}" style="height:48px;">
        </a>
        <hr>
        <div class="menu-barra-lateral">
            <div class="seccion-menu">
                <a href="{{ route('admin.inicio') }}"
                   class="elemento-menu {{ request()->routeIs('admin.inicio') ? 'activo' : '' }}">
                    <i class="fa-solid fa-tachometer-alt"></i><span>Dashboard</span>
                </a>
                <a href="{{ route('compras.index') }}"
                   class="elemento-menu {{ request()->routeIs('compras.*') ? 'activo' : '' }}">
                    <i class="ri-shopping-cart-2-line"></i><span>Compras</span>
                </a>
                <a href="{{ route('devolucion.index') }}"
                   class="elemento-menu {{ request()->routeIs('devolucion.*') ? 'activo' : '' }}">
                    <i class="ri-arrow-go-back-line"></i><span>Devoluciones</span>
                </a>
                <a href="{{ route('ventas.index') }}"
                   class="elemento-menu {{ request()->routeIs('ventas.*') ? 'activo' : '' }}">
                    <i class="ri-price-tag-3-line"></i><span>Ventas</span>
                </a>
                <a href="{{ route('auditoria.index') }}"
                   class="elemento-menu {{ request()->routeIs('auditoria.*') ? 'activo' : '' }}">
                    <i class="ri-shield-check-line"></i><span>Auditoría</span>
                </a>
            </div>
            <hr>
            <div class="seccion-menu">
                <a href="{{ route('productos.index') }}"
                   class="elemento-menu {{ request()->routeIs('productos.*') ? 'activo' : '' }}">
                    <i class="ri-box-3-line"></i><span>Productos</span>
                </a>
                <a href="{{ route('proveedor.index') }}"
                   class="elemento-menu {{ request()->routeIs('proveedor.*') ? 'activo' : '' }}">
                    <i class="ri-truck-line"></i><span>Proveedores</span>
                </a>
                <div class="dropdown">
                    <a class="elemento-menu d-flex align-items-center text-white text-decoration-none dropdown-toggle
                       {{ request()->routeIs('clientes.*') || request()->routeIs('empleados.*') ? 'activo' : '' }}"
                       href="#" data-bs-toggle="dropdown">
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
    <!-- ===================== FIN SIDEBAR ===================== -->

    <!-- ===================== CONTENIDO PRINCIPAL ===================== -->
    <div class="contenido-principal flex-grow-1">

        <!-- NAVBAR SUPERIOR -->
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">

                <button class="btn-sidebar-toggle" id="btnToggleSidebar">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <a class="navbar-brand">Sistema gestión de inventarios</a>

                <div class="dropdown ms-auto">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle"
                       id="dropdownUser1" data-bs-toggle="dropdown">
                        <img src="{{ session('foto') ?? asset('Imagenes/default-user.png') }}"
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

        <!-- CONTENIDO -->
        <div class="container py-4">

            <!-- Título + botón volver -->
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('Imagenes/Logo.webp') }}" style="height:48px;">
                    <h1 class="mb-0">Detalle de Compras</h1>
                </div>
                <a href="{{ route('compras.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Volver a Compras
                </a>
            </div>

            @if(session('mensaje'))
                <div id="alertaMensaje" class="alert alert-success text-center mt-3">{{ session('mensaje') }}</div>
                <script>setTimeout(()=>{let a=document.getElementById('alertaMensaje');if(a){a.style.transition="opacity 0.5s";a.style.opacity=0;setTimeout(()=>a.remove(),500);}},2000);</script>
            @endif
            @if(session('error'))
                <div id="alertaError" class="alert alert-danger text-center mt-3">{{ session('error') }}</div>
                <script>setTimeout(()=>{let a=document.getElementById('alertaError');if(a){a.style.transition="opacity 0.5s";a.style.opacity=0;setTimeout(()=>a.remove(),500);}},2000);</script>
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
                            <th>ID Entrada</th>
                            <th class="col-ocultar-sm">Fecha Entrada</th>
                            <th>Cantidad</th>
                            <th class="col-ocultar-sm">Proveedor</th>
                            <th class="col-ocultar-sm">Producto</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($detalles as $detalle)
                        <tr>
                            <td>{{ $detalle->ID_Entrada }}</td>
                            <td class="col-ocultar-sm">{{ $detalle->Fecha_Entrada }}</td>
                            <td>{{ $detalle->Cantidad }}</td>
                            <td class="col-ocultar-sm">{{ $detalle->proveedor->Nombre_Proveedor ?? 'N/A' }}</td>
                            <td class="col-ocultar-sm">{{ $detalle->compra->nombre_producto ?? 'N/A' }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editarModal{{ $detalle->ID_Proveedor }}{{ $detalle->ID_Entrada }}">
                                    <i class="fa fa-edit"></i>
                                </button>
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
                                    @csrf @method('PUT')
                                    <input type="hidden" name="ID_Proveedor" value="{{ $detalle->ID_Proveedor }}">
                                    <input type="hidden" name="ID_Entrada" value="{{ $detalle->ID_Entrada }}">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title"><i class="fa fa-edit"></i> Editar Detalle</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-info">
                                                <strong>ID Entrada:</strong> {{ $detalle->ID_Entrada }}<br>
                                                <strong>Proveedor:</strong> {{ $detalle->proveedor->Nombre_Proveedor ?? 'N/A' }}<br>
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
                                            <button type="submit" class="btn btn-warning"><i class="fa fa-save"></i> Actualizar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Modal Eliminar -->
                        <div class="modal fade" id="eliminarModal{{ $detalle->ID_Proveedor }}{{ $detalle->ID_Entrada }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('detallecompras.destroy') }}">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="ID_Proveedor" value="{{ $detalle->ID_Proveedor }}">
                                    <input type="hidden" name="ID_Entrada" value="{{ $detalle->ID_Entrada }}">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title"><i class="fa fa-trash"></i> Eliminar Detalle</h5>
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
                                            <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> Eliminar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    @empty
                        <tr><td colspan="6" class="text-muted">No hay detalles registrados.</td></tr>
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
                                <h5 class="modal-title"><i class="fa fa-plus"></i> Añadir Detalle</h5>
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
                                        <option value="{{ $prov->ID_Proveedor }}">{{ $prov->Nombre_Proveedor }}</option>
                                    @endforeach
                                </select>
                                <label>Fecha Entrada</label>
                                <input type="date" name="Fecha_Entrada" class="form-control mb-3"
                                       value="{{ date('Y-m-d') }}"
                                       min="{{ date('Y-m-d') }}"
                                       max="{{ date('Y-m-d') }}"
                                       readonly required>
                                <label>Cantidad</label>
                                <input type="number" name="Cantidad" class="form-control" min="1" required>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
    <!-- ===================== FIN CONTENIDO PRINCIPAL ===================== -->

</div>

<div style="position: fixed; bottom: 10px; left: 0; width: 100%; text-align: center; margin-left: 115px;">
    <p style="color: #aaaaaa; font-size: 13px; margin: 0;">Copyright © 2026 Fonrio</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const btnToggle = document.getElementById('btnToggleSidebar');
    const sidebar   = document.getElementById('sidebar');
    const overlay   = document.getElementById('overlay');

    btnToggle.addEventListener('click', function () {
        sidebar.classList.toggle('abierto');
        overlay.classList.toggle('activo');
    });

    overlay.addEventListener('click', function () {
        sidebar.classList.remove('abierto');
        overlay.classList.remove('activo');
    });
</script>

</body>
</html>