<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="icon" href="<?php echo e(asset('Imagenes/Logo.webp')); ?>" type="image/webp">
    <title>Mi Perfil - TECNICELL RM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('css/menu.css')); ?>">
</head>
<body>

<div class="d-flex" style="min-height:100vh">

    
    <div class="barra-lateral d-flex flex-column flex-shrink-0 p-3 bg-primary text-white">
        <a class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            TECNICELL RM <img src="<?php echo e(asset('Imagenes/Logo.webp')); ?>" style="height:48px;">
        </a>
        <hr>
        <div class="menu-barra-lateral">
            <div class="seccion-menu">
                <a href="<?php echo e(route('admin.inicio')); ?>" class="elemento-menu">
                    <i class="fa-solid fa-tachometer-alt"></i><span>Dashboard</span>
                </a>
                <a href="<?php echo e(route('compras.index')); ?>" class="elemento-menu">
                    <i class="ri-shopping-cart-2-line"></i><span>Compras</span>
                </a>
                <a href="<?php echo e(route('devolucion.index')); ?>" class="elemento-menu">
                    <i class="ri-arrow-go-back-line"></i><span>Devoluciones</span>
                </a>
                <a href="<?php echo e(route('ventas.index')); ?>" class="elemento-menu">
                    <i class="ri-price-tag-3-line"></i><span>Ventas</span>
                </a>
                  <a href="<?php echo e(route('auditoria.index')); ?>"
                   class="elemento-menu <?php echo e(request()->routeIs('auditoria.*') ? 'activo' : ''); ?>">
                    <i class="ri-shield-check-line"></i>
                    <span>Auditoría</span>
                </a>
            </div>
            <hr>
            <div class="seccion-menu">
                <a href="<?php echo e(route('productos.index')); ?>" class="elemento-menu">
                    <i class="ri-box-3-line"></i><span>Productos</span>
                </a>
                <a href="<?php echo e(route('proveedor.index')); ?>" class="elemento-menu">
                    <i class="ri-truck-line"></i><span>Proveedores</span>
                </a>
                <div class="dropdown">
                    <a class="elemento-menu d-flex align-items-center text-white text-decoration-none dropdown-toggle"
                       data-bs-toggle="dropdown">
                        <i class="ri-user-line"></i><span>Usuarios</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?php echo e(route('clientes.index')); ?>">Cliente</a></li>
                        <li><a class="dropdown-item" href="<?php echo e(route('empleados.index')); ?>">Empleado</a></li>
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
                        <img src="<?php echo e(session('foto') ?? asset('Imagenes/default-user.png')); ?>"
                             width="32" height="32" class="rounded-circle me-2">
                        <strong><?php echo e(session('nombre') ?? 'Perfil'); ?></strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                        <li><a class="dropdown-item" href="<?php echo e(route('perfil')); ?>">Mi perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="<?php echo e(route('logout')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="dropdown-item">Cerrar sesión</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container py-4">

            <div class="d-flex justify-content-center align-items-center gap-3 mb-4">
                <img src="<?php echo e(asset('Imagenes/Logo.webp')); ?>" style="height:48px;">
                <h1>Mi Perfil</h1>
            </div>

            
            <?php if(session('mensaje')): ?>
                <div id="alertaMensaje" class="alert alert-success text-center">
                    <i class="fa fa-check-circle me-2"></i><?php echo e(session('mensaje')); ?>

                </div>
                <script>
                    setTimeout(() => {
                        let a = document.getElementById('alertaMensaje');
                        if (a) { a.style.transition = "opacity 0.5s"; a.style.opacity = 0; setTimeout(() => a.remove(), 500); }
                    }, 3000);
                </script>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div id="alertaError" class="alert alert-danger text-center">
                    <i class="fa fa-exclamation-circle me-2"></i><?php echo e(session('error')); ?>

                </div>
                <script>
                    setTimeout(() => {
                        let a = document.getElementById('alertaError');
                        if (a) { a.style.transition = "opacity 0.5s"; a.style.opacity = 0; setTimeout(() => a.remove(), 500); }
                    }, 4000);
                </script>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?php echo e(route('perfil.actualizar')); ?>" method="POST"
                  enctype="multipart/form-data" id="formPerfil">
                <?php echo csrf_field(); ?>

                <div class="row g-4">

                    
                    <div class="col-md-3">
                        <div class="card shadow-sm text-center p-4 h-100">
                            <div class="position-relative d-inline-block mx-auto mb-3">
                                <img id="previewFoto"
                                     src="<?php echo e($fotoUrl ?? asset('Imagenes/default-user.png')); ?>"
                                     class="rounded-circle shadow"
                                     style="width:140px; height:140px; object-fit:cover; border:4px solid #dee2e6;">

                                
                                <label for="inputFoto"
                                       class="btn btn-primary btn-sm rounded-circle position-absolute"
                                       style="bottom:5px; right:5px; width:34px; height:34px; cursor:pointer;"
                                       title="Cambiar foto">
                                    <i class="fa fa-camera"></i>
                                </label>
                                <input type="file" id="inputFoto" name="Fotos"
                                       accept=".jpg,.jpeg,.png,.webp" class="d-none">
                            </div>

                            <h5 class="fw-bold mb-0"><?php echo e($empleado->Nombre_Usuario); ?> <?php echo e($empleado->Apellido_Usuario); ?></h5>
                            <small class="text-muted">
                                <?php echo e($empleado->ID_Rol == 1 ? 'Administrador' : 'Empleado'); ?>

                            </small>

                            <hr>

                            
                            <div class="text-start small">
                                <p class="mb-1">
                                    <i class="fa fa-id-card text-primary me-2"></i>
                                    <strong>Documento:</strong> <?php echo e($empleado->Documento_Empleado); ?>

                                </p>
                                <p class="mb-1">
                                    <i class="fa fa-file-alt text-primary me-2"></i>
                                    <strong>Tipo:</strong> <?php echo e($empleado->Tipo_Documento); ?>

                                </p>
                                <p class="mb-1">
                                    <i class="fa fa-birthday-cake text-primary me-2"></i>
                                    <strong>Edad:</strong> <?php echo e($empleado->Edad); ?>

                                </p>
                                <p class="mb-1">
                                    <i class="fa fa-venus-mars text-primary me-2"></i>
                                    <strong>Género:</strong> <?php echo e($empleado->Genero); ?>

                                </p>
                                <p class="mb-0">
                                    <i class="fa fa-circle text-<?php echo e($empleado->ID_Estado == 1 ? 'success' : 'secondary'); ?> me-2"></i>
                                    <strong>Estado:</strong>
                                    <?php echo e($empleado->ID_Estado == 1 ? 'Activo' : 'Inactivo'); ?>

                                </p>
                            </div>

                            <div id="fotoNombreArchivo" class="mt-2 text-muted small d-none">
                                <i class="fa fa-check-circle text-success"></i>
                                <span id="fotoNombre"></span>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-md-9">
                        <div class="card shadow-sm p-4">

                            <h5 class="fw-bold mb-4">
                                <i class="fa fa-user-edit text-primary me-2"></i>
                                Información Personal
                            </h5>

                            <div class="row g-3">

                                
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nombre</label>
                                    <input type="text" name="Nombre_Usuario"
                                           class="form-control campo-editable <?php $__errorArgs = ['Nombre_Usuario'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           value="<?php echo e(old('Nombre_Usuario', $empleado->Nombre_Usuario)); ?>"
                                           required>
                                    <?php $__errorArgs = ['Nombre_Usuario'];
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

                                
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Apellido</label>
                                    <input type="text" name="Apellido_Usuario"
                                           class="form-control campo-editable <?php $__errorArgs = ['Apellido_Usuario'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           value="<?php echo e(old('Apellido_Usuario', $empleado->Apellido_Usuario)); ?>"
                                           required>
                                    <?php $__errorArgs = ['Apellido_Usuario'];
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

                                
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Correo Electrónico</label>
                                    <input type="email" name="Correo_Electronico"
                                           class="form-control campo-editable <?php $__errorArgs = ['Correo_Electronico'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           value="<?php echo e(old('Correo_Electronico', $empleado->Correo_Electronico)); ?>"
                                           required>
                                    <?php $__errorArgs = ['Correo_Electronico'];
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

                                
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Teléfono</label>
                                    <input type="text" name="Telefono"
                                           class="form-control campo-editable <?php $__errorArgs = ['Telefono'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           value="<?php echo e(old('Telefono', $empleado->Telefono)); ?>"
                                           required>
                                    <?php $__errorArgs = ['Telefono'];
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

                            <hr class="my-4">

                            
                            <h5 class="fw-bold mb-3">
                                <i class="fa fa-lock text-primary me-2"></i>
                                Contraseña
                                <small class="text-muted fw-normal fs-6">(opcional — solo si deseas cambiarla)</small>
                            </h5>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nueva Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" name="nueva_contrasena"
                                               id="nueva_contrasena"
                                               class="form-control campo-editable <?php $__errorArgs = ['nueva_contrasena'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               placeholder="Mínimo 8 caracteres">
                                        <span class="input-group-text" id="toggleNueva" style="cursor:pointer;">
                                            <i class="fa fa-eye" id="iconNueva"></i>
                                        </span>
                                    </div>
                                    <?php $__errorArgs = ['nueva_contrasena'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Confirmar Nueva Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" name="nueva_contrasena_confirmation"
                                               id="confirmar_contrasena"
                                               class="form-control campo-editable"
                                               placeholder="Repite la contraseña">
                                        <span class="input-group-text" id="toggleConfirmar" style="cursor:pointer;">
                                            <i class="fa fa-eye" id="iconConfirmar"></i>
                                        </span>
                                    </div>
                                    <small id="matchTexto" class="mt-1"></small>
                                </div>
                            </div>

                            <hr class="my-4">

                            
                            <div class="alert alert-warning">
                                <i class="fa fa-shield-alt me-2"></i>
                                <strong>Seguridad:</strong> Ingresa tu contraseña actual para confirmar los cambios.
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Contraseña Actual</label>
                                <div class="input-group">
                                    <input type="password" name="contrasena_actual"
                                           id="contrasena_actual"
                                           class="form-control <?php $__errorArgs = ['contrasena_actual'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           placeholder="Ingresa tu contraseña actual"
                                           required>
                                    <span class="input-group-text" id="toggleActual" style="cursor:pointer;">
                                        <i class="fa fa-eye" id="iconActual"></i>
                                    </span>
                                </div>
                                <?php $__errorArgs = ['contrasena_actual'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            
                            <div class="d-flex gap-3 mt-4">
                                <button type="submit" class="btn btn-primary px-4" id="btnGuardar" disabled>
                                    <i class="fa fa-save me-2"></i> Guardar Cambios
                                </button>
                                <button type="button" class="btn btn-secondary px-4" id="btnCancelar">
                                    <i class="fa fa-times me-2"></i> Cancelar
                                </button>
                            </div>

                        </div>
                    </div>

                </div>
            </form>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>

/* ============================================================
   VALORES ORIGINALES para detectar cambios
============================================================ */
const valoresOriginales = {
    Nombre_Usuario:     '<?php echo e($empleado->Nombre_Usuario); ?>',
    Apellido_Usuario:   '<?php echo e($empleado->Apellido_Usuario); ?>',
    Correo_Electronico: '<?php echo e($empleado->Correo_Electronico); ?>',
    Telefono:           '<?php echo e($empleado->Telefono); ?>',
};

let fotoModificada = false;

function hayModificaciones() {
    const campos = document.querySelectorAll('.campo-editable');
    for (let campo of campos) {
        if (campo.name in valoresOriginales) {
            if (campo.value !== valoresOriginales[campo.name]) return true;
        }
        // Si escribió nueva contraseña
        if (campo.name === 'nueva_contrasena' && campo.value.length > 0) return true;
    }
    return fotoModificada;
}

function verificarCambios() {
    const btnGuardar   = document.getElementById('btnGuardar');
    const contrasenaOk = document.getElementById('contrasena_actual').value.length > 0;
    btnGuardar.disabled = !(hayModificaciones() && contrasenaOk);
}

// Escuchar cambios en todos los campos editables
document.querySelectorAll('.campo-editable').forEach(campo => {
    campo.addEventListener('input', verificarCambios);
});

document.getElementById('contrasena_actual').addEventListener('input', verificarCambios);

/* ============================================================
   PREVISUALIZACIÓN DE FOTO
============================================================ */
document.getElementById('inputFoto').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;

    // Validar tamaño
    if (file.size > 2 * 1024 * 1024) {
        alert('⚠️ La imagen no debe superar 2MB.');
        this.value = '';
        return;
    }

    // Validar formato
    const formatos = ['image/jpeg', 'image/png', 'image/webp'];
    if (!formatos.includes(file.type)) {
        alert('⚠️ Formatos permitidos: JPG, PNG, WEBP.');
        this.value = '';
        return;
    }

    // Preview
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('previewFoto').src = e.target.result;
    };
    reader.readAsDataURL(file);

    // Mostrar nombre
    document.getElementById('fotoNombre').textContent = file.name;
    document.getElementById('fotoNombreArchivo').classList.remove('d-none');

    fotoModificada = true;
    verificarCambios();
});

/* ============================================================
   VALIDAR CONTRASEÑAS COINCIDEN
============================================================ */
document.getElementById('confirmar_contrasena').addEventListener('input', function() {
    const nueva    = document.getElementById('nueva_contrasena').value;
    const confirma = this.value;
    const texto    = document.getElementById('matchTexto');

    if (confirma === '') { texto.textContent = ''; return; }

    if (nueva === confirma) {
        texto.textContent = '✅ Las contraseñas coinciden';
        texto.className   = 'text-success small mt-1';
    } else {
        texto.textContent = '❌ Las contraseñas no coinciden';
        texto.className   = 'text-danger small mt-1';
    }
    verificarCambios();
});

/* ============================================================
   CANCELAR — revertir cambios
============================================================ */
document.getElementById('btnCancelar').addEventListener('click', function() {
    // Restaurar valores originales
    document.querySelector('[name="Nombre_Usuario"]').value     = valoresOriginales.Nombre_Usuario;
    document.querySelector('[name="Apellido_Usuario"]').value   = valoresOriginales.Apellido_Usuario;
    document.querySelector('[name="Correo_Electronico"]').value = valoresOriginales.Correo_Electronico;
    document.querySelector('[name="Telefono"]').value           = valoresOriginales.Telefono;
    document.querySelector('[name="nueva_contrasena"]').value   = '';
    document.querySelector('[name="nueva_contrasena_confirmation"]').value = '';
    document.getElementById('contrasena_actual').value          = '';
    document.getElementById('matchTexto').textContent           = '';

    // Restaurar foto
    document.getElementById('previewFoto').src = '<?php echo e($fotoUrl ?? asset("Imagenes/default-user.png")); ?>';
    document.getElementById('inputFoto').value = '';
    document.getElementById('fotoNombreArchivo').classList.add('d-none');
    fotoModificada = false;

    document.getElementById('btnGuardar').disabled = true;
});

/* ============================================================
   TOGGLE VER/OCULTAR CONTRASEÑAS
============================================================ */
[
    ['toggleNueva',    'nueva_contrasena',    'iconNueva'],
    ['toggleConfirmar','confirmar_contrasena', 'iconConfirmar'],
    ['toggleActual',   'contrasena_actual',   'iconActual'],
].forEach(([toggleId, inputId, iconId]) => {
    document.getElementById(toggleId).addEventListener('click', function() {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });
});

/* ============================================================
   VALIDAR ANTES DE ENVIAR
============================================================ */
document.getElementById('formPerfil').addEventListener('submit', function(e) {
    const nueva    = document.getElementById('nueva_contrasena').value;
    const confirma = document.getElementById('confirmar_contrasena').value;

    if (nueva && nueva !== confirma) {
        e.preventDefault();
        alert('Las contraseñas nuevas no coinciden.');
        return;
    }

    if (nueva && nueva.length < 8) {
        e.preventDefault();
        alert('La nueva contraseña debe tener al menos 8 caracteres.');
    }
});
</script>
<div style="position: fixed; bottom: 10px; left: 0; width: 100%; text-align: center; margin-left: 115px;">
    <p style="color: #aaaaaa; font-size: 13px; margin: 0;">Copyright © 2026 Fonrio</p>
</div>
</body>
</html><?php /**PATH C:\Users\rnico\laraveeeeee\miapp\resources\views/Perfil/Perfil.blade.php ENDPATH**/ ?>