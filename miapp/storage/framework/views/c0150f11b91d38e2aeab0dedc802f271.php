<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?php echo e(asset('Imagenes/Logo.webp')); ?>" type="image/webp">
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
            <img src="<?php echo e(asset('Imagenes/Logo.webp')); ?>" style="height:56px;" class="mb-3">
            <h4 class="fw-bold">Recuperar Contraseña</h4>
            <p class="text-muted small">Ingresa tu correo registrado y te enviaremos un enlace para restablecer tu contraseña.</p>
        </div>

        
        <?php if(session('mensaje')): ?>
            <div class="alert alert-success text-center">
                <i class="fa fa-check-circle me-2"></i><?php echo e(session('mensaje')); ?>

            </div>
        <?php endif; ?>

        
        <?php if(session('error')): ?>
            <div class="alert alert-danger text-center">
                <i class="fa fa-exclamation-circle me-2"></i><?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('password.email')); ?>">
            <?php echo csrf_field(); ?>

            <div class="mb-4">
                <label class="form-label fw-semibold">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fa fa-envelope text-muted"></i>
                    </span>
                    <input
                        type="email"
                        name="email"
                        class="form-control border-start-0 <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        placeholder="ejemplo@correo.com"
                        value="<?php echo e(old('email')); ?>"
                        required
                        autofocus>
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-3">
                <i class="fa fa-paper-plane me-2"></i> Enviar enlace de recuperación
            </button>

            <div class="text-center">
                <a href="<?php echo e(route('login.form')); ?>" class="text-decoration-none text-muted small">
                    <i class="fa fa-arrow-left me-1"></i> Volver al inicio de sesión
                </a>
            </div>

        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html><?php /**PATH C:\Users\Kevin Morave\Music\Laravel\laravell\miapp\resources\views/auth/forgot-password.blade.php ENDPATH**/ ?>