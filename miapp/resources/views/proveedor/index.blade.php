<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Proveedores</title>

  <!-- Bootstrap + Font Awesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<link rel="stylesheet" href="{{ asset('css/ventas.css') }}">
</head>
<body>
  <div class="d-flex" style="min-height: 100vh;">

    <!-- SIDEBAR -->
    <div class="barra-lateral d-flex flex-column flex-shrink-0 p-3 bg-primary text-white">
      <a class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        TECNICELL RM
      </a>
      <hr>
      <div class="menu-barra-lateral">
        <div class="seccion-menu">
          <a href="/Fonrio//Vista/InicioA.php" class="elemento-menu">
            <i class="fa-solid fa-tachometer-alt"></i><span>Dashboard</span>
          </a>
          <a href="/Fonrio/indexcompras.php" class="elemento-menu">
            <i class="fa-solid fa-shopping-cart"></i><span>Compras</span>
          </a>
          <a href="/Fonrio/indexdev.php" class="elemento-menu">
            <i class="fa-solid fa-undo"></i><span>Devoluciones</span>
          </a>
          <a href="/Fonrio/indexventas.php" class="elemento-menu">
            <i class="fa-solid fa-chart-line"></i><span>Ventas</span>
          </a>
        </div>
        <hr>
        <div class="seccion-menu">
          <a href="/Fonrio/indexproveedor.php" class="elemento-menu active">
            <i class="fa-solid fa-users"></i><span>Proveedores</span>
          </a>
          <a href="/Fonrio/indexproducto.php" class="elemento-menu">
            <i class="fa-solid fa-boxes"></i><span>Productos</span>
          </a>

          <a class="elemento-menu d-flex align-items-center text-white text-decoration-none dropdown-toggle"
             href="#" id="rolesMenu" data-bs-toggle="dropdown">
            <i class="fas fa-user-friends me-2"></i><span>Roles</span>
          </a>

          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="/Fonrio/indexcli.php">Cliente</a></li>
            <li><a class="dropdown-item" href="/Fonrio/indexempleado.php">Empleado</a></li>
          </ul>

        </div>
      </div>
    </div>

    <!-- CONTENIDO -->
    <div class="contenido-principal flex-grow-1">

      <!-- NAVBAR -->
      <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
          <a class="navbar-brand">Sistema gestión de inventarios</a>

          <div class="collapse navbar-collapse" id="navbarNav"></div>

          <div class="dropdown ms-auto">
            <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle"
               id="dropdownUser1" data-bs-toggle="dropdown">
              <img src="/Fonrio/php/fotos_empleados/686fe89fe865f_Foto Kevin.jpeg"
                   width="32" height="32" class="rounded-circle me-2">
              <strong>Perfil</strong>
            </a>

            <ul class="dropdown-menu dropdown-menu-dark shadow">
              <li><a class="dropdown-item" href="Perfil.html">Mi perfil</a></li>
              <li><a class="dropdown-item" href="EditarPerfil.php">Editar perfil</a></li>
              <li><a class="dropdown-item" href="Registro.php">Registrarse</a></li>
              <li><a class="dropdown-item" href="/Fonrio/Vista/Index.php">Cerrar Sesión</a></li>
            </ul>

          </div>

        </div>
      </nav>

      <!-- CABECERA -->
      <div class="container py-4">

        <div class="d-flex align-items-center justify-content-center gap-3">
          <img src="../Imagenes/Logo.webp" style="height:48px" />
          <h1 class="m-0">Registro de Proveedores</h1>
        </div>

        <!-- MENSAJE LARAVEL -->
        @if(session('mensaje'))
          <div class="alert alert-success mt-3">
            {{ session('mensaje') }}
          </div>
        @endif

        <!-- TABLA -->
        <div class="mt-4">
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped align-middle">
              <thead class="table-dark text-center">
                <tr>
                  <th>ID_Proveedor</th>
                  <th>Nombre_Proveedor</th>
                  <th>Correo_Electronico</th>
                  <th>Telefono</th>
                  <th>ID_Estado</th>
                </tr>
              </thead>
              <tbody>

              @forelse($proveedores as $prov)
                <tr>
                  <td>{{ $prov->ID_Proveedor }}</td>
                  <td>{{ $prov->Nombre_Proveedor }}</td>
                  <td>{{ $prov->Correo_Electronico }}</td>
                  <td>{{ $prov->Telefono }}</td>
                  <td>{{ $prov->ID_Estado }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted">No hay proveedores para mostrar.</td>
                </tr>
              @endforelse

              </tbody>
            </table>
          </div>
        </div>

        <!-- FORMULARIOS -->
        <div class="mt-5">
          <div class="row g-4">

            <!-- CREAR -->
            <div class="col-md-6">
              <div class="card shadow-sm">
                <div class="card-body">
                  <h2 class="h4 mb-3">Añadir Proveedor</h2>

                  <form method="POST" action="{{ route('proveedor.store') }}" class="row g-3">
                    @csrf

                    <div class="col-md-6">
                      <label class="form-label">ID_Proveedor</label>
                      <input type="text" name="ID_Proveedor" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Nombre_Proveedor</label>
                      <input type="text" name="Nombre_Proveedor" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Correo_Electronico</label>
                      <input type="email" name="Correo_Electronico" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Telefono</label>
                      <input type="text" name="Telefono" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">ID_Estado</label>
                      <select name="ID_Estado" class="form-select" required>
                        <option value="EST001">Activo</option>
                        <option value="EST002">Inactivo</option>
                        <option value="EST003">En Proceso</option>
                      </select>
                    </div>

                    <div class="col-12 text-center mt-3">
                      <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>

                  </form>

                </div>
              </div>
            </div>

            <!-- ACTUALIZAR -->
            <div class="col-md-6">
              <div class="card shadow-sm">
                <div class="card-body">
                  <h2 class="h4 mb-3">Actualizar Proveedor</h2>

                  <form method="POST" action="{{ route('proveedor.update') }}" class="row g-3">
                    @csrf
                    @method('PUT')

                    <div class="col-md-6">
                      <label class="form-label">ID_Proveedor</label>
                      <input type="text" name="ID_Proveedor" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Nombre_Proveedor</label>
                      <input type="text" name="Nombre_Proveedor" class="form-control">
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Correo_Electronico</label>
                      <input type="email" name="Correo_Electronico" class="form-control">
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Telefono</label>
                      <input type="text" name="Telefono" class="form-control">
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">ID_Estado</label>
                      <select name="ID_Estado" class="form-select">
                        <option value="">(sin cambio)</option>
                        <option value="EST001">Activo</option>
                        <option value="EST002">Inactivo</option>
                        <option value="EST003">En Proceso</option>
                      </select>
                    </div>

                    <div class="col-12 text-center mt-3">
                      <button type="submit" class="btn btn-warning">Actualizar</button>
                    </div>

                  </form>

                </div>
              </div>
            </div>

            <!-- ELIMINAR -->
            <div class="col-md-6">
              <div class="card shadow-sm">
                <div class="card-body">
                  <h2 class="h4 mb-3">Eliminar Proveedor</h2>

                  <form method="POST" action="{{ route('proveedor.destroy') }}" class="row g-3">
                    @csrf
                    @method('DELETE')

                    <div class="col-md-6">
                      <label class="form-label">ID_Proveedor</label>
                      <input type="text" name="ID_Proveedor" class="form-control" required>
                    </div>

                    <div class="col-12 text-center mt-3">
                      <button type="submit" class="btn btn-danger">Eliminar</button>
                    </div>

                  </form>

                </div>
              </div>
            </div>

          </div>
        </div>

        <footer class="footer mt-5 text-center text-muted">
          <p class="m-0">Copyright © 2025 Fonrio</p>
        </footer>

      </div>
    </div>

  </div>

</body>
</html>