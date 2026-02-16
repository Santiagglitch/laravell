<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="{{ asset('Imagenes/Logo.webp') }}" type="image/webp">
    <title>Detalle de Devolución</title>
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
                <a href="{{ route('devolucion.index') }}" class="elemento-menu activo">
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
                    <h1>Detalle de Devoluciones</h1>
                </div>
                <a href="{{ route('devolucion.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Volver a Devoluciones
                </a>
            </div>

            @if(session('mensaje'))
                <div id="alertaMensaje" class="alert alert-success text-center mt-3">
                    {{ session('mensaje') }}
                </div>
                <script>
                    setTimeout(() => {
                        let alerta = document.getElementById('alertaMensaje');
                        if (alerta) { alerta.style.transition = "opacity 0.5s"; alerta.style.opacity = 0; setTimeout(() => alerta.remove(), 500); }
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
                        if (alerta) { alerta.style.transition = "opacity 0.5s"; alerta.style.opacity = 0; setTimeout(() => alerta.remove(), 500); }
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
                            <th>ID Devolución</th>
                            <th>Cantidad Devuelta</th>
                            <th>ID Venta</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($detalles as $detalle)
                        <tr>
                            <td>{{ $detalle->ID_Devolucion }}</td>
                            <td>{{ $detalle->Cantidad_Devuelta }}</td>
                            <td>{{ $detalle->ID_Venta }}</td>
                            <td>
                                {{-- Botón editar: llama a JS con los datos del detalle --}}
                                <button class="btn btn-warning btn-sm"
                                        onclick="abrirModalEditar(
                                            {{ $detalle->ID_Devolucion }},
                                            {{ $detalle->ID_Venta }},
                                            {{ $detalle->Cantidad_Devuelta }}
                                        )">
                                    <i class="fa fa-edit"></i>
                                </button>

                                <button class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#eliminarModal{{ $loop->index }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Eliminar -->
                        <div class="modal fade" id="eliminarModal{{ $loop->index }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('detalledevolucion.destroy', $detalle->ID_Devolucion) }}">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title"><i class="fa fa-trash"></i> Eliminar Detalle</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>¿Seguro que deseas eliminar este detalle?</p>
                                            <div class="alert alert-warning">
                                                <strong>ID Devolución:</strong> {{ $detalle->ID_Devolucion }}<br>
                                                <strong>Cantidad Devuelta:</strong> {{ $detalle->Cantidad_Devuelta }}<br>
                                                <strong>ID Venta:</strong> {{ $detalle->ID_Venta }}
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
                        <tr><td colspan="4" class="text-muted">No hay detalles registrados.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>


            {{-- ===================== MODAL EDITAR (único, reutilizable) ===================== --}}
            <div class="modal fade" id="editarModal">
                <div class="modal-dialog modal-lg">
                    <form method="POST" id="formEditar">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="ID_Venta" id="edit_id_venta">

                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h5 class="modal-title"><i class="fa fa-edit"></i> Editar Detalle de Devolución</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">

                                <div id="edit_cargando" class="text-center py-3 d-none">
                                    <div class="spinner-border text-warning" role="status"></div>
                                    <p class="mt-2 text-muted">Cargando información...</p>
                                </div>

                                <div id="edit_info" class="d-none">
                                    <div class="card border-info mb-3">
                                        <div class="card-header bg-info text-white">
                                            <i class="fa fa-info-circle"></i> Información de la Venta
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <strong>ID Venta:</strong><br>
                                                    <span id="edit_venta_id" class="fs-5 text-primary fw-bold"></span>
                                                </div>
                                                <div class="col-md-4">
                                                    <strong>Producto:</strong><br>
                                                    <span id="edit_producto" class="fw-bold"></span>
                                                </div>
                                                <div class="col-md-4">
                                                    <strong>Cantidad comprada:</strong><br>
                                                    <span id="edit_cantidad_comprada" class="badge bg-success fs-6"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <label class="form-label fw-bold">Cantidad a Devolver</label>
                                    <input type="number" name="Cantidad_Devuelta" id="edit_cantidad"
                                           class="form-control form-control-lg" min="1" required>

                                    <div id="edit_alerta_cantidad" class="alert alert-danger mt-2 d-none">
                                        <i class="fa fa-exclamation-triangle"></i>
                                        <span id="edit_alerta_texto"></span>
                                    </div>

                                    <small class="text-muted">
                                        Ingresa un número entre 1 y <strong id="edit_max_label"></strong> unidades.
                                    </small>
                                </div>

                                <div id="edit_error" class="alert alert-danger d-none">
                                    <i class="fa fa-exclamation-triangle"></i> No se pudo cargar la información de la venta.
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-warning" id="edit_btn_guardar" disabled>
                                    <i class="fa fa-save"></i> Actualizar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            {{-- ===================== MODAL CREAR ===================== --}}
            <div class="modal fade" id="crearModal">
                <div class="modal-dialog modal-lg">
                    <form method="POST" action="{{ route('detalledevolucion.store') }}">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title"><i class="fa fa-plus"></i> Añadir Detalle de Devolución</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">

                                <label>ID Devolución</label>
                                <select name="ID_Devolucion" class="form-control mb-3" required>
                                    <option value="">Seleccione una devolución</option>
                                    @foreach($devoluciones as $dev)
                                        <option value="{{ $dev->ID_Devolucion }}">
                                            ID: {{ $dev->ID_Devolucion }} - {{ $dev->Motivo ?? '' }}
                                        </option>
                                    @endforeach
                                </select>

                                <hr>

                                <label>Documento Cliente</label>
                                <div class="input-group mb-3">
                                    <input type="text" id="documento_cliente" class="form-control"
                                           placeholder="Ingrese el documento del cliente">
                                    <button type="button" class="btn btn-primary" onclick="buscarCliente()">
                                        <i class="fa fa-search"></i> Buscar
                                    </button>
                                </div>

                                <input type="hidden" name="ID_Venta" id="id_venta">

                                <div id="mensajeError" class="alert alert-danger d-none">
                                    <i class="fa fa-exclamation-triangle"></i> Cliente no encontrado o sin ventas registradas
                                </div>

                                <div id="infoCliente" class="d-none">
                                    <div class="card border-info mb-3">
                                        <div class="card-header bg-info text-white">
                                            <i class="fa fa-user"></i> Información del Cliente
                                        </div>
                                        <div class="card-body">
                                            <p class="mb-2"><strong>Nombre:</strong> <span id="nombreCliente"></span></p>
                                            <p class="mb-0"><strong>Documento:</strong> <span id="docCliente"></span></p>
                                        </div>
                                    </div>

                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <i class="fa fa-shopping-cart"></i> Productos Comprados
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Producto</th>
                                                            <th>Cantidad Comprada</th>
                                                            <th>ID Venta</th>
                                                            <th>Acción</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="productosCliente"></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="formularioDevolucion" class="d-none mt-3">
                                    <hr>
                                    <label>Cantidad a Devolver</label>
                                    <input type="number" name="Cantidad_Devuelta" id="cantidad_devuelta"
                                           class="form-control" min="1" required>
                                    <small class="text-muted">Cantidad disponible: <span id="cantidadDisponible"></span></small>

                                    <div id="crear_alerta_cantidad" class="alert alert-danger mt-2 d-none">
                                        <i class="fa fa-exclamation-triangle"></i>
                                        <span id="crear_alerta_texto"></span>
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-success" id="btnGuardar" disabled>
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

<script>

/* ============================================================
   MODAL EDITAR — carga info de la venta y valida cantidad
   ============================================================ */
let editMaxCantidad = 0;

function abrirModalEditar(idDevolucion, idVenta, cantidadActual) {
    // Resetear estado
    document.getElementById('edit_cargando').classList.remove('d-none');
    document.getElementById('edit_info').classList.add('d-none');
    document.getElementById('edit_error').classList.add('d-none');
    document.getElementById('edit_btn_guardar').disabled = true;
    document.getElementById('edit_alerta_cantidad').classList.add('d-none');

    // Poner la acción del form con el ID correcto (ruta admin)
    document.getElementById('formEditar').action = `/detalledevolucion/${idDevolucion}`;

    // Abrir modal
    new bootstrap.Modal(document.getElementById('editarModal')).show();

    // Buscar info de la venta via AJAX (ruta admin)
    fetch(`/venta-info/${idVenta}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('edit_cargando').classList.add('d-none');

            if (data.error) {
                document.getElementById('edit_error').classList.remove('d-none');
                return;
            }

            editMaxCantidad = data.cantidad;

            document.getElementById('edit_id_venta').value            = idVenta;
            document.getElementById('edit_venta_id').textContent      = idVenta;
            document.getElementById('edit_producto').textContent      = data.producto;
            document.getElementById('edit_cantidad_comprada').textContent = data.cantidad + ' unidades';
            document.getElementById('edit_max_label').textContent     = data.cantidad;
            document.getElementById('edit_cantidad').max              = data.cantidad;
            document.getElementById('edit_cantidad').value            = cantidadActual;

            document.getElementById('edit_info').classList.remove('d-none');
            document.getElementById('edit_btn_guardar').disabled = false;
        })
        .catch(() => {
            document.getElementById('edit_cargando').classList.add('d-none');
            document.getElementById('edit_error').classList.remove('d-none');
        });
}

// Validar cantidad en tiempo real en modal editar
document.getElementById('edit_cantidad').addEventListener('input', function () {
    const val    = parseInt(this.value);
    const alerta = document.getElementById('edit_alerta_cantidad');
    const texto  = document.getElementById('edit_alerta_texto');
    const btn    = document.getElementById('edit_btn_guardar');

    if (val > editMaxCantidad) {
        texto.textContent = `⚠️ No puedes devolver más de ${editMaxCantidad} unidades. Ingresa un número entre 1 y ${editMaxCantidad}.`;
        alerta.classList.remove('d-none');
        btn.disabled = true;
    } else if (val < 1 || isNaN(val)) {
        texto.textContent = '⚠️ La cantidad debe ser al menos 1.';
        alerta.classList.remove('d-none');
        btn.disabled = true;
    } else {
        alerta.classList.add('d-none');
        btn.disabled = false;
    }
});

// Validar antes de enviar editar
document.getElementById('formEditar').addEventListener('submit', function (e) {
    const val = parseInt(document.getElementById('edit_cantidad').value);
    if (val > editMaxCantidad || val < 1 || isNaN(val)) {
        e.preventDefault();
        alert(`⚠️ La cantidad debe estar entre 1 y ${editMaxCantidad} unidades.`);
    }
});


/* ============================================================
   MODAL CREAR
   ============================================================ */
let ventaSeleccionada = null;

function buscarCliente() {
    let documento    = document.getElementById('documento_cliente').value.trim();
    let mensajeError = document.getElementById('mensajeError');
    let infoCliente  = document.getElementById('infoCliente');

    mensajeError.classList.add('d-none');
    infoCliente.classList.add('d-none');
    document.getElementById('formularioDevolucion').classList.add('d-none');
    document.getElementById('btnGuardar').disabled = true;

    if (!documento) {
        mensajeError.textContent = 'Por favor ingrese un documento';
        mensajeError.classList.remove('d-none');
        return;
    }

    fetch(`/ventas/por-documento/${documento}`)
        .then(res => res.json())
        .then(data => {
            if (data && data.ventas && data.ventas.length > 0) {
                document.getElementById('nombreCliente').textContent = data.cliente.nombre || 'N/A';
                document.getElementById('docCliente').textContent    = data.cliente.documento;

                let productosHTML = '';
                data.ventas.forEach(venta => {
                    productosHTML += `
                        <tr>
                            <td>${venta.producto}</td>
                            <td><span class="badge bg-primary">${venta.cantidad}</span></td>
                            <td>${venta.id_venta}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-success"
                                        onclick="seleccionarVenta(${venta.id_venta}, ${venta.cantidad}, '${venta.producto}', this)">
                                    <i class="fa fa-check"></i> Seleccionar
                                </button>
                            </td>
                        </tr>`;
                });

                document.getElementById('productosCliente').innerHTML = productosHTML;
                infoCliente.classList.remove('d-none');
            } else {
                mensajeError.classList.remove('d-none');
            }
        })
        .catch(() => mensajeError.classList.remove('d-none'));
}

function seleccionarVenta(idVenta, cantidad, producto, btn) {
    ventaSeleccionada = { id: idVenta, cantidad: cantidad, producto: producto };

    document.getElementById('id_venta').value                = idVenta;
    document.getElementById('cantidadDisponible').textContent = cantidad + ' unidades de ' + producto;
    document.getElementById('cantidad_devuelta').max          = cantidad;
    document.getElementById('cantidad_devuelta').value        = '';
    document.getElementById('crear_alerta_cantidad').classList.add('d-none');

    document.getElementById('formularioDevolucion').classList.remove('d-none');
    document.getElementById('btnGuardar').disabled = false;

    document.querySelectorAll('#productosCliente tr').forEach(tr => tr.classList.remove('table-success'));
    btn.closest('tr').classList.add('table-success');
}

// Validar cantidad en tiempo real en modal crear
document.getElementById('cantidad_devuelta').addEventListener('input', function () {
    if (!ventaSeleccionada) return;
    const val    = parseInt(this.value);
    const alerta = document.getElementById('crear_alerta_cantidad');
    const texto  = document.getElementById('crear_alerta_texto');
    const btn    = document.getElementById('btnGuardar');

    if (val > ventaSeleccionada.cantidad) {
        texto.textContent = `⚠️ No puedes devolver más de ${ventaSeleccionada.cantidad} unidades de "${ventaSeleccionada.producto}". Ingresa un número entre 1 y ${ventaSeleccionada.cantidad}.`;
        alerta.classList.remove('d-none');
        btn.disabled = true;
    } else if (val < 1 || isNaN(val)) {
        texto.textContent = '⚠️ La cantidad debe ser al menos 1.';
        alerta.classList.remove('d-none');
        btn.disabled = true;
    } else {
        alerta.classList.add('d-none');
        btn.disabled = false;
    }
});

// Validar antes de enviar crear
document.querySelector('#crearModal form').addEventListener('submit', function (e) {
    const val = parseInt(document.getElementById('cantidad_devuelta').value);
    if (!ventaSeleccionada) {
        e.preventDefault();
        alert('⚠️ Por favor selecciona un producto para devolver');
        return;
    }
    if (val > ventaSeleccionada.cantidad) {
        e.preventDefault();
        alert(`⚠️ No puedes devolver ${val} unidades. Solo se compraron ${ventaSeleccionada.cantidad} unidades de "${ventaSeleccionada.producto}".`);
        return;
    }
    if (val < 1 || isNaN(val)) {
        e.preventDefault();
        alert('⚠️ La cantidad debe ser al menos 1');
    }
});
</script>

</body>
</html>
