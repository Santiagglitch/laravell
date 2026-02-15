<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="{{ asset('Imagenes/Logo.webp') }}" type="image/webp">
    <title>Empleados</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">

    {{-- SheetJS para leer/escribir Excel --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

<div class="d-flex" style="min-height:100vh">

    {{-- ===== BARRA LATERAL ===== --}}
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
                    <a class="elemento-menu d-flex align-items-center text-white text-decoration-none dropdown-toggle activo"
                       href="#" role="button" data-bs-toggle="dropdown">
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

    {{-- ===== CONTENIDO PRINCIPAL ===== --}}
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
                    <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item" href="#">Mi perfil</a></li>
                        <li><a class="dropdown-item" href="#">Editar perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
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

            {{-- Título --}}
            <div class="d-flex justify-content-center align-items-center gap-3">
                <img src="{{ asset('Imagenes/Logo.webp') }}" style="height:48px;">
                <h1>Registro de Empleado</h1>
            </div>

            {{-- Alertas --}}
            @if(session('mensaje'))
                <div id="alertaMensaje" class="alert alert-success text-center mt-3">{{ session('mensaje') }}</div>
                <script>setTimeout(()=>{let a=document.getElementById('alertaMensaje');if(a){a.style.transition="opacity 0.5s";a.style.opacity=0;setTimeout(()=>a.remove(),500);}},2000);</script>
            @endif
            @if($errors->any())
                <div class="alert alert-danger mt-3">
                    <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            {{-- Botones de acción (igual que en productos) --}}
            <div class="d-flex justify-content-end mt-4 gap-2">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fa fa-plus"></i> Añadir Empleado
                </button>
                <button class="btn btn-warning" onclick="document.getElementById('archivoExcelEmpleados').click()">
                    <i class="fa fa-upload"></i> Importar desde Excel
                </button>
                <input type="file" id="archivoExcelEmpleados" accept=".xlsx,.xls" style="display:none;"
                       onchange="importarDesdeExcel(event)">
                <button class="btn btn-primary" onclick="iniciarExportacion()">
                    <i class="fa fa-download"></i> Exportar a Excel
                </button>
            </div>

            {{-- Barra de progreso --}}
            <div id="progreso" class="mt-2"></div>

            {{-- Tabla --}}
            <div class="table-responsive mt-4">
                <table class="table table-bordered table-striped table-hover text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Documento</th>
                            <th>Tipo Doc</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Edad</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Género</th>
                            <th>Estado</th>
                            <th>Rol</th>
                            <th>Foto</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($empleados as $emp)
                        <tr>
                            <td>{{ $emp->Documento_Empleado }}</td>
                            <td>{{ $emp->Tipo_Documento }}</td>
                            <td>{{ $emp->Nombre_Usuario }}</td>
                            <td>{{ $emp->Apellido_Usuario }}</td>
                            <td>{{ $emp->Edad }}</td>
                            <td>{{ $emp->Correo_Electronico }}</td>
                            <td>{{ $emp->Telefono }}</td>
                            <td>{{ $emp->Genero }}</td>
                            <td>{{ $emp->estado->Nombre_Estado ?? 'Sin estado' }}</td>
                            <td>{{ $emp->rol->Nombre ?? 'Sin rol' }}</td>
                            <td>
                                @if($emp->Fotos)
                                    @php
                                        $springBase = rtrim(config('services.spring.base_url', 'http://192.168.80.13:8080'), '/');
                                        $foto = trim($emp->Fotos);
                                        $fotoUrl = str_starts_with($foto, 'http')
                                            ? $foto
                                            : (str_starts_with($foto, 'uploads/') ? $springBase.'/'.$foto : asset($foto));
                                    @endphp
                                    <img src="{{ $fotoUrl }}" width="50" height="50" class="rounded" style="object-fit:cover;">
                                @else
                                    <i class="fa-solid fa-image text-secondary" style="font-size:30px;"></i>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editarModal{{ $emp->Documento_Empleado }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#eliminarModal{{ $emp->Documento_Empleado }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- Modal Editar --}}
                        <div class="modal fade" id="editarModal{{ $emp->Documento_Empleado }}">
                            <div class="modal-dialog modal-lg">
                                <form method="POST" action="{{ route('empleados.update') }}" enctype="multipart/form-data" autocomplete="off">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="Documento_Empleado" value="{{ $emp->Documento_Empleado }}">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning text-white">
                                            <h5 class="modal-title">Editar Empleado</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body row g-3">
                                            <div class="col-md-6">
                                                <label>Nombre</label>
                                                <input class="form-control" name="Nombre_Usuario" value="{{ $emp->Nombre_Usuario }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Apellido</label>
                                                <input class="form-control" name="Apellido_Usuario" value="{{ $emp->Apellido_Usuario }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Edad</label>
                                                <input class="form-control" name="Edad" value="{{ $emp->Edad }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Correo</label>
                                                <input type="email" class="form-control" name="Correo_Electronico" value="{{ $emp->Correo_Electronico }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Teléfono</label>
                                                <input class="form-control" name="Telefono" value="{{ $emp->Telefono }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Tipo de documento</label>
                                                <select name="Tipo_Documento" class="form-control" required>
                                                    <option value="CC"  {{ $emp->Tipo_Documento=='CC'  ? 'selected':'' }}>Cédula de ciudadanía</option>
                                                    <option value="TI"  {{ $emp->Tipo_Documento=='TI'  ? 'selected':'' }}>Tarjeta de identidad</option>
                                                    <option value="CE"  {{ $emp->Tipo_Documento=='CE'  ? 'selected':'' }}>Cédula de extranjería</option>
                                                    <option value="PA"  {{ $emp->Tipo_Documento=='PA'  ? 'selected':'' }}>Pasaporte</option>
                                                    <option value="NIT" {{ $emp->Tipo_Documento=='NIT' ? 'selected':'' }}>NIT</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Género</label>
                                                <select name="Genero" class="form-control" required>
                                                    <option value="F" {{ $emp->Genero=='F'?'selected':'' }}>Femenino</option>
                                                    <option value="M" {{ $emp->Genero=='M'?'selected':'' }}>Masculino</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Estado</label>
                                                <select name="ID_Estado" class="form-control" required>
                                                    <option value="1" {{ (int)$emp->ID_Estado===1?'selected':'' }}>Activo</option>
                                                    <option value="2" {{ (int)$emp->ID_Estado===2?'selected':'' }}>Inactivo</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Rol</label>
                                                <select name="ID_Rol" class="form-control" required>
                                                    <option value="1" {{ (int)$emp->ID_Rol===1?'selected':'' }}>Administrador</option>
                                                    <option value="2" {{ (int)$emp->ID_Rol===2?'selected':'' }}>Empleado</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 position-relative">
                                                <label>Nueva Contraseña</label>
                                                <input type="password" name="Contrasena"
                                                       id="editarContrasena{{ $emp->Documento_Empleado }}"
                                                       class="form-control" placeholder="Dejar en blanco para no cambiar"
                                                       autocomplete="new-password" minlength="4">
                                                <i class="fa fa-eye position-absolute" style="top:38px;right:10px;cursor:pointer;"
                                                   onclick="togglePassword('editarContrasena{{ $emp->Documento_Empleado }}',this)"></i>
                                                <small class="text-muted">Mínimo 4 caracteres (dejar vacío para no cambiar)</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Nueva Foto (opcional)</label>
                                                <input type="file" name="Fotos" class="form-control">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-warning">Actualizar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Modal Eliminar --}}
                        <div class="modal fade" id="eliminarModal{{ $emp->Documento_Empleado }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('empleados.destroy') }}">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="Documento_Empleado" value="{{ $emp->Documento_Empleado }}">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Eliminar Empleado</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            ¿Seguro que deseas eliminar este empleado?
                                            <div class="alert alert-warning mt-3">
                                                <strong>Nombre:</strong> {{ $emp->Nombre_Usuario }} {{ $emp->Apellido_Usuario }}<br>
                                                <strong>Documento:</strong> {{ $emp->Documento_Empleado }}
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
                        <tr><td colspan="12" class="text-center text-muted">No hay empleados registrados.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Modal Crear --}}
            <div class="modal fade" id="crearModal">
                <div class="modal-dialog modal-lg">
                    <form method="POST" action="{{ route('empleados.store') }}" enctype="multipart/form-data" autocomplete="off">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">Añadir Empleado</h5>
                                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body row g-3">
                                <div class="col-md-6">
                                    <label>Documento</label>
                                    <input name="Documento_Empleado" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label>Tipo de documento</label>
                                    <select name="Tipo_Documento" class="form-control" required>
                                        <option value="" selected disabled>--Seleccione--</option>
                                        <option value="CC">Cédula de ciudadanía</option>
                                        <option value="TI">Tarjeta de identidad</option>
                                        <option value="CE">Cédula de extranjería</option>
                                        <option value="PA">Pasaporte</option>
                                        <option value="NIT">NIT</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Nombre</label>
                                    <input name="Nombre_Usuario" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label>Apellido</label>
                                    <input name="Apellido_Usuario" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label>Edad</label>
                                    <input type="number" name="Edad" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label>Correo</label>
                                    <input type="email" name="Correo_Electronico" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label>Teléfono</label>
                                    <input name="Telefono" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label>Género</label>
                                    <select name="Genero" class="form-control" required>
                                        <option value="" selected disabled>--Seleccione--</option>
                                        <option value="F">Femenino</option>
                                        <option value="M">Masculino</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Estado</label>
                                    <select name="ID_Estado" class="form-control" required>
                                        <option value="" selected disabled>--Seleccione--</option>
                                        <option value="1">Activo</option>
                                        <option value="2">Inactivo</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Rol</label>
                                    <select name="ID_Rol" class="form-control" required>
                                        <option value="" selected disabled>--Seleccione--</option>
                                        <option value="1">Administrador</option>
                                        <option value="2">Empleado</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Foto</label>
                                    <input type="file" name="Fotos" class="form-control">
                                </div>
                                <div class="col-md-6 position-relative">
                                    <label>Contraseña</label>
                                    <input type="password" name="Contrasena" id="contrasenaInput"
                                           class="form-control" required minlength="4"
                                           placeholder="Mínimo 4 caracteres">
                                    <i class="fa fa-eye position-absolute" id="toggleContrasena"
                                       style="top:38px;right:10px;cursor:pointer;"></i>
                                    <small class="text-muted">Debe tener al menos 4 caracteres</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button class="btn btn-success" type="submit">Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>{{-- /container --}}
    </div>{{-- /contenido-principal --}}
</div>{{-- /d-flex --}}

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ============================================
// TOGGLE CONTRASEÑA
// ============================================
const toggleContrasena = document.getElementById('toggleContrasena');
const contrasenaInput  = document.getElementById('contrasenaInput');
if (toggleContrasena && contrasenaInput) {
    toggleContrasena.addEventListener('click', () => {
        contrasenaInput.type = contrasenaInput.type === 'password' ? 'text' : 'password';
        toggleContrasena.classList.toggle('fa-eye-slash');
    });
}
function togglePassword(idInput, icon) {
    const input = document.getElementById(idInput);
    if (input) {
        input.type = input.type === 'password' ? 'text' : 'password';
        icon.classList.toggle('fa-eye-slash');
    }
}

// ============================================
// HELPERS (igual que en productos)
// ============================================
function normalizarClaves(obj) {
    const r = {};
    Object.keys(obj).forEach(key => {
        r[key.normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().trim()] = obj[key];
    });
    return r;
}
function buscarClave(o, ...ps) {
    for (const p of ps) {
        const n = p.normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().trim();
        if (o[n] !== undefined) return o[n];
    }
    return null;
}

// ============================================
// IMPORTACIÓN DESDE EXCEL
// Acepta exactamente las columnas de tu Excel:
//   Documento | Tipo Doc | Nombre | Apellido | Edad
//   Correo | Teléfono | Género | Estado | Rol | contraseña | Fotos
// ============================================
async function importarDesdeExcel(event) {
    const archivo = event.target.files[0];
    if (!archivo) return;

    const progresoDiv = document.getElementById('progreso');
    progresoDiv.className = 'alert alert-info';
    progresoDiv.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Leyendo archivo Excel...';

    try {
        const data     = await archivo.arrayBuffer();
        const workbook = XLSX.read(data);
        const hoja     = workbook.Sheets[workbook.SheetNames[0]];
        const filas    = XLSX.utils.sheet_to_json(hoja);

        if (filas.length === 0) {
            progresoDiv.className = 'alert alert-warning';
            progresoDiv.innerHTML = '<i class="fa fa-exclamation-triangle"></i> El archivo está vacío';
            return;
        }

        // Mostrar columnas reales del Excel para diagnóstico
        const columnasReales = Object.keys(filas[0]);
        console.log('✅ Columnas detectadas en el Excel:', columnasReales);

        // Función: busca un valor en la fila por múltiples posibles nombres de columna
        // (ignora mayúsculas, acentos, guiones bajos y espacios extra)
        function limpiar(s) {
            return String(s ?? '')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')  // quita acentos
                .replace(/[\s_]+/g, '')            // quita espacios y guiones bajos
                .toLowerCase()
                .trim();
        }
        function leerCampo(fila, ...nombres) {
            const limpio = {};
            for (const [k, v] of Object.entries(fila)) limpio[limpiar(k)] = v;
            for (const nombre of nombres) {
                const val = limpio[limpiar(nombre)];
                if (val !== undefined && val !== null && val !== '') return val;
            }
            return null;
        }

        // Mapear cada fila del Excel al objeto que espera el PHP
        const datosValidados = filas.map(fila => ({
            Documento_Empleado: leerCampo(fila, 'Documento_Empleado', 'Documento'),
            Tipo_Documento:     leerCampo(fila, 'Tipo_Documento', 'Tipo Doc', 'TipoDoc') ?? 'CC',
            Nombre_Usuario:     leerCampo(fila, 'Nombre_Usuario', 'Nombre'),
            Apellido_Usuario:   leerCampo(fila, 'Apellido_Usuario', 'Apellido'),
            Edad:               leerCampo(fila, 'Edad'),
            Correo_Electronico: leerCampo(fila, 'Correo_Electronico', 'Correo', 'Email'),
            Telefono:           leerCampo(fila, 'Telefono', 'Teléfono'),
            Genero:             leerCampo(fila, 'Genero', 'Género'),
            Estado:             leerCampo(fila, 'ID_Estado', 'Estado') ?? 1,
            Rol:                leerCampo(fila, 'ID_Rol', 'Rol') ?? 2,
            Fotos:              leerCampo(fila, 'Fotos', 'Foto') ?? null,
            Contrasena:         leerCampo(fila, 'Contrasena', 'Contraseña', 'contraseña', 'Password') ?? null,
        }));

        // Mostrar preview de la primera fila para diagnóstico
        console.log('📦 Primera fila procesada:', JSON.stringify(datosValidados[0], null, 2));

        // Verificar que al menos el documento esté presente
        const sinDocumento = datosValidados.filter(d => !d.Documento_Empleado);
        if (sinDocumento.length === datosValidados.length) {
            progresoDiv.className = 'alert alert-danger';
            progresoDiv.innerHTML = `
                <i class="fa fa-exclamation-triangle"></i>
                <strong>Error: No se detectó la columna "Documento".</strong><br>
                <small>Columnas encontradas en el Excel: <code>${columnasReales.join(', ')}</code></small>
            `;
            event.target.value = '';
            return;
        }

        // Importar en lotes de 10
        const tamañoLote = 10;
        let importados   = 0;
        let todosLosErrores = [];

        for (let i = 0; i < datosValidados.length; i += tamañoLote) {
            const lote    = datosValidados.slice(i, i + tamañoLote);
            const progreso = Math.round(((i + lote.length) / datosValidados.length) * 100);

            progresoDiv.innerHTML = `
                <div class="d-flex align-items-center">
                    <strong>Importando empleados...</strong>
                    <div class="ms-auto">${progreso}%</div>
                </div>
                <div class="progress mt-2">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning"
                         style="width: ${progreso}%"></div>
                </div>
                <small class="text-muted mt-2 d-block">
                    Registros: ${i + lote.length} / ${datosValidados.length}
                </small>`;

            const response = await fetch('/migracion/empleados/importar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ modulo: 'empleados', datos: lote })
            });

            const texto = await response.text();
            let resultado;
            try { resultado = JSON.parse(texto); }
            catch(e) {
                throw new Error('El servidor devolvió HTML en lugar de JSON. Verifica que la ruta /migracion/empleados/importar esté registrada en web.php.');
            }

            if (!resultado.success) throw new Error(resultado.mensaje);
            importados += resultado.importados || 0;
            if (resultado.errores?.length > 0) todosLosErrores.push(...resultado.errores);

            await new Promise(r => setTimeout(r, 300));
        }

        // Mostrar resultado final con errores si los hay
        let htmlFinal = `
            <i class="fa fa-check-circle"></i>
            <strong>¡Importación completada!</strong>
            <br><small>Se importaron <strong>${importados}</strong> empleados correctamente.</small>
        `;
        if (todosLosErrores.length > 0) {
            htmlFinal += `
                <hr class="my-2">
                <small><strong>Advertencias (${todosLosErrores.length}):</strong>
                <ul class="mb-0 mt-1 text-start">
                    ${todosLosErrores.map(e => `<li>${e}</li>`).join('')}
                </ul></small>
            `;
        }
        progresoDiv.className = importados > 0 ? 'alert alert-success' : 'alert alert-warning';
        progresoDiv.innerHTML = htmlFinal;

        if (importados > 0) setTimeout(() => location.reload(), 4000);

    } catch (error) {
        console.error('Error:', error);
        progresoDiv.className = 'alert alert-danger';
        progresoDiv.innerHTML = `<i class="fa fa-exclamation-triangle"></i> Error: ${error.message}`;
    }

    event.target.value = '';
}

// ============================================
// EXPORTACIÓN A EXCEL
// ============================================
async function iniciarExportacion() {
    const btnExportar = event.target;
    btnExportar.disabled = true;
    btnExportar.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Exportando...';

    const progresoDiv = document.getElementById('progreso');

    try {
        progresoDiv.className = 'alert alert-info';
        progresoDiv.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Iniciando exportación...';

        // 1. Iniciar sesión de exportación
        const initResp = await fetch('/migracion/empleados/iniciar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ modulo: 'empleados' })
        });
        const initData = await initResp.json();
        if (!initData.success) throw new Error(initData.mensaje);

        // 2. Recopilar todos los lotes
        let todosLosDatos = [];
        let completado = false, intentos = 0;

        while (!completado && intentos < 100) {
            const loteResp = await fetch('/migracion/empleados/lote', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ modulo: 'empleados' })
            });
            const loteData = await loteResp.json();
            if (!loteData.success) throw new Error(loteData.mensaje);
            if (loteData.datos?.length > 0) todosLosDatos = todosLosDatos.concat(loteData.datos);

            progresoDiv.innerHTML = `
                <div class="d-flex align-items-center">
                    <strong>Exportando empleados...</strong>
                    <div class="ms-auto">${loteData.progreso}%</div>
                </div>
                <div class="progress mt-2">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                         style="width: ${loteData.progreso}%"></div>
                </div>
                <small class="text-muted mt-2 d-block">
                    Registros: ${loteData.registros_migrados} / ${loteData.total_registros} (Lote ${loteData.lote_actual})
                </small>`;

            completado = loteData.completado;
            intentos++;
            await new Promise(r => setTimeout(r, 300));
        }

        if (todosLosDatos.length === 0) {
            progresoDiv.className = 'alert alert-warning';
            progresoDiv.innerHTML = '<i class="fa fa-exclamation-triangle"></i> No hay datos para exportar';
            btnExportar.disabled = false;
            btnExportar.innerHTML = '<i class="fa fa-download"></i> Exportar a Excel';
            return;
        }

        progresoDiv.innerHTML += '<br><i class="fa fa-spinner fa-spin"></i> Generando Excel...';

        // 3. Construir hoja principal
        // Se usan los mismos nombres que acepta importarEmpleados en el PHP
        const hoja = todosLosDatos.map(emp => ({
            'Documento_Empleado': emp.Documento_Empleado,
            'Tipo_Documento':     emp.Tipo_Documento,
            'Nombre_Usuario':     emp.Nombre_Usuario,
            'Apellido_Usuario':   emp.Apellido_Usuario,
            'Edad':               emp.Edad,
            'Correo_Electronico': emp.Correo_Electronico,
            'Telefono':           emp.Telefono,
            'Genero':             emp.Genero,
            'Estado':             emp.Estado,   // texto "Activo"/"Inactivo" — el PHP lo convierte
            'Rol':                emp.Rol,      // texto "Administrador"/"Empleado" — el PHP lo convierte
            'Fotos':              emp.Fotos,
            // contraseña NO se exporta por seguridad
        }));

        // 4. Función de estilos (igual que en productos)
        function estilos(ws, colorH, colorF) {
            const rng  = XLSX.utils.decode_range(ws['!ref']);
            const cols = [];
            for (let C = rng.s.c; C <= rng.e.c; C++) {
                let w = 10;
                for (let R = rng.s.r; R <= rng.e.r; R++) {
                    const c = ws[XLSX.utils.encode_cell({r:R,c:C})];
                    if (c?.v) w = Math.max(w, c.v.toString().length);
                }
                cols.push({wch: w+2});
            }
            ws['!cols'] = cols;
            for (let C = rng.s.c; C <= rng.e.c; C++) {
                const a = XLSX.utils.encode_cell({r:0,c:C});
                if (!ws[a]) continue;
                ws[a].s = {
                    font: {name:'Calibri',sz:12,bold:true,color:{rgb:'FFFFFF'}},
                    fill: {fgColor:{rgb:colorH}},
                    alignment: {horizontal:'center',vertical:'center'},
                    border: {top:{style:'thin',color:{rgb:'000000'}},bottom:{style:'thin',color:{rgb:'000000'}},left:{style:'thin',color:{rgb:'000000'}},right:{style:'thin',color:{rgb:'000000'}}}
                };
            }
            for (let R = rng.s.r+1; R <= rng.e.r; R++) {
                for (let C = rng.s.c; C <= rng.e.c; C++) {
                    const a = XLSX.utils.encode_cell({r:R,c:C});
                    if (!ws[a]) continue;
                    ws[a].s = {
                        font: {name:'Calibri',sz:11},
                        fill: {fgColor:{rgb: R%2===0?'FFFFFF':colorF}},
                        alignment: {horizontal:'left',vertical:'center'},
                        border: {top:{style:'thin',color:{rgb:'D3D3D3'}},bottom:{style:'thin',color:{rgb:'D3D3D3'}},left:{style:'thin',color:{rgb:'D3D3D3'}},right:{style:'thin',color:{rgb:'D3D3D3'}}}
                    };
                }
            }
        }

        // 5. Crear libro Excel con dos hojas
        const wb  = XLSX.utils.book_new();
        const ws1 = XLSX.utils.json_to_sheet(hoja);
        estilos(ws1, '4472C4', 'F2F2F2');
        XLSX.utils.book_append_sheet(wb, ws1, 'Empleados');

        const info = XLSX.utils.aoa_to_sheet([
            ['REPORTE DE EMPLEADOS'],[''],
            ['Fecha de Generación:', new Date().toLocaleString('es-ES')],
            ['Total Empleados:', todosLosDatos.length],
            ['Generado por:', 'TECNICELL RM']
        ]);
        if (info['A1']) {
            info['A1'].s = {font:{name:'Calibri',sz:16,bold:true,color:{rgb:'4472C4'}},alignment:{horizontal:'center'}};
        }
        info['!cols'] = [{wch:25},{wch:30}];
        XLSX.utils.book_append_sheet(wb, info, 'Información');

        // 6. Descargar
        XLSX.writeFile(wb, `Empleados_${new Date().toISOString().split('T')[0]}.xlsx`, {bookType:'xlsx', cellStyles:true});

        progresoDiv.className = 'alert alert-success';
        progresoDiv.innerHTML = `
            <i class="fa fa-check-circle"></i> <strong>¡Exportación completada!</strong>
            <br><small>${todosLosDatos.length} empleados exportados</small>
        `;
        setTimeout(() => { progresoDiv.innerHTML=''; progresoDiv.className=''; }, 8000);

    } catch (error) {
        progresoDiv.className = 'alert alert-danger';
        progresoDiv.innerHTML = `<i class="fa fa-exclamation-triangle"></i> Error: ${error.message}`;
    } finally {
        btnExportar.disabled = false;
        btnExportar.innerHTML = '<i class="fa fa-download"></i> Exportar a Excel';
    }
}
</script>

</body>
</html>
