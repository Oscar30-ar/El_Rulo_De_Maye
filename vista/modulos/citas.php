<?php
if (!isset($_SESSION["iniciarSesion"])) {
    echo '<script>window.location = "index.php?ruta=login";</script>';
    exit();
}
$servicios = UsuarioModelo::mdlObtenerServicios();
$misCitas = UsuarioModelo::mdlListarCitas($_SESSION["id"]);
$crearCita = new ControladorUsuarios();
$alertaCita = $crearCita->ctrNuevaCita();
?>

<div class="section" style="padding-top: 20px;">
    <div class="grid-citas">
        <div class="auth-box" style="margin: 0; width: 100%; max-width: 100%;">
            <h2 style="font-family:'Playfair Display', serif; margin-bottom: 15px;">Reserva tu Cita</h2>

            <!-- 9. Alerta Visual Personalizada -->
            <?php if (!empty($alertaCita)): ?>
                <?= $alertaCita ?>
            <?php endif; ?>

            <form method="POST">
                <div style="margin-bottom:14px;">
                    <label style="font-size:0.85rem; font-weight:600;">Servicio</label>
                    <select name="agendarServicio" class="form-control" required>
                        <option value="">Selecciona un servicio...</option>
                        <?php foreach ($servicios as $srv): ?>
                            <?php if ($srv["categoria_estado"] === "activo"): ?>
                                <option value="<?= $srv["id"] ?>" <?= (isset($_GET["servicio"]) && $_GET["servicio"] == $srv["id"]) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($srv["nombre"]) ?> - $<?= number_format($srv["precio"], 0, ',', '.') ?> COP
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="margin-bottom:14px;">
                    <label style="font-size:0.85rem; font-weight:600;">Fecha Deseada</label>
                    <input type="date" name="agendarFecha" min="<?= date('Y-m-d') ?>" class="form-control" required>
                </div>

                <div style="margin-bottom:14px;">
                    <label style="font-size:0.85rem; font-weight:600;">Hora</label>
                    <select name="agendarHora" class="form-control" required>
                        <option value="08:00:00">08:00 AM</option>
                        <option value="09:30:00">09:30 AM</option>
                        <option value="11:00:00">11:00 AM</option>
                        <option value="01:00:00">01:00 PM</option>
                        <option value="02:30:00">02:30 PM</option>
                        <option value="04:00:00">04:00 PM</option>
                        <option value="05:30:00">05:30 PM</option>
                    </select>
                </div>

                <div style="margin-bottom:14px;">
                    <label style="font-size:0.85rem; font-weight:600;">Forma de Pago</label>
                    <select name="metodoPago" class="form-control" required>
                        <option value="efectivo">Pagar en el Salón (Efectivo / Transferencia)</option>
                        <option value="wompi">Wompi / PSE / Tarjetas</option>
                        <option value="mercadopago">Mercado Pago</option>
                    </select>
                </div>

                <div style="margin-bottom:14px;">
                    <label style="font-size:0.85rem; font-weight:600;">Notas o especificaciones</label>
                    <textarea name="agendarNotas" class="form-control" rows="2" placeholder="Técnica deseada, uña dañada, decoración especial..."></textarea>
                </div>

                <button type="submit" class="btn-primary" style="width:100%;">Confirmar Cita</button>
            </form>
        </div>

        <!-- Tabla Responsive de Citas del Usuario -->
        <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white">
            <h4 class="font-playfair mb-3">Tus Citas Programadas</h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Servicio</th>
                            <th>Fecha & Hora</th>
                            <th>Estado Cita</th>
                            <th>Pago</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($misCitas)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No tienes citas agendadas actualmente.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($misCitas as $c): ?>
                                <tr>
                                    <td>
                                        <span class="fw-bold text-dark d-block"><?= htmlspecialchars($c["servicio"]) ?></span>
                                        <small class="text-muted">$<?= number_format($c["precio"], 0, ',', '.') ?> COP</small>
                                    </td>
                                    <td>
                                        <i class="bi bi-calendar-event me-1 text-muted"></i><?= $c["fecha"] ?><br>
                                        <i class="bi bi-clock me-1 text-muted"></i><small><?= substr($c["hora"], 0, 5) ?></small>
                                    </td>
                                    <td>
                                        <?php if ($c['estado'] === 'confirmada'): ?>
                                            <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-2">Confirmada</span>
                                        <?php elseif ($c['estado'] === 'cancelada'): ?>
                                            <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">Cancelada</span>
                                        <?php else: ?>
                                            <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2">Pendiente</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-credit-card me-1"></i><?= ucfirst($c["pago_estado"]) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="https://wa.me/573001234567?text=Hola%20Maye,%20tengo%20una%20duda%20sobre%20mi%20cita%20del%20<?= $c['fecha'] ?>"
                                            target="_blank"
                                            class="btn btn-sm btn-outline-success rounded-pill px-3">
                                            <i class="bi bi-whatsapp"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="mb-3">
    <label class="form-label fw-semibold small">Fecha Deseada</label>
    <input type="date" id="inputFechaCita" name="agendarFecha" min="<?= date('Y-m-d') ?>" class="form-control" required onchange="cargarHorasMaye(this.value)">
</div>

<div class="mb-3">
    <label class="form-label fw-semibold small">Hora de Atención (Según Horario de Maye)</label>
    <select name="agendarHora" id="selectHoraCita" class="form-control" required>
        <option value="">Selecciona primero una fecha...</option>
    </select>
    <div id="alertaHorarioMaye" class="mt-2 text-danger small"></div>
</div>

<script>
function cargarHorasMaye(fecha) {
    const select = document.getElementById('selectHoraCita');
    const alerta = document.getElementById('alertaHorarioMaye');
    select.innerHTML = '<option value="">Cargando horarios de Maye...</option>';
    alerta.textContent = '';

    fetch(`ajax/horariosDisponibles.php?fecha=${fecha}`)
        .then(res => res.json())
        .then(data => {
            select.innerHTML = '';
            if (data.status === 'cerrado') {
                select.innerHTML = '<option value="">Día no disponible</option>';
                alerta.textContent = data.mensaje;
                return;
            }

            let opcionesDisponibles = 0;
            data.horas.forEach(h => {
                const opt = document.createElement('option');
                opt.value = h.valor;
                if (h.ocupado) {
                    opt.textContent = `${h.texto} (Ocupado)`;
                    opt.disabled = true;
                    opt.style.color = '#dc3545';
                } else {
                    opt.textContent = h.texto;
                    opcionesDisponibles++;
                }
                select.appendChild(opt);
            });

            if (opcionesDisponibles === 0) {
                alerta.textContent = 'No hay turnos libres para esta fecha. Elige otro día.';
            }
        });
}
</script>