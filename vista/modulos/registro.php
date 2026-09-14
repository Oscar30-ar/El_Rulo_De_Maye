<?php
$registro = new ControladorUsuarios();
?>
<div class="container my-5">
    <div class="auth-card mx-auto">
        <h2 class="auth-title text-center mb-3">Crea tu Cuenta</h2>
        <p class="text-center text-muted mb-4 small">Únete a El Rulo De Maye y agenda tus citas fácilmente</p>

        <?php $registro->ctrRegistroUsuario(); ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold small">Nombre Completo</label>
                <input type="text" name="regNombre" class="form-control" placeholder="Tu nombre" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">Teléfono / WhatsApp</label>
                <input type="tel" name="regTelefono" class="form-control" placeholder="3001234567" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">Correo Electrónico</label>
                <input type="email" name="regEmail" class="form-control" placeholder="correo@ejemplo.com" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">Contraseña (Mínimo 6 caracteres)</label>
                <div class="input-group">
                    <input type="password" name="regPassword" minlength="6" class="form-control" placeholder="••••••••" required>
                    <button type="button" class="input-group-text bg-light" onclick="alternarVisibilidadPass(this)">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Checkbox que abre el Modal Card sin cambiar de pestaña -->
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="checkTerminos" name="checkTerminos" required>
                <label class="form-check-label small text-muted" for="checkTerminos">
                    Acepto los <a href="#" class="fw-bold color-primary text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalTerminos">Términos y Condiciones</a> y la Política de Tratamiento de Datos.
                </label>
            </div>

            <button type="submit" class="btn-primary-custom w-100 py-2">Registrarme</button>
        </form>

        <p class="text-center small text-muted mt-4">
            ¿Ya tienes cuenta? <a href="index.php?ruta=login" class="color-primary fw-bold text-decoration-none">Inicia sesión</a>
        </p>
    </div>
</div>

<!-- MODAL CARD: Términos y Condiciones -->
<div class="modal fade" id="modalTerminos" tabindex="-1" aria-labelledby="modalTerminosLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title font-playfair color-primary-dark fw-bold" id="modalTerminosLabel">
                    🌸 Términos, Condiciones & Privacidad
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-secondary small lh-lg px-4">
                <h6 class="fw-bold text-dark mt-2">1. Política de Citas y Asistencia</h6>
                <p>Cada reserva asegura un tiempo exclusivo de atención con Maye. Contamos con una tolerancia de espera máxima de 15 minutos. Si requieres cancelar o reprogramar, te solicitamos hacerlo con al menos 3 horas de antelación.</p>

                <h6 class="fw-bold text-dark mt-3">2. Garantía del Servicio</h6>
                <p>Nuestros trabajos en Esmaltado Semipermanente, Rubber, Capping y Extensiones (Soft Gel, Poligel y Acrílico) ofrecen 5 días hábiles de garantía sobre desprendimientos involuntarios del material.</p>

                <h6 class="fw-bold text-dark mt-3">3. Tratamiento de Datos Personales</h6>
                <p>Tus datos (nombre, teléfono y correo) se almacenan de forma segura y se usan exclusivamente para el envío de recordatorios de citas por WhatsApp y comprobantes de reservas. No transferimos tu información a terceros.</p>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn-primary-custom px-4 py-1" data-bs-dismiss="modal" onclick="document.getElementById('checkTerminos').checked = true;">
                    Entendido y Aceptar
                </button>
            </div>
        </div>
    </div>
</div>