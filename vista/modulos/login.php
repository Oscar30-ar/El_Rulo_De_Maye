<?php
$login = new ControladorUsuarios();
?>
<div class="container my-5">
    <div class="auth-card mx-auto">
        <!-- Muñeca con Crespos y Animación -->
        <div class="beauty-mascot-wrapper" id="mascotWrapper">
            <svg class="beauty-mascot-svg" viewBox="0 0 160 160" width="140" height="140">
                <!-- Melena Crespa Trasera -->
                <g class="curly-hair">
                    <circle cx="46" cy="55" r="16" />
                    <circle cx="34" cy="72" r="15" />
                    <circle cx="32" cy="92" r="15" />
                    <circle cx="42" cy="110" r="14" />
                    <circle cx="114" cy="55" r="16" />
                    <circle cx="126" cy="72" r="15" />
                    <circle cx="128" cy="92" r="15" />
                    <circle cx="118" cy="110" r="14" />
                    <circle cx="80" cy="38" r="22" />
                    <circle cx="60" cy="42" r="18" />
                    <circle cx="100" cy="42" r="18" />
                </g>

                <!-- Rostro y Orejas -->
                <circle cx="44" cy="84" r="8" fill="#fadcd9" />
                <circle cx="116" cy="84" r="8" fill="#fadcd9" />
                <circle cx="80" cy="84" r="38" fill="#fadcd9" />

                <!-- Capul y Rizos Delanteros con Flor de Maye -->
                <g class="curly-hair">
                    <circle cx="55" cy="58" r="11" />
                    <circle cx="72" cy="54" r="12" />
                    <circle cx="88" cy="54" r="12" />
                    <circle cx="105" cy="58" r="11" />
                    <circle cx="114" cy="50" r="8" fill="#d48b94" />
                    <circle cx="114" cy="50" r="3" fill="#ffffff" />
                </g>

                <!-- Ojos Abiertos (Visibles por defecto) -->
                <g class="eyes-open">
                    <ellipse cx="67" cy="84" rx="4" ry="4.5" fill="#3c3234" />
                    <ellipse cx="93" cy="84" rx="4" ry="4.5" fill="#3c3234" />
                    <circle cx="68.5" cy="82.5" r="1.5" fill="#ffffff" />
                    <circle cx="94.5" cy="82.5" r="1.5" fill="#ffffff" />
                </g>

                <!-- Ojos Cerrados / Guiño (Aparecen al taparse) -->
                <g class="eyes-closed">
                    <path d="M 62 85 Q 67 80 72 85" stroke="#3c3234" stroke-width="2.5" fill="none" stroke-linecap="round" />
                    <path d="M 88 85 Q 93 80 98 85" stroke="#3c3234" stroke-width="2.5" fill="none" stroke-linecap="round" />
                </g>

                <!-- Rubor y Sonrisa -->
                <ellipse cx="60" cy="93" rx="6" ry="3.5" fill="#f48fb1" opacity="0.6" />
                <ellipse cx="100" cy="93" rx="6" ry="3.5" fill="#f48fb1" opacity="0.6" />
                <path d="M 75 97 Q 80 102 85 97" stroke="#b86b75" stroke-width="2.2" fill="none" stroke-linecap="round" />

                <!-- Manitas que van a los ojos -->
                <g class="mascot-hand hand-left">
                    <ellipse cx="40" cy="155" rx="14" ry="11" fill="#fadcd9" stroke="#d48b94" stroke-width="1.8" />
                    <!-- Deditos con uña pintada -->
                    <ellipse cx="33" cy="148" rx="4" ry="4" fill="#d48b94" />
                    <ellipse cx="40" cy="146" rx="4" ry="4" fill="#d48b94" />
                    <ellipse cx="47" cy="148" rx="4" ry="4" fill="#d48b94" />
                </g>

                <g class="mascot-hand hand-right">
                    <ellipse cx="120" cy="155" rx="14" ry="11" fill="#fadcd9" stroke="#d48b94" stroke-width="1.8" />
                    <!-- Deditos con uña pintada -->
                    <ellipse cx="113" cy="148" rx="4" ry="4" fill="#d48b94" />
                    <ellipse cx="120" cy="146" rx="4" ry="4" fill="#d48b94" />
                    <ellipse cx="127" cy="148" rx="4" ry="4" fill="#d48b94" />
                </g>
            </svg>
        </div>

        <h2 class="auth-title text-center">¡Bienvenida!</h2>
        <p class="text-center text-muted mb-4 small">Ingresa tus credenciales para continuar</p>

        <?php $login->ctrIngresoUsuario(); ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold small">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                    <input type="email" name="ingEmail" class="form-control border-start-0" placeholder="correo@ejemplo.com" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-lock text-muted"></i></span>
                    <input type="password" name="ingPassword" class="form-control border-start-0 border-end-0" placeholder="••••••••" required>
                    <button type="button" class="input-group-text bg-light border-start-0" onclick="alternarVisibilidadPass(this)">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-primary-custom w-100 py-2 mt-2">Iniciar Sesión</button>
        </form>

        <div class="d-flex justify-content-between align-items-center mt-4 small">
            <a href="index.php?ruta=recuperar" class="text-muted text-decoration-none">¿Olvidaste tu contraseña?</a>
            <a href="index.php?ruta=registro" class="color-primary fw-bold text-decoration-none">Crear Cuenta</a>
        </div>
    </div>
</div>