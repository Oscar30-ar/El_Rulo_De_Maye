<?php
$registro = new ControladorUsuarios();
?>
<div class="auth-box">
    <h2 style="font-family:'Playfair Display', serif; text-align:center; margin-bottom: 20px;">Crear Cuenta</h2>
    <?php $registro->ctrRegistroUsuario(); ?>

    <form method="POST" id="formRegistro">
        <div style="margin-bottom: 14px;">
            <label style="font-size: 0.85rem; font-weight:600;">Nombre Completo</label>
            <input type="text" name="regNombre" class="form-control" required placeholder="Tu nombre">
        </div>

        <div style="margin-bottom: 14px;">
            <label style="font-size: 0.85rem; font-weight:600;">Teléfono / WhatsApp</label>
            <input type="tel" name="regTelefono" class="form-control" required placeholder="3001234567">
        </div>

        <div style="margin-bottom: 14px;">
            <label style="font-size: 0.85rem; font-weight:600;">Correo Electrónico</label>
            <input type="email" name="regEmail" class="form-control" required placeholder="tucorreo@ejemplo.com">
        </div>

        <div style="margin-bottom: 14px;">
            <label style="font-size: 0.85rem; font-weight:600;">Contraseña (Mínimo 6 caracteres)</label>
            <div class="input-password-wrapper">
                <input type="password" name="regPassword" minlength="6" class="form-control pwd-field" required placeholder="••••••••">
                <button type="button" class="btn-toggle-eye">👁️</button>
            </div>
        </div>

        <!-- 5.4. Aceptar Términos y Condiciones Obligatorio -->
        <div class="terms-container mb-3">
            <input type="checkbox" id="checkTerminos" name="checkTerminos" required>
            <label for="checkTerminos" class="small text-muted ms-2">
                Acepto los <a href="index.php?ruta=terminos" target="_blank" class="fw-bold color-primary">Términos y Condiciones</a> y la Política de Privacidad.
            </label>
        </div>

        <button type="submit" class="btn-primary" style="width:100%; margin-top:8px;">Registrarme</button>
    </form>
    <p style="text-align:center; font-size:0.85rem; margin-top:16px;">
        ¿Ya tienes cuenta? <a href="index.php?ruta=login" style="color:var(--primary); font-weight:600;">Inicia sesión</a>
    </p>
</div>