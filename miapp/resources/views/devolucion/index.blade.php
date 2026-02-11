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
    
    <style>
        .modal-detalle-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            display: none;
        }

        .modal-detalle-content {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            z-index: 1050;
            max-width: 900px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .devoluciones-background {
            opacity: 0.3;
            pointer-events: none;
        }
    </style>
</head>

<body>

<div class="d-flex" style="min-height:100vh" id="mainContent">

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
                <a href="{{ route('devolucion.index') }}" class="elemento-menu activo">
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
                       id="dropdownUser1" data-bs-toggle="dropdown">
                        <img src="{{ asset('fotos_empleados/686fe89fe865f_Foto Kevin.jpeg') }}"
                             width="32" height="32" class="rounded-circle me-2">
                        <strong>{{ session('nombre') ?? 'Perfil' }}</strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item">Mi perfil</a></li>
                        <li><a class="dropdown-item">Editar perfil</a></li>
                        <li><hr></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">@csrf
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
                <h1>Registro de Devoluciones</h1>
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

            <div class="d-flex justify-content-end mt-4 gap-2">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fa fa-plus"></i> Añadir Devolución
                </button>
            </div>

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
                                <button class="btn btn-info btn-sm" 
                                        onclick="abrirDetalleModal({{ $dev->ID_Devolucion }})">
                                    <i class="fa fa-eye"></i>
                                </button>

                                <button class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editarModal{{ $dev->ID_Devolucion }}">
                                    <i class="fa fa-edit"></i>
                                </button>

                                <button class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#eliminarModal{{ $dev->ID_Devolucion }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Editar -->
                        <div class="modal fade" id="editarModal{{ $dev->ID_Devolucion }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('devolucion.update') }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="ID_Devolucion" value="{{ $dev->ID_Devolucion }}">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Editar Devolución #{{ $dev->ID_Devolucion }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <label>Fecha Devolución</label>
                                            <input type="date"
                                                   name="Fecha_Devolucion"
                                                   class="form-control mb-3"
                                                   value="{{ $dev->Fecha_Devolucion }}"
                                                   min="{{ date('Y-m-d', strtotime('-5 days')) }}"
                                                   max="{{ date('Y-m-d') }}"
                                                   required>

                                            <label>Motivo</label>
                                            <textarea name="Motivo" 
                                                      class="form-control" 
                                                      rows="3" 
                                                      required>{{ $dev->Motivo }}</textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-warning">Actualizar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Modal Eliminar -->
                        <div class="modal fade" id="eliminarModal{{ $dev->ID_Devolucion }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('devolucion.destroy') }}">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="ID_Devolucion" value="{{ $dev->ID_Devolucion }}">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Eliminar Devolución</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            ¿Seguro que deseas eliminar esta devolución?
                                            <div class="alert alert-warning mt-3">
                                                <strong>Fecha:</strong> {{ $dev->Fecha_Devolucion }}<br>
                                                <strong>Motivo:</strong> {{ $dev->Motivo }}
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-danger">Eliminar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    @empty
                        <tr><td colspan="4" class="text-muted">No hay devoluciones registradas.</td></tr>
                    @endforelse
                    </tbody>

                </table>
            </div>

            <!-- Modal Crear -->
            <div class="modal fade" id="crearModal">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('devolucion.store') }}">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">Añadir Devolución</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <label>Fecha Devolución</label>
                                <input type="date"
                                       name="Fecha_Devolucion"
                                       class="form-control mb-3"
                                       value="{{ date('Y-m-d') }}"
                                       min="{{ date('Y-m-d', strtotime('-5 days')) }}"
                                       max="{{ date('Y-m-d') }}"
                                       required>

                                <label class="form-label">Motivo</label>
                                <textarea name="Motivo"
                                          class="form-control"
                                          rows="3"
                                          placeholder="Ingrese el motivo de la devolución"
                                          required></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-success">Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal emergente para detalles -->
<div class="modal-detalle-backdrop" id="detalleBackdrop" onclick="cerrarDetalleModal()"></div>
<div class="modal-detalle-content" id="detalleModal" style="display: none;"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<script>
const urlDetalleDevolucion = "{{ route('detalledevolucion.index') }}";

function abrirDetalleModal(idDevolucion) {
    document.getElementById('mainContent').classList.add('devoluciones-background');
    document.getElementById('detalleBackdrop').style.display = 'block';
    
    fetch(`/devolucion/${idDevolucion}/detalles`)
        .then(response => response.json())
        .then(data => {
            console.log('Datos recibidos:', data);
            mostrarDetalles(data);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los detalles');
            cerrarDetalleModal();
        });
}

function mostrarDetalles(data) {
    const devolucion = data.devolucion;
    
    let detallesHTML = '';
    if (devolucion.detalles && devolucion.detalles.length > 0) {
        devolucion.detalles.forEach(detalle => {
            detallesHTML += `
                <tr>
                    <td class="py-3">
                        <i class="fa fa-box text-primary me-2"></i> 
                        <strong>${detalle.Cantidad_Devuelta}</strong> unidades
                    </td>
                    <td class="py-3">
                        <i class="fa fa-receipt text-success me-2"></i> 
                        Venta #<strong>${detalle.ID_Venta}</strong>
                    </td>
                    <td class="py-3 text-center">
                        <a href="${urlDetalleDevolucion}" class="btn btn-sm btn-warning">
                            <i class="fa fa-edit"></i> Editar
                        </a>
                    </td>
                </tr>
            `;
        });
    } else {
        detallesHTML = '<tr><td colspan="3" class="text-center text-muted py-5"><i class="fa fa-inbox fa-3x mb-3 d-block"></i><p class="mb-0">No hay detalles registrados para esta devolución</p></td></tr>';
    }
    
    const modalHTML = `
        <div class="modal-header bg-primary text-white py-3">
            <h5 class="modal-title">
                <i class="fa fa-info-circle me-2"></i> Detalle de Devolución - <span class="badge bg-light text-primary ms-2">ID: ${devolucion.ID_Devolucion}</span>
            </h5>
            <button type="button" class="btn-close btn-close-white" onclick="cerrarDetalleModal()"></button>
        </div>
        <div class="modal-body p-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                                    <i class="fa fa-calendar fa-lg text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1 small">Fecha Devolución</h6>
                                    <p class="mb-0 fw-bold fs-5">${devolucion.Fecha_Devolucion}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-info bg-opacity-10 p-3 rounded me-3">
                                    <i class="fa fa-clipboard fa-lg text-info"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-1 small">Motivo</h6>
                                    <p class="mb-0 fw-bold">${devolucion.Motivo}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <h6 class="mb-3 fw-bold"><i class="fa fa-list me-2"></i> Detalles de la Devolución:</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="py-3"><i class="fa fa-sort-numeric-up me-2"></i> Cantidad Devuelta</th>
                            <th class="py-3"><i class="fa fa-receipt me-2"></i> ID Venta</th>
                            <th class="py-3 text-center"><i class="fa fa-cog me-2"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${detallesHTML}
                    </tbody>
                </table>
            </div>
            
           
        <div class="modal-footer bg-light py-3">
            <button type="button" class="btn btn-secondary px-4" onclick="cerrarDetalleModal()">
                <i class="fa fa-times me-2"></i> Cerrar
            </button>
            <a href="${urlDetalleDevolucion}" class="btn btn-primary px-4">
                <i class="fa fa-external-link-alt me-2"></i> Ir a Detalle de Devoluciones
            </a>
        </div>
    `;
    
    document.getElementById('detalleModal').innerHTML = modalHTML;
    document.getElementById('detalleModal').style.display = 'block';
}

function cerrarDetalleModal() {
    document.getElementById('mainContent').classList.remove('devoluciones-background');
    document.getElementById('detalleBackdrop').style.display = 'none';
    document.getElementById('detalleModal').style.display = 'none';
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        cerrarDetalleModal();
    }
});
</script>

</body>
</html>
