<?php
if (!isset($_SESSION["iniciarSesion"])) {
    echo '<script>window.location="index.php?ruta=login";</script>';
    exit();
}

$ctrl = new ControladorUsuarios();
$ctrl->ctrActualizarPerfil();
$ctrl->ctrCambiarPasswordPerfil();
$usuario = UsuarioModelo::mdlMostrarUsuario("id", $_SESSION["id"]);

// Extraer Iniciales
$palabras = explode(' ', trim($usuario['nombre']));
$iniciales = strtoupper(substr($palabras[0], 0, 1) . (isset($palabras[1]) ? substr($palabras[1], 0, 1) : ''));
?>

<div class="container my-5">
    <div class="row g-4">
        <!-- Columna Izquierda: Datos y Foto -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white">
                <form method="POST" enctype="multipart/form-data">
                    <div class="profile-avatar-container mx-auto position-relative mb-3">
                        <?php if (!empty($usuario['foto']) && file_exists($usuario['foto'])): ?>
                            <img src="<?= htmlspecialchars($usuario['foto']) ?>" class="profile-img" alt="Perfil">
                        <?php else: ?>
                            <div class="profile-initials"><?= $iniciales ?></div>
                        <?php endif; ?>
                        
                        <!-- Botón flotante para subir foto -->
                        <label for="inputFoto" class="btn-edit-photo" title="Cambiar foto">
                            <i class="bi bi-camera-fill"></i>
                        </label>
                        <input type="file" id="inputFoto" name="fotoPerfil" accept="image/*" class="d-none" onchange="this.form.submit()">
                    </div>

                    <h4 class="fw-bold mb-1"><?= htmlspecialchars($usuario['nombre']) ?></h4>
                    <p class="text-muted small mb-4"><?= htmlspecialchars($usuario['email']) ?></p>

                    <div class="text-start mb-3">
                        <label class="form-label small fw-semibold">Nombre Completo</label>
                        <input type="text" name="perfilNombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                    </div>

                    <div class="text-start mb-4">
                        <label class="form-label small fw-semibold">Teléfono / WhatsApp</label>
                        <input type="text" name="perfilTelefono" class="form-control" value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>" required>
                    </div>

                    <button type="submit" name="btnActualizarDatos" class="btn-primary-custom w-100">Guardar Información</button>
                </form>

                <hr class="my-4 text-muted">
                <div class="text-start">
                    <a href="index.php?ruta=terminos" class="text-decoration-none text-muted small d-flex align-items-center gap-2">
                        <i class="bi bi-shield-check color-primary fs-5"></i> Consultar Términos & Condiciones
                    </a>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Cambio de Contraseña -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h5 class="fw-bold mb-3"><i class="bi bi-key me-2 color-primary"></i>Seguridad y Contraseña</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Contraseña Actual</label>
                        <div class="input-group">
                            <input type="password" name="passActual" class="form-control pwd-input" required>
                            <button type="button" class="input-group-text bg-light toggle-pass-btn"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nueva Contraseña (Mínimo 6 caracteres)</label>
                        <div class="input-group">
                            <input type="password" name="passNueva" minlength="6" class="form-control pwd-input" required>
                            <button type="button" class="input-group-text bg-light toggle-pass-btn"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>

                    <button type="submit" name="btnCambiarPass" class="btn-primary-custom px-4 py-2 mt-2">Actualizar Contraseña</button>
                </form>
            </div>
        </div>
    </div>
</div>