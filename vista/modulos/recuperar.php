<?php
$ctrl = new ControladorUsuarios();
$paso = isset($_SESSION["email_recuperacion"]) ? 2 : 1;

if (isset($_POST["accionRecuperar"]) && $_POST["accionRecuperar"] === "enviarCodigo") {
    $ctrl->ctrSolicitarCodigoRecuperacion();
    if (isset($_SESSION["email_recuperacion"])) {
        $paso = 2;
    }
}

if (isset($_POST["accionRecuperar"]) && $_POST["accionRecuperar"] === "validarYCambiar") {
    $ctrl->ctrCambiarPasswordConCodigo();
}
?>

<div class="container my-5">
    <div class="auth-card mx-auto shadow-sm p-4 p-md-5 rounded-4 bg-white" style="max-width: 440px; border: 1px solid #f6dfe2;">
        
        <div class="text-center mb-4">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 mb-2 small fw-bold text-uppercase">
                Seguridad Maye
            </span>
            <h2 class="auth-title font-playfair color-primary-dark fw-bold mb-1">Recuperar Contraseña</h2>
        </div>
        
        <?php if ($paso === 1): ?>
            <p class="text-muted text-center small mb-4">Ingresa tu correo registrado y te enviaremos un código de 6 dígitos válido por 10 minutos.</p>
            
            <form method="POST">
                <input type="hidden" name="accionRecuperar" value="enviarCodigo">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Correo Electrónico</label>
                    <input type="email" name="recEmail" class="form-control" placeholder="correo@ejemplo.com" required autofocus>
                </div>
                <button type="submit" class="btn-primary-custom w-100 py-2">
                    <i class="bi bi-send me-1"></i> Enviar Código
                </button>
            </form>
        <?php else: ?>
            <div class="alert alert-info custom-alert small d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-envelope-check-fill fs-5"></i>
                <span>Código enviado a: <strong><?= htmlspecialchars($_SESSION["email_recuperacion"] ?? '') ?></strong></span>
            </div>

            <form method="POST">
                <input type="hidden" name="accionRecuperar" value="validarYCambiar">
                
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Código de 6 Dígitos</label>
                    <input type="text" name="codigo6" maxlength="6" class="form-control text-center fs-4 fw-bold" placeholder="123456" style="letter-spacing: 5px;" required autofocus>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nueva Contraseña</label>
                    <div class="input-group">
                        <input type="password" name="nuevaPassword" id="inputPassNueva" minlength="6" class="form-control" placeholder="Mínimo 6 caracteres" required>
                        <button type="button" class="input-group-text bg-light" onclick="alternarVisibilidadPass(this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-primary-custom w-100 py-2">
                    <i class="bi bi-check2-circle me-1"></i> Restablecer Contraseña
                </button>
            </form>

            <div class="text-center mt-3">
                <form method="POST">
                    <button type="submit" name="cambiarCorreo" class="btn btn-link small text-muted text-decoration-none p-0">
                        <i class="bi bi-arrow-left me-1"></i> Probar con otro correo
                    </button>
                </form>
                <?php
                if (isset($_POST["cambiarCorreo"])) {
                    unset($_SESSION["email_recuperacion"]);
                    echo '<script>window.location = "index.php?ruta=recuperar";</script>';
                }
                ?>
            </div>
        <?php endif; ?>

        <div class="text-center mt-4 pt-3 border-top">
            <a href="index.php?ruta=login" class="small text-secondary text-decoration-none fw-semibold">
                Volver al Inicio de Sesión
            </a>
        </div>
    </div>
</div>

<script>
function alternarVisibilidadPass(btn) {
    const input = document.getElementById('inputPassNueva');
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