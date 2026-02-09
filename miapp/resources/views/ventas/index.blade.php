<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <link rel="icon" href="{{ asset('Imagenes/Logo.webp') }}" type="image/webp">
    <title>Ventas</title>

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
            </div>
            <hr>
            <div class="seccion-menu">
                <a href="{{ route('productos.index') }}" class="elemento-menu">
                    <i class="ri-box-3-line"></i><span>Productos</span>
                </a>
                <a href="{{ route('proveedor.index') }}" class="elemento-menu activo">
                    <i class="ri-truck-line"></i><span>Proveedores</span>
                </a>
                <div class="dropdown">
                    <a class="elemento-menu d-flex align-items-center text-white text-decoration-none dropdown-toggle"
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
                <h1>Registro de Ventas</h1>
            </div>

            {{-- Mensaje de éxito --}}
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

            {{-- Mensaje de error --}}
            @if(session('error'))
                <div id="alertaError" class="alert alert-danger text-center mt-3">
                    <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
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

            <div class="d-flex justify-content-end mt-4 gap-2">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fa fa-plus"></i> Añadir Venta
                </button>

                <a href="{{ route('detalleventas.index') }}" class="btn btn-primary">
                    <i class="fa fa-list"></i> Detalle Ventas
                </a>
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
                                <button class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editarModal{{ $venta->ID_Venta }}">
                                    <i class="fa fa-edit"></i>
                                </button>

                                <button class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#eliminarModal{{ $venta->ID_Venta }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- Modal Editar --}}
                        <div class="modal fade" id="editarModal{{ $venta->ID_Venta }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('ventas.update') }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="ID_Venta" value="{{ $venta->ID_Venta }}">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Editar Venta</h5>
                                            <button class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <label>Documento Cliente</label>
                                            <input name="Documento_Cliente" class="form-control"
                                                   value="{{ $venta->Documento_Cliente }}">
                                            <label class="mt-3">Documento Empleado</label>
                                            <input name="Documento_Empleado" class="form-control"
                                                   value="{{ $venta->Documento_Empleado }}">
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-warning" type="submit">Actualizar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Modal Eliminar --}}
                        <div class="modal fade" id="eliminarModal{{ $venta->ID_Venta }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('ventas.destroy') }}">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="ID_Venta" value="{{ $venta->ID_Venta }}">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Eliminar Venta</h5>
                                            <button class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            ¿Seguro que deseas eliminar esta venta?
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
                            <td colspan="4" class="text-muted">No hay ventas registradas.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ============================================ --}}
            {{-- MODAL CREAR VENTA CON AUTOCOMPLETE --}}
            {{-- ============================================ --}}
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
                                            placeholder="Ej: 1013262104"
                                            autocomplete="off"
                                            required>
                                        
                                        {{-- Spinner de carga --}}
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

                                {{-- Mensaje de resultado de búsqueda --}}
                                <div id="mensajeCliente" class="alert d-none mb-3"></div>

                                {{-- Campos para Cliente Nuevo (ocultos por defecto) --}}
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

                                {{-- Documento Empleado --}}
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fa fa-user-tie"></i> Documento Empleado
                                    </label>
                                    <input type="text" 
                                           name="Documento_Empleado" 
                                           class="form-control" 
                                           value="{{ session('documento') }}" 
                                           required>
                                    <small class="text-muted">Empleado que realiza la venta</small>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

{{-- ============================================ --}}
{{-- JAVASCRIPT PARA AUTOCOMPLETE --}}
{{-- ============================================ --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputDocumento = document.getElementById('buscarCliente');
    const spinnerBusqueda = document.getElementById('spinnerBusqueda');
    const mensajeCliente = document.getElementById('mensajeCliente');
    const camposNuevoCliente = document.getElementById('camposNuevoCliente');
    const inputClienteNuevo = document.getElementById('clienteNuevo');
    const nombreCliente = document.getElementById('nombreCliente');
    const apellidoCliente = document.getElementById('apellidoCliente');
    const formVenta = document.getElementById('formVenta');
    
    let timeoutBusqueda;
    let clienteValidado = false;

    // Buscar cliente cuando escribe o presiona Enter
    inputDocumento.addEventListener('keyup', function(e) {
        clearTimeout(timeoutBusqueda);
        
        const documento = this.value.trim();
        
        // Resetear validación
        clienteValidado = false;
        
        // Si el documento es muy corto, no buscar
        if (documento.length < 5) {
            ocultarMensajes();
            return;
        }

        // Si presiona Enter, buscar inmediatamente
        if (e.key === 'Enter') {
            e.preventDefault();
            buscarCliente(documento);
            return;
        }

        // Esperar 1 segundo después de dejar de escribir
        timeoutBusqueda = setTimeout(() => {
            buscarCliente(documento);
        }, 1000);
    });

    function buscarCliente(documento) {
        spinnerBusqueda.style.display = 'block';
        mensajeCliente.classList.add('d-none');
        clienteValidado = false;
        }

        function mostrarClienteEncontrado(cliente) {
        mensajeCliente.className = 'alert alert-success';
        mensajeCliente.innerHTML = `
            <i class="fa fa-check-circle"></i> 
            <strong>Cliente encontrado:</strong> 
            ${cliente.Nombre_Cliente} ${cliente.Apellido_Cliente}
            ${cliente.ID_Estado == '1' ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>'}
        `;
        mensajeCliente.classList.remove('d-none');
        
        // Ocultar campos de nuevo cliente
        camposNuevoCliente.style.display = 'none';
        inputClienteNuevo.value = '0';
        
        // Quitar required de los campos del cliente
        nombreCliente.removeAttribute('required');
        apellidoCliente.removeAttribute('required');
        
        // ✅ MARCAR COMO VALIDADO
        clienteValidado = true;
    }

    function mostrarFormularioNuevoCliente() {
        mensajeCliente.className = 'alert alert-warning';
        mensajeCliente.innerHTML = `
            <i class="fa fa-exclamation-triangle"></i> 
            <strong>Cliente no encontrado.</strong> 
            Complete los datos para registrar un nuevo cliente:
        `;
        mensajeCliente.classList.remove('d-none');
        
        // Mostrar campos de nuevo cliente
        camposNuevoCliente.style.display = 'block';
        inputClienteNuevo.value = '1';
        
        // Agregar required a los campos del cliente
        nombreCliente.setAttribute('required', 'required');
        apellidoCliente.setAttribute('required', 'required');
        
        // ✅ MARCAR COMO VALIDADO (para permitir envío con datos completos)
        clienteValidado = true;
    }

    function ocultarMensajes() {
        mensajeCliente.classList.add('d-none');
        camposNuevoCliente.style.display = 'none';
        inputClienteNuevo.value = '0';
        nombreCliente.removeAttribute('required');
        apellidoCliente.removeAttribute('required');
        clienteValidado = false;
    }

    // Limpiar formulario al cerrar el modal
    const modalCrear = document.getElementById('crearModal');
    modalCrear.addEventListener('hidden.bs.modal', function() {
        formVenta.reset();
        ocultarMensajes();
        spinnerBusqueda.style.display = 'none';
        clienteValidado = false;
    });
});
</script>

</body>
</html>