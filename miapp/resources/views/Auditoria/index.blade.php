<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <link rel="icon" href="{{ asset('Imagenes/Logo.webp') }}" type="image/webp">
    <title>Auditoría</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link rel="stylesheet" href="{{ asset('css/Inicio.css') }}">
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        .badge-op { font-weight: 700; letter-spacing: .3px; }
        .op-insert { background:#198754 !important; }
        .op-update { background:#0d6efd !important; }
        .op-delete { background:#dc3545 !important; }

        .audit-card { border:0; border-radius:16px; box-shadow: 0 8px 20px rgba(0,0,0,.06); }
        .audit-kpi { border-radius:16px; border:0; box-shadow: 0 8px 20px rgba(0,0,0,.06); }
        .audit-kpi .kpi-icon { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; }
        .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
        .small-muted { font-size:.9rem; color:#6c757d; }

        .audit-table thead th { position: sticky; top: 0; background: #fff; z-index: 2; }
        .audit-table td { vertical-align: middle; }
        .audit-preview {
            max-width: 520px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>

<body>

<div class="d-flex" style="min-height:100vh">

    <!-- SIDEBAR -->
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

    <!-- CONTENIDO -->
    <div class="contenido-principal flex-grow-1">

        <!-- NAVBAR -->
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <a class="navbar-brand">Sistema gestión de inventarios</a>

                <div class="dropdown ms-auto">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle"
                       data-bs-toggle="dropdown">
                        <img src="{{ session('foto') ?? asset('Imagenes/default-user.png') }}"
                             width="32"
                             height="32"
                             class="rounded-circle me-2"
                             alt="Perfil">
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

        <!-- PÁGINA AUDITORÍA -->
        <div class="container py-4">

            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <div>
                    <h3 class="mb-1">Auditoría de movimientos</h3>
                </div>

                <div class="d-flex gap-2">
                    <a class="btn btn-outline-secondary" href="{{ route('auditoria.index') }}">
                        <i class="ri-refresh-line"></i> Actualizar
                    </a>
                </div>
            </div>

            <!-- KPIs -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card audit-kpi p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="small-muted">Registros (hoy)</div>
                                <div class="fs-4 fw-bold">{{ $stats['hoy'] ?? 0 }}</div>
                            </div>
                            <div class="kpi-icon bg-light">
                                <i class="ri-timer-flash-line fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card audit-kpi p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="small-muted">INSERT</div>
                                <div class="fs-4 fw-bold">{{ $stats['insert'] ?? 0 }}</div>
                            </div>
                            <div class="kpi-icon bg-light">
                                <i class="ri-add-circle-line fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card audit-kpi p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="small-muted">UPDATE</div>
                                <div class="fs-4 fw-bold">{{ $stats['update'] ?? 0 }}</div>
                            </div>
                            <div class="kpi-icon bg-light">
                                <i class="ri-edit-2-line fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card audit-kpi p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="small-muted">DELETE</div>
                                <div class="fs-4 fw-bold">{{ $stats['delete'] ?? 0 }}</div>
                            </div>
                            <div class="kpi-icon bg-light">
                                <i class="ri-delete-bin-6-line fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FILTROS -->
            <div class="card audit-card p-3 mb-3">
                <form method="GET" action="{{ route('auditoria.index') }}" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Tabla</label>
                        <select class="form-select" name="tabla">
                            <option value="">Todas</option>
                            @foreach(($tablas ?? []) as $t)
                                <option value="{{ $t }}" @selected(request('tabla') === $t)>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Operación</label>
                        <select class="form-select" name="op">
                            <option value="">Todas</option>
                            <option value="INSERT" @selected(request('op') === 'INSERT')>INSERT</option>
                            <option value="UPDATE" @selected(request('op') === 'UPDATE')>UPDATE</option>
                            <option value="DELETE" @selected(request('op') === 'DELETE')>DELETE</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Desde</label>
                        <input class="form-control" type="date" name="desde" value="{{ request('desde') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Hasta</label>
                        <input class="form-control" type="date" name="hasta" value="{{ request('hasta') }}">
                    </div>

                    <div class="col-12 d-flex gap-2 mt-2">
                        <button class="btn btn-primary" type="submit">
                            <i class="ri-filter-3-line"></i> Filtrar
                        </button>
                        <a class="btn btn-outline-secondary" href="{{ route('auditoria.index') }}">
                            <i class="ri-close-circle-line"></i> Limpiar
                        </a>
                    </div>
                </form>
            </div>

            <!-- TABLA -->
            <div class="card audit-card">
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 62vh;">
                        <table class="table table-hover mb-0 audit-table">
                            <thead>
                            <tr>
                                <th style="width:90px;">ID</th>
                                <th style="width:130px;">Operación</th>
                                <th style="width:170px;">Tabla</th>
                                <th style="width:260px;">Registro</th>
                                <th style="width:200px;">Fecha</th>
                                <th>Antes</th>
                                <th>Después</th>
                                <th style="width:120px;" class="text-end">Detalle</th>
                            </tr>
                            </thead>

                            <tbody>
                            @forelse($auditorias as $a)
                                <tr>
                                    <td class="mono">#{{ $a->ID_Auditoria }}</td>

                                    <td>
                                        @php
                                            $op = strtoupper($a->Operacion);
                                            $cls = $op === 'INSERT' ? 'op-insert' : ($op === 'UPDATE' ? 'op-update' : 'op-delete');
                                        @endphp
                                        <span class="badge badge-op {{ $cls }}">{{ $op }}</span>
                                    </td>

                                    <td>{{ $a->Tabla_Afectada }}</td>
                                    <td class="mono">{{ $a->ID_Registro }}</td>
                                    <td class="mono">{{ $a->Fecha }}</td>

                                    <td>
                                        <div class="audit-preview mono" title="{{ $a->Datos_Antes ?? '' }}">
                                            {{ $a->Datos_Antes ?? '—' }}
                                        </div>
                                    </td>

                                    <td>
                                        <div class="audit-preview mono" title="{{ $a->Datos_Despues ?? '' }}">
                                            {{ $a->Datos_Despues ?? '—' }}
                                        </div>
                                    </td>

                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#auditModal"
                                                data-id="{{ $a->ID_Auditoria }}"
                                                data-tabla="{{ $a->Tabla_Afectada }}"
                                                data-op="{{ $a->Operacion }}"
                                                data-reg="{{ $a->ID_Registro }}"
                                                data-fecha="{{ $a->Fecha }}"
                                                data-antes="{{ e($a->Datos_Antes ?? '') }}"
                                                data-despues="{{ e($a->Datos_Despues ?? '') }}">
                                            Ver
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        No hay registros de auditoría con los filtros actuales.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="small-muted">
                            Mostrando {{ $auditorias->count() }} registros en esta página.
                        </div>
                        <div>
                            {{ $auditorias->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- MODAL DETALLE -->
<div class="modal fade" id="auditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content" style="border-radius:16px;">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de auditoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2 mb-3">
                    <div class="col-md-3"><div class="small-muted">ID</div><div class="mono fw-bold" id="m_id"></div></div>
                    <div class="col-md-3"><div class="small-muted">Operación</div><div class="mono fw-bold" id="m_op"></div></div>
                    <div class="col-md-6"><div class="small-muted">Tabla</div><div class="mono fw-bold" id="m_tabla"></div></div>

                    <div class="col-md-6"><div class="small-muted">Registro</div><div class="mono" id="m_reg"></div></div>
                    <div class="col-md-6"><div class="small-muted">Fecha</div><div class="mono" id="m_fecha"></div></div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="fw-bold mb-1">Antes</div>
                        <pre class="p-3 bg-light rounded-3 mono" style="min-height:140px; white-space:pre-wrap;" id="m_antes">—</pre>
                    </div>
                    <div class="col-md-6">
                        <div class="fw-bold mb-1">Después</div>
                        <pre class="p-3 bg-light rounded-3 mono" style="min-height:140px; white-space:pre-wrap;" id="m_despues">—</pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const modal = document.getElementById('auditModal');
    modal.addEventListener('show.bs.modal', event => {
        const btn = event.relatedTarget;

        document.getElementById('m_id').textContent = '#' + (btn.getAttribute('data-id') || '');
        document.getElementById('m_op').textContent = btn.getAttribute('data-op') || '';
        document.getElementById('m_tabla').textContent = btn.getAttribute('data-tabla') || '';
        document.getElementById('m_reg').textContent = btn.getAttribute('data-reg') || '';
        document.getElementById('m_fecha').textContent = btn.getAttribute('data-fecha') || '';

        const antes = btn.getAttribute('data-antes') || '';
        const despues = btn.getAttribute('data-despues') || '';

        document.getElementById('m_antes').textContent = antes.trim() ? antes : '—';
        document.getElementById('m_despues').textContent = despues.trim() ? despues : '—';
    });
</script>

</body>
</html>
