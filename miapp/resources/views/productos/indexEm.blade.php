<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="{{ asset('Imagenes/Logo.webp') }}" type="image/webp">
    <title>Productos</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
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

                <a href="{{ route('ventas.indexEm') }}" class="elemento-menu activo">
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

                <a href="{{ route('clientes.indexEm') }}" class="elemento-menu">
                    <i class="ri-user-line"></i><span>Cliente</span>
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
                       data-bs-toggle="dropdown">
                        <img src="{{ asset('fotos_empleados/686fe89fe865f_Foto Kevin.jpeg') }}"
                             width="32" height="32" class="rounded-circle me-2">
                        <strong>{{ session('nombre') ?? 'Perfil' }}</strong>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                        <li><a class="dropdown-item" href="#">Mi perfil</a></li>
                        <li><a class="dropdown-item" href="#">Editar perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="dropdown-item">Cerrar sesión</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container py-4">
            <div class="d-flex justify-content-center align-items-center gap-3">
                <img src="{{ asset('Imagenes/Logo.webp') }}" style="height:48px;">
                <h1>Gestión de Productos</h1>
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

            <div class="text-end mt-4">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fa fa-plus"></i> Añadir Producto
                </button>
            </div>

            <div class="table-responsive mt-4">
                <table class="table table-bordered table-hover table-striped align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th>Stock Mín</th>
                            <th>Categoría</th>
                            <th>Estado</th>
                            <th>Gama</th>
                            <th>Foto</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($productos as $pro)
                        <tr>
                            <td>{{ $pro['ID_Producto'] }}</td>
                            <td>{{ $pro['Nombre_Producto'] }}</td>
                            <td>{{ $pro['Descripcion'] }}</td>
                            <td>{{ $pro['Precio_Venta'] }}</td>
                            <td>{{ $pro['Stock_Minimo'] }}</td>
                            <td>{{ $pro['ID_Categoria'] }}</td>
                            <td>{{ $pro['ID_Estado'] }}</td>
                            <td>{{ $pro['ID_Gama'] }}</td>
                            <td>
                                @if(!empty($pro['Fotos']))
                                    <img src="{{ asset('storage/' . $pro['Fotos']) }}" width="50" class="rounded">
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

                        <div class="modal fade" id="editarModal{{ $pro['ID_Producto'] }}">
                            <div class="modal-dialog modal-lg">
                                <form method="POST" action="{{ route('productos.updateEm') }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="ID_Producto" value="{{ $pro['ID_Producto'] }}">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Editar Producto</h5>
                                            <button class="btn-close" data-bs-dismiss="modal"></button>
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
                                                <label>Nueva Foto</label>
                                                <input type="file" name="Fotos" class="form-control">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-warning" type="submit">Actualizar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="modal fade" id="eliminarModal{{ $pro['ID_Producto'] }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('productos.destroyEm') }}">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="ID_Producto" value="{{ $pro['ID_Producto'] }}">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Eliminar Producto</h5>
                                            <button class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            ¿Seguro que deseas eliminar este producto?
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-danger" type="submit">Eliminar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="10" class="text-muted">No hay productos registrados.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="modal fade" id="crearModal">
                <div class="modal-dialog modal-lg">
                    <form method="POST" action="{{ route('productos.storeEm') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">Añadir Producto</h5>
                                <button class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body row g-3">
                                <div class="col-md-6">
                                    <label>ID Producto</label>
                                    <input name="ID_Producto" class="form-control" required>
                                </div>
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
                                    <label>Foto</label>
                                    <input type="file" name="Fotos" class="form-control">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-success" type="submit">Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
