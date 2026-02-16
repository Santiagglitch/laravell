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
                       id="dropdownUser1" data-bs-toggle="dropdown">
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
                    }, 3000);
                </script>
            @endif

            <div class="d-flex justify-content-end mt-4 gap-2">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fa fa-plus"></i> Añadir Compra
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
                                            <p>¿Seguro que deseas eliminar esta compra?</p>
                                            <div class="alert alert-warning">
                                                <strong>Producto:</strong> {{ $compra->nombre_producto }}<br>
                                                <strong>Precio:</strong> ${{ number_format($compra->Precio_Compra, 2) }}
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
// Hoja 1: Compras   → Precio Compra | Nombre Producto
// Hoja 2: Detalles  → Nombre Proveedor | Fecha Entrada | Cantidad
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

        // Hoja 1: Compras
        const hojaCompras = workbook.Sheets[workbook.SheetNames[0]];
        const compras     = XLSX.utils.sheet_to_json(hojaCompras).map(normalizarClaves);

        // Hoja 2: Detalles
        let detalles = [];
        if (workbook.SheetNames.length > 1) {
            const hojaDet = workbook.Sheets[workbook.SheetNames[1]];
            detalles = XLSX.utils.sheet_to_json(hojaDet).map(normalizarClaves);
        }

        console.log('Compras raw:', compras);
        console.log('Detalles raw:', detalles);

        if (compras.length === 0) {
            progresoDiv.className = 'alert alert-warning';
            progresoDiv.innerHTML = '<i class="fa fa-exclamation-triangle"></i> El archivo está vacío';
            return;
        }

        progresoDiv.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Validando datos...';

        const datosValidados = [];

        for (let i = 0; i < compras.length; i++) {
            const comp = compras[i];

            const precio  = parseFloat(buscarClave(comp, 'Precio Compra', 'Precio_Compra') ?? 0);
            const nombreProducto = buscarClave(comp, 'Nombre Producto', 'Nombre_Producto') ?? null;

            if (!nombreProducto) {
                throw new Error(`Fila ${i+2}: Falta el nombre del producto`);
            }

            // Detalles que corresponden a esta fila
            const detsFila = detalles.filter((_, idx) => idx === i);
            const detallesValidados = [];

            for (const det of detsFila) {
                const nombreProveedor = buscarClave(det, 'Nombre Proveedor', 'Nombre_Proveedor');
                const fechaEntrada    = buscarClave(det, 'Fecha Entrada', 'Fecha_Entrada') ?? new Date().toISOString().split('T')[0];
                const cantidad        = parseInt(buscarClave(det, 'Cantidad') ?? 0);

                if (!nombreProveedor || cantidad <= 0) {
                    continue;
                }

                detallesValidados.push({
                    Nombre_Proveedor: nombreProveedor,
                    Fecha_Entrada:    fechaEntrada,
                    Cantidad:         cantidad
                });
            }

            datosValidados.push({
                Precio_Compra:   precio,
                Nombre_Producto: nombreProducto,
                detalles:        detallesValidados
            });
        }

        console.log('✅ Datos validados para enviar:', JSON.stringify(datosValidados, null, 2));

        // Importar en lotes
        const tamañoLote = 10;
        let importados   = 0;

        for (let i = 0; i < datosValidados.length; i += tamañoLote) {
            const lote    = datosValidados.slice(i, i + tamañoLote);
            const progreso = Math.round(((i + lote.length) / datosValidados.length) * 100);

            progresoDiv.innerHTML = `
                <div class="d-flex align-items-center">
                    <strong>Importando compras...</strong>
                    <div class="ms-auto">${progreso}%</div>
                </div>
                <div class="progress mt-2">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning"
                         style="width: ${progreso}%"></div>
                </div>
                <small class="text-muted mt-2 d-block">
                    Registros: ${i + lote.length} / ${datosValidados.length}
                </small>`;

            const response = await fetch('/migracion/importar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ modulo: 'compras', datos: lote })
            });

            const texto = await response.text();
            let resultado;
            try { resultado = JSON.parse(texto); }
            catch(e) { throw new Error('El servidor devolvió HTML. Verifica la ruta /migracion-compras/importar.'); }

            if (!resultado.success) throw new Error(resultado.mensaje);
            importados += resultado.importados || 0;
            await new Promise(r => setTimeout(r, 300));
        }

        progresoDiv.className = 'alert alert-success';
        progresoDiv.innerHTML = `
            <i class="fa fa-check-circle"></i>
            <strong>¡Importación completada!</strong>
            <br><small>Se importaron ${importados} compras con sus detalles correctamente</small>
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
// Hoja 1: Compras   → Precio Compra | Nombre Producto
// Hoja 2: Detalles  → Nombre Proveedor | Fecha Entrada | Cantidad
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
            body: JSON.stringify({ modulo: 'compras' })
        });
        const initData = await initResp.json();
        if (!initData.success) throw new Error(initData.mensaje);

        let todosLosDatos = [];
        let completado = false, intentos = 0;

        while (!completado && intentos < 100) {
            const loteResp = await fetch('/migracion/lote', {
                method: 'POST',
                headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ modulo: 'compras' })
            });
            const loteData = await loteResp.json();
            if (!loteData.success) throw new Error(loteData.mensaje);
            if (loteData.datos?.length > 0) todosLosDatos = todosLosDatos.concat(loteData.datos);

            progresoDiv.innerHTML = `
                <div class="d-flex align-items-center">
                    <strong>Exportando compras...</strong>
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

        // ── HOJA 1: Compras ──
        const hoja1 = todosLosDatos.map(compra => ({
            'Precio Compra':   compra.Precio_Compra,
            'Nombre Producto': compra.Nombre_Producto
        }));

        // ── HOJA 2: Detalles ──
        const hoja2 = [];
        todosLosDatos.forEach(compra => {
            (compra.detalles ?? []).forEach(det => {
                hoja2.push({
                    'Nombre Proveedor': det.Nombre_Proveedor ?? 'N/A',
                    'Fecha Entrada':    det.Fecha_Entrada    ?? '',
                    'Cantidad':         det.Cantidad         ?? 0,
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
        XLSX.utils.book_append_sheet(wb, ws1, 'Compras');

        if (hoja2.length > 0) {
            const ws2 = XLSX.utils.json_to_sheet(hoja2);
            estilos(ws2, 'ED7D31', 'FFF2CC');
            XLSX.utils.book_append_sheet(wb, ws2, 'Detalles');
        }

        const info = XLSX.utils.aoa_to_sheet([
            ['REPORTE DE COMPRAS'],[''],
            ['Fecha de Generación:', new Date().toLocaleString('es-ES')],
            ['Total Compras:', todosLosDatos.length],
            ['Total Detalles:', hoja2.length],
            ['Generado por:', 'TECNICELL RM']
        ]);
        info['A1'].s = { font:{name:'Calibri',sz:16,bold:true,color:{rgb:'4472C4'}}, alignment:{horizontal:'center'} };
        info['!cols'] = [{wch:25},{wch:30}];
        XLSX.utils.book_append_sheet(wb, info, 'Información');

        XLSX.writeFile(wb, `Compras_${new Date().toISOString().split('T')[0]}.xlsx`, {bookType:'xlsx', cellStyles:true});

        progresoDiv.className = 'alert alert-success';
        progresoDiv.innerHTML = `
            <i class="fa fa-check-circle"></i> <strong>¡Exportación completada!</strong>
            <br><small>${todosLosDatos.length} compras · ${hoja2.length} detalles · 3 hojas</small>
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
                    <td><i class="fa fa-truck"></i> ${detalle.proveedor ? detalle.proveedor.Nombre_Proveedor : 'N/A'}</td>
                    <td>
                        <a href="${urlDetalleCompras}" class="btn btn-sm btn-warning">
                            <i class="fa fa-edit"></i> Editar
                        </a>
                    </td>
                </tr>
            `;
        });
    } else {
        detallesHTML = '<tr><td colspan="4" class="text-center text-muted py-4"><i class="fa fa-inbox fa-2x mb-2"></i><br>No hay detalles registrados para esta compra</td></tr>';
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
                            <th><i class="fa fa-truck"></i> Proveedor</th>
                            <th><i class="fa fa-cog"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${detallesHTML}
                    </tbody>
                </table>
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