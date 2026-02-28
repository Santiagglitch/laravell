<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('Imagenes/Logo.webp') }}" type="image/webp">
    <title>Detalle de Ventas - Empleado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
</head>

<body>
<div class="d-flex" style="min-height:100vh">

    {{-- SIDEBAR EMPLEADO --}}
    <div class="barra-lateral d-flex flex-column flex-shrink-0 p-3 bg-primary text-white">
        <a class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
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
                    <i class="ri-box-3-line"></i><span>Productos</span>
                </a>
                <a href="{{ route('clientes.indexEm') }}" class="elemento-menu">
                    <i class="ri-user-line"></i><span>Clientes</span>
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
                       id="dropdownUser1" data-bs-toggle="dropdown">
                        <img src="{{ session('foto') ?? asset('Imagenes/default-user.png') }}"
                             width="32" height="32" class="rounded-circle me-2">
                        <strong>{{ session('nombre') ?? 'Perfil' }}</strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                        <li><a class="dropdown-item" href="{{ route('perfilEm') }}">Mi perfil</a></li>
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
                    <h1>Detalle de Ventas</h1>
                </div>
                <a href="{{ route('ventas.indexEm') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Volver a Ventas
                </a>
            </div>

            @if(session('mensaje'))
                <div id="alertaMensaje" class="alert alert-success text-center mt-3">
                    {{ session('mensaje') }}
                </div>
                <script>
                    setTimeout(() => {
                        let a = document.getElementById('alertaMensaje');
                        if (a) { a.style.transition = "opacity 0.5s"; a.style.opacity = 0; setTimeout(() => a.remove(), 500); }
                    }, 2000);
                </script>
            @endif

            @if(session('error'))
                <div id="alertaError" class="alert alert-danger text-center mt-3">
                    {{ session('error') }}
                </div>
                <script>
                    setTimeout(() => {
                        let a = document.getElementById('alertaError');
                        if (a) { a.style.transition = "opacity 0.5s"; a.style.opacity = 0; setTimeout(() => a.remove(), 500); }
                    }, 3000);
                </script>
            @endif

            <div class="text-end mt-4">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fa fa-plus"></i> Añadir Detalle
                </button>
            </div>

            {{-- TABLA --}}
            <div class="table-responsive mt-4">
                <table class="table table-bordered table-striped table-hover text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Fecha Salida</th>
                            <th>ID Venta</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($detalles as $detalle)
                        <tr>
                            <td>{{ $detalle->Nombre_Producto }}</td>
                            <td>{{ $detalle->Cantidad }}</td>
                            <td>{{ $detalle->Fecha_Salida }}</td>
                            <td>{{ $detalle->ID_Venta }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm"
                                        onclick="abrirModalEditar(
                                            {{ $detalle->ID_Venta }},
                                            {{ $detalle->ID_Producto }},
                                            {{ $detalle->Cantidad }},
                                            {{ $detalle->Stock_Minimo }}
                                        )">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#eliminarModal{{ $detalle->ID_Producto }}{{ $detalle->ID_Venta }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- Modal Eliminar --}}
                        <div class="modal fade" id="eliminarModal{{ $detalle->ID_Producto }}{{ $detalle->ID_Venta }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('detalleventas.destroyEm') }}">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="ID_Producto" value="{{ $detalle->ID_Producto }}">
                                    <input type="hidden" name="ID_Venta" value="{{ $detalle->ID_Venta }}">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title"><i class="fa fa-trash"></i> Eliminar Detalle</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>¿Seguro que deseas eliminar este detalle?</p>
                                            <div class="alert alert-warning">
                                                <strong>Producto:</strong> {{ $detalle->Nombre_Producto }}<br>
                                                <strong>Cantidad:</strong> {{ $detalle->Cantidad }}<br>
                                                <strong>ID Venta:</strong> {{ $detalle->ID_Venta }}
                                            </div>
                                            <div class="alert alert-info">
                                                <i class="fa fa-info-circle"></i>
                                                Al eliminar, se devolverán <strong>{{ $detalle->Cantidad }}</strong> unidades al stock de <strong>{{ $detalle->Nombre_Producto }}</strong>.
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
                        <tr><td colspan="5" class="text-muted">No hay detalles registrados.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ===================== MODAL EDITAR ===================== --}}
            <div class="modal fade" id="editarModal">
                <div class="modal-dialog modal-lg">
                    <form method="POST" action="{{ route('detalleventas.updateEm') }}" id="formEditar">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="ID_Venta" id="edit_id_venta">
                        <input type="hidden" name="ID_Producto" id="edit_id_producto">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h5 class="modal-title"><i class="fa fa-edit"></i> Editar Detalle de Venta</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="card border-info mb-3">
                                    <div class="card-header bg-info text-white">
                                        <i class="fa fa-info-circle"></i> Información del Detalle
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
                                                <strong>Stock disponible:</strong><br>
                                                <span id="edit_stock" class="badge bg-success fs-6"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <label class="form-label fw-bold">Cantidad</label>
                                <input type="number" name="Cantidad" id="edit_cantidad"
                                       class="form-control form-control-lg" min="1" required>

                                <div id="edit_alerta_cantidad" class="alert alert-danger mt-2 d-none">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    <span id="edit_alerta_texto"></span>
                                </div>

                                <small class="text-muted">
                                    Cantidad actual + stock disponible = máximo <strong id="edit_max_label"></strong> unidades.
                                </small>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-warning" id="edit_btn_guardar">
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
                    <form method="POST" action="{{ route('detalleventas.storeEm') }}" id="formCrear">
                        @csrf
                        <input type="hidden" name="ID_Producto" id="crear_id_producto">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title"><i class="fa fa-plus"></i> Añadir Detalle de Venta</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">

                                {{-- 1. BUSCAR PRODUCTO --}}
                                <label class="form-label fw-bold">
                                    <i class="fa fa-search"></i> Buscar Producto
                                </label>
                                <div class="input-group mb-2">
                                    <input type="text" id="buscar_producto_input" class="form-control"
                                           placeholder="Escribe el nombre del producto...">
                                    <button type="button" class="btn btn-primary" onclick="buscarProducto()">
                                        <i class="fa fa-search"></i> Buscar
                                    </button>
                                </div>

                                <div id="productos_resultado" class="d-none mb-3">
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <i class="fa fa-box"></i> Productos encontrados
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-sm table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Nombre</th>
                                                        <th>Stock</th>
                                                        <th>Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="productos_lista"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div id="producto_seleccionado" class="d-none mb-3">
                                    <div class="card border-primary">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <strong>Producto seleccionado:</strong><br>
                                                    <span id="crear_nombre_producto" class="fw-bold text-primary fs-5"></span>
                                                </div>
                                                <div class="col-md-4">
                                                    <strong>Stock disponible:</strong><br>
                                                    <span id="crear_stock" class="badge bg-success fs-6"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="mensaje_producto_error" class="alert alert-danger d-none">
                                    <i class="fa fa-exclamation-triangle"></i> No se encontraron productos con ese nombre.
                                </div>

                                {{-- 2. CANTIDAD --}}
                                <div id="seccion_cantidad" class="d-none">
                                    <label class="form-label fw-bold mt-2">Cantidad</label>
                                    <input type="number" name="Cantidad" id="crear_cantidad"
                                           class="form-control" min="1" required>
                                    <small class="text-muted">Máximo: <span id="crear_max_cantidad"></span> unidades disponibles.</small>
                                    <div id="crear_alerta_cantidad" class="alert alert-danger mt-2 d-none">
                                        <i class="fa fa-exclamation-triangle"></i>
                                        <span id="crear_alerta_texto"></span>
                                    </div>
                                </div>

                                {{-- 3. FECHA --}}
                                <div id="seccion_fecha" class="d-none">
                                    <label class="form-label fw-bold mt-3">Fecha de Salida</label>
                                    <input type="date" name="Fecha_Salida" id="crear_fecha"
                                           class="form-control" required>
                                    <small class="text-muted">Solo se puede registrar la fecha de hoy.</small>
                                </div>

                                {{-- 4. ID VENTA --}}
                                <div id="seccion_venta" class="d-none">
                                    <label class="form-label fw-bold mt-3">
                                        <i class="fa fa-receipt"></i> Seleccionar Venta
                                    </label>
                                    <select name="ID_Venta" id="crear_id_venta" class="form-select" required>
                                        <option value="">-- Seleccione una venta --</option>
                                        @foreach($ultimasVentas as $venta)
                                            <option value="{{ $venta->ID_Venta }}">
                                                Venta #{{ $venta->ID_Venta }} — Cliente: {{ $venta->Documento_Cliente }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Se muestran las últimas 5 ventas registradas.</small>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-success" id="crear_btn_guardar" disabled>
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
   MODAL CREAR
============================================================ */
let productoSeleccionado = null;

function buscarProducto() {
    const nombre  = document.getElementById('buscar_producto_input').value.trim();
    const error   = document.getElementById('mensaje_producto_error');
    const result  = document.getElementById('productos_resultado');
    const selDiv  = document.getElementById('producto_seleccionado');

    error.classList.add('d-none');
    result.classList.add('d-none');
    selDiv.classList.add('d-none');
    ocultarSeccionesCampos();

    if (!nombre) return;

    fetch(`/empleado/detalleventas/buscar-producto/${encodeURIComponent(nombre)}`)
        .then(r => r.json())
        .then(data => {
            if (data.error || !data.productos || data.productos.length === 0) {
                error.classList.remove('d-none');
                return;
            }
            let html = '';
            data.productos.forEach(p => {
                html += `
                    <tr>
                        <td>${p.Nombre_Producto}</td>
                        <td><span class="badge bg-${p.Stock_Minimo > 0 ? 'success' : 'danger'}">${p.Stock_Minimo} uds</span></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary"
                                    onclick="seleccionarProducto(${p.ID_Producto}, '${p.Nombre_Producto}', ${p.Stock_Minimo}, this)"
                                    ${p.Stock_Minimo <= 0 ? 'disabled' : ''}>
                                <i class="fa fa-check"></i> Seleccionar
                            </button>
                        </td>
                    </tr>`;
            });
            document.getElementById('productos_lista').innerHTML = html;
            result.classList.remove('d-none');
        })
        .catch(() => error.classList.remove('d-none'));
}

function seleccionarProducto(idProducto, nombre, stock, btn) {
    productoSeleccionado = { id: idProducto, nombre: nombre, stock: stock };

    document.getElementById('crear_id_producto').value          = idProducto;
    document.getElementById('crear_nombre_producto').textContent = nombre;
    document.getElementById('crear_stock').textContent          = stock + ' unidades';
    document.getElementById('crear_max_cantidad').textContent   = stock;
    document.getElementById('crear_cantidad').max               = stock;
    document.getElementById('crear_cantidad').value             = '';
    document.getElementById('crear_alerta_cantidad').classList.add('d-none');

    const hoy = new Date().toISOString().split('T')[0];
    document.getElementById('crear_fecha').value = hoy;
    document.getElementById('crear_fecha').min   = hoy;
    document.getElementById('crear_fecha').max   = hoy;

    document.getElementById('producto_seleccionado').classList.remove('d-none');
    document.getElementById('productos_resultado').classList.add('d-none');
    document.getElementById('seccion_cantidad').classList.remove('d-none');
    document.getElementById('seccion_fecha').classList.remove('d-none');
    document.getElementById('seccion_venta').classList.remove('d-none');
    document.getElementById('crear_btn_guardar').disabled = false;

    document.querySelectorAll('#productos_lista tr').forEach(tr => tr.classList.remove('table-success'));
    btn.closest('tr').classList.add('table-success');
}

function ocultarSeccionesCampos() {
    document.getElementById('seccion_cantidad').classList.add('d-none');
    document.getElementById('seccion_fecha').classList.add('d-none');
    document.getElementById('seccion_venta').classList.add('d-none');
    document.getElementById('crear_btn_guardar').disabled = true;
    productoSeleccionado = null;
}

let timeoutBusqueda;
document.getElementById('buscar_producto_input').addEventListener('keyup', function() {
    clearTimeout(timeoutBusqueda);
    const nombre = this.value.trim();
    if (nombre.length < 2) {
        document.getElementById('productos_resultado').classList.add('d-none');
        document.getElementById('mensaje_producto_error').classList.add('d-none');
        return;
    }
    timeoutBusqueda = setTimeout(() => buscarProducto(), 400);
});

document.getElementById('crear_cantidad').addEventListener('input', function() {
    if (!productoSeleccionado) return;
    const val    = parseInt(this.value);
    const alerta = document.getElementById('crear_alerta_cantidad');
    const texto  = document.getElementById('crear_alerta_texto');
    const btn    = document.getElementById('crear_btn_guardar');

    if (val > productoSeleccionado.stock) {
        texto.textContent = `⚠️ Stock insuficiente. Solo hay ${productoSeleccionado.stock} unidades disponibles.`;
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

document.getElementById('formCrear').addEventListener('submit', function(e) {
    if (!productoSeleccionado) { e.preventDefault(); alert('⚠️ Por favor selecciona un producto.'); return; }
    const val = parseInt(document.getElementById('crear_cantidad').value);
    if (val < 1 || isNaN(val)) { e.preventDefault(); alert('⚠️ La cantidad debe ser al menos 1.'); return; }
    if (val > productoSeleccionado.stock) { e.preventDefault(); alert(`⚠️ Solo hay ${productoSeleccionado.stock} unidades disponibles.`); return; }
    const venta = document.getElementById('crear_id_venta').value;
    if (!venta) { e.preventDefault(); alert('⚠️ Por favor selecciona una venta.'); }
});

document.getElementById('crearModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('formCrear').reset();
    document.getElementById('productos_resultado').classList.add('d-none');
    document.getElementById('producto_seleccionado').classList.add('d-none');
    document.getElementById('mensaje_producto_error').classList.add('d-none');
    document.getElementById('crear_alerta_cantidad').classList.add('d-none');
    ocultarSeccionesCampos();
});

/* ============================================================
   MODAL EDITAR
============================================================ */
let editMaxCantidad = 0;

function abrirModalEditar(idVenta, idProducto, cantidadActual, stockActual) {
    editMaxCantidad = cantidadActual + stockActual;

    document.getElementById('edit_id_venta').value       = idVenta;
    document.getElementById('edit_id_producto').value    = idProducto;
    document.getElementById('edit_venta_id').textContent = idVenta;
    document.getElementById('edit_cantidad').value       = cantidadActual;
    document.getElementById('edit_cantidad').max         = editMaxCantidad;
    document.getElementById('edit_max_label').textContent = editMaxCantidad;
    document.getElementById('edit_alerta_cantidad').classList.add('d-none');
    document.getElementById('edit_btn_guardar').disabled = false;

    fetch(`/empleado/detalleventas/venta-info/${idVenta}`)
        .then(r => r.json())
        .then(data => {
            // ventaInfo devuelve array; buscamos el producto correcto
            const item = Array.isArray(data)
                ? (data.find(d => d.id_producto == idProducto) || data[0])
                : data;
            document.getElementById('edit_producto').textContent = item.producto || 'N/A';
            document.getElementById('edit_stock').textContent   = stockActual + ' disponibles';
        });

    new bootstrap.Modal(document.getElementById('editarModal')).show();
}

document.getElementById('edit_cantidad').addEventListener('input', function() {
    const val    = parseInt(this.value);
    const alerta = document.getElementById('edit_alerta_cantidad');
    const texto  = document.getElementById('edit_alerta_texto');
    const btn    = document.getElementById('edit_btn_guardar');

    if (val > editMaxCantidad) {
        texto.textContent = `⚠️ Máximo permitido: ${editMaxCantidad} unidades.`;
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

document.getElementById('formEditar').addEventListener('submit', function(e) {
    const val = parseInt(document.getElementById('edit_cantidad').value);
    if (val > editMaxCantidad || val < 1 || isNaN(val)) {
        e.preventDefault();
        alert(`⚠️ La cantidad debe estar entre 1 y ${editMaxCantidad} unidades.`);
    }
});
</script>

<div style="position: fixed; bottom: 10px; left: 0; width: 100%; text-align: center; margin-left: 115px;">
    <p style="color: #aaaaaa; font-size: 13px; margin: 0;">Copyright © 2026 Fonrio</p>
</div>
</body>
</html>
