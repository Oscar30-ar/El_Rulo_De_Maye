<?php
$ctrl = new ControladorUsuarios();
$usuario = UsuarioModelo::mdlMostrarUsuario("id", $_SESSION["id"]);
$mensajeAlerta = $ctrl->ctrActualizarPerfil();

// Generar iniciales si no tiene foto (toma las 2 primeras palabras)
$iniciales = "U";
if (!empty($usuario["nombre"])) {
    $palabras = preg_split('/\s+/', trim($usuario["nombre"]));
    if (count($palabras) >= 2) {
        $iniciales = mb_strtoupper(mb_substr($palabras[0], 0, 1) . mb_substr($palabras[1], 0, 1));
    } else {
        $iniciales = mb_strtoupper(mb_substr($palabras[0], 0, 2));
    }
}

$tieneFotoReal = (!empty($usuario["foto"]) && file_exists($usuario["foto"]));
?>

<div class="container my-5">
    <?= $mensajeAlerta ?>

    <div class="row g-4 justify-content-center">
        <!-- Columna Datos del Perfil y Foto -->
        <div class="col-12 col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white" style="border: 1px solid #f6dfe2 !important;">
                
                <!-- 1. Formulario Exclusivo de Foto (Auto-Submit al Elegir Archivo) -->
                <form method="POST" enctype="multipart/form-data" id="formFotoPerfilDirecto">
                    <div class="position-relative d-inline-block mx-auto mb-2">
                        <?php if ($tieneFotoReal): ?>
                            <img src="<?= htmlspecialchars($usuario["foto"]) ?>" class="rounded-circle border shadow-xs" style="width: 110px; height: 110px; object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary-subtle text-primary fw-bold shadow-xs mx-auto" style="width: 110px; height: 110px; font-size: 2.1rem; letter-spacing: 1px;">
                                <?= $iniciales ?>
                            </div>
                        <?php endif; ?>

                        <!-- Botón Cámara que dispara la subida automática al seleccionar archivo -->
                        <label for="inputFotoPerfilDirecto" class="btn btn-sm btn-primary rounded-circle position-absolute bottom-0 end-0 shadow-sm d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; cursor: pointer;" title="Cambiar foto">
                            <i class="bi bi-camera-fill text-white" style="font-size: 0.9rem;"></i>
                        </label>
                        <input type="file" name="fotoPerfil" id="inputFotoPerfilDirecto" class="d-none" accept="image/jpeg,image/png,image/webp" onchange="document.getElementById('formFotoPerfilDirecto').submit();">
                    </div>
                </form>

                <!-- 2. Formulario de Eliminación de Foto con SweetAlert2 -->
                <?php if ($tieneFotoReal): ?>
                    <form method="POST" id="formEliminarFotoPerfil" class="mb-3">
                        <input type="hidden" name="btnEliminarFoto" value="1">
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 shadow-xs" onclick="confirmarEliminarFoto()">
                            <i class="bi bi-trash3 me-1"></i> Eliminar Foto de Perfil
                        </button>
                    </form>
                <?php endif; ?>

                <h4 class="font-playfair fw-bold text-dark mb-0"><?= htmlspecialchars($usuario["nombre"]) ?></h4>
                <p class="text-muted small mb-4"><?= htmlspecialchars($usuario["email"]) ?></p>

                <!-- 3. Formulario de Datos Personales (Nombre, Correo, Teléfono) -->
                <form method="POST">
                    <div class="mb-3 text-start">
                        <label class="form-label small fw-semibold">Nombre Completo</label>
                        <input type="text" name="perfilNombre" class="form-control" value="<?= htmlspecialchars($usuario["nombre"]) ?>" required>
                    </div>

                    <div class="mb-3 text-start">
                        <label class="form-label small fw-semibold">Correo Electrónico</label>
                        <input type="email" name="perfilEmail" class="form-control" value="<?= htmlspecialchars($usuario["email"]) ?>" required>
                    </div>

                    <div class="mb-4 text-start">
                        <label class="form-label small fw-semibold">Teléfono / WhatsApp</label>
                        <input type="text" name="perfilTelefono" class="form-control" value="<?= htmlspecialchars($usuario["telefono"] ?? '') ?>" required>
                    </div>

                    <button type="submit" name="btnActualizarDatos" value="1" class="btn-primary-custom w-100 py-2 shadow-sm">
                        Guardar Información
                    </button>
                </form>

                <div class="mt-3">
                    <a href="index.php?ruta=terminos" class="small text-muted text-decoration-none">
                        <i class="bi bi-shield-check me-1"></i> Consultar Términos & Condiciones
                    </a>
                </div>

            </div>
        </div>

        <!-- Columna Actualizar Contraseña -->
        <div class="col-12 col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white" style="border: 1px solid #f6dfe2 !important;">
                <h5 class="font-playfair fw-bold color-primary-dark mb-3">
                    <i class="bi bi-shield-lock me-1"></i> Actualizar Contraseña
                </h5>

                <form method="POST">
                    <input type="hidden" name="btnCambiarPass" value="1">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Contraseña Actual</label>
                        <div class="input-group">
                            <input type="password" name="passActual" id="inputPassActual" class="form-control" required>
                            <button type="button" class="input-group-text bg-light" onclick="alternarVisibilidadPass('inputPassActual', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nueva Contraseña (Mínimo 6 caracteres)</label>
                        <div class="input-group">
                            <input type="password" name="passNueva" id="inputPassNueva" minlength="6" class="form-control" required>
                            <button type="button" class="input-group-text bg-light" onclick="alternarVisibilidadPass('inputPassNueva', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary-custom w-100 py-2">
                        Guardar Nueva Contraseña
                    </button>
                </form>

                <?php $ctrl->ctrCambiarPasswordPerfil(); ?>
            </div>
        </div>
    </div>
</div>

<script>
// Confirmación visual con SweetAlert2 para eliminar la foto
function confirmarEliminarFoto() {
    Swal.fire({
        title: '¿Eliminar foto de perfil?',
        text: "Tu avatar volverá a mostrar tus iniciales principales.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d48b94',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formEliminarFotoPerfil').submit();
        }
    });
}

function alternarVisibilidadPass(idInput, btn) {
    const input = document.getElementById(idInput);
    const icono = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icono.classList.remove('bi-eye');
        icono.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icono.classList.remove('bi-eye-slash');
        icono.classList.add('bi-eye');
    }
}
</script>