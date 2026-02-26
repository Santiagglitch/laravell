<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <link rel="icon" href="{{ asset('Imagenes/Logo.webp') }}" type="image/webp">
    <title>Auditoría</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/Inicio.css') }}">
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        .badge-op { font-weight: 700; }
        .op-insert { background:#198754 !important; }
        .op-update { background:#0d6efd !important; }
        .op-delete { background:#dc3545 !important; }

        .audit-card {
            border:0;
            border-radius:16px;
            box-shadow: 0 8px 20px rgba(0,0,0,.06);
        }

        .audit-kpi {
            border-radius:16px;
            border:0;
            box-shadow: 0 8px 20px rgba(0,0,0,.06);
        }

        .mono { font-family: ui-monospace, Consolas, monospace; }

        .field-line { margin-bottom:4px; }
        .field-label { font-weight:600; color:#6c757d; }
    </style>
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
                    <a class="elemento-menu d-flex align-items-center text-white text-decoration-none dropdown-toggle activo"
                       href="#" data-bs-toggle="dropdown">
                        <i class="ri-user-line"></i><span>Usuarios</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item active" href="{{ route('clientes.index') }}">Cliente</a></li>
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
                             alt="Perfil" width="32" height="32" class="rounded-circle me-2">
                        <strong>{{ session('nombre') ?? 'Perfil' }}</strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item" href="#">Mi perfil</a></li>
                        <li><a class="dropdown-item" href="#">Editar perfil</a></li>
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

            <h3 class="mb-4">Auditoría de movimientos</h3>

            <!-- FICHAS -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card audit-kpi p-3">
                        <div class="text-muted">Registros hoy</div>
                        <div class="fs-4 fw-bold">{{ $stats['hoy'] ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card audit-kpi p-3">
                        <div class="text-muted">Insert</div>
                        <div class="fs-4 fw-bold">{{ $stats['insert'] ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card audit-kpi p-3">
                        <div class="text-muted">Update</div>
                        <div class="fs-4 fw-bold">{{ $stats['update'] ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card audit-kpi p-3">
                        <div class="text-muted">Delete</div>
                        <div class="fs-4 fw-bold">{{ $stats['delete'] ?? 0 }}</div>
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
                    <table class="table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Operación</th>
                            <th>Tabla</th>
                            <th>Registro</th>
                            <th>Fecha</th>
                            <th>Antes</th>
                            <th>Después</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($auditorias as $a)
                            <tr>
                                <td class="mono">#{{ $a->ID_Auditoria }}</td>

                                <td>
                                    @php
                                        $op = strtoupper($a->Operacion);
                                        $cls = $op === 'INSERT' ? 'op-insert' :
                                               ($op === 'UPDATE' ? 'op-update' : 'op-delete');
                                    @endphp
                                    <span class="badge badge-op {{ $cls }}">{{ $op }}</span>
                                </td>

                                <td>{{ $a->Tabla_Afectada }}</td>
                                <td class="mono">{{ $a->ID_Registro }}</td>
                                <td class="mono">{{ $a->Fecha }}</td>

                                <td>{!! $a->Datos_Antes !!}</td>
                                <td>{!! $a->Datos_Despues !!}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    No hay registros de auditoría con los filtros actuales.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- ✅ PAGINACIÓN 1,2,3... (5 por página en el controller) -->
                <!-- PAGINACIÓN SOLO NÚMEROS -->
<div class="card-footer bg-white text-center">

    <div class="text-muted mb-2">
        Mostrando {{ $auditorias->count() }} de {{ $auditorias->total() }} registros.
    </div>

    @if ($auditorias->lastPage() > 1)
        <nav>
            <ul class="pagination justify-content-center mb-0">

                @for ($i = 1; $i <= $auditorias->lastPage(); $i++)
                    <li class="page-item {{ $auditorias->currentPage() == $i ? 'active' : '' }}">
                        <a class="page-link"
                           href="{{ $auditorias->url($i) }}">
                            {{ $i }}
                        </a>
                    </li>
                @endfor

            </ul>
        </nav>
    @endif

</div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>