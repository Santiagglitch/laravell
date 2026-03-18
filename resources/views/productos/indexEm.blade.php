<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('Imagenes/Logo.webp') }}" type="image/webp">
    <title>Productos - Empleado</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/Inicio.css') }}">
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

<!-- Overlay oscuro al abrir sidebar -->
<div class="overlay-sidebar" id="overlay"></div>

<div class="d-flex" style="min-height: 100vh;">

    <!-- ===================== SIDEBAR ===================== -->
    <div class="barra-lateral d-flex flex-column flex-shrink-0 p-3 bg-primary text-white" id="sidebar">
        <a class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            TECNICELL RM <img src="{{ asset('Imagenes/Logo.webp') }}" style="height:48px;">
        </a>
        <hr>
        <div class="menu-barra-lateral">
            <div class="seccion-menu">
                <a href="{{ route('InicioE.index') }}"
                   class="elemento-menu {{ request()->routeIs('InicioE.*') ? 'activo' : '' }}">
                    <i class="fa-solid fa-tachometer-alt"></i><span>Dashboard</span>
                </a>
                <a href="{{ route('ventas.indexEm') }}"
                   class="elemento-menu {{ request()->routeIs('ventas.indexEm') ? 'activo' : '' }}">
                    <i class="ri-price-tag-3-line"></i><span>Ventas</span>
                </a>
                <a href="{{ route('devolucion.indexEm') }}"
                   class="elemento-menu {{ request()->routeIs('devolucion.indexEm') ? 'activo' : '' }}">
                    <i class="ri-arrow-go-back-line"></i><span>Devoluciones</span>
                </a>
            </div>
            <hr>
            <div class="seccion-menu">
                <a href="{{ route('productos.indexEm') }}"
                   class="elemento-menu {{ request()->routeIs('productos.indexEm') ? 'activo' : '' }}">
                    <i class="ri-box-3-line"></i><span>Productos</span>
                </a>
                <a href="{{ route('clientes.indexEm') }}"
                   class="elemento-menu {{ request()->routeIs('clientes.indexEm') ? 'activo' : '' }}">
                    <i class="ri-user-line"></i><span>Clientes</span>
                </a>
            </div>
        </div>
    </div>
    <!-- ===================== FIN SIDEBAR ===================== -->

    <!-- ===================== CONTENIDO PRINCIPAL ===================== -->
    <div class="contenido-principal flex-grow-1">

        <!-- NAVBAR SUPERIOR -->
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">

                <button class="btn-sidebar-toggle" id="btnToggleSidebar">
                    <i class="fa-solid fa-bars"></i>
                </button>

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

        <!-- CONTENIDO -->
        <div class="container py-4">

            <div class="d-flex justify-content-center align-items-center gap-3">
                <img src="{{ asset('Imagenes/Logo.webp') }}" style="height:48px;">
                <h1>Gestión de Productos</h1>
            </div>

            @if(session('mensaje'))
                <div id="alertaMensaje" class="alert alert-success text-center mt-3">{{ session('mensaje') }}</div>
                <script>setTimeout(()=>{let a=document.getElementById('alertaMensaje');if(a){a.style.transition="opacity 0.5s";a.style.opacity=0;setTimeout(()=>a.remove(),500);}},2000);</script>
            @endif
            @if(session('error'))
                <div id="alertaError" class="alert alert-danger text-center mt-3">{{ session('error') }}</div>
                <script>setTimeout(()=>{let a=document.getElementById('alertaError');if(a){a.style.transition="opacity 0.5s";a.style.opacity=0;setTimeout(()=>a.remove(),500);}},2000);</script>
            @endif

            <div class="d-flex justify-content-end mt-4 gap-2 flex-wrap">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fa fa-plus"></i> Añadir Producto
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
                            <th>ID</th>
                            <th>Nombre</th>
                            <th class="col-ocultar-sm">Descripción</th>
                            <th>Precio</th>
                            <th class="col-ocultar-sm">Stock Mín</th>
                            <th class="col-ocultar-md">Categoría</th>
                            <th class="col-ocultar-md">Estado</th>
                            <th class="col-ocultar-md">Gama</th>
                            <th class="col-ocultar-sm">Foto</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($productos as $pro)
                        <tr>
                            <td>{{ $pro['ID_Producto'] }}</td>
                            <td>{{ $pro['Nombre_Producto'] }}</td>
                            <td class="col-ocultar-sm">{{ $pro['Descripcion'] }}</td>
                            <td>{{ $pro['Precio_Venta'] }}</td>
                            <td class="col-ocultar-sm">{{ $pro['Stock_Minimo'] }}</td>
                            <td class="col-ocultar-md">{{ $pro['Categoria'] ?? $pro['ID_Categoria'] }}</td>
                            <td class="col-ocultar-md">{{ $pro['Estado'] ?? $pro['ID_Estado'] }}</td>
                            <td class="col-ocultar-md">{{ $pro['Gama'] ?? $pro['ID_Gama'] }}</td>
                            <td class="col-ocultar-sm">
                                @if(!empty($pro['Fotos']))
                                    @php
                                        $foto = $pro['Fotos'];
                                        if (\Illuminate\Support\Str::startsWith($foto, ['http://', 'https://'])) {
                                            $fotoUrl = $foto;
                                        } else {
                                            $fotoLimpia = ltrim($foto, '/');
                                            if (\Illuminate\Support\Str::startsWith($fotoLimpia, 'uploads/')) {
                                                $fotoLimpia = substr($fotoLimpia, strlen('uploads/'));
                                            }
                                            $fotoUrl = 'http://localhost:8080/' . $fotoLimpia;
                                        }
                                    @endphp
                                    <img src="{{ $fotoUrl }}" width="50" height="50" class="rounded" style="object-fit:cover;">
                                @else
                                    <i class="fa-solid fa-image text-secondary" style="font-size:30px;"></i>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editarModal{{ $pro['ID_Producto'] }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#eliminarModal{{ $pro['ID_Producto'] }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Editar -->
                        <div class="modal fade" id="editarModal{{ $pro['ID_Producto'] }}">
                            <div class="modal-dialog modal-lg">
                                <form method="POST" action="{{ route('productos.updateEm') }}" enctype="multipart/form-data">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="ID_Producto" value="{{ $pro['ID_Producto'] }}">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Editar Producto #{{ $pro['ID_Producto'] }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body row g-3">
                                            <div class="col-md-6">
                                                <label>Nombre</label>
                                                <input class="form-control" name="Nombre_Producto"
                                                       value="{{ $pro['Nombre_Producto'] }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Precio Venta</label>
                                                <input class="form-control" name="Precio_Venta"
                                                       value="{{ $pro['Precio_Venta'] }}" required>
                                            </div>
                                            <div class="col-md-12">
                                                <label>Descripción</label>
                                                <textarea class="form-control" name="Descripcion" required>{{ $pro['Descripcion'] }}</textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Stock Mínimo</label>
                                                <input class="form-control" name="Stock_Minimo"
                                                       value="{{ $pro['Stock_Minimo'] }}" required>
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

                        <!-- Modal Eliminar -->
                        <div class="modal fade" id="eliminarModal{{ $pro['ID_Producto'] }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('productos.destroyEm') }}">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="ID_Producto" value="{{ $pro['ID_Producto'] }}">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Eliminar Producto</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            ¿Seguro que deseas eliminar este producto?
                                            <div class="alert alert-warning mt-3">
                                                <strong>Nombre:</strong> {{ $pro['Nombre_Producto'] }}<br>
                                                <strong>Precio:</strong> {{ $pro['Precio_Venta'] }}
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
                        <tr><td colspan="10" class="text-muted">No hay productos registrados.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Modal Crear -->
            <div class="modal fade" id="crearModal">
                <div class="modal-dialog modal-lg">
                    <form method="POST" action="{{ route('productos.storeEm') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">Añadir Producto</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body row g-3">
                                <div class="col-md-6">
                                    <label>Nombre</label>
                                    <input name="Nombre_Producto" class="form-control" required>
                                </div>
                                <div class="col-md-12">
                                    <label>Descripción</label>
                                    <textarea name="Descripcion" class="form-control" required></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label>Precio Venta</label>
                                    <input name="Precio_Venta" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label>Stock Mínimo</label>
                                    <input name="Stock_Minimo" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label>Categoría</label>
                                    <select name="ID_Categoria" class="form-control" required>
                                        <option value="">--Seleccione--</option>
                                        @foreach($categorias as $id => $nombre)
                                            <option value="{{ $id }}">{{ $nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Estado</label>
                                    <select name="ID_Estado" class="form-control" required>
                                        <option value="">--Seleccione--</option>
                                        @foreach($estados as $id => $nombre)
                                            <option value="{{ $id }}">{{ $nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Gama</label>
                                    <select name="ID_Gama" class="form-control" required>
                                        <option value="">--Seleccione--</option>
                                        @foreach($gamas as $id => $nombre)
                                            <option value="{{ $id }}">{{ $nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Foto (opcional)</label>
                                    <input type="file" name="Fotos" class="form-control">
                                </div>
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
    <!-- ===================== FIN CONTENIDO PRINCIPAL ===================== -->

</div>

<div style="position: fixed; bottom: 10px; left: 0; width: 100%; text-align: center; margin-left: 115px;">
    <p style="color: #aaaaaa; font-size: 13px; margin: 0;">Copyright © 2026 Fonrio</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // ============================================
    // SIDEBAR RESPONSIVE
    // ============================================
    const btnToggle = document.getElementById('btnToggleSidebar');
    const sidebar   = document.getElementById('sidebar');
    const overlay   = document.getElementById('overlay');

    btnToggle.addEventListener('click', function () {
        sidebar.classList.toggle('abierto');
        overlay.classList.toggle('activo');
    });

    overlay.addEventListener('click', function () {
        sidebar.classList.remove('abierto');
        overlay.classList.remove('activo');
    });

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
            const data     = await archivo.arrayBuffer();
            const workbook = XLSX.read(data);
            const hoja     = workbook.Sheets[workbook.SheetNames[0]];
            const productos = XLSX.utils.sheet_to_json(hoja).map(normalizarClaves);

            if (productos.length === 0) {
                progresoDiv.className = 'alert alert-warning';
                progresoDiv.innerHTML = '<i class="fa fa-exclamation-triangle"></i> El archivo está vacío';
                return;
            }

            const datosValidados = productos.map(prod => ({
                Nombre_Producto: buscarClave(prod, 'nombre_producto', 'nombre producto', 'nombre') ?? 'Sin nombre',
                Descripcion:     buscarClave(prod, 'descripcion') ?? 'Sin descripción',
                Precio_Venta:    buscarClave(prod, 'precio_venta', 'precio venta', 'precio') ?? 0,
                Stock_Minimo:    buscarClave(prod, 'stock_minimo', 'stock minimo', 'stock') ?? 0,
                Categoria:       buscarClave(prod, 'categoria') ?? null,
                Estado:          buscarClave(prod, 'estado') ?? null,
                Gama:            buscarClave(prod, 'gama') ?? null,
                Fotos:           buscarClave(prod, 'fotos', 'foto') ?? '',
            }));

            const tamañoLote = 10;
            let importados   = 0;
            let todosLosErrores = [];

            for (let i = 0; i < datosValidados.length; i += tamañoLote) {
                const lote     = datosValidados.slice(i, i + tamañoLote);
                const progreso = Math.round(((i + lote.length) / datosValidados.length) * 100);

                progresoDiv.innerHTML = `
                    <div class="d-flex align-items-center"><strong>Importando productos...</strong><div class="ms-auto">${progreso}%</div></div>
                    <div class="progress mt-2"><div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" style="width:${progreso}%"></div></div>
                    <small class="text-muted mt-2 d-block">Registros: ${i + lote.length} / ${datosValidados.length}</small>`;

                const response = await fetch('/migracion/productos/importar', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ modulo: 'productos', datos: lote })
                });

                const texto = await response.text();
                let resultado;
                try { resultado = JSON.parse(texto); }
                catch(e) { throw new Error('El servidor devolvió HTML. Verifica la ruta /migracion/productos/importar'); }

                if (!resultado.success) throw new Error(resultado.mensaje || 'Error desconocido');
                importados += resultado.importados || 0;
                if (resultado.errores?.length > 0) todosLosErrores = todosLosErrores.concat(resultado.errores);
                await new Promise(r => setTimeout(r, 300));
            }

            progresoDiv.className = 'alert alert-success';
            let mensajeFinal = `<i class="fa fa-check-circle"></i> <strong>¡Importación completada!</strong><br><small>Se importaron ${importados} productos correctamente</small>`;
            if (todosLosErrores.length > 0) {
                mensajeFinal += `<div class="mt-3"><strong>⚠️ Advertencias (${todosLosErrores.length}):</strong><ul class="text-start mt-2">${todosLosErrores.slice(0,10).map(e=>`<li>${e}</li>`).join('')}${todosLosErrores.length>10?`<li><em>...y ${todosLosErrores.length-10} más</em></li>`:''}</ul></div>`;
            }
            progresoDiv.innerHTML = mensajeFinal;
            if (importados > 0) setTimeout(() => location.reload(), 3000);

        } catch (error) {
            progresoDiv.className = 'alert alert-danger';
            progresoDiv.innerHTML = `<i class="fa fa-exclamation-triangle"></i> <strong>Error:</strong> ${error.message}`;
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

            const initResp = await fetch('/migracion/productos/iniciar', {
                method: 'POST',
                headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ modulo: 'productos' })
            });
            const initData = await initResp.json();
            if (!initData.success) throw new Error(initData.mensaje);

            let todosLosDatos = [];
            let completado = false, intentos = 0;

            while (!completado && intentos < 100) {
                const loteResp = await fetch('/migracion/productos/lote', {
                    method: 'POST',
                    headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ modulo: 'productos' })
                });
                const loteData = await loteResp.json();
                if (!loteData.success) throw new Error(loteData.mensaje);
                if (loteData.datos?.length > 0) todosLosDatos = todosLosDatos.concat(loteData.datos);

                progresoDiv.innerHTML = `
                    <div class="d-flex align-items-center"><strong>Exportando productos...</strong><div class="ms-auto">${loteData.progreso}%</div></div>
                    <div class="progress mt-2"><div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width:${loteData.progreso}%"></div></div>
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

            const hoja = todosLosDatos.map(prod => ({
                'Nombre_Producto': prod.Nombre_Producto,
                'Descripcion':     prod.Descripcion,
                'Precio_Venta':    prod.Precio_Venta,
                'Stock_Minimo':    prod.Stock_Minimo,
                'Categoria':       prod.Categoria,
                'Estado':          prod.Estado,
                'Gama':            prod.Gama,
                'Fotos':           prod.Fotos
            }));

            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.json_to_sheet(hoja);
            XLSX.utils.book_append_sheet(wb, ws, 'Productos');
            XLSX.writeFile(wb, `Productos_${new Date().toISOString().split('T')[0]}.xlsx`);

            progresoDiv.className = 'alert alert-success';
            progresoDiv.innerHTML = `<i class="fa fa-check-circle"></i> <strong>¡Exportación completada!</strong><br><small>${todosLosDatos.length} productos exportados</small>`;
            setTimeout(() => { progresoDiv.innerHTML=''; progresoDiv.className=''; }, 5000);

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