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
            position: fixed; top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1040; display: none;
        }
        .modal-detalle-content {
            position: fixed; top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            background: white; border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            z-index: 1050; max-width: 900px; width: 90%;
            max-height: 90vh; overflow-y: auto;
        }
        .devoluciones-background { opacity: 0.3; pointer-events: none; }
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
                       id="dropdownUser1" data-bs-toggle="dropdown">
                        <img src="{{ session('foto') ?? asset('Imagenes/default-user.png') }}"
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

            <div class="d-flex justify-content-center align-items-center gap-3 mb-4">
                <img src="{{ asset('Imagenes/Logo.webp') }}" style="height:48px;">
                <h1 class="mb-0">Registro de Ventas</h1>
            </div>

            @if(session('mensaje'))
                <div id="alertaMensaje" class="alert alert-success text-center mt-3">{{ session('mensaje') }}</div>
                <script>setTimeout(()=>{let a=document.getElementById('alertaMensaje');if(a){a.style.transition="opacity 0.5s";a.style.opacity=0;setTimeout(()=>a.remove(),500);}},2000);</script>
            @endif

            @if(session('error'))
                <div id="alertaError" class="alert alert-danger text-center mt-3">{{ session('error') }}</div>
                <script>setTimeout(()=>{let a=document.getElementById('alertaError');if(a){a.style.transition="opacity 0.5s";a.style.opacity=0;setTimeout(()=>a.remove(),500);}},3000);</script>
            @endif

            <div class="d-flex justify-content-end mt-4 gap-2">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fa fa-plus"></i> Añadir Venta
                </button>
                <button class="btn btn-warning" onclick="document.getElementById('archivoExcel').click()">
                    <i class="fa fa-upload"></i> Importar desde Excel
                </button>
                <input type="file" id="archivoExcel" accept=".xlsx,.xls" style="display:none;" onchange="importarDesdeExcel(event)">
                <button class="btn btn-primary" onclick="iniciarExportacion()">
                    <i class="fa fa-download"></i> Exportar a Excel
                </button>
            </div>

            <div id="progreso" class="mt-2"></div>

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
                                <button class="btn btn-info btn-sm" onclick="abrirDetalleModal({{ $venta->ID_Venta }})">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#eliminarModal{{ $venta->ID_Venta }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <div class="modal fade" id="eliminarModal{{ $venta->ID_Venta }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('ventas.destroy') }}">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="ID_Venta" value="{{ $venta->ID_Venta }}">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Eliminar Venta</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            ¿Seguro que deseas eliminar esta venta?
                                            <div class="alert alert-warning mt-3">
                                                <strong>ID Venta:</strong> {{ $venta->ID_Venta }}<br>
                                                <strong>Cliente:</strong> {{ $venta->Documento_Cliente }}<br>
                                                <strong>Empleado:</strong> {{ $venta->Documento_Empleado }}
                                            </div>
                                            <div class="alert alert-danger mt-2">
                                                <i class="fa fa-exclamation-triangle"></i>
                                                <strong>Atención:</strong> Si esta venta tiene detalles asociados, debes eliminarlos primero.
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
                        <tr><td colspan="4" class="text-muted">No hay ventas registradas.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MODAL CREAR VENTA --}}
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
                                        <input type="text" id="buscarCliente" name="Documento_Cliente" class="form-control"
                                               placeholder="Ej: 1234567890" autocomplete="off" required>
                                        <div id="spinnerBusqueda" class="spinner-border spinner-border-sm text-primary position-absolute"
                                             style="right: 10px; top: 10px; display: none;"></div>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fa fa-info-circle"></i>
                                        Ingrese el documento y espere 1 segundo o presione Enter
                                    </small>
                                </div>

                                <div id="mensajeCliente" class="alert d-none mb-3"></div>

                                {{-- Campos Cliente Nuevo --}}
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

                                {{-- Dropdown Empleado desde BD --}}
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fa fa-user-tie"></i> Empleado que realiza la venta
                                    </label>
                                    <select name="Documento_Empleado" class="form-select" required>
                                        <option value="">-- Seleccione un empleado --</option>
                                        @foreach($empleados as $emp)
                                            <option value="{{ $emp->Documento_Empleado }}"
                                                {{ session('documento') == $emp->Documento_Empleado ? 'selected' : '' }}>
                                                {{ $emp->Nombre_Usuario }} {{ $emp->Apellido_Usuario }}
                                                ({{ $emp->Documento_Empleado }})
                                            </option>
                                        @endforeach
                                    </select>
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

{{-- Modal emergente para detalles --}}
<div class="modal-detalle-backdrop" id="detalleBackdrop" onclick="cerrarDetalleModal()"></div>
<div class="modal-detalle-content" id="detalleModal" style="display: none;"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<script>
const urlDetalleVentas = "{{ route('detalleventas.index') }}";

// ============================================
// HELPERS
// ============================================
function normalizarClaves(obj){const r={};Object.keys(obj).forEach(key=>{r[key.normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().trim()]=obj[key];});return r;}
function buscarClave(o,...ps){for(const p of ps){const n=p.normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().trim();if(o[n]!==undefined)return o[n];}return null;}

// ============================================
// IMPORTACIÓN DESDE EXCEL
// Hoja 1: Ventas   → Documento Cliente | Nombre Cliente | Apellido Cliente | Documento Empleado
// Hoja 2: Detalles → Nombre Producto | Cantidad | Fecha Salida
// ============================================
async function importarDesdeExcel(event){
    const archivo=event.target.files[0];if(!archivo)return;
    const progresoDiv=document.getElementById('progreso');
    progresoDiv.className='alert alert-info';
    progresoDiv.innerHTML='<i class="fa fa-spinner fa-spin"></i> Leyendo archivo...';
    
    try{
        const data=await archivo.arrayBuffer();
        const workbook=XLSX.read(data);
        
        // Hoja 1: Ventas
        const hojaVentas=workbook.Sheets[workbook.SheetNames[0]];
        const ventas=XLSX.utils.sheet_to_json(hojaVentas).map(normalizarClaves);
        
        // Hoja 2: Detalles
        let detalles=[];
        if(workbook.SheetNames.length>1){
            const hojaDet=workbook.Sheets[workbook.SheetNames[1]];
            detalles=XLSX.utils.sheet_to_json(hojaDet).map(normalizarClaves);
        }
        
        console.log('Ventas raw:',ventas);
        console.log('Detalles raw:',detalles);
        
        if(ventas.length===0){progresoDiv.className='alert alert-warning';progresoDiv.innerHTML='<i class="fa fa-exclamation-triangle"></i> Archivo vacío';return;}
        
        progresoDiv.innerHTML='<i class="fa fa-spinner fa-spin"></i> Validando datos...';
        
        const datosValidados=[];
        for(let i=0;i<ventas.length;i++){
            const v=ventas[i];
            const docCliente=buscarClave(v,'Documento Cliente','Documento_Cliente','documento cliente')||'';
            const nombreCliente=buscarClave(v,'Nombre Cliente','Nombre_Cliente','nombre')||'';
            const apellidoCliente=buscarClave(v,'Apellido Cliente','Apellido_Cliente','apellido')||'';
            const estadoCliente=buscarClave(v,'Estado Cliente','Estado_Cliente','estado')||1;
            const docEmpleado=buscarClave(v,'Documento Empleado','Documento_Empleado','documento empleado')||'';
            
            if(!docCliente){throw new Error(`Fila ${i+2}: Falta documento del cliente`);}
            
            // Detalles de esta venta
            const detsFila=detalles.filter((_,idx)=>idx===i);
            const detallesValidados=[];
            
            for(const det of detsFila){
                const nombreProducto=buscarClave(det,'Nombre Producto','Nombre_Producto','producto');
                const cantidad=parseInt(buscarClave(det,'Cantidad')||0);
                const fechaSalida=buscarClave(det,'Fecha Salida','Fecha_Salida')||new Date().toISOString().split('T')[0];
                
                if(!nombreProducto||cantidad<=0)continue;
                
                detallesValidados.push({
                    Nombre_Producto:nombreProducto,
                    Cantidad:cantidad,
                    Fecha_Salida:fechaSalida
                });
            }
            
            datosValidados.push({
                Documento_Cliente:docCliente,
                Nombre_Cliente:nombreCliente,
                Apellido_Cliente:apellidoCliente,
                Estado_Cliente:estadoCliente,
                Documento_Empleado:docEmpleado,
                detalles:detallesValidados
            });
        }
        
        console.log('✅ Datos validados:',JSON.stringify(datosValidados,null,2));
        
        const tamañoLote=10;let importados=0;
        for(let i=0;i<datosValidados.length;i+=tamañoLote){
            const lote=datosValidados.slice(i,i+tamañoLote);
            const progreso=Math.round(((i+lote.length)/datosValidados.length)*100);
            progresoDiv.innerHTML=`<div class="d-flex align-items-center"><strong>Importando ventas...</strong><div class="ms-auto">${progreso}%</div></div><div class="progress mt-2"><div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" style="width:${progreso}%"></div></div><small class="text-muted mt-2 d-block">Registros: ${i+lote.length}/${datosValidados.length}</small>`;
            
            const response=await fetch('/migracion/importar',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},body:JSON.stringify({modulo:'ventas',datos:lote})});
            const resultado=await response.json();
            if(!resultado.success)throw new Error(resultado.mensaje);
            importados+=resultado.importados||0;
            await new Promise(r=>setTimeout(r,300));
        }
        progresoDiv.className='alert alert-success';
        progresoDiv.innerHTML=`<i class="fa fa-check-circle"></i><strong>¡Importación completada!</strong><br><small>Se importaron ${importados} ventas con detalles</small>`;
        setTimeout(()=>location.reload(),3000);
    }catch(error){
        console.error('Error:',error);
        progresoDiv.className='alert alert-danger';
        progresoDiv.innerHTML=`<i class="fa fa-exclamation-triangle"></i> Error: ${error.message}`;
    }
    event.target.value='';
}

// ============================================
// EXPORTACIÓN A EXCEL
// Hoja 1: Ventas   → Documento Cliente | Nombre Cliente | Apellido Cliente | Documento Empleado
// Hoja 2: Detalles → Nombre Producto | Cantidad | Fecha Salida
// ============================================
async function iniciarExportacion(){
    const btnExportar=event.target;btnExportar.disabled=true;btnExportar.innerHTML='<i class="fa fa-spinner fa-spin"></i> Exportando...';
    const progresoDiv=document.getElementById('progreso');
    try{
        progresoDiv.className='alert alert-info';progresoDiv.innerHTML='<i class="fa fa-spinner fa-spin"></i> Iniciando...';
        const initResp=await fetch('/migracion/iniciar',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},body:JSON.stringify({modulo:'ventas'})});
        const initData=await initResp.json();if(!initData.success)throw new Error(initData.mensaje);
        
        let todosLosDatos=[];let completado=false,intentos=0;
        while(!completado&&intentos<100){
            const loteResp=await fetch('/migracion/lote',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},body:JSON.stringify({modulo:'ventas'})});
            const loteData=await loteResp.json();if(!loteData.success)throw new Error(loteData.mensaje);
            if(loteData.datos?.length>0)todosLosDatos=todosLosDatos.concat(loteData.datos);
            progresoDiv.innerHTML=`<div class="d-flex align-items-center"><strong>Exportando ventas...</strong><div class="ms-auto">${loteData.progreso}%</div></div><div class="progress mt-2"><div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width:${loteData.progreso}%"></div></div><small class="text-muted mt-2 d-block">Registros: ${loteData.registros_migrados}/${loteData.total_registros}</small>`;
            completado=loteData.completado;intentos++;await new Promise(r=>setTimeout(r,300));
        }
        if(todosLosDatos.length===0){progresoDiv.className='alert alert-warning';progresoDiv.innerHTML='<i class="fa fa-exclamation-triangle"></i> No hay datos';btnExportar.disabled=false;btnExportar.innerHTML='<i class="fa fa-download"></i> Exportar';return;}
        
        progresoDiv.innerHTML+='<br><i class="fa fa-spinner fa-spin"></i> Generando Excel...';
        
        // Hoja 1: Ventas → Separando Nombre y Apellido
        const hoja1=todosLosDatos.map(v=>{
            const nombreCompleto=v.Nombre_Cliente||'';
            const partes=nombreCompleto.trim().split(' ');
            let nombre='',apellido='';
            if(partes.length===1){nombre=partes[0];}
            else if(partes.length>=2){nombre=partes[0];apellido=partes.slice(1).join(' ');}
            
            return {
                'Documento Cliente':v.Documento_Cliente,
                'Nombre Cliente':nombre,
                'Apellido Cliente':apellido,
                'Documento Empleado':v.Documento_Empleado
            };
        });
        
        // Hoja 2: Detalles
        const hoja2=[];
        todosLosDatos.forEach(v=>{
            (v.detalles||[]).forEach(d=>{
                hoja2.push({
                    'Nombre Producto':d.Producto,
                    'Cantidad':d.Cantidad,
                    'Fecha Salida':d.Fecha_Salida
                });
            });
        });
        
        const wb=XLSX.utils.book_new();
        const ws1=XLSX.utils.json_to_sheet(hoja1);ws1['!cols']=[{wch:18},{wch:20},{wch:20},{wch:18}];
        XLSX.utils.book_append_sheet(wb,ws1,'Ventas');
        
        if(hoja2.length>0){
            const ws2=XLSX.utils.json_to_sheet(hoja2);ws2['!cols']=[{wch:30},{wch:10},{wch:15}];
            XLSX.utils.book_append_sheet(wb,ws2,'Detalles');
        }
        
        const info=XLSX.utils.aoa_to_sheet([['REPORTE DE VENTAS - TECNICELL RM'],[''],['Fecha:',new Date().toLocaleString('es-ES')],['Total Ventas:',todosLosDatos.length],['Total Detalles:',hoja2.length],['Usuario:','{{ session("nombre") ?? "TECNICELL RM" }}']]);
        info['!cols']=[{wch:25},{wch:30}];XLSX.utils.book_append_sheet(wb,info,'Información');
        
        XLSX.writeFile(wb,`Ventas_${new Date().toISOString().split('T')[0]}.xlsx`);
        progresoDiv.className='alert alert-success';progresoDiv.innerHTML=`<i class="fa fa-check-circle"></i> <strong>¡Exportación completada!</strong><br><small>${todosLosDatos.length} ventas · ${hoja2.length} detalles exportados</small>`;
        setTimeout(()=>{progresoDiv.innerHTML='';progresoDiv.className='';},8000);
    }catch(error){progresoDiv.className='alert alert-danger';progresoDiv.innerHTML=`<i class="fa fa-exclamation-triangle"></i> Error: ${error.message}`;}
    finally{btnExportar.disabled=false;btnExportar.innerHTML='<i class="fa fa-download"></i> Exportar a Excel';}
}

// ============================================
// MODAL DE DETALLES Y BÚSQUEDA DE CLIENTE
// ============================================
function abrirDetalleModal(idVenta) {
    document.getElementById('mainContent').classList.add('devoluciones-background');
    document.getElementById('detalleBackdrop').style.display = 'block';
    fetch(`/ventas/${idVenta}/detalles`)
        .then(response => response.json())
        .then(data => mostrarDetalles(data))
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los detalles');
            cerrarDetalleModal();
        });
}

function mostrarDetalles(data) {
    const venta = data.venta;
    let detallesHTML = '';
    if (venta.detalles && venta.detalles.length > 0) {
        venta.detalles.forEach(detalle => {
            detallesHTML += `
                <tr>
                    <td class="py-3"><i class="fa fa-box text-primary me-2"></i><strong>${detalle.Nombre_Producto}</strong></td>
                    <td class="py-3"><span class="badge bg-primary">${detalle.Cantidad}</span> unidades</td>
                    <td class="py-3">${detalle.Fecha_Salida}</td>
                    <td class="py-3 text-center">
                        <a href="${urlDetalleVentas}" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i> Editar</a>
                    </td>
                </tr>
            `;
        });
    } else {
        detallesHTML = '<tr><td colspan="4" class="text-center text-muted py-5"><i class="fa fa-inbox fa-3x mb-3 d-block"></i><p>No hay detalles registrados para esta venta</p></td></tr>';
    }
    
    const modalHTML = `
        <div class="modal-header bg-primary text-white py-3">
            <h5 class="modal-title"><i class="fa fa-info-circle me-2"></i> Detalle de Venta - <span class="badge bg-light text-primary ms-2">ID: ${venta.ID_Venta}</span></h5>
            <button type="button" class="btn-close btn-close-white" onclick="cerrarDetalleModal()"></button>
        </div>
        <div class="modal-body p-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-primary bg-opacity-10 p-3 rounded me-3"><i class="fa fa-user fa-lg text-primary"></i></div>
                                <div><h6 class="text-muted mb-1 small">Documento Cliente</h6><p class="mb-0 fw-bold fs-5">${venta.Documento_Cliente}</p></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-info bg-opacity-10 p-3 rounded me-3"><i class="fa fa-user-tie fa-lg text-info"></i></div>
                                <div><h6 class="text-muted mb-1 small">Documento Empleado</h6><p class="mb-0 fw-bold">${venta.Documento_Empleado}</p></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <h6 class="mb-3 fw-bold"><i class="fa fa-list me-2"></i> Productos en esta Venta:</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th><i class="fa fa-box me-2"></i> Producto</th>
                            <th><i class="fa fa-sort-numeric-up me-2"></i> Cantidad</th>
                            <th><i class="fa fa-calendar me-2"></i> Fecha Salida</th>
                            <th class="text-center"><i class="fa fa-cog me-2"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>${detallesHTML}</tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer bg-light py-3">
            <button type="button" class="btn btn-secondary px-4" onclick="cerrarDetalleModal()"><i class="fa fa-times me-2"></i> Cerrar</button>
            <a href="${urlDetalleVentas}" class="btn btn-primary px-4"><i class="fa fa-external-link-alt me-2"></i> Ir a Detalle de Ventas</a>
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
    if (event.key === 'Escape') cerrarDetalleModal();
});

document.addEventListener('DOMContentLoaded', function() {
    const inputDocumento  = document.getElementById('buscarCliente');
    const spinnerBusqueda = document.getElementById('spinnerBusqueda');
    const mensajeCliente  = document.getElementById('mensajeCliente');
    const camposNuevo     = document.getElementById('camposNuevoCliente');
    const inputNuevo      = document.getElementById('clienteNuevo');
    const nombreCliente   = document.getElementById('nombreCliente');
    const apellidoCliente = document.getElementById('apellidoCliente');
    const formVenta       = document.getElementById('formVenta');
    const btnGuardar      = document.getElementById('btnGuardar');

    let timeoutBusqueda;
    let clienteValidado = false;

    btnGuardar.disabled = true;

    inputDocumento.addEventListener('keyup', function(e) {
        clearTimeout(timeoutBusqueda);
        const documento = this.value.trim();
        clienteValidado = false;
        btnGuardar.disabled = true;

        if (documento.length < 5) { ocultarMensajes(); return; }
        if (e.key === 'Enter') { e.preventDefault(); buscarCliente(documento); return; }

        timeoutBusqueda = setTimeout(() => buscarCliente(documento), 1000);
    });

    formVenta.addEventListener('submit', function(e) {
        if (!clienteValidado) {
            e.preventDefault();
            return false;
        }
    });

    function buscarCliente(documento) {
        spinnerBusqueda.style.display = 'block';
        mensajeCliente.classList.add('d-none');
        clienteValidado = false;
        btnGuardar.disabled = true;

        fetch(`/api/buscar-cliente/${documento}`)
            .then(r => r.json())
            .then(data => {
                spinnerBusqueda.style.display = 'none';
                if (data.encontrado && data.cliente) {
                    mostrarClienteEncontrado(data.cliente);
                } else {
                    mostrarFormularioNuevo();
                }
            })
            .catch(() => {
                spinnerBusqueda.style.display = 'none';
                mostrarFormularioNuevo();
            });
    }

    function mostrarClienteEncontrado(cliente) {
        mensajeCliente.className = 'alert alert-success';
        mensajeCliente.innerHTML = `
            <i class="fa fa-check-circle"></i>
            <strong>Cliente encontrado:</strong>
            ${cliente.Nombre_Cliente} ${cliente.Apellido_Cliente}
            ${cliente.ID_Estado == '1'
                ? '<span class="badge bg-success">Activo</span>'
                : '<span class="badge bg-secondary">Inactivo</span>'}
        `;
        mensajeCliente.classList.remove('d-none');
        camposNuevo.style.display = 'none';
        inputNuevo.value = '0';
        nombreCliente.removeAttribute('required');
        apellidoCliente.removeAttribute('required');
        clienteValidado = true;
        btnGuardar.disabled = false;
    }

    function mostrarFormularioNuevo() {
        mensajeCliente.className = 'alert alert-warning';
        mensajeCliente.innerHTML = `
            <i class="fa fa-exclamation-triangle"></i>
            <strong>Cliente no encontrado.</strong>
            Complete los datos para registrar un nuevo cliente:
        `;
        mensajeCliente.classList.remove('d-none');
        camposNuevo.style.display = 'block';
        inputNuevo.value = '1';
        nombreCliente.setAttribute('required', 'required');
        apellidoCliente.setAttribute('required', 'required');
        clienteValidado = true;
        btnGuardar.disabled = false;
    }

    function ocultarMensajes() {
        mensajeCliente.classList.add('d-none');
        camposNuevo.style.display = 'none';
        inputNuevo.value = '0';
        nombreCliente.removeAttribute('required');
        apellidoCliente.removeAttribute('required');
        clienteValidado = false;
        btnGuardar.disabled = true;
    }

    document.getElementById('crearModal').addEventListener('hidden.bs.modal', function() {
        formVenta.reset();
        ocultarMensajes();
        spinnerBusqueda.style.display = 'none';
        clienteValidado = false;
        btnGuardar.disabled = true;
    });
});
</script>
<div style="position: fixed; bottom: 10px; left: 0; width: 100%; text-align: center; margin-left: 115px;">
    <p style="color: #aaaaaa; font-size: 13px; margin: 0;">Copyright © 2026 Fonrio</p>
</div>
</body>
</html>