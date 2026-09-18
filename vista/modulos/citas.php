<?php
if (!isset($_SESSION["iniciarSesion"])) {
    echo '<script>window.location = "index.php?ruta=login";</script>';
    exit();
}
$servicios = UsuarioModelo::mdlObtenerServicios();
$misCitas = UsuarioModelo::mdlListarCitas($_SESSION["id"]);
$crearCita = new ControladorUsuarios();
$alertaCita = $crearCita->ctrNuevaCita();

// Fecha actual para inicializar el formulario
$fechaHoy = date('Y-m-d');
?>

<?php
if (!isset($_SESSION["iniciarSesion"])) {
    echo '<script>window.location = "index.php?ruta=login";</script>';
    exit();
}
$servicios = UsuarioModelo::mdlObtenerServicios();
$misCitas = UsuarioModelo::mdlListarCitas($_SESSION["id"]);
$crearCita = new ControladorUsuarios();
$alertaCita = $crearCita->ctrNuevaCita();

// Procesar reseña si fue enviada
$crearCita->ctrCrearResenaCliente();

$fechaHoy = date('Y-m-d');
?>

<div class="container my-4">
    <div class="row g-4">
        <!-- Formulario de Agendamiento -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h4 class="font-playfair mb-3">Reserva tu Turno</h4>

                <?php if (!empty($alertaCita)): ?>
                    <?= $alertaCita ?>
                <?php endif; ?>

                <form method="POST" id="formAgendarCita">

                    <!-- Selección de Servicio -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Servicio</label>
                        <select name="agendarServicio" id="selectServicioCita" class="form-select" required onchange="dispararCargaHoras()">
                            <option value="">Selecciona un servicio...</option>
                            <?php foreach ($servicios as $srv): ?>
                                <?php if ($srv["categoria_estado"] === "activo"): ?>
                                    <option value="<?= $srv["id"] ?>"
                                        data-nombre="<?= htmlspecialchars($srv["nombre"]) ?>"
                                        data-precio="<?= $srv["precio"] ?>"
                                        <?= (isset($_GET["servicio"]) && $_GET["servicio"] == $srv["id"]) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($srv["nombre"]) ?> (<?= $srv["duracion_minutos"] ?> min) - $<?= number_format($srv["precio"], 0, ',', '.') ?> COP
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Fecha -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Fecha Deseada</label>
                        <input type="date" id="inputFechaCita" name="agendarFecha" min="<?= $fechaHoy ?>" value="<?= $fechaHoy ?>" class="form-control" required onchange="dispararCargaHoras()">
                    </div>

                    <!-- Hora Disponible calculada dinámicamente -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Hora de Atención</label>
                        <select name="agendarHora" id="selectHoraCita" class="form-select" required>
                            <option value="">Selecciona primero servicio y fecha...</option>
                        </select>
                        <div id="alertaHorarioMaye" class="mt-2 text-danger small"></div>
                    </div>

                    <!-- Métodos de Pago 100% Sin Comisiones -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Forma de Pago</label>
                        <select name="metodoPago" id="selectMetodoPago" class="form-select" required onchange="alternarInfoTransferencia(this.value)">
                            <option value="nequi_daviplata">Transferencia Inmediata (Nequi / Daviplata / Bre-B) - $0 Comisión</option>
                            <option value="efectivo">Pagar en el Salón (Efectivo / Al finalizar)</option>
                        </select>
                    </div>

                    <!-- Tarjeta Informativa para Transferencia Directa -->
                    <div id="boxInfoTransferencia" class="p-3 mb-3 bg-light rounded-3 border border-success-subtle">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-success-subtle text-success border border-success-subtle">0% Comisión Intermediaria</span>
                        </div>
                        <p class="small text-secondary mb-1">Cuentas directas de Maye:</p>
                        <div class="fw-bold text-dark small">
                            <div>📱 Nequi / Daviplata: <span class="color-primary-dark">314 318 3150</span></div>
                            <div>⚡ Llave Bre-B / Transfiya: <span class="color-primary-dark">314 318 3150</span></div>
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size: 0.78rem;">Al confirmar, podrás enviar el comprobante directamente a su WhatsApp.</small>
                    </div>

                    <!-- Notas Adicionales -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Notas o Especificaciones</label>
                        <textarea name="agendarNotas" class="form-control" rows="2" placeholder="Ej: Traigo retiro de acrílico anterior, uña partida, diseño especial..."></textarea>
                    </div>

                    <!-- FOTO DE REFERENCIA / INSPIRACIÓN PINTEREST O INSTAGRAM -->
                    <div class="mb-3 text-start">
                        <label class="form-label small fw-semibold d-flex align-items-center justify-content-between">
                            <span><i class="bi bi-image text-primary me-1"></i> Foto de Referencia / Inspiración</span>
                            <span class="badge bg-light text-secondary border small">Opcional</span>
                        </label>
                        <input type="file" name="fotoReferencia" class="form-control form-control-sm" accept="image/*">
                        <small class="text-muted" style="font-size: 0.75rem;">
                            ¿Viste un diseño que te encantó en Instagram o Pinterest? Adjúntalo para que Maye lo prepare especialmente para ti.
                        </small>
                    </div>

                    <button type="submit" class="btn-primary-custom w-100 py-2">Confirmar Cita</button>
                </form>
            </div>
        </div>

        <!-- Tabla de Citas del Usuario con Paginador y Filtro -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <h4 class="font-playfair mb-0">Mis Citas</h4>

                    <!-- Buscador -->
                    <div class="position-relative" style="max-width: 220px;">
                        <input type="text" id="buscadorCliente" class="form-control form-control-sm ps-5 rounded-pill" placeholder="Filtrar citas...">
                        <i class="bi bi-search position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tablaMisCitas">
                        <thead class="table-light">
                            <tr>
                                <th>Servicio</th>
                                <th>Fecha & Hora</th>
                                <th>Estado</th>
                                <th>Pago</th>
                                <th>WhatsApp</th>
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
                                        <!-- Estado de la Cita -->
                                        <td>
                                            <?php if ($c['estado'] === 'confirmada'): ?>
                                                <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-2">Confirmada</span>
                                            <?php elseif ($c['estado'] === 'finalizada'): ?>
                                                <span class="badge rounded-pill bg-secondary-subtle text-secondary border px-3 py-2 d-block mb-1">Finalizada</span>
                                                <!-- Botón para calificar servicio terminado -->
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1"
                                                    style="font-size: 0.75rem;"
                                                    onclick="abrirModalResena('<?= $c['id'] ?>', '<?= htmlspecialchars($c['servicio'], ENT_QUOTES) ?>')">
                                                    <i class="bi bi-star-fill text-warning me-1"></i> Calificar
                                                </button>
                                            <?php elseif ($c['estado'] === 'cancelada'): ?>
                                                <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">Cancelada</span>
                                            <?php else: ?>
                                                <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2">Pendiente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                <i class="bi bi-wallet2 me-1"></i><?= ucfirst($c["pago_estado"]) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="https://wa.me/573143183150?text=Hola%20Maye,%20tengo%20una%20pregunta%20sobre%20mi%20cita%20del%20<?= $c['fecha'] ?>"
                                                target="_blank"
                                                class="btn btn-sm btn-outline-success rounded-pill px-3"
                                                title="Contactar a Maye">
                                                <i class="bi bi-whatsapp"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Contenedor del Paginador Inferior -->
                <div class="d-flex justify-content-end align-items-center pt-3 border-top mt-3" id="paginacionCliente">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Pago Directo (Nequi / Daviplata / Bre-B) -->
<div class="modal fade" id="modalPagoDirecto" tabindex="-1" aria-labelledby="modalPagoDirectoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title font-playfair color-primary-dark fw-bold" id="modalPagoDirectoLabel">
                    ✨ ¡Cita Apartada en El Rulo De Maye!
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center px-4">
                <p class="small text-muted mb-3">Para asegurar tu lugar sin intermediarios ni cobros extra, transfiere a Maye mediante cualquiera de estos canales:</p>

                <div class="p-3 bg-light rounded-4 text-start mb-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small text-muted">Servicio:</span>
                        <strong id="modalServicioNombre" class="text-dark small">-</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-muted">Total a transferir:</span>
                        <strong id="modalServicioPrecio" class="color-primary-dark fs-5">$0 COP</strong>
                    </div>
                    <hr class="my-2">
                    <div class="small fw-semibold text-secondary">
                        <div>📲 Nequi / Daviplata: <span class="text-dark fw-bold">314 318 3150</span></div>
                        <div>⚡ Llave Bre-B / Transfiya: <span class="text-dark fw-bold">314 318 3150</span></div>
                        <div>Titular: <span class="text-dark fw-bold">Maye</span></div>
                    </div>
                </div>

                <a id="btnEnviarComprobanteWA"
                    href="#"
                    target="_blank"
                    class="btn-primary-custom w-100 py-2 text-decoration-none d-inline-flex justify-content-center align-items-center gap-2">
                    <i class="bi bi-whatsapp fs-5"></i> Enviar Comprobante por WhatsApp
                </a>
            </div>
            <div class="modal-footer border-top-0 pt-0 justify-content-center pb-4">
                <button type="button" class="btn btn-sm btn-link text-muted text-decoration-none" data-bs-dismiss="modal">
                    Cerrar ventana
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Interactivo para Dejar Reseña -->
<div class="modal fade" id="modalDejarResena" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title font-playfair color-primary-dark fw-bold">
                    🌸 ¿Cómo fue tu experiencia en El Rulo De Maye?
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form method="POST">
                <div class="modal-body px-4 text-center">
                    <input type="hidden" name="btnEnviarResena" value="1">
                    <input type="hidden" name="citaIdResena" id="inputCitaIdResena">

                    <p class="small text-muted mb-2">Servicio recibido: <strong id="txtServicioResena" class="text-dark">-</strong></p>

                    <!-- Selector de 5 Estrellas -->
                    <label class="form-label small fw-semibold d-block text-secondary mb-1">Tu Calificación</label>
                    <div class="d-flex justify-content-center gap-2 mb-3 fs-3 text-warning" id="contenedorEstrellas">
                        <i class="bi bi-star-fill estrella-btn" data-valor="1" onclick="seleccionarEstrellas(1)"></i>
                        <i class="bi bi-star-fill estrella-btn" data-valor="2" onclick="seleccionarEstrellas(2)"></i>
                        <i class="bi bi-star-fill estrella-btn" data-valor="3" onclick="seleccionarEstrellas(3)"></i>
                        <i class="bi bi-star-fill estrella-btn" data-valor="4" onclick="seleccionarEstrellas(4)"></i>
                        <i class="bi bi-star-fill estrella-btn" data-valor="5" onclick="seleccionarEstrellas(5)"></i>
                    </div>
                    <input type="hidden" name="calificacion" id="inputCalificacion" value="5">

                    <!-- Comentario -->
                    <div class="text-start mb-2">
                        <label class="form-label small fw-semibold">Cuéntale a Maye y a futuras clientas qué tal te pareció:</label>
                        <textarea name="comentarioResena" class="form-control rounded-3" rows="3" required placeholder="Ej: Me encantó la delicadeza con la que Maye atendió mis cutículas, el diseño quedó idéntico a la foto..."></textarea>
                    </div>
                    <small class="text-muted d-block text-start" style="font-size: 0.78rem;">
                        <i class="bi bi-shield-check text-success me-1"></i> Tu reseña será revisada antes de publicarse en la página principal.
                    </small>
                </div>

                <div class="modal-footer border-top-0 pt-0 pb-4 px-4 justify-content-end">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-primary-custom px-4 py-2">Enviar Opinión</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .estrella-btn {
        cursor: pointer;
        transition: transform 0.2s ease, color 0.2s ease;
    }

    .estrella-btn:hover {
        transform: scale(1.25);
    }
</style>

<script>
    // Abrir el modal cargando los datos de la cita finalizada
    function abrirModalResena(citaId, servicioNombre) {
        document.getElementById('inputCitaIdResena').value = citaId;
        document.getElementById('txtServicioResena').textContent = servicioNombre;
        seleccionarEstrellas(5); // Por defecto 5 estrellas

        const modal = new bootstrap.Modal(document.getElementById('modalDejarResena'));
        modal.show();
    }

    // Pintar interactivamente las estrellas seleccionadas
    function seleccionarEstrellas(puntaje) {
        document.getElementById('inputCalificacion').value = puntaje;
        const estrellas = document.querySelectorAll('#contenedorEstrellas .estrella-btn');

        estrellas.forEach((est, idx) => {
            if (idx < puntaje) {
                est.classList.remove('bi-star', 'text-muted');
                est.classList.add('bi-star-fill', 'text-warning');
            } else {
                est.classList.remove('bi-star-fill', 'text-warning');
                est.classList.add('bi-star', 'text-muted');
            }
        });
    }
</script>

<script>
    // Mostrar u ocultar la caja informativa según el método de pago
    function alternarInfoTransferencia(metodo) {
        const box = document.getElementById('boxInfoTransferencia');
        if (box) {
            box.style.display = (metodo === 'nequi_daviplata') ? 'block' : 'none';
        }
    }

    // Carga de horarios evaluando la duración del servicio
    function dispararCargaHoras() {
        const selectServicio = document.getElementById('selectServicioCita');
        const inputFecha = document.getElementById('inputFechaCita');
        const selectHora = document.getElementById('selectHoraCita');
        const alerta = document.getElementById('alertaHorarioMaye');

        const servicioId = selectServicio.value;
        const fecha = inputFecha.value;

        if (!servicioId || !fecha) {
            selectHora.innerHTML = '<option value="">Selecciona primero servicio y fecha...</option>';
            return;
        }

        selectHora.innerHTML = '<option value="">Calculando turnos disponibles...</option>';
        alerta.textContent = '';

        fetch(`ajax/horariosDisponibles.php?fecha=${fecha}&servicio_id=${servicioId}`)
            .then(res => res.json())
            .then(data => {
                selectHora.innerHTML = '';
                if (data.status === 'cerrado') {
                    selectHora.innerHTML = '<option value="">Día no laboral</option>';
                    alerta.textContent = data.mensaje;
                    return;
                }

                let disponibles = 0;
                data.horas.forEach(h => {
                    const opt = document.createElement('option');
                    opt.value = h.valor;
                    if (h.ocupado) {
                        opt.textContent = `${h.texto} (Ocupado)`;
                        opt.disabled = true;
                        opt.style.color = '#dc3545';
                    } else {
                        opt.textContent = h.texto;
                        disponibles++;
                    }
                    selectHora.appendChild(opt);
                });

                if (disponibles === 0) {
                    alerta.textContent = 'No hay espacio continuo suficiente para la duración de este servicio.';
                }
            })
            .catch(err => {
                selectHora.innerHTML = '<option value="">Error al cargar horarios</option>';
                alerta.textContent = 'Hubo un error al consultar la agenda. Intenta recargar.';
            });
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Carga inmediata si el servicio viene seleccionado
        if (document.getElementById('selectServicioCita').value) {
            dispararCargaHoras();
        }

        // Si se acaba de crear una cita por Nequi/Daviplata, abrir el modal con los datos
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('pago_directo') === '1') {
            const servicio = urlParams.get('srv') || 'Servicio de Belleza';
            const precio = urlParams.get('val') || '0';

            document.getElementById('modalServicioNombre').textContent = decodeURIComponent(servicio);
            document.getElementById('modalServicioPrecio').textContent = `$${Number(precio).toLocaleString('es-CO')} COP`;

            const mensajeWA = encodeURIComponent(`Hola Maye! Acabo de agendar mi cita para ${decodeURIComponent(servicio)} por valor de $${Number(precio).toLocaleString('es-CO')} COP. Aquí te adjunto el comprobante de transferencia.`);
            document.getElementById('btnEnviarComprobanteWA').href = `https://wa.me/573143183150?text=${mensajeWA}`;

            const modal = new bootstrap.Modal(document.getElementById('modalPagoDirecto'));
            modal.show();
        }
    });
</script>