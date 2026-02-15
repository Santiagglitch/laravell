<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('Imagenes/Logo.webp') }}" type="image/webp">
    <title>Recuperar Contraseña - Fonrio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            border: none;
            width: 100%;
            max-width: 420px;
        }
        .btn-primary {
            background: #3B82F6;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 500;
        }
        .btn-primary:hover {
            background: #2563EB;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px;
            border: 1.5px solid #e5e7eb;
        }
        .form-control:focus {
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        }
    </style>
</head>
<body>
    <div class="card p-4 p-md-5">

        <div class="text-center mb-4">
            <img src="{{ asset('Imagenes/Logo.webp') }}" style="height:56px;" class="mb-3">
            <h4 class="fw-bold">Recuperar Contraseña</h4>
            <p class="text-muted small">Ingresa tu correo registrado y te enviaremos un enlace para restablecer tu contraseña.</p>
        </div>

        {{-- Mensaje de éxito --}}
        @if(session('mensaje'))
            <div class="alert alert-success text-center">
                <i class="fa fa-check-circle me-2"></i>{{ session('mensaje') }}
            </div>
        @endif

        {{-- Mensaje de error --}}
        @if(session('error'))
            <div class="alert alert-danger text-center">
                <i class="fa fa-exclamation-circle me-2"></i>{{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-4">
                <label class="form-label fw-semibold">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fa fa-envelope text-muted"></i>
                    </span>
                    <input
                        type="email"
                        name="email"
                        class="form-control border-start-0 @error('email') is-invalid @enderror"
                        placeholder="ejemplo@correo.com"
                        value="{{ old('email') }}"
                        required
                        autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-3">
                <i class="fa fa-paper-plane me-2"></i> Enviar enlace de recuperación
            </button>

            <div class="text-center">
                <a href="{{ route('login.form') }}" class="text-decoration-none text-muted small">
                    <i class="fa fa-arrow-left me-1"></i> Volver al inicio de sesión
                </a>
            </div>

        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>