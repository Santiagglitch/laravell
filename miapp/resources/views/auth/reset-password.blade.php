<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('Imagenes/Logo.webp') }}" type="image/webp">
    <title>Nueva Contraseña - TECNICELL RM</title>
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
        .password-strength {
            height: 4px;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="card p-4 p-md-5">

        <div class="text-center mb-4">
            <img src="{{ asset('Imagenes/Logo.webp') }}" style="height:56px;" class="mb-3">
            <h4 class="fw-bold">Nueva Contraseña</h4>
            <p class="text-muted small">Ingresa tu nueva contraseña. Debe tener al menos 8 caracteres.</p>
        </div>

        {{-- Mensaje de error --}}
        @if(session('error'))
            <div class="alert alert-danger text-center">
                <i class="fa fa-exclamation-circle me-2"></i>{{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            {{-- Nueva contraseña --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Nueva Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fa fa-lock text-muted"></i>
                    </span>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control border-start-0 border-end-0 @error('password') is-invalid @enderror"
                        placeholder="Mínimo 8 caracteres"
                        required
                        autofocus>
                    <span class="input-group-text bg-white" id="togglePassword1" style="cursor:pointer;">
                        <i class="fa fa-eye text-muted" id="iconPassword1"></i>
                    </span>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Indicador de fortaleza --}}
                <div class="mt-2">
                    <div class="password-strength bg-secondary" id="strengthBar" style="width:0%"></div>
                    <small id="strengthText" class="text-muted"></small>
                </div>
            </div>

            {{-- Confirmar contraseña --}}
            <div class="mb-4">
                <label class="form-label fw-semibold">Confirmar Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fa fa-lock text-muted"></i>
                    </span>
                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        class="form-control border-start-0 border-end-0"
                        placeholder="Repite tu contraseña"
                        required>
                    <span class="input-group-text bg-white" id="togglePassword2" style="cursor:pointer;">
                        <i class="fa fa-eye text-muted" id="iconPassword2"></i>
                    </span>
                </div>
                <small id="matchText" class="text-muted"></small>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-3" id="btnSubmit">
                <i class="fa fa-key me-2"></i> Restablecer Contraseña
            </button>

            <div class="text-center">
                <a href="{{ route('login.form') }}" class="text-decoration-none text-muted small">
                    <i class="fa fa-arrow-left me-1"></i> Volver al inicio de sesión
                </a>
            </div>

        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle ver/ocultar contraseña 1
        document.getElementById('togglePassword1').addEventListener('click', function() {
            const input = document.getElementById('password');
            const icon  = document.getElementById('iconPassword1');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });

        // Toggle ver/ocultar contraseña 2
        document.getElementById('togglePassword2').addEventListener('click', function() {
            const input = document.getElementById('password_confirmation');
            const icon  = document.getElementById('iconPassword2');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });

        // Indicador de fortaleza de contraseña
        document.getElementById('password').addEventListener('input', function() {
            const val      = this.value;
            const bar      = document.getElementById('strengthBar');
            const text     = document.getElementById('strengthText');

            let strength = 0;
            if (val.length >= 8)                        strength++;
            if (/[A-Z]/.test(val))                      strength++;
            if (/[0-9]/.test(val))                      strength++;
            if (/[^A-Za-z0-9]/.test(val))               strength++;

            const levels = [
                { width: '0%',   color: 'bg-secondary', label: '' },
                { width: '25%',  color: 'bg-danger',    label: 'Muy débil' },
                { width: '50%',  color: 'bg-warning',   label: 'Débil' },
                { width: '75%',  color: 'bg-info',      label: 'Buena' },
                { width: '100%', color: 'bg-success',   label: 'Muy fuerte' },
            ];

            bar.style.width         = levels[strength].width;
            bar.className           = `password-strength ${levels[strength].color}`;
            text.textContent        = levels[strength].label;

            checkMatch();
        });

        // Verificar que las contraseñas coinciden
        document.getElementById('password_confirmation').addEventListener('input', checkMatch);

        function checkMatch() {
            const pass    = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirmation').value;
            const text    = document.getElementById('matchText');
            const btn     = document.getElementById('btnSubmit');

            if (confirm === '') {
                text.textContent = '';
                return;
            }

            if (pass === confirm) {
                text.textContent  = '✅ Las contraseñas coinciden';
                text.className    = 'text-success small';
                btn.disabled      = false;
            } else {
                text.textContent  = '❌ Las contraseñas no coinciden';
                text.className    = 'text-danger small';
                btn.disabled      = true;
            }
        }
    </script>
</body>
</html>