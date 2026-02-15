<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('Imagenes/Logo.webp') }}" type="image/webp">
    <title>Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ventas.css') }}">
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
                <a href="{{ route('devolucion.index') }}" class="elemento-menu">
                    <i class="ri-arrow-go-back-line"></i><span>Devoluciones</span>
                </a>
                <a href="{{ route('ventas.index') }}" class="elemento-menu activo">
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
                       data-bs-toggle="dropdown">
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
                <h1>Registro de Ventas</h1>
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
                    }, 3000);
                </script>
            @endif

            <div class="d-flex justify-content-end mt-4">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fa fa-plus"></i> Añadir Venta
                </button>
            </div>

            <div class="table-responsive mt-4">
                <table class="table table-bordered table-striped table-hover text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>ID Venta</th>
                            <th>Documento Cliente</th>
                            <th>Documento Empleado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($ventas as $venta)
                        <tr>
                            <td>{{ $venta->ID_Venta }}</td>
                            <td>{{ $venta->Documento_Cliente }}</td>
                            <td>{{ $venta->Documento_Empleado }}</td>
                            <td>
                                <button class="btn btn-info btn-sm"
                                        onclick="abrirDetalleModal({{ $venta->ID_Venta }})">
                                    <i class="fa fa-eye"></i>
                                </button>

                                <button class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#eliminarModal{{ $venta->ID_Venta }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <div class="modal fade" id="eliminarModal{{ $venta->ID_Venta }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('ventas.destroy') }}">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="ID_Venta" value="{{ $venta->ID_Venta }}">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Eliminar Venta</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            ¿Seguro que deseas eliminar esta venta?
                                            <div class="alert alert-warning mt-3">
                                                <strong>ID Venta:</strong> {{ $venta->ID_Venta }}<br>
                                                <strong>Cliente:</strong> {{ $venta->Documento_Cliente }}<br>
                                                <strong>Empleado:</strong> {{ $venta->Documento_Empleado }}
                                            </div>
                                            <div class="alert alert-danger mt-2">
                                                <i class="fa fa-exclamation-triangle"></i>
                                                <strong>Atención:</strong> Si esta venta tiene detalles asociados, debes eliminarlos primero.
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
                        <tr>
                            <td colspan="4" class="text-muted">No hay ventas registradas.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MODAL CREAR VENTA --}}
            <div class="modal fade" id="crearModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <form method="POST" action="{{ route('ventas.store') }}" id="formVenta">
                        @csrf
                        <input type="hidden" name="cliente_nuevo" id="clienteNuevo" value="0">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">
                                    <i class="fa fa-plus-circle"></i> Registrar Nueva Venta
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">

                                {{-- Búsqueda de Cliente --}}
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fa fa-user"></i> Documento del Cliente
                                    </label>
                                    <div class="position-relative">
                                        <input
                                            type="text"
                                            id="buscarCliente"
                                            name="Documento_Cliente"
                                            class="form-control"
                                            placeholder="Ej: 1234567890"
                                            autocomplete="off"
                                            required>
                                        <div id="spinnerBusqueda"
                                             class="spinner-border spinner-border-sm text-primary position-absolute"
                                             style="right: 10px; top: 10px; display: none;">
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fa fa-info-circle"></i>
                                        Ingrese el documento y espere 1 segundo o presione Enter
                                    </small>
                                </div>

                                <div id="mensajeCliente" class="alert d-none mb-3"></div>

                                {{-- Campos Cliente Nuevo --}}
                                <div id="camposNuevoCliente" style="display: none;">
                                    <div class="card border-warning mb-3">
                                        <div class="card-header bg-warning bg-opacity-25">
                                            <strong><i class="fa fa-user-plus"></i> Datos del Nuevo Cliente</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Nombre</label>
                                                    <input type="text" name="Nombre_Cliente" id="nombreCliente" class="form-control">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Apellido</label>
                                                    <input type="text" name="Apellido_Cliente" id="apellidoCliente" class="form-control">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Estado</label>
                                                <select name="Estado_Cliente" id="estadoCliente" class="form-select">
                                                    <option value="activo" selected>Activo</option>
                                                    <option value="inactivo">Inactivo</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                {{-- ✅ Dropdown Empleado desde BD --}}
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fa fa-user-tie"></i> Empleado que realiza la venta
                                    </label>
                                    <select name="Documento_Empleado" class="form-select" required>
                                        <option value="">-- Seleccione un empleado --</option>
                                        @foreach($empleados as $emp)
                                            <option value="{{ $emp->Documento_Empleado }}"
                                                {{ session('documento') == $emp->Documento_Empleado ? 'selected' : '' }}>
                                                {{ $emp->Nombre_Usuario }} {{ $emp->Apellido_Usuario }}
                                                ({{ $emp->Documento_Empleado }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fa fa-times"></i> Cancelar
                                </button>
                                <button type="submit" class="btn btn-success" id="btnGuardar">
                                    <i class="fa fa-save"></i> Guardar Venta
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Modal emergente para detalles --}}
<div class="modal-detalle-backdrop" id="detalleBackdrop" onclick="cerrarDetalleModal()"></div>
<div class="modal-detalle-content" id="detalleModal" style="display: none;"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<script>
const urlDetalleVentas = "{{ route('detalleventas.index') }}";

function abrirDetalleModal(idVenta) {
    document.getElementById('mainContent').classList.add('devoluciones-background');
    document.getElementById('detalleBackdrop').style.display = 'block';

    fetch(`/ventas/${idVenta}/detalles`)
        .then(response => response.json())
        .then(data => mostrarDetalles(data))
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los detalles');
            cerrarDetalleModal();
        });
}

function mostrarDetalles(data) {
    const venta = data.venta;

    let detallesHTML = '';
    if (venta.detalles && venta.detalles.length > 0) {
        venta.detalles.forEach(detalle => {
            detallesHTML += `
                <tr>
                    <td class="py-3">
                        <i class="fa fa-box text-primary me-2"></i>
                        <strong>${detalle.Nombre_Producto}</strong>
                    </td>
                    <td class="py-3">
                        <span class="badge bg-primary">${detalle.Cantidad}</span> unidades
                    </td>
                    <td class="py-3">${detalle.Fecha_Salida}</td>
                    <td class="py-3 text-center">
                        <a href="${urlDetalleVentas}" class="btn btn-sm btn-warning">
                            <i class="fa fa-edit"></i> Editar
                        </a>
                    </td>
                </tr>
            `;
        });
    } else {
        detallesHTML = '<tr><td colspan="4" class="text-center text-muted py-5"><i class="fa fa-inbox fa-3x mb-3 d-block"></i><p>No hay detalles registrados para esta venta</p></td></tr>';
    }

    const modalHTML = `
        <div class="modal-header bg-primary text-white py-3">
            <h5 class="modal-title">
                <i class="fa fa-info-circle me-2"></i> Detalle de Venta -
                <span class="badge bg-light text-primary ms-2">ID: ${venta.ID_Venta}</span>
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
                                    <i class="fa fa-user fa-lg text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1 small">Documento Cliente</h6>
                                    <p class="mb-0 fw-bold fs-5">${venta.Documento_Cliente}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-info bg-opacity-10 p-3 rounded me-3">
                                    <i class="fa fa-user-tie fa-lg text-info"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1 small">Documento Empleado</h6>
                                    <p class="mb-0 fw-bold">${venta.Documento_Empleado}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <h6 class="mb-3 fw-bold"><i class="fa fa-list me-2"></i> Productos en esta Venta:</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th><i class="fa fa-box me-2"></i> Producto</th>
                            <th><i class="fa fa-sort-numeric-up me-2"></i> Cantidad</th>
                            <th><i class="fa fa-calendar me-2"></i> Fecha Salida</th>
                            <th class="text-center"><i class="fa fa-cog me-2"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${detallesHTML}
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer bg-light py-3">
            <button type="button" class="btn btn-secondary px-4" onclick="cerrarDetalleModal()">
                <i class="fa fa-times me-2"></i> Cerrar
            </button>
            <a href="${urlDetalleVentas}" class="btn btn-primary px-4">
                <i class="fa fa-external-link-alt me-2"></i> Ir a Detalle de Ventas
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
    if (event.key === 'Escape') cerrarDetalleModal();
});

document.addEventListener('DOMContentLoaded', function() {
    const inputDocumento  = document.getElementById('buscarCliente');
    const spinnerBusqueda = document.getElementById('spinnerBusqueda');
    const mensajeCliente  = document.getElementById('mensajeCliente');
    const camposNuevo     = document.getElementById('camposNuevoCliente');
    const inputNuevo      = document.getElementById('clienteNuevo');
    const nombreCliente   = document.getElementById('nombreCliente');
    const apellidoCliente = document.getElementById('apellidoCliente');
    const formVenta       = document.getElementById('formVenta');
    const btnGuardar      = document.getElementById('btnGuardar');

    let timeoutBusqueda;
    let clienteValidado = false;

    btnGuardar.disabled = true;

    inputDocumento.addEventListener('keyup', function(e) {
        clearTimeout(timeoutBusqueda);
        const documento = this.value.trim();
        clienteValidado = false;
        btnGuardar.disabled = true;

        if (documento.length < 5) { ocultarMensajes(); return; }
        if (e.key === 'Enter') { e.preventDefault(); buscarCliente(documento); return; }

        timeoutBusqueda = setTimeout(() => buscarCliente(documento), 1000);
    });

    formVenta.addEventListener('submit', function(e) {
        if (!clienteValidado) {
            e.preventDefault();
            return false;
        }
    });

    function buscarCliente(documento) {
        spinnerBusqueda.style.display = 'block';
        mensajeCliente.classList.add('d-none');
        clienteValidado = false;
        btnGuardar.disabled = true;

        fetch(`/api/buscar-cliente/${documento}`)
            .then(r => r.json())
            .then(data => {
                spinnerBusqueda.style.display = 'none';
                if (data.encontrado && data.cliente) {
                    mostrarClienteEncontrado(data.cliente);
                } else {
                    mostrarFormularioNuevo();
                }
            })
            .catch(() => {
                spinnerBusqueda.style.display = 'none';
                mostrarFormularioNuevo();
            });
    }

    function mostrarClienteEncontrado(cliente) {
        mensajeCliente.className = 'alert alert-success';
        mensajeCliente.innerHTML = `
            <i class="fa fa-check-circle"></i>
            <strong>Cliente encontrado:</strong>
            ${cliente.Nombre_Cliente} ${cliente.Apellido_Cliente}
            ${cliente.ID_Estado == '1'
                ? '<span class="badge bg-success">Activo</span>'
                : '<span class="badge bg-secondary">Inactivo</span>'}
        `;
        mensajeCliente.classList.remove('d-none');
        camposNuevo.style.display = 'none';
        inputNuevo.value = '0';
        nombreCliente.removeAttribute('required');
        apellidoCliente.removeAttribute('required');
        clienteValidado = true;
        btnGuardar.disabled = false;
    }

    function mostrarFormularioNuevo() {
        mensajeCliente.className = 'alert alert-warning';
        mensajeCliente.innerHTML = `
            <i class="fa fa-exclamation-triangle"></i>
            <strong>Cliente no encontrado.</strong>
            Complete los datos para registrar un nuevo cliente:
        `;
        mensajeCliente.classList.remove('d-none');
        camposNuevo.style.display = 'block';
        inputNuevo.value = '1';
        nombreCliente.setAttribute('required', 'required');
        apellidoCliente.setAttribute('required', 'required');
        clienteValidado = true;
        btnGuardar.disabled = false;
    }

    function ocultarMensajes() {
        mensajeCliente.classList.add('d-none');
        camposNuevo.style.display = 'none';
        inputNuevo.value = '0';
        nombreCliente.removeAttribute('required');
        apellidoCliente.removeAttribute('required');
        clienteValidado = false;
        btnGuardar.disabled = true;
    }

    document.getElementById('crearModal').addEventListener('hidden.bs.modal', function() {
        formVenta.reset();
        ocultarMensajes();
        spinnerBusqueda.style.display = 'none';
        clienteValidado = false;
        btnGuardar.disabled = true;
    });
});
</script>

</body>
</html>