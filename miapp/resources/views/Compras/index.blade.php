<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="{{ asset('Imagenes/Logo.webp') }}" type="image/webp">
    <title>Compras</title>
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

        .compras-background {
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
                <a href="{{ route('compras.index') }}" class="elemento-menu activo">
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
                <h1>Registro de Compras</h1>
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
                    <i class="fa fa-plus"></i> Añadir Compra
                </button>
            </div>

            <div class="table-responsive mt-4">
                <table class="table table-bordered table-striped table-hover text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>ID Entrada</th>
                            <th>Precio</th>
                            <th>Producto</th>
                            <th>Documento Empleado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($compras as $compra)
                        <tr>
                            <td>{{ $compra->ID_Entrada }}</td>
                            <td>${{ number_format($compra->Precio_Compra, 2) }}</td>
                            <td>{{ $compra->nombre_producto }}</td>
                            <td>{{ $compra->Documento_Empleado }}</td>
                            <td>
                                <button class="btn btn-info btn-sm" 
                                        onclick="abrirDetalleModal({{ $compra->ID_Entrada }})">
                                    <i class="fa fa-eye"></i>
                                </button>

                                <button class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editarModal{{ $compra->ID_Entrada }}">
                                    <i class="fa fa-edit"></i>
                                </button>

                                <button class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#eliminarModal{{ $compra->ID_Entrada }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Editar -->
                        <div class="modal fade" id="editarModal{{ $compra->ID_Entrada }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('compras.update', $compra->ID_Entrada) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Editar Compra #{{ $compra->ID_Entrada }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <label>Precio Compra</label>
                                            <input name="Precio_Compra" type="number" step="0.01" class="form-control mb-3" value="{{ $compra->Precio_Compra }}" required>
                                            
                                            <label>Producto</label>
                                            <select name="ID_Producto" class="form-control" required>
                                                @foreach($productos as $prod)
                                                    <option value="{{ $prod->ID_Producto }}" 
                                                        {{ $compra->ID_Producto == $prod->ID_Producto ? 'selected' : '' }}>
                                                        {{ $prod->Nombre_Producto }}
                                                    </option>
                                                @endforeach
                                            </select>
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
                        <div class="modal fade" id="eliminarModal{{ $compra->ID_Entrada }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('compras.destroy', $compra->ID_Entrada) }}">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Eliminar Compra</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            ¿Seguro que deseas eliminar esta compra?
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
                        <tr><td colspan="5" class="text-muted">No hay compras registradas.</td></tr>
                    @endforelse
                    </tbody>

                </table>
            </div>

            <!-- Modal Crear -->
            <div class="modal fade" id="crearModal">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('compras.store') }}">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">Añadir Compra</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <label>Precio Compra</label>
                                <input name="Precio_Compra" type="number" step="0.01" class="form-control mb-3" required>
                                
                                <label>Producto</label>
                                <select name="ID_Producto" class="form-control" required>
                                    <option value="">Seleccione un producto</option>
                                    @foreach($productos as $prod)
                                        <option value="{{ $prod->ID_Producto }}">{{ $prod->Nombre_Producto }}</option>
                                    @endforeach
                                </select>

                                <input type="hidden" name="Documento_Empleado" value="{{ session('documento') }}">
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
const urlDetalleCompras = "{{ route('detallecompras.index') }}";

function abrirDetalleModal(idEntrada) {
    document.getElementById('mainContent').classList.add('compras-background');
    document.getElementById('detalleBackdrop').style.display = 'block';
    
    fetch(`/compras/${idEntrada}/detalles`)
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
    const compra = data.compra;
    
    let nombreEmpleado = 'No disponible';
    if (compra.empleado) {
        if (compra.empleado.Nombre_Empleado) {
            nombreEmpleado = compra.empleado.Nombre_Empleado;
        } else if (compra.Documento_Empleado) {
            nombreEmpleado = 'Doc: ' + compra.Documento_Empleado;
        }
    } else if (compra.Documento_Empleado) {
        nombreEmpleado = 'Doc: ' + compra.Documento_Empleado;
    }
    
    let detallesHTML = '';
    if (compra.detalles && compra.detalles.length > 0) {
        compra.detalles.forEach(detalle => {
            detallesHTML += `
                <tr>
                    <td><i class="fa fa-calendar"></i> ${detalle.Fecha_Entrada}</td>
                    <td><i class="fa fa-box"></i> ${detalle.Cantidad} unidades</td>
                    <td>
                        <a href="${urlDetalleCompras}" class="btn btn-sm btn-warning">
                            <i class="fa fa-edit"></i> Editar
                        </a>
                    </td>
                </tr>
            `;
        });
    } else {
        detallesHTML = '<tr><td colspan="3" class="text-center text-muted py-4"><i class="fa fa-inbox fa-2x mb-2"></i><br>No hay detalles registrados para esta compra</td></tr>';
    }
    
    const modalHTML = `
        <div class="modal-header bg-primary text-white">
            <h5 class="modal-title">
                <i class="fa fa-info-circle"></i> Detalle de Compra - <span class="badge bg-light text-primary">ID: ${compra.ID_Entrada}</span>
            </h5>
            <button type="button" class="btn-close btn-close-white" onclick="cerrarDetalleModal()"></button>
        </div>
        <div class="modal-body">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h6 class="text-muted mb-2"><i class="fa fa-box-open"></i> Producto</h6>
                            <p class="mb-0 fw-bold">${compra.producto_info ? compra.producto_info.Nombre_Producto : 'N/A'}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted mb-2"><i class="fa fa-dollar-sign"></i> Precio Compra</h6>
                            <p class="mb-0 fw-bold text-success">$${parseFloat(compra.Precio_Compra).toFixed(2)}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted mb-2"><i class="fa fa-user"></i> Empleado</h6>
                            <p class="mb-0 fw-bold">${nombreEmpleado}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <h6 class="mb-3"><i class="fa fa-list"></i> Detalles de la Compra:</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th><i class="fa fa-calendar-alt"></i> Fecha Entrada</th>
                            <th><i class="fa fa-sort-numeric-up"></i> Cantidad</th>
                            <th><i class="fa fa-cog"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${detallesHTML}
                    </tbody>
                </table>
            </div>
            
            <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="fa fa-info-circle fa-2x me-3"></i>
                <div>
                    Para editar los detalles, haz clic en el botón "Editar" de la tabla o ve a la sección 
                    <a href="${urlDetalleCompras}" class="alert-link">Detalle de Compras</a>.
                </div>
            </div>
        </div>
        <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" onclick="cerrarDetalleModal()">
                <i class="fa fa-times"></i> Cerrar
            </button>
            <a href="${urlDetalleCompras}" class="btn btn-primary">
                <i class="fa fa-external-link-alt"></i> Ir a Detalle de Compras
            </a>
        </div>
    `;
    
    document.getElementById('detalleModal').innerHTML = modalHTML;
    document.getElementById('detalleModal').style.display = 'block';
}

function cerrarDetalleModal() {
    document.getElementById('mainContent').classList.remove('compras-background');
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