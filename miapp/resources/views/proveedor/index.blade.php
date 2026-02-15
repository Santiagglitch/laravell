<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="{{ asset('Imagenes/Logo.webp') }}" type="image/webp">
    <title>Proveedores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

            <div class="d-flex justify-content-center align-items-center gap-3">
                <img src="{{ asset('Imagenes/Logo.webp') }}" style="height:48px;">
                <h1>Registro de Proveedores</h1>
            </div>

            @if(session('mensaje'))
                <div id="alertaMensaje" class="alert alert-success text-center mt-3">{{ session('mensaje') }}</div>
                <script>setTimeout(()=>{let a=document.getElementById('alertaMensaje');if(a){a.style.transition="opacity 0.5s";a.style.opacity=0;setTimeout(()=>a.remove(),500);}},2000);</script>
            @endif
            @if(session('error'))
                <div id="alertaError" class="alert alert-danger text-center mt-3">{{ session('error') }}</div>
                <script>setTimeout(()=>{let a=document.getElementById('alertaError');if(a){a.style.transition="opacity 0.5s";a.style.opacity=0;setTimeout(()=>a.remove(),500);}},5000);</script>
            @endif
            @if($errors->any())
                <div class="alert alert-danger mt-3">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            {{-- Botones igual que productos --}}
            <div class="d-flex justify-content-end mt-4 gap-2">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fa fa-plus"></i> Añadir Proveedor
                </button>
                <button class="btn btn-warning" onclick="document.getElementById('archivoExcelProveedores').click()">
                    <i class="fa fa-upload"></i> Importar desde Excel
                </button>
                <input type="file" id="archivoExcelProveedores" accept=".xlsx,.xls" style="display:none;"
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
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($proveedores as $prov)
                        <tr>
                            <td>{{ $prov->ID_Proveedor }}</td>
                            <td>{{ $prov->Nombre_Proveedor }}</td>
                            <td>{{ $prov->Correo_Electronico }}</td>
                            <td>{{ $prov->Telefono }}</td>
                            <td>
                                @if($prov->ID_Estado == 1)
                                    <span class="badge bg-success">Activo</span>
                                @elseif($prov->ID_Estado == 2)
                                    <span class="badge bg-danger">Inactivo</span>
                                @else
                                    <span class="badge bg-warning text-dark">En proceso</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editar{{ $prov->ID_Proveedor }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#eliminar{{ $prov->ID_Proveedor }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- Modal Editar --}}
                        <div class="modal fade" id="editar{{ $prov->ID_Proveedor }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('proveedor.update') }}">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="ID_Proveedor" value="{{ $prov->ID_Proveedor }}">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Editar Proveedor</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <label>Nombre</label>
                                            <input class="form-control mb-3" name="Nombre_Proveedor"
                                                   value="{{ $prov->Nombre_Proveedor }}" required>
                                            <label>Correo</label>
                                            <input type="email" class="form-control mb-3" name="Correo_Electronico"
                                                   value="{{ $prov->Correo_Electronico }}" required>
                                            <label>Teléfono</label>
                                            <input class="form-control mb-3" name="Telefono"
                                                   value="{{ $prov->Telefono }}" required>
                                            <label>Estado</label>
                                            <select name="ID_Estado" class="form-control" required>
                                                <option value="1" {{ $prov->ID_Estado==1?'selected':'' }}>Activo</option>
                                                <option value="2" {{ $prov->ID_Estado==2?'selected':'' }}>Inactivo</option>
                                                <option value="3" {{ $prov->ID_Estado==3?'selected':'' }}>En proceso</option>
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

                        {{-- Modal Eliminar --}}
                        <div class="modal fade" id="eliminar{{ $prov->ID_Proveedor }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('proveedor.destroy') }}">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="ID_Proveedor" value="{{ $prov->ID_Proveedor }}">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">¿Eliminar proveedor?</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>¿Está seguro de eliminar este proveedor?</p>
                                            <div class="alert alert-warning">
                                                <strong>Nombre:</strong> {{ $prov->Nombre_Proveedor }}<br>
                                                <strong>Correo:</strong> {{ $prov->Correo_Electronico }}<br>
                                                <strong>Teléfono:</strong> {{ $prov->Telefono }}
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
                        <tr><td colspan="6" class="text-center text-muted">No hay proveedores registrados.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Modal Crear --}}
            <div class="modal fade" id="crearModal">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('proveedor.store') }}">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">Añadir Proveedor</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <label>Nombre</label>
                                <input class="form-control mb-3" name="Nombre_Proveedor" placeholder="Nombre del proveedor" required>
                                <label>Correo</label>
                                <input type="email" class="form-control mb-3" name="Correo_Electronico" placeholder="ejemplo@correo.com" required>
                                <label>Teléfono</label>
                                <input class="form-control mb-3" name="Telefono" placeholder="Teléfono" required>
                                <label>Estado</label>
                                <select class="form-control" name="ID_Estado" required>
                                    <option value="">--Seleccione--</option>
                                    <option value="1">Activo</option>
                                    <option value="2">Inactivo</option>
                                    <option value="3">En proceso</option>
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
function limpiar(s) {
    return String(s ?? '')
        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        .replace(/[\s_]+/g, '').toLowerCase().trim();
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

// ============================================
// IMPORTACIÓN DESDE EXCEL
// Columnas aceptadas: Nombre_Proveedor | Correo_Electronico | Telefono | Estado/ID_Estado
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

        const datosValidados = filas.map(fila => ({
            Nombre_Proveedor:   leerCampo(fila, 'Nombre_Proveedor',   'Nombre Proveedor',   'Nombre')   ?? null,
            Correo_Electronico: leerCampo(fila, 'Correo_Electronico', 'Correo Electronico', 'Correo', 'Email') ?? null,
            Telefono:           leerCampo(fila, 'Telefono',           'Teléfono')                        ?? null,
            ID_Estado:          leerCampo(fila, 'ID_Estado',          'Estado')                          ?? 1,
        }));

        const sinNombre = datosValidados.filter(d => !d.Nombre_Proveedor);
        if (sinNombre.length === datosValidados.length) {
            progresoDiv.className = 'alert alert-danger';
            progresoDiv.innerHTML = `<i class="fa fa-exclamation-triangle"></i>
                <strong>No se detectó la columna "Nombre_Proveedor".</strong><br>
                <small>Columnas encontradas: <code>${Object.keys(filas[0]).join(', ')}</code></small>`;
            event.target.value = '';
            return;
        }

        const tamañoLote = 10;
        let importados   = 0;
        let todosLosErrores = [];

        for (let i = 0; i < datosValidados.length; i += tamañoLote) {
            const lote    = datosValidados.slice(i, i + tamañoLote);
            const progreso = Math.round(((i + lote.length) / datosValidados.length) * 100);

            progresoDiv.innerHTML = `
                <div class="d-flex align-items-center">
                    <strong>Importando proveedores...</strong>
                    <div class="ms-auto">${progreso}%</div>
                </div>
                <div class="progress mt-2">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning"
                         style="width: ${progreso}%"></div>
                </div>
                <small class="text-muted mt-2 d-block">
                    Registros: ${i + lote.length} / ${datosValidados.length}
                </small>`;

            const response = await fetch('/migracion/proveedores/importar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ modulo: 'proveedores', datos: lote })
            });

            const texto = await response.text();
            let resultado;
            try { resultado = JSON.parse(texto); }
            catch(e) { throw new Error('El servidor devolvió HTML. Verifica la ruta /migracion/proveedores/importar.'); }

            if (!resultado.success) throw new Error(resultado.mensaje);
            importados += resultado.importados || 0;
            if (resultado.errores?.length > 0) todosLosErrores.push(...resultado.errores);

            await new Promise(r => setTimeout(r, 300));
        }

        let htmlFinal = `
            <i class="fa fa-check-circle"></i>
            <strong>¡Importación completada!</strong>
            <br><small>Se importaron <strong>${importados}</strong> proveedores correctamente.</small>`;
        if (todosLosErrores.length > 0) {
            htmlFinal += `<hr class="my-2"><small><strong>Advertencias (${todosLosErrores.length}):</strong>
                <ul class="mb-0 mt-1 text-start">${todosLosErrores.map(e => `<li>${e}</li>`).join('')}</ul></small>`;
        }
        progresoDiv.className = importados > 0 ? 'alert alert-success' : 'alert alert-warning';
        progresoDiv.innerHTML = htmlFinal;

        if (importados > 0) setTimeout(() => location.reload(), 3000);

    } catch (error) {
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

        const initResp = await fetch('/migracion/proveedores/iniciar', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ modulo: 'proveedores' })
        });
        const initData = await initResp.json();
        if (!initData.success) throw new Error(initData.mensaje);

        let todosLosDatos = [];
        let completado = false, intentos = 0;

        while (!completado && intentos < 100) {
            const loteResp = await fetch('/migracion/proveedores/lote', {
                method: 'POST',
                headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ modulo: 'proveedores' })
            });
            const loteData = await loteResp.json();
            if (!loteData.success) throw new Error(loteData.mensaje);
            if (loteData.datos?.length > 0) todosLosDatos = todosLosDatos.concat(loteData.datos);

            progresoDiv.innerHTML = `
                <div class="d-flex align-items-center">
                    <strong>Exportando proveedores...</strong>
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

        // Hoja de datos — nombres técnicos para poder reimportar
        const hoja = todosLosDatos.map(prov => ({
            'Nombre_Proveedor':   prov.Nombre_Proveedor,
            'Correo_Electronico': prov.Correo_Electronico,
            'Telefono':           prov.Telefono,
            'Estado':             prov.Estado,
        }));

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
                ws[a].s = { font:{name:'Calibri',sz:12,bold:true,color:{rgb:'FFFFFF'}}, fill:{fgColor:{rgb:colorH}}, alignment:{horizontal:'center',vertical:'center'}, border:{top:{style:'thin',color:{rgb:'000000'}},bottom:{style:'thin',color:{rgb:'000000'}},left:{style:'thin',color:{rgb:'000000'}},right:{style:'thin',color:{rgb:'000000'}}} };
            }
            for (let R = rng.s.r+1; R <= rng.e.r; R++) {
                for (let C = rng.s.c; C <= rng.e.c; C++) {
                    const a = XLSX.utils.encode_cell({r:R,c:C});
                    if (!ws[a]) continue;
                    ws[a].s = { font:{name:'Calibri',sz:11}, fill:{fgColor:{rgb: R%2===0?'FFFFFF':colorF}}, alignment:{horizontal:'left',vertical:'center'}, border:{top:{style:'thin',color:{rgb:'D3D3D3'}},bottom:{style:'thin',color:{rgb:'D3D3D3'}},left:{style:'thin',color:{rgb:'D3D3D3'}},right:{style:'thin',color:{rgb:'D3D3D3'}}} };
                }
            }
        }

        const wb  = XLSX.utils.book_new();
        const ws1 = XLSX.utils.json_to_sheet(hoja);
        estilos(ws1, '4472C4', 'F2F2F2');
        XLSX.utils.book_append_sheet(wb, ws1, 'Proveedores');

        const info = XLSX.utils.aoa_to_sheet([
            ['REPORTE DE PROVEEDORES'],[''],
            ['Fecha de Generación:', new Date().toLocaleString('es-ES')],
            ['Total Proveedores:', todosLosDatos.length],
            ['Generado por:', 'TECNICELL RM']
        ]);
        if (info['A1']) info['A1'].s = {font:{name:'Calibri',sz:16,bold:true,color:{rgb:'4472C4'}},alignment:{horizontal:'center'}};
        info['!cols'] = [{wch:25},{wch:30}];
        XLSX.utils.book_append_sheet(wb, info, 'Información');

        XLSX.writeFile(wb, `Proveedores_${new Date().toISOString().split('T')[0]}.xlsx`, {bookType:'xlsx', cellStyles:true});

        progresoDiv.className = 'alert alert-success';
        progresoDiv.innerHTML = `<i class="fa fa-check-circle"></i> <strong>¡Exportación completada!</strong>
            <br><small>${todosLosDatos.length} proveedores exportados</small>`;
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
