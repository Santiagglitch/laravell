<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Iniciar Sesión</title>

    <link rel="icon" type="image/png" href="{{ asset('Imagenes/Logo.webp') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>



    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('css/IniciarSesion.css') }}" rel="stylesheet"/>
</head>
<body>
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-4">
            <div class="InicioI">
                <div class="text-center mb-4">
                    <h1>Bienvenido</h1>
                    <p>Inicia sesión para continuar</p>
                </div>

                @if ($errors->has('login'))
                    <div class="alert alert-danger">
                        {{ $errors->first('login') }}
                    </div>
                @endif

                @if ($errors->any() && !$errors->has('login'))
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="usuario" class="form-label">Documento</label>
                        <input
                            type="text"
                            class="form-control"
                            id="usuario"
                            name="usuario"
                            placeholder="Ingrese su documento"
                            value="{{ old('usuario') }}"
                            required
                        />
                    </div>

                 <div class="mb-1 position-relative">
                <label for="contrasena" class="form-label">Contraseña</label>
                <div class="input-group">
                 <input
                 type="password"
                 class="form-control"
                 id="contrasena"
                 name="contrasena"
                 placeholder="Ingrese su contraseña"
                 required
                 />
        <span class="input-group-text" id="togglePassword" style="cursor:pointer;">
            <i class="bi bi-eye"></i>
        </span>
    </div>
        </div>
        <script>
    
    
    document.getElementById('togglePassword').addEventListener('click', function () {
        const input = document.getElementById('contrasena');
        const icon = this.querySelector('i');

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("bi-eye");
            icon.classList.add("bi-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("bi-eye-slash");
            icon.classList.add("bi-eye");
        }
    });
    </script>


                    <div class="mb-3 text-end">
                        {{-- Ajusta cuando tengas la vista en Laravel --}}
                        <a href="#" class="link-recuperar">¿Olvidaste tu contraseña?</a>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Entrar</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
</body>
</html>