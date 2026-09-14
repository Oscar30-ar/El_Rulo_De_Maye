<?php
$login = new ControladorUsuarios();
?>
<div class="auth-card mx-auto">
    <!-- Muñeca con cabello rizado (Crespos) -->
    <div class="beauty-mascot-wrapper" id="mascotWrapper">
        <svg class="beauty-mascot-svg" viewBox="0 0 160 160" width="140" height="140">
            <!-- Melena posterior rizada (Crespos de Maye) -->
            <g class="curly-hair">
                <circle cx="46" cy="55" r="16" />
                <circle cx="34" cy="72" r="15" />
                <circle cx="32" cy="92" r="15" />
                <circle cx="42" cy="110" r="14" />
                <circle cx="114" cy="55" r="16" />
                <circle cx="126" cy="72" r="15" />
                <circle cx="128" cy="92" r="15" />
                <circle cx="118" cy="110" r="14" />
                <circle cx="80" cy="40" r="22" />
                <circle cx="60" cy="44" r="18" />
                <circle cx="100" cy="44" r="18" />
            </g>

            <!-- Rostro -->
            <circle cx="80" cy="84" r="38" fill="#fadcd9" />

            <!-- Capul rizado / Flequillo crespo -->
            <g class="curly-hair">
                <circle cx="55" cy="58" r="11" />
                <circle cx="72" cy="54" r="12" />
                <circle cx="88" cy="54" r="12" />
                <circle cx="105" cy="58" r="11" />
                <!-- Detalle flor decorativa en el rulo -->
                <circle cx="114" cy="50" r="8" fill="#d48b94" />
                <circle cx="114" cy="50" r="3" fill="#fff" />
            </g>

            <!-- Ojos -->
            <ellipse cx="67" cy="84" rx="4" ry="4.5" fill="#3c3234" />
            <ellipse cx="93" cy="84" rx="4" ry="4.5" fill="#3c3234" />
            <circle cx="68.5" cy="82.5" r="1.5" fill="#ffffff" />
            <circle cx="94.5" cy="82.5" r="1.5" fill="#ffffff" />

            <!-- Mejillas y Sonrisa -->
            <ellipse cx="60" cy="92" rx="6" ry="3.5" fill="#f48fb1" opacity="0.6" />
            <ellipse cx="100" cy="92" rx="6" ry="3.5" fill="#f48fb1" opacity="0.6" />
            <path d="M74 97 Q80 103 86 97" stroke="#b86b75" stroke-width="2.2" fill="none" stroke-linecap="round" />

            <!-- Manos que suben a tapar los ojos -->
            <g class="hand-left" id="handLeft">
                <rect x="42" y="148" width="22" height="36" rx="11" fill="#fadcd9" stroke="#d48b94" stroke-width="2" />
            </g>
            <g class="hand-right" id="handRight">
                <rect x="96" y="148" width="22" height="36" rx="11" fill="#fadcd9" stroke="#d48b94" stroke-width="2" />
            </g>
        </svg>
    </div>

    <h2 class="auth-title text-center">¡Bienvenida!</h2>
    <p class="text-center text-muted mb-4 small">Ingresa tus credenciales</p>

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
                <input type="password" id="passInputLogin" name="ingPassword" class="form-control border-start-0 border-end-0" placeholder="••••••••" required>
                <button type="button" class="input-group-text bg-light border-start-0 btn-toggle-eye" onclick="alternarVisibilidadPass(this)">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-primary-custom w-100 py-2 mt-2">Iniciar Sesión</button>
    </form>
</div>