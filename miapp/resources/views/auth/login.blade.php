<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Iniciar Sesión</title>

    <link rel="icon" type="image/png" href="{{ asset('Imagenes/Logo.webp') }}">

    {{-- CSS --}}
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

                {{-- Mensaje de error general de login --}}
                @if ($errors->has('login'))
                    <div class="alert alert-danger">
                        {{ $errors->first('login') }}
                    </div>
                @endif

                {{-- Errores de validación de campos --}}
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

                    <div class="mb-1">
                        <label for="contrasena" class="form-label">Contraseña</label>
                        <input
                            type="password"
                            class="form-control"
                            id="contrasena"
                            name="contrasena"
                            placeholder="Ingrese su contraseña"
                            required
                        />
                    </div>

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
