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
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
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
                <a href="{{ route('InicioE.index') }}" class="elemento-menu">
                    <i class="fa-solid fa-tachometer-alt"></i><span>Dashboard</span>
                </a>

                <a href="{{ route('ventas.indexEm') }}" class="elemento-menu">
                    <i class="ri-price-tag-3-line"></i><span>Ventas</span>
                </a>

                <a href="{{ route('devolucion.indexEm') }}" class="elemento-menu activo">
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

            <!-- Errores de importación -->
            <div id="erroresImportacion" class="mt-2" style="display:none;">
                <div class="alert alert-warning">
                    <strong><i class="fa fa-exclamation-triangle"></i> Filas con errores de cantidad:</strong>
                    <ul id="listaErrores" class="mb-0 mt-2"></ul>
                </div>
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
                                <form method="POST" action="{{ route('devolucion.updateEm') }}">
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
                                            <input type="date" name="Fecha_Devolucion" class="form-control mb-3"
                                                   value="{{ $dev->Fecha_Devolucion }}"
                                                   min="{{ date('Y-m-d', strtotime('-5 days')) }}"
                                                   max="{{ date('Y-m-d') }}" required>
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
                                <form method="POST" action="{{ route('devolucion.destroyEm') }}">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="ID_Devolucion" value="{{ $dev->ID_Devolucion }}">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Eliminar Devolución</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>¿Seguro que deseas eliminar esta devolución?</p>
                                            <div class="alert alert-warning">
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
                    <form method="POST" action="{{ route('devolucion.storeEm') }}">
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
const urlDetalleDevolucion = "{{ route('detalledevolucion.indexEm') }}";

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
    const erroresDiv  = document.getElementById('erroresImportacion');
    const listaErrores = document.getElementById('listaErrores');

    progresoDiv.className = 'alert alert-info';
    progresoDiv.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Leyendo archivo Excel...';
    erroresDiv.style.display = 'none';
    listaErrores.innerHTML  = '';

    try {
        const data     = await archivo.arrayBuffer();
        const workbook = XLSX.read(data);

        const hojaDev  = workbook.Sheets[workbook.SheetNames[0]];
        const devs     = XLSX.utils.sheet_to_json(hojaDev).map(normalizarClaves);

        let dets = [];
        if (workbook.SheetNames.length > 1) {
            const hojaDet = workbook.Sheets[workbook.SheetNames[1]];
            dets = XLSX.utils.sheet_to_json(hojaDet).map(normalizarClaves);
        }

        if (devs.length === 0) {
            progresoDiv.className = 'alert alert-warning';
            progresoDiv.innerHTML = '<i class="fa fa-exclamation-triangle"></i> El archivo está vacío';
            return;
        }

        progresoDiv.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Validando cantidades con el servidor...';

        const errores = [];
        const datosValidados = [];

        for (let i = 0; i < devs.length; i++) {
            const dev = devs[i];
            const fecha  = buscarClave(dev, 'Fecha Devolucion', 'Fecha_Devolucion') ?? null;
            const motivo = buscarClave(dev, 'Motivo') ?? 'Sin motivo';
            const detsFila = dets.filter((_, idx) => idx === i);
            const detallesValidados = [];

            for (const det of detsFila) {
                const docCliente   = buscarClave(det, 'Documento Cliente', 'Documento_Cliente');
                const cantDevuelta = parseFloat(buscarClave(det, 'Cantidad Devuelta', 'Cantidad_Devuelta') ?? 0);

                const resp = await fetch('/migracion/buscar-venta', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ Documento_Cliente: docCliente })
                });

                const ventaData = await resp.json();

                if (!ventaData.success) {
                    errores.push(`Fila ${i+2}: Cliente ${docCliente} no encontrado o sin ventas.`);
                    continue;
                }

                const cantMax = ventaData.cantidad_maxima;
                const idVenta = ventaData.ID_Venta;

                if (cantDevuelta > cantMax) {
                    errores.push(`Fila ${i+2}: Cliente ${docCliente} — cantidad devuelta (${cantDevuelta}) supera el máximo permitido (${cantMax}). Se ajustó al máximo.`);
                    detallesValidados.push({ ID_Venta: idVenta, Cantidad_Devuelta: cantMax });
                } else {
                    detallesValidados.push({ ID_Venta: idVenta, Cantidad_Devuelta: cantDevuelta });
                }
            }

            datosValidados.push({
                Fecha_Devolucion: fecha,
                Motivo: motivo,
                detalles: detallesValidados
            });
        }

        if (errores.length > 0) {
            erroresDiv.style.display = 'block';
            errores.forEach(e => {
                const li = document.createElement('li');
                li.textContent = e;
                listaErrores.appendChild(li);
            });
        }

        const tamañoLote = 10;
        let importados = 0;

        for (let i = 0; i < datosValidados.length; i += tamañoLote) {
            const lote = datosValidados.slice(i, i + tamañoLote);
            const progreso = Math.round(((i + lote.length) / datosValidados.length) * 100);

            progresoDiv.innerHTML = `
                <div class="d-flex align-items-center">
                    <strong>Importando devoluciones...</strong>
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
                body: JSON.stringify({ modulo: 'devoluciones', datos: lote })
            });

            const texto = await response.text();
            let resultado;
            try { resultado = JSON.parse(texto); }
            catch(e) { throw new Error('El servidor devolvió HTML. Verifica la ruta /migracion/importar.'); }

            if (!resultado.success) throw new Error(resultado.mensaje);
            importados += resultado.importados || 0;
            await new Promise(r => setTimeout(r, 300));
        }

        progresoDiv.className = 'alert alert-success';
        progresoDiv.innerHTML = `
            <i class="fa fa-check-circle"></i>
            <strong>¡Importación completada!</strong>
            <br><small>Se importaron ${importados} devoluciones con sus detalles correctamente</small>
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
            body: JSON.stringify({ modulo: 'devoluciones' })
        });
        const initData = await initResp.json();
        if (!initData.success) throw new Error(initData.mensaje);

        let todosLosDatos = [];
        let completado = false, intentos = 0;

        while (!completado && intentos < 100) {
            const loteResp = await fetch('/migracion/lote', {
                method: 'POST',
                headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ modulo: 'devoluciones' })
            });
            const loteData = await loteResp.json();
            if (!loteData.success) throw new Error(loteData.mensaje);
            if (loteData.datos?.length > 0) todosLosDatos = todosLosDatos.concat(loteData.datos);

            progresoDiv.innerHTML = `
                <div class="d-flex align-items-center">
                    <strong>Exportando devoluciones...</strong>
                    <div class="ms-auto">${loteData.progreso}%</div>
                </div>
                <div class="progress mt-2">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                         style="width: ${loteData.progreso}%"></div>
                </div>
                <small class="text-muted mt-2 d-block">Registros: ${loteData.registros_migrados} / ${loteData.total_registros} (Lote ${loteData.lote_actual})</small>`;

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

        const hoja1 = todosLosDatos.map(dev => ({
            'Fecha Devolucion': dev.Fecha_Devolucion,
            'Motivo': dev.Motivo
        }));

        const hoja2 = [];
        todosLosDatos.forEach(dev => {
            (dev.detalles ?? []).forEach(det => {
                hoja2.push({
                    'Documento Cliente': det.Documento_Cliente ?? 'N/A',
                    'Cantidad Devuelta': det.Cantidad_Devuelta ?? 0,
                    'Cantidad Maxima': det.Cantidad_Maxima ?? 'N/A',
                });
            });
        });

        const wb = XLSX.utils.book_new();

        function estilos(ws, colorH, colorF) {
            const rng = XLSX.utils.decode_range(ws['!ref']);
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

        const ws1 = XLSX.utils.json_to_sheet(hoja1);
        estilos(ws1, '4472C4', 'F2F2F2');
        XLSX.utils.book_append_sheet(wb, ws1, 'Devoluciones');

        if (hoja2.length > 0) {
            const ws2 = XLSX.utils.json_to_sheet(hoja2);
            estilos(ws2, 'ED7D31', 'FFF2CC');
            XLSX.utils.book_append_sheet(wb, ws2, 'Detalles');
        }

        const info = XLSX.utils.aoa_to_sheet([
            ['REPORTE DE DEVOLUCIONES - TECNICELL RM'],[''],
            ['Fecha de Generación:', new Date().toLocaleString('es-ES')],
            ['Total Devoluciones:', todosLosDatos.length],
            ['Total Detalles:', hoja2.length],
            ['Generado por:', '{{ session("nombre") ?? "Empleado" }}']
        ]);
        info['A1'].s = { font:{name:'Calibri',sz:16,bold:true,color:{rgb:'4472C4'}}, alignment:{horizontal:'center'} };
        info['!cols'] = [{wch:30},{wch:30}];
        XLSX.utils.book_append_sheet(wb, info, 'Información');

        XLSX.writeFile(wb, `Devoluciones_${new Date().toISOString().split('T')[0]}.xlsx`, {bookType:'xlsx', cellStyles:true});

        progresoDiv.className = 'alert alert-success';
        progresoDiv.innerHTML = `
            <i class="fa fa-check-circle"></i> <strong>¡Exportación completada!</strong>
            <br><small>${todosLosDatos.length} devoluciones · ${hoja2.length} detalles · 3 hojas</small>
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

// ============================================
// MODAL DE DETALLES
// ============================================
function abrirDetalleModal(idDevolucion) {
    document.getElementById('mainContent').classList.add('devoluciones-background');
    document.getElementById('detalleBackdrop').style.display = 'block';
    
    fetch(`/empleado/devolucion/${idDevolucion}/detalles`)
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
<div style="position: fixed; bottom: 10px; left: 0; width: 100%; text-align: center; margin-left: 115px;">
    <p style="color: #aaaaaa; font-size: 13px; margin: 0;">Copyright © 2026 Fonrio</p>
</div>
</body>
</html>