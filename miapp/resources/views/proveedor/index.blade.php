<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="{{ asset('Imagenes/Logo.webp') }}" type="image/webp">
    <title>Proveedores - TECNICELL RM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

<div class="d-flex" style="min-height: 100vh;">

    <!-- BARRA LATERAL -->
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

    <!-- CONTENIDO PRINCIPAL -->
    <div class="contenido-principal flex-grow-1">

        <!-- NAVBAR -->
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <a class="navbar-brand">Sistema gestión de inventarios</a>

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

        <!-- CONTENIDO -->
        <div class="container py-4">

            <!-- TÍTULO -->
            <div class="d-flex justify-content-center align-items-center gap-3 mb-4">
                <img src="{{ asset('Imagenes/Logo.webp') }}" style="height:48px;">
                <h1 class="mb-0">Gestión de Proveedores</h1>
            </div>

            <!-- ALERTAS -->
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

            <!-- BOTONES DE ACCIÓN -->
            <div class="d-flex justify-content-end mt-4 gap-2">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fa fa-plus"></i> Añadir Proveedor
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

            <!-- BARRA DE PROGRESO -->
            <div id="progreso" class="mt-3"></div>

            <!-- TABLA DE PROVEEDORES -->
            <div class="table-responsive mt-4">
                <table class="table table-bordered table-hover table-striped align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo Electrónico</th>
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
                                {{-- Mostrar estado con nombre igual que empleados --}}
                                {{ $prov->estado->Nombre_Estado ?? 'Sin estado' }}
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

                        <!-- MODAL EDITAR -->
                        <div class="modal fade" id="editar{{ $prov->ID_Proveedor }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('proveedor.update') }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="ID_Proveedor" value="{{ $prov->ID_Proveedor }}">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Editar Proveedor</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <label>Nombre <span class="text-danger">*</span></label>
                                            <input class="form-control mb-3" name="Nombre_Proveedor"
                                                   value="{{ $prov->Nombre_Proveedor }}" required>

                                            <label>Correo Electrónico</label>
                                            <input type="email" class="form-control mb-3" name="Correo_Electronico"
                                                   value="{{ $prov->Correo_Electronico }}">

                                            <label>Teléfono</label>
                                            <input class="form-control mb-3" name="Telefono"
                                                   value="{{ $prov->Telefono }}">

                                            <label>Estado <span class="text-danger">*</span></label>
                                            <select name="ID_Estado" class="form-control" required>
                                                <option value="1" {{ (int)$prov->ID_Estado===1?'selected':'' }}>Activo</option>
                                                <option value="2" {{ (int)$prov->ID_Estado===2?'selected':'' }}>Inactivo</option>
                                                <option value="3" {{ (int)$prov->ID_Estado===3?'selected':'' }}>En proceso</option>
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
                        <div class="modal fade" id="eliminar{{ $prov->ID_Proveedor }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('proveedor.destroy') }}">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="ID_Proveedor" value="{{ $prov->ID_Proveedor }}">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">¿Eliminar proveedor?</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>¿Está seguro de eliminar este proveedor?</p>
                                            <div class="alert alert-warning">
                                                <strong>ID:</strong> {{ $prov->ID_Proveedor }}<br>
                                                <strong>Nombre:</strong> {{ $prov->Nombre_Proveedor }}<br>
                                                <strong>Correo:</strong> {{ $prov->Correo_Electronico }}
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
                            <td colspan="6" class="text-center text-muted">
                                No hay proveedores registrados.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- MODAL CREAR -->
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
                                <label>Nombre <span class="text-danger">*</span></label>
                                <input class="form-control mb-3" name="Nombre_Proveedor" 
                                       placeholder="Ingrese el nombre" required>

                                <label>Correo Electrónico</label>
                                <input type="email" class="form-control mb-3" name="Correo_Electronico" 
                                       placeholder="ejemplo@correo.com">

                                <label>Teléfono</label>
                                <input class="form-control mb-3" name="Telefono" 
                                       placeholder="3001234567">

                                <label>Estado <span class="text-danger">*</span></label>
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
function normalizarClaves(obj) {
    const r = {};
    Object.keys(obj).forEach(key => {
        const normalizada = key
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .trim()
            .replace(/\s+/g, '_');
        r[normalizada] = obj[key];
    });
    return r;
}

function buscarClave(o, ...posiblesClaves) {
    for (const clave of posiblesClaves) {
        const normalizada = clave
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .trim()
            .replace(/\s+/g, '_');
        if (o[normalizada] !== undefined && o[normalizada] !== null && o[normalizada] !== '') {
            return o[normalizada];
        }
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
        const proveedores = XLSX.utils.sheet_to_json(hoja).map(normalizarClaves);

        console.log('✅ Proveedores leídos del Excel:', proveedores);

        if (proveedores.length === 0) {
            progresoDiv.className = 'alert alert-warning';
            progresoDiv.innerHTML = '<i class="fa fa-exclamation-triangle"></i> El archivo está vacío';
            return;
        }

        const datosValidados = proveedores.map((prov, index) => {
            const nombre = buscarClave(prov, 'nombre_proveedor', 'nombre proveedor', 'nombre');
            const correo = buscarClave(prov, 'correo_electronico', 'correo electronico', 'correo', 'email');
            const telefono = buscarClave(prov, 'telefono', 'telefono');
            let estado = buscarClave(prov, 'estado', 'id_estado');

            if (typeof estado === 'string') {
                const estadoLower = estado.toLowerCase().trim();
                if (estadoLower === 'activo') estado = 1;
                else if (estadoLower === 'inactivo') estado = 2;
                else if (estadoLower === 'en proceso') estado = 3;
                else estado = 1;
            } else if (!estado) {
                estado = 1;
            }

            return {
                Nombre_Proveedor: nombre || '',
                Correo_Electronico: correo || null,
                Telefono: telefono || null,
                ID_Estado: estado
            };
        });

        console.log('✅ Datos validados para enviar:', datosValidados);

        const tamañoLote = 10;
        let importados = 0;
        let erroresAcumulados = [];

        for (let i = 0; i < datosValidados.length; i += tamañoLote) {
            const lote = datosValidados.slice(i, i + tamañoLote);
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
                <small class="text-muted mt-2 d-block">Registros: ${i + lote.length} / ${datosValidados.length}</small>`;

            const response = await fetch('/migracion/importar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ modulo: 'proveedores', datos: lote })
            });

            const resultado = await response.json();
            console.log('Respuesta del servidor:', resultado);

            if (!resultado.success) {
                throw new Error(resultado.mensaje);
            }

            importados += resultado.importados || 0;
            
            if (resultado.errores && resultado.errores.length > 0) {
                erroresAcumulados = erroresAcumulados.concat(resultado.errores);
            }
            
            await new Promise(r => setTimeout(r, 300));
        }

        let mensajeFinal = `<i class="fa fa-check-circle"></i> <strong>¡Importación completada!</strong>
                           <br><small>Se importaron ${importados} proveedores correctamente</small>`;

        if (erroresAcumulados.length > 0) {
            mensajeFinal += `<br><br><div class="alert alert-warning mt-2 mb-0">
                              <strong>Advertencias:</strong><br>
                              ${erroresAcumulados.slice(0, 5).join('<br>')}
                              ${erroresAcumulados.length > 5 ? `<br>... y ${erroresAcumulados.length - 5} más` : ''}
                            </div>`;
        }

        progresoDiv.className = 'alert alert-success';
        progresoDiv.innerHTML = mensajeFinal;
        
        setTimeout(() => location.reload(), 3000);

    } catch (error) {
        console.error('❌ Error:', error);
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
            headers: { 
                'Content-Type':'application/json', 
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
            },
            body: JSON.stringify({ modulo: 'proveedores' })
        });
        const initData = await initResp.json();
        
        if (!initData.success) throw new Error(initData.mensaje);

        let todosLosDatos = [];
        let completado = false, intentos = 0;

        while (!completado && intentos < 100) {
            const loteResp = await fetch('/migracion/lote', {
                method: 'POST',
                headers: { 
                    'Content-Type':'application/json', 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
                },
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

        const hoja1 = todosLosDatos.map(prov => ({
            'ID Proveedor': prov.ID_Proveedor,
            'Nombre Proveedor': prov.Nombre_Proveedor,
            'Correo Electrónico': prov.Correo_Electronico,
            'Teléfono': prov.Telefono,
            'Estado': prov.Estado
        }));

        const wb = XLSX.utils.book_new();
        const ws1 = XLSX.utils.json_to_sheet(hoja1);
        
        ws1['!cols'] = [{wch:15},{wch:30},{wch:30},{wch:15},{wch:15}];
        
        XLSX.utils.book_append_sheet(wb, ws1, 'Proveedores');

        const info = XLSX.utils.aoa_to_sheet([
            ['REPORTE DE PROVEEDORES - TECNICELL RM'],[''],
            ['Fecha de Generación:', new Date().toLocaleString('es-ES')],
            ['Total Proveedores:', todosLosDatos.length],
            ['Generado por:', '{{ session("nombre") ?? "TECNICELL RM" }}']
        ]);
        info['!cols'] = [{wch:30},{wch:30}];
        XLSX.utils.book_append_sheet(wb, info, 'Información');

        XLSX.writeFile(wb, `Proveedores_${new Date().toISOString().split('T')[0]}.xlsx`);

        progresoDiv.className = 'alert alert-success';
        progresoDiv.innerHTML = `
            <i class="fa fa-check-circle"></i> <strong>¡Exportación completada!</strong>
            <br><small>${todosLosDatos.length} proveedores exportados</small>
        `;
        setTimeout(() => { 
            progresoDiv.innerHTML=''; 
            progresoDiv.className=''; 
        }, 8000);

    } catch (error) {
        console.error('❌ Error:', error);
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
