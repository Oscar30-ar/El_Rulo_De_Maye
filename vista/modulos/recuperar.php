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
    <div class="auth-card mx-auto">
        <h2 class="auth-title text-center mb-3">Recuperar Contraseña</h2>
        
        <?php if ($paso === 1): ?>
            <p class="text-muted text-center small mb-4">Ingresa tu correo registrado y te enviaremos un código de 6 dígitos válido por 10 minutos.</p>
            <form method="POST">
                <input type="hidden" name="accionRecuperar" value="enviarCodigo">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Correo Electrónico</label>
                    <input type="email" name="recEmail" class="form-control" placeholder="correo@ejemplo.com" required>
                </div>
                <button type="submit" class="btn-primary-custom w-100 py-2">Enviar Código</button>
            </form>
        <?php else: ?>
            <div class="alert alert-info custom-alert small">
                Hemos enviado un código de 6 dígitos a <strong><?= htmlspecialchars($_SESSION["email_recuperacion"] ?? '') ?></strong>.
            </div>
            <form method="POST">
                <input type="hidden" name="accionRecuperar" value="validarYCambiar">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Código de 6 Dígitos</label>
                    <input type="text" name="codigo6" maxlength="6" class="form-control text-center fs-4 fw-bold" placeholder="123456" style="letter-spacing: 5px;" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nueva Contraseña</label>
                    <div class="input-group">
                        <input type="password" name="nuevaPassword" minlength="6" class="form-control" placeholder="••••••••" required>
                        <button type="button" class="input-group-text bg-light" onclick="alternarVisibilidadPass(this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn-primary-custom w-100 py-2">Restablecer Contraseña</button>
            </form>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="index.php?ruta=login" class="small text-muted text-decoration-none">Volver al Inicio de Sesión</a>
        </div>
    </div>
</div>