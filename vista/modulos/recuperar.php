<?php
$ctrl = new ControladorUsuarios();
$paso = isset($_SESSION["email_recuperacion"]) ? 2 : 1;

if (isset($_POST["accionRecuperar"]) && $_POST["accionRecuperar"] === "enviarCodigo") {
    $ctrl->ctrSolicitarCodigoRecuperacion();
    $paso = 2;
}

if (isset($_POST["accionRecuperar"]) && $_POST["accionRecuperar"] === "validarYCambiar") {
    $ctrl->ctrCambiarPasswordConCodigo();
}
?>

<div class="auth-box">
    <h2 style="font-family:'Playfair Display', serif; text-align:center; margin-bottom: 15px;">Recuperar Contraseña</h2>
    
    <?php if ($paso === 1): ?>
        <p style="font-size:0.85rem; color:var(--text-light); text-align:center; margin-bottom: 20px;">
            Ingresa tu correo registrado y te enviaremos un código de 6 dígitos válido por 10 minutos.
        </p>
        <form method="POST">
            <input type="hidden" name="accionRecuperar" value="enviarCodigo">
            <div style="margin-bottom: 16px;">
                <label style="font-size:0.85rem; font-weight:600;">Correo Electrónico</label>
                <input type="email" name="recEmail" class="form-control" required placeholder="correo@ejemplo.com">
            </div>
            <button type="submit" class="btn-primary" style="width:100%;">Enviar Código</button>
        </form>
    <?php else: ?>
        <div class="custom-alert info">
            Código enviado a <strong><?= htmlspecialchars($_SESSION["email_recuperacion"] ?? '') ?></strong>.
        </div>
        <form method="POST">
            <input type="hidden" name="accionRecuperar" value="validarYCambiar">
            <div style="margin-bottom: 14px;">
                <label style="font-size:0.85rem; font-weight:600;">Código de 6 Dígitos</label>
                <input type="text" name="codigo6" maxlength="6" class="form-control" required placeholder="123456" style="letter-spacing:4px; font-size:1.2rem; text-align:center;">
            </div>
            <div style="margin-bottom: 14px;">
                <label style="font-size:0.85rem; font-weight:600;">Nueva Contraseña</label>
                <div class="input-password-wrapper">
                    <input type="password" name="nuevaPassword" minlength="6" class="form-control pwd-field" required placeholder="••••••••">
                    <button type="button" class="btn-toggle-eye">👁️</button>
                </div>
            </div>
            <button type="submit" class="btn-primary" style="width:100%;">Actualizar Contraseña</button>
        </form>
    <?php endif; ?>
    
    <div style="text-align:center; margin-top:20px;">
        <a href="index.php?ruta=login" style="font-size:0.85rem; color:var(--primary-dark); text-decoration:none;">Volver al inicio de sesión</a>
    </div>
</div>