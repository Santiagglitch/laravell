<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="{{ asset('Imagenes/Logo.webp') }}" type="image/webp">
    <title>Clientes - Empleado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

<div class="d-flex" style="min-height: 100vh;">

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
                <a href="{{ route('ventas.indexEm') }}" class="elemento-menu">
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
                <a href="{{ route('clientes.indexEm') }}" class="elemento-menu activo">
                    <i class="ri-user-line"></i><span>Clientes</span>
                </a>
            </div>
        </div>
    </div>

    <div class="contenido-principal flex-grow-1">

        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <a class="navbar-brand">Sistema gestión de inventarios - Empleado</a>

                <div class="dropdown ms-auto">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle"
                       data-bs-toggle="dropdown">
                        <img src="{{ asset('fotos_empleados/686fe89fe865f_Foto Kevin.jpeg') }}"
                             alt="Perfil" width="32" height="32" class="rounded-circle me-2">
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

            <div class="d-flex justify-content-center align-items-center gap-3 mb-4">
                <img src="{{ asset('Imagenes/Logo.webp') }}" style="height:48px;">
                <h1 class="mb-0">Registro de Clientes</h1>
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
                    }, 5000);
                </script>
            @endif

            @if($errors->any())
                <div id="alertaErrores" class="alert alert-danger mt-3">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <script>
                    setTimeout(() => {
                        let alerta = document.getElementById('alertaErrores');
                        if (alerta) {
                            alerta.style.transition = "opacity 0.5s";
                            alerta.style.opacity = 0;
                            setTimeout(() => alerta.remove(), 500);
                        }
                    }, 4000);
                </script>
            @endif

            <div class="d-flex justify-content-end mt-4 gap-2">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fa fa-plus"></i> Añadir Cliente
                </button>
                <button class="btn btn-warning" onclick="document.getElementById('archivoExcel').click()">
                    <i class="fa fa-upload"></i> Importar desde Excel
                </button>
                <input type="file" id="archivoExcel" accept=".xlsx,.xls" style="display:none;"
                       onchange="importarDesdeExcel(event)">
                <button class="btn btn-primary" onclick="iniciarExportacion()">
                    <i class="fa fa-download"></i> Exportar a Excel
                </button>
            </div>

            <div id="progreso" class="mt-2"></div>

            <div class="table-responsive mt-4">
                <table class="table table-bordered table-hover table-striped align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Documento</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($clientes as $cli)
                        <tr>
                            <td>{{ $cli->Documento_Cliente }}</td>
                            <td>{{ $cli->Nombre_Cliente }}</td>
                            <td>{{ $cli->Apellido_Cliente }}</td>
                            <td>
                                @if($cli->ID_Estado == 1)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editar{{ $cli->Documento_Cliente }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#eliminar{{ $cli->Documento_Cliente }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- MODAL EDITAR -->
                        <div class="modal fade" id="editar{{ $cli->Documento_Cliente }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('clientes.updateEm') }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="Documento_Cliente" value="{{ $cli->Documento_Cliente }}">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Editar Cliente</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <label>Documento</label>
                                            <input class="form-control mb-3" name="Documento_Cliente"
                                                   value="{{ $cli->Documento_Cliente }}" readonly>

                                            <label>Nombre</label>
                                            <input class="form-control mb-3" name="Nombre_Cliente"
                                                   value="{{ $cli->Nombre_Cliente }}" required>

                                            <label>Apellido</label>
                                            <input class="form-control mb-3" name="Apellido_Cliente"
                                                   value="{{ $cli->Apellido_Cliente }}" required>

                                            <label>Estado</label>
                                            <select name="ID_Estado" class="form-control" required>
                                                <option value="1" {{ $cli->ID_Estado==1?'selected':'' }}>Activo</option>
                                                <option value="2" {{ $cli->ID_Estado==2?'selected':'' }}>Inactivo</option>
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

                        <!-- MODAL ELIMINAR -->
                        <div class="modal fade" id="eliminar{{ $cli->Documento_Cliente }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('clientes.destroyEm') }}">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="Documento_Cliente" value="{{ $cli->Documento_Cliente }}">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">¿Eliminar cliente?</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>¿Está seguro de eliminar este cliente?</p>
                                            <div class="alert alert-warning">
                                                <strong>Documento:</strong> {{ $cli->Documento_Cliente }}<br>
                                                <strong>Nombre:</strong> {{ $cli->Nombre_Cliente }} {{ $cli->Apellido_Cliente }}
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
                            <td colspan="5" class="text-center text-muted">
                                No hay clientes registrados.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- MODAL CREAR -->
            <div class="modal fade" id="crearModal">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('clientes.storeEm') }}">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">Añadir Cliente</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <label>Documento</label>
                                <input class="form-control mb-3" name="Documento_Cliente" 
                                       placeholder="Ingrese el documento" required>

                                <label>Nombre</label>
                                <input class="form-control mb-3" name="Nombre_Cliente" 
                                       placeholder="Ingrese el nombre" required>

                                <label>Apellido</label>
                                <input class="form-control mb-3" name="Apellido_Cliente" 
                                       placeholder="Ingrese el apellido" required>

                                <label>Estado</label>
                                <select class="form-control" name="ID_Estado" required>
                                    <option value="">--Seleccione--</option>
                                    <option value="1">Activo</option>
                                    <option value="2">Inactivo</option>
                                </select>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ============================================
// HELPERS
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
// ============================================
async function importarDesdeExcel(event) {
    const archivo = event.target.files[0];
    if (!archivo) return;

    const progresoDiv = document.getElementById('progreso');
    progresoDiv.className = 'alert alert-info';
    progresoDiv.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Leyendo archivo Excel...';

    try {
        const data = await archivo.arrayBuffer();
        const workbook = XLSX.read(data);
        const hoja = workbook.Sheets[workbook.SheetNames[0]];
        const clientes = XLSX.utils.sheet_to_json(hoja).map(normalizarClaves);

        if (clientes.length === 0) {
            progresoDiv.className = 'alert alert-warning';
            progresoDiv.innerHTML = '<i class="fa fa-exclamation-triangle"></i> El archivo está vacío';
            return;
        }

        const datosValidados = clientes.map(cli => ({
            Documento_Cliente: buscarClave(cli, 'Documento Cliente', 'Documento_Cliente', 'documento') || '',
            Nombre_Cliente: buscarClave(cli, 'Nombre Cliente', 'Nombre_Cliente', 'nombre') || '',
            Apellido_Cliente: buscarClave(cli, 'Apellido Cliente', 'Apellido_Cliente', 'apellido') || '',
            ID_Estado: buscarClave(cli, 'Estado', 'ID_Estado', 'estado') || 1
        }));

        const tamañoLote = 10;
        let importados = 0;

        for (let i = 0; i < datosValidados.length; i += tamañoLote) {
            const lote = datosValidados.slice(i, i + tamañoLote);
            const progreso = Math.round(((i + lote.length) / datosValidados.length) * 100);

            progresoDiv.innerHTML = `
                <div class="d-flex align-items-center">
                    <strong>Importando clientes...</strong>
                    <div class="ms-auto">${progreso}%</div>
                </div>
                <div class="progress mt-2">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning"
                         style="width: ${progreso}%"></div>
                </div>
                <small class="text-muted mt-2 d-block">Registros: ${i + lote.length} / ${datosValidados.length}</small>`;

            const response = await fetch('/migracion/importar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ modulo: 'clientes', datos: lote })
            });

            const resultado = await response.json();
            if (!resultado.success) throw new Error(resultado.mensaje);
            importados += resultado.importados || 0;
            
            // Mostrar errores si los hay
            if (resultado.errores && resultado.errores.length > 0) {
                console.warn('Errores durante importación:', resultado.errores);
            }
            
            await new Promise(r => setTimeout(r, 300));
        }

        progresoDiv.className = 'alert alert-success';
        progresoDiv.innerHTML = `
            <i class="fa fa-check-circle"></i>
            <strong>¡Importación completada!</strong>
            <br><small>Se importaron ${importados} clientes correctamente</small>
        `;
        setTimeout(() => location.reload(), 3000);

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

        const initResp = await fetch('/migracion/iniciar', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ modulo: 'clientes' })
        });
        const initData = await initResp.json();
        if (!initData.success) throw new Error(initData.mensaje);

        let todosLosDatos = [];
        let completado = false, intentos = 0;

        while (!completado && intentos < 100) {
            const loteResp = await fetch('/migracion/lote', {
                method: 'POST',
                headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ modulo: 'clientes' })
            });
            const loteData = await loteResp.json();
            if (!loteData.success) throw new Error(loteData.mensaje);
            if (loteData.datos?.length > 0) todosLosDatos = todosLosDatos.concat(loteData.datos);

            progresoDiv.innerHTML = `
                <div class="d-flex align-items-center">
                    <strong>Exportando clientes...</strong>
                    <div class="ms-auto">${loteData.progreso}%</div>
                </div>
                <div class="progress mt-2">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                         style="width: ${loteData.progreso}%"></div>
                </div>
                <small class="text-muted mt-2 d-block">Registros: ${loteData.registros_migrados} / ${loteData.total_registros}</small>`;

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

        const hoja1 = todosLosDatos.map(cli => ({
            'Documento Cliente': cli.Documento_Cliente,
            'Nombre Cliente': cli.Nombre_Cliente,
            'Apellido Cliente': cli.Apellido_Cliente,
            'Estado': cli.Estado
        }));

        const wb = XLSX.utils.book_new();
        const ws1 = XLSX.utils.json_to_sheet(hoja1);
        
        // Estilos
        const rng = XLSX.utils.decode_range(ws1['!ref']);
        ws1['!cols'] = [{wch:20},{wch:25},{wch:25},{wch:15}];
        
        XLSX.utils.book_append_sheet(wb, ws1, 'Clientes');

        const info = XLSX.utils.aoa_to_sheet([
            ['REPORTE DE CLIENTES - TECNICELL RM'],[''],
            ['Fecha de Generación:', new Date().toLocaleString('es-ES')],
            ['Total Clientes:', todosLosDatos.length],
            ['Generado por:', '{{ session("nombre") ?? "TECNICELL RM" }}']
        ]);
        info['!cols'] = [{wch:30},{wch:30}];
        XLSX.utils.book_append_sheet(wb, info, 'Información');

        XLSX.writeFile(wb, `Clientes_${new Date().toISOString().split('T')[0]}.xlsx`);

        progresoDiv.className = 'alert alert-success';
        progresoDiv.innerHTML = `
            <i class="fa fa-check-circle"></i> <strong>¡Exportación completada!</strong>
            <br><small>${todosLosDatos.length} clientes exportados</small>
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