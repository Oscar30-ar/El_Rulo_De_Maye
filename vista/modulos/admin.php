<?php
if (!isset($_SESSION["iniciarSesion"]) || $_SESSION["rol"] !== "admin") {
    echo '<script>window.location = "index.php?ruta=login";</script>';
    exit();
}

$ctrl = new ControladorUsuarios();
$ctrl->ctrGestionAdmin();

// Procesamiento de acciones GET
if (isset($_GET["accion"]) && isset($_GET["id_cita"])) {
    UsuarioModelo::mdlActualizarEstadoCita($_GET["id_cita"], $_GET["accion"]);
    echo '<script>window.location = "index.php?ruta=admin&tab=citas";</script>';
    exit();
}

if (isset($_GET["pago"]) && isset($_GET["id_cita"])) {
    UsuarioModelo::mdlActualizarPagoCita($_GET["id_cita"], $_GET["pago"]);
    echo '<script>window.location = "index.php?ruta=admin&tab=citas";</script>';
    exit();
}

if (isset($_GET["eliminar_cat"])) {
    UsuarioModelo::mdlEliminarCategoria($_GET["eliminar_cat"]);
    echo '<script>window.location="index.php?ruta=admin&tab=categorias";</script>';
    exit();
}

if (isset($_GET["eliminar_srv"])) {
    UsuarioModelo::mdlEliminarServicio($_GET["eliminar_srv"]);
    echo '<script>window.location="index.php?ruta=admin&tab=servicios";</script>';
    exit();
}

if (isset($_GET["eliminar_usr"])) {
    UsuarioModelo::mdlEliminarUsuario($_GET["eliminar_usr"]);
    echo '<script>window.location="index.php?ruta=admin&tab=usuarios";</script>';
    exit();
}

if (isset($_GET["eliminar_gal"])) {
    UsuarioModelo::mdlEliminarGaleria($_GET["eliminar_gal"]);
    echo '<script>window.location="index.php?ruta=admin&tab=galeria";</script>';
    exit();
}

if (isset($_GET["eliminar_historia"])) {
    // Eliminar archivo físico si existe y registro de base de datos
    $historiaId = (int)$_GET["eliminar_historia"];
    $stmtH = Conexion::conectar()->prepare("SELECT archivo FROM historias WHERE id = :id LIMIT 1");
    $stmtH->bindParam(":id", $historiaId, PDO::PARAM_INT);
    $stmtH->execute();
    $hBorrar = $stmtH->fetch(PDO::FETCH_ASSOC);
    if ($hBorrar && file_exists($hBorrar["archivo"])) {
        @unlink($hBorrar["archivo"]);
    }
    $stmtDel = Conexion::conectar()->prepare("DELETE FROM historias WHERE id = :id");
    $stmtDel->bindParam(":id", $historiaId, PDO::PARAM_INT);
    $stmtDel->execute();

    echo '<script>window.location="index.php?ruta=admin&tab=historia";</script>';
    exit();
}

if (isset($_GET["aprobar_resena"])) {
    UsuarioModelo::mdlCambiarEstadoResena($_GET["aprobar_resena"], 'aprobada');
    echo '<script>window.location="index.php?ruta=admin&tab=resenas";</script>';
    exit();
}

if (isset($_GET["rechazar_resena"])) {
    UsuarioModelo::mdlCambiarEstadoResena($_GET["rechazar_resena"], 'rechazada');
    echo '<script>window.location="index.php?ruta=admin&tab=resenas";</script>';
    exit();
}

// Guardar Horarios de Atención
if (isset($_POST["actualizarHorarios"])) {
    foreach ($_POST["dias"] as $dia => $datos) {
        $activo = isset($datos["activo"]) ? 1 : 0;
        $apertura = $datos["apertura"];
        $cierre = $datos["cierre"];
        UsuarioModelo::mdlActualizarHorarioMaye($dia, $apertura, $cierre, $activo);
    }
    echo '<script>window.location="index.php?ruta=admin&tab=citas";</script>';
    exit();
}

// Consultas principales
$tabActiva = $_GET["tab"] ?? "citas";
if ($tabActiva === "historias") {
    $tabActiva = "historia";
}
$todasLasCitas = UsuarioModelo::mdlListarCitas();
$categorias = UsuarioModelo::mdlListarCategorias();
$servicios = UsuarioModelo::mdlObtenerServicios();
$usuarios = UsuarioModelo::mdlListarUsuarios();
$resenas = UsuarioModelo::mdlListarTodasResenas();
$galeria = UsuarioModelo::mdlListarGaleriaConCategoria();
$configFidelizacion = UsuarioModelo::mdlObtenerConfigFidelizacion();
$horarios = UsuarioModelo::mdlObtenerTodosHorarios();
$historiasAdmin = method_exists('UsuarioModelo', 'mdlListarHistoriasActivas') ? UsuarioModelo::mdlListarHistoriasActivas() : [];

$nombresDias = [
    1 => 'Lunes',
    2 => 'Martes',
    3 => 'Miércoles',
    4 => 'Jueves',
    5 => 'Viernes',
    6 => 'Sábado',
    7 => 'Domingo'
];

// Métricas de Agenda
$totalCitas = count($todasLasCitas);
$pendientesAtencion = 0;
$totalIngresosCobrados = 0;

foreach ($todasLasCitas as $itemCita) {
    if ($itemCita["estado"] === 'pendiente') {
        $pendientesAtencion++;
    }
    if ($itemCita["pago_estado"] === 'pagado' && $itemCita["estado"] !== 'cancelada') {
        $totalIngresosCobrados += (float)$itemCita["precio"];
    }
}

// Edición de servicio
$servicioAEditar = null;
if (isset($_GET["editar_srv"])) {
    foreach ($servicios as $srvItem) {
        if ($srvItem["id"] == $_GET["editar_srv"]) {
            $servicioAEditar = $srvItem;
            break;
        }
    }
}
?>

<div class="container my-4">
    <!-- Pestañas de Navegación -->
    <ul class="nav nav-pills nav-fill mb-4 bg-white p-2 rounded-4 shadow-sm flex-wrap gap-2">
        <li class="nav-item">
            <a class="nav-link <?= $tabActiva === 'citas' ? 'active bg-primary' : 'text-dark' ?>" href="index.php?ruta=admin&tab=citas">
                <i class="bi bi-calendar-check me-1"></i> Agenda
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $tabActiva === 'servicios' ? 'active bg-primary' : 'text-dark' ?>" href="index.php?ruta=admin&tab=servicios">
                <i class="bi bi-scissors me-1"></i> Servicios
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $tabActiva === 'categorias' ? 'active bg-primary' : 'text-dark' ?>" href="index.php?ruta=admin&tab=categorias">
                <i class="bi bi-tags me-1"></i> Categorías
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $tabActiva === 'galeria' ? 'active bg-primary' : 'text-dark' ?>" href="index.php?ruta=admin&tab=galeria">
                <i class="bi bi-images me-1"></i> Galería
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($tabActiva === 'historia' || $tabActiva === 'historias') ? 'active bg-primary' : 'text-dark' ?>" href="index.php?ruta=admin&tab=historia">
                <i class="bi bi-instagram me-1"></i> Historias Maye
                <?php if (!empty($historiasAdmin)): ?>
                    <span class="badge bg-danger rounded-pill ms-1"><?= count($historiasAdmin) ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $tabActiva === 'resenas' ? 'active bg-primary' : 'text-dark' ?>" href="index.php?ruta=admin&tab=resenas">
                <i class="bi bi-star me-1"></i> Reseñas
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $tabActiva === 'fidelizacion' ? 'active bg-primary' : 'text-dark' ?>" href="index.php?ruta=admin&tab=fidelizacion">
                <i class="bi bi-gem me-1"></i> Fidelización
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $tabActiva === 'usuarios' ? 'active bg-primary' : 'text-dark' ?>" href="index.php?ruta=admin&tab=usuarios">
                <i class="bi bi-people me-1"></i> Clientas
            </a>
        </li>
    </ul>

    <!-- =======================================================
         1. AGENDA Y TURNOS
    ============================================================ -->
    <?php if ($tabActiva === 'citas'): ?>
        <!-- Métricas Rápidas -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white d-flex flex-row align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary-subtle text-primary" style="width: 50px; height: 50px;">
                        <i class="bi bi-calendar-week fs-4 color-primary-dark"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Total Reservas</span>
                        <h4 class="fw-bold mb-0 text-dark"><?= $totalCitas ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white d-flex flex-row align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning-subtle text-warning" style="width: 50px; height: 50px;">
                        <i class="bi bi-hourglass-split fs-4 text-warning-emphasis"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Por Confirmar</span>
                        <h4 class="fw-bold mb-0 text-dark"><?= $pendientesAtencion ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white d-flex flex-row align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-success-subtle text-success" style="width: 50px; height: 50px;">
                        <i class="bi bi-cash-stack fs-4 text-success"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Ingresos Cobrados</span>
                        <h4 class="fw-bold mb-0 text-success">$<?= number_format($totalIngresosCobrados, 0, ',', '.') ?> COP</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Horarios de Maye -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <h4 class="font-playfair mb-1 color-primary-dark">⚙️ Configuración de Horario de Atención</h4>
                    <p class="text-muted small mb-0">Selecciona los días que laboras (incluyendo domingos) y define la jornada.</p>
                </div>
                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" type="button" onclick="alternarHorarioMaye()">
                    <i class="bi bi-eye-slash me-1" id="iconoToggleHorario"></i> <span id="textoToggleHorario">Ocultar Horarios</span>
                </button>
            </div>

            <div id="seccionHorariosMaye">
                <form method="POST">
                    <input type="hidden" name="actualizarHorarios" value="1">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <thead>
                                <tr class="table-light small text-uppercase">
                                    <th>Día</th>
                                    <th>Estado Laboral</th>
                                    <th>Hora Inicio</th>
                                    <th>Hora Cierre</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($horarios as $h):
                                    $diaNum = (int)$h["dia_semana"];
                                    $esDomingo = ($diaNum === 7);
                                ?>
                                    <tr class="<?= $esDomingo ? 'bg-light-subtle' : '' ?>">
                                        <td class="fw-bold">
                                            <?= $nombresDias[$diaNum] ?>
                                            <?php if ($esDomingo): ?>
                                                <span class="badge bg-secondary-subtle text-secondary small ms-1">Especial</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="dias[<?= $diaNum ?>][activo]" value="1" id="sw_dia_<?= $diaNum ?>" <?= $h["activo"] ? 'checked' : '' ?>>
                                                <label class="form-check-label small fw-semibold" for="sw_dia_<?= $diaNum ?>">
                                                    <?= $h["activo"] ? '<span class="text-success">Disponible</span>' : '<span class="text-muted">Descanso</span>' ?>
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="time" class="form-control form-control-sm" name="dias[<?= $diaNum ?>][apertura]" value="<?= substr($h["hora_apertura"], 0, 5) ?>" required>
                                        </td>
                                        <td>
                                            <input type="time" class="form-control form-control-sm" name="dias[<?= $diaNum ?>][cierre]" value="<?= substr($h["hora_cierre"], 0, 5) ?>" required>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end mt-3">
                        <button type="submit" class="btn-primary-custom px-4 py-2">Guardar Horario</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de Citas -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                <h4 class="font-playfair mb-0 color-primary-dark">📋 Turnos del Salón</h4>
                <div class="position-relative" style="min-width: 250px;">
                    <input type="text" id="buscadorAdmin" class="form-control form-control-sm ps-5 rounded-pill" placeholder="Buscar cliente, fecha, servicio...">
                    <i class="bi bi-search position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tablaCitasAdmin">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Cliente</th>
                            <th>Servicio</th>
                            <th>Inspiración / Ref</th>
                            <th>Notas</th>
                            <th>Fecha & Hora</th>
                            <th>Estado Cita</th>
                            <th>Estado Pago</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($todasLasCitas)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Sin citas registradas aún.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($todasLasCitas as $c):
                                // Formatear número limpio para WhatsApp
                                $telefonoLimpio = preg_replace('/[^0-9]/', '', $c["telefono"] ?? '');
                                if (!empty($telefonoLimpio) && strlen($telefonoLimpio) === 10) {
                                    $telefonoLimpio = '57' . $telefonoLimpio; // Código de Colombia
                                }
                                $msjWhatsApp = urlencode("¡Hola " . $c["cliente"] . "! 🌸 Te escribe Maye de *El Rulo De Maye*. Te recordamos tu cita agendada para el *" . $c["fecha"] . "* a las *" . substr($c["hora"], 0, 5) . "* para tu servicio de *" . $c["servicio"] . "*. ¿Nos confirmas tu asistencia? ¡Te esperamos!");
                            ?>
                                <tr>
                                    <td>
                                        <strong class="d-block text-dark"><?= htmlspecialchars($c["cliente"]) ?></strong>
                                        <small class="text-muted"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($c["telefono"] ?: 'N/A') ?></small>
                                    </td>
                                    <td>
                                        <span class="fw-semibold"><?= htmlspecialchars($c["servicio"]) ?></span>
                                        <small class="d-block text-muted"><?= $c["duracion_minutos"] ?> min | $<?= number_format($c["precio"], 0, ',', '.') ?> COP</small>
                                    </td>
                                    <!-- Miniatura Foto de Referencia / Pinterest -->
                                    <td class="text-center">
                                        <?php if (!empty($c["foto_referencia"])): ?>
                                            <a href="javascript:void(0)" onclick="mostrarFotoModal('<?= htmlspecialchars($c['foto_referencia']) ?>', '<?= htmlspecialchars($c['cliente'], ENT_QUOTES) ?>')">
                                                <img src="<?= htmlspecialchars($c['foto_referencia']) ?>" class="rounded-3 shadow-xs border" style="width: 45px; height: 45px; object-fit: cover;" title="Clic para ver diseño de referencia">
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small"><em>Sin foto</em></span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="max-width: 180px;">
                                        <?php if (!empty($c["notas"])): ?>
                                            <div class="p-2 bg-light rounded-3 small border text-secondary" style="font-size: 0.82rem;">
                                                <i class="bi bi-chat-left-text me-1 color-primary-dark"></i><?= htmlspecialchars($c["notas"]) ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted small"><em>Sin notas</em></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark"><?= $c["fecha"] ?></span><br>
                                        <small class="badge bg-light text-dark border"><?= substr($c["hora"], 0, 5) ?></small>
                                    </td>
                                    <td>
                                        <?php if ($c['estado'] === 'confirmada'): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Confirmada</span>
                                        <?php elseif ($c['estado'] === 'finalizada'): ?>
                                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">Finalizada</span>
                                        <?php elseif ($c['estado'] === 'cancelada'): ?>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Cancelada</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">Pendiente</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($c['pago_estado'] === 'pagado'): ?>
                                            <span class="badge bg-success text-white px-2 py-1">Pagado</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark px-2 py-1">Pendiente</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                                            <!-- Botón Recordatorio WhatsApp -->
                                            <?php if (!empty($telefonoLimpio)): ?>
                                                <a href="https://api.whatsapp.com/send?phone=<?= $telefonoLimpio ?>&text=<?= $msjWhatsApp ?>" target="_blank" class="btn btn-sm btn-outline-success" title="Recordar por WhatsApp">
                                                    <i class="bi bi-whatsapp"></i>
                                                </a>
                                            <?php endif; ?>

                                            <?php if ($c['estado'] === 'pendiente'): ?>
                                                <a href="index.php?ruta=admin&tab=citas&accion=confirmada&id_cita=<?= $c['id'] ?>" class="btn btn-sm btn-outline-success" title="Confirmar Cita"><i class="bi bi-check-lg"></i></a>
                                            <?php endif; ?>
                                            <?php if ($c['pago_estado'] === 'pendiente' && $c['estado'] !== 'cancelada'): ?>
                                                <a href="index.php?ruta=admin&tab=citas&pago=pagado&id_cita=<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary" title="Marcar Pagado"><i class="bi bi-cash"></i></a>
                                            <?php endif; ?>
                                            <?php if ($c['estado'] === 'confirmada'): ?>
                                                <a href="index.php?ruta=admin&tab=citas&accion=finalizada&id_cita=<?= $c['id'] ?>" class="btn btn-sm btn-success text-white" title="Finalizar"><i class="bi bi-hand-thumbs-up"></i></a>
                                            <?php endif; ?>
                                            <?php if ($c['estado'] !== 'cancelada' && $c['estado'] !== 'finalizada'): ?>
                                                <a href="index.php?ruta=admin&tab=citas&accion=cancelada&id_cita=<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger" title="Cancelar" onclick="return confirm('¿Cancelar cita?')"><i class="bi bi-x-circle"></i></a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end align-items-center pt-3 border-top mt-3" id="paginacionAdmin"></div>
        </div>

        <!-- =======================================================
         2. SERVICIOS (CREAR / EDITAR)
    ============================================================ -->
    <?php elseif ($tabActiva === 'servicios'): ?>
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <h5 class="fw-bold mb-3 color-primary-dark">
                <?= $servicioAEditar ? '✏️ Editar Servicio: ' . htmlspecialchars($servicioAEditar['nombre']) : '➕ Agregar Nuevo Servicio' ?>
            </h5>
            <form method="POST" class="row g-3">
                <input type="hidden" name="btnGuardarServicio" value="1">
                <input type="hidden" name="srvId" value="<?= $servicioAEditar ? $servicioAEditar['id'] : '' ?>">

                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Categoría</label>
                    <select name="srvCategoria" class="form-select form-select-sm" required>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($servicioAEditar && $servicioAEditar['categoria_id'] == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Nombre del Servicio</label>
                    <input type="text" name="srvNombre" class="form-control form-control-sm" value="<?= $servicioAEditar ? htmlspecialchars($servicioAEditar['nombre']) : '' ?>" required placeholder="Ej: Kapping Gel">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Precio ($ COP)</label>
                    <input type="number" name="srvPrecio" class="form-control form-control-sm" value="<?= $servicioAEditar ? (int)$servicioAEditar['precio'] : '' ?>" required placeholder="55000">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Duración (minutos)</label>
                    <input type="number" name="srvDuracion" class="form-control form-control-sm" value="<?= $servicioAEditar ? (int)$servicioAEditar['duracion_minutos'] : '60' ?>" required placeholder="60">
                </div>
                <div class="col-md-9">
                    <label class="form-label small fw-semibold">Descripción del Servicio</label>
                    <input type="text" name="srvDescripcion" class="form-control form-control-sm" value="<?= $servicioAEditar ? htmlspecialchars($servicioAEditar['descripcion'] ?? '') : '' ?>" placeholder="Detalles de preparación o técnica">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Estado</label>
                    <select name="srvEstado" class="form-select form-select-sm">
                        <option value="activo" <?= ($servicioAEditar && $servicioAEditar['estado'] === 'activo') ? 'selected' : '' ?>>Activo</option>
                        <option value="inactivo" <?= ($servicioAEditar && $servicioAEditar['estado'] === 'inactivo') ? 'selected' : '' ?>>Inactivo / Próximamente</option>
                    </select>
                </div>
                <div class="col-12 text-end">
                    <?php if ($servicioAEditar): ?>
                        <a href="index.php?ruta=admin&tab=servicios" class="btn btn-sm btn-outline-secondary me-2">Cancelar Edición</a>
                    <?php endif; ?>
                    <button type="submit" class="btn-primary-custom px-4 py-1">
                        <?= $servicioAEditar ? 'Guardar Cambios' : 'Guardar Servicio' ?>
                    </button>
                </div>
            </form>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <h5 class="fw-bold mb-3 color-primary-dark">Catálogo de Servicios</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Categoría</th>
                            <th>Servicio</th>
                            <th>Precio</th>
                            <th>Duración</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($servicios as $s): ?>
                            <tr>
                                <td><?= htmlspecialchars($s['categoria']) ?></td>
                                <td><strong><?= htmlspecialchars($s['nombre']) ?></strong></td>
                                <td>$<?= number_format($s['precio'], 0, ',', '.') ?> COP</td>
                                <td><?= $s['duracion_minutos'] ?> min</td>
                                <td>
                                    <span class="badge <?= $s['estado'] == 'activo' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border' ?>">
                                        <?= ucfirst($s['estado']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="index.php?ruta=admin&tab=servicios&editar_srv=<?= $s['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar Servicio"><i class="bi bi-pencil-square"></i></a>
                                    <a href="index.php?ruta=admin&tab=servicios&eliminar_srv=<?= $s['id'] ?>" class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="return confirm('¿Eliminar este servicio?')"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- =======================================================
         3. CATEGORÍAS
    ============================================================ -->
    <?php elseif ($tabActiva === 'categorias'): ?>
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h5 class="fw-bold mb-3 color-primary-dark">Crear Categoría</h5>
                    <form method="POST">
                        <input type="hidden" name="btnGuardarCategoria" value="1">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nombre de la Categoría</label>
                            <input type="text" name="catNombre" class="form-control" required placeholder="Ej: Peluquería & Color">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Estado</label>
                            <select name="catEstado" class="form-select">
                                <option value="activo">Activo</option>
                                <option value="proximamente">Próximamente</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-primary-custom w-100 py-2">Guardar Categoría</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h5 class="fw-bold mb-3 color-primary-dark">Categorías Existentes</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Estado</th>
                                    <th class="text-center">Eliminar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categorias as $cat): ?>
                                    <tr>
                                        <td>#<?= $cat['id'] ?></td>
                                        <td><strong><?= htmlspecialchars($cat['nombre']) ?></strong></td>
                                        <td>
                                            <span class="badge <?= $cat['estado'] == 'activo' ? 'bg-success-subtle text-success border' : 'bg-warning-subtle text-warning border' ?>">
                                                <?= ucfirst($cat['estado']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="index.php?ruta=admin&tab=categorias&eliminar_cat=<?= $cat['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar categoría y sus servicios?')"><i class="bi bi-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- =======================================================
         4. GALERÍA (CON PREVIEWS Y CATEGORÍAS)
    ============================================================ -->
    <?php elseif ($tabActiva === 'galeria'): ?>
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <h5 class="fw-bold mb-3 color-primary-dark">Subir Nuevo Trabajo a la Galería</h5>
            <form method="POST" enctype="multipart/form-data" class="row g-3">
                <input type="hidden" name="btnSubirGaleria" value="1">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold">Título del Trabajo</label>
                    <input type="text" name="galTitulo" class="form-control form-control-sm" required placeholder="Ej: Uñas Esculturales Baby Boomer">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Categoría</label>
                    <select name="galCategoria" class="form-select form-select-sm" required>
                        <option value="">Selecciona categoría...</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Descripción corta</label>
                    <input type="text" name="galDescripcion" class="form-control form-control-sm" placeholder="Ej: Reconstrucción y nivelación">
                </div>

                <div class="col-md-6 text-center">
                    <label class="form-label small fw-semibold d-block text-start">Foto ANTES</label>
                    <input type="file" name="fotoAntes" id="inputFotoAntes" class="form-control form-control-sm mb-2" accept="image/*" required onchange="previsualizarImagen(this, 'previewAntes')">
                    <div class="border rounded-3 p-2 bg-light d-flex align-items-center justify-content-center" style="height: 180px; overflow: hidden;">
                        <img id="previewAntes" src="" alt="Vista Previa Antes" style="max-height: 100%; max-width: 100%; display: none; object-fit: cover; border-radius: 8px;">
                        <span id="txtPlaceholderAntes" class="text-muted small">Vista previa de foto ANTES</span>
                    </div>
                </div>

                <div class="col-md-6 text-center">
                    <label class="form-label small fw-semibold d-block text-start">Foto DESPUÉS</label>
                    <input type="file" name="fotoDespues" id="inputFotoDespues" class="form-control form-control-sm mb-2" accept="image/*" required onchange="previsualizarImagen(this, 'previewDespues')">
                    <div class="border rounded-3 p-2 bg-light d-flex align-items-center justify-content-center" style="height: 180px; overflow: hidden;">
                        <img id="previewDespues" src="" alt="Vista Previa Después" style="max-height: 100%; max-width: 100%; display: none; object-fit: cover; border-radius: 8px;">
                        <span id="txtPlaceholderDespues" class="text-muted small">Vista previa de foto DESPUÉS</span>
                    </div>
                </div>

                <div class="col-12 text-end mt-3">
                    <button type="submit" class="btn-primary-custom px-4 py-2">Publicar en Galería</button>
                </div>
            </form>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <h5 class="fw-bold mb-3 color-primary-dark">Galería de Trabajos Registrados</h5>
            <div class="row g-4">
                <?php if (empty($galeria)): ?>
                    <div class="col-12 text-center py-4 text-muted">Sin publicaciones en la galería.</div>
                <?php else: ?>
                    <?php foreach ($galeria as $g): ?>
                        <div class="col-md-4">
                            <div class="border rounded-4 p-3 h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-primary-subtle text-primary border small"><?= htmlspecialchars($g['categoria'] ?: 'General') ?></span>
                                        <a href="index.php?ruta=admin&tab=galeria&eliminar_gal=<?= $g['id'] ?>" class="text-danger" onclick="return confirm('¿Eliminar publicación?')" title="Eliminar"><i class="bi bi-trash"></i></a>
                                    </div>
                                    <div class="d-flex gap-2 mb-2" style="height: 120px;">
                                        <img src="<?= htmlspecialchars($g['foto_antes']) ?>" class="w-50 rounded-3 border" style="object-fit: cover;" title="Antes">
                                        <img src="<?= htmlspecialchars($g['foto_despues']) ?>" class="w-50 rounded-3 border" style="object-fit: cover;" title="Después">
                                    </div>
                                    <h6 class="fw-bold mb-1"><?= htmlspecialchars($g['titulo']) ?></h6>
                                    <p class="small text-muted mb-0"><?= htmlspecialchars($g['descripcion']) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- =======================================================
         5. HISTORIAS MAYE (TIPO INSTAGRAM 24 HORAS)
    ============================================================ -->
    <?php elseif ($tabActiva === 'historia'): ?>
        <div class="row g-4">
            <!-- Formulario para publicar historia -->
            <div class="col-lg-5">
                <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-instagram fs-4 color-primary-dark"></i>
                        <h5 class="font-playfair fw-bold color-primary-dark mb-0">Publicar Historia (24 Horas)</h5>
                    </div>
                    <p class="text-muted small mb-4">Sube un trabajo, color o video de hoy. Se exhibirá en el logo con aro animado de la página principal y se borrará sola a las 24 horas.</p>

                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="btnSubirHistoria" value="1">

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Foto o Video del Trabajo</label>
                            <input type="file" name="archivoHistoria" class="form-control" accept="image/*,video/*" required onchange="previsualizarHistoriaAdmin(this)">
                        </div>

                        <!-- Previsualización de la historia -->
                        <div class="mb-3 border rounded-4 bg-light d-flex align-items-center justify-content-center overflow-hidden position-relative" style="height: 240px;">
                            <img id="previewHistoriaImg" src="" style="max-height: 100%; max-width: 100%; display: none; object-fit: cover;">
                            <video id="previewHistoriaVid" controls style="max-height: 100%; max-width: 100%; display: none;"></video>
                            <span id="txtStoryPlaceholder" class="text-muted small">Vista previa de la historia</span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Pie de Foto / Técnica (Opcional)</label>
                            <input type="text" name="pieFotoHistoria" class="form-control" placeholder="Ej: Nivelación Rubber con cristales ✨💅">
                        </div>

                        <button type="submit" class="btn-primary-custom w-100 py-2">
                            <i class="bi bi-stars me-1"></i> Publicar Historia Ahora
                        </button>
                    </form>
                </div>
            </div>

            <!-- Historias actualmente activas y reacciones -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h5 class="font-playfair fw-bold color-primary-dark mb-3">Historias Publicadas Hoy</h5>

                    <?php if (empty($historiasAdmin)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-clock-history fs-1 d-block mb-2 text-secondary"></i>
                            <strong>No hay historias activas</strong>
                            <p class="small mb-0">Publica una foto o video para que tus clientas vean tus creaciones de hoy y puedan reaccionar.</p>
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach ($historiasAdmin as $st):
                                $stmtR = Conexion::conectar()->prepare("
            SELECT reaccion, COUNT(*) as cantidad 
            FROM historia_reacciones 
            WHERE historia_id = :hid 
            GROUP BY reaccion
        ");
                                $stmtR->bindParam(":hid", $st["id"], PDO::PARAM_INT);
                                $stmtR->execute();
                                $reaccionesGrupos = $stmtR->fetchAll(PDO::FETCH_ASSOC);

                                // Obtenemos el detalle de usuarias para esta historia
                                $detalleReacciones = UsuarioModelo::mdlObtenerDetalleReaccionesHistoria($st["id"]);
                                $totalReacciones = count($detalleReacciones);

                                $segundosTranscurridos = time() - strtotime($st["fecha_publicacion"]);
                                $horasRestantes = max(0, 24 - floor($segundosTranscurridos / 3600));
                            ?>
                                <div class="col-md-6">
                                    <div class="border rounded-4 p-3 h-100 d-flex flex-column justify-content-between position-relative shadow-xs bg-white">
                                        <div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge bg-danger-subtle text-danger border small">
                                                    <i class="bi bi-hourglass me-1"></i> Quedan ~<?= $horasRestantes ?>h
                                                </span>
                                                <a href="index.php?ruta=admin&tab=historia&eliminar_historia=<?= $st['id'] ?>" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="return confirm('¿Eliminar esta historia antes de tiempo?')" title="Eliminar Historia">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>

                                            <div class="rounded-3 overflow-hidden bg-dark text-center mb-2" style="height: 180px;">
                                                <?php if ($st["tipo"] === 'video'): ?>
                                                    <video src="<?= htmlspecialchars($st['archivo']) ?>" class="w-100 h-100" style="object-fit: cover;" controls></video>
                                                <?php else: ?>
                                                    <img src="<?= htmlspecialchars($st['archivo']) ?>" class="w-100 h-100" style="object-fit: cover;">
                                                <?php endif; ?>
                                            </div>

                                            <p class="small text-dark fw-semibold mb-2 lh-sm">
                                                <?= htmlspecialchars($st["pie_foto"] ?: 'Sin pie de foto') ?>
                                            </p>
                                        </div>

                                        <!-- Detalle interactivo de Reacciones -->
                                        <div class="pt-2 border-top">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small class="text-muted" style="font-size: 0.75rem;">Reacciones recibidas:</small>
                                                <?php if ($totalReacciones > 0): ?>
                                                    <button type="button" class="btn btn-link p-0 text-decoration-none small text-primary fw-bold"
                                                        onclick='abrirModalQuienReacciono(<?= json_encode($detalleReacciones, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                                                        <i class="bi bi-people-fill me-1"></i> Ver quiénes (<?= $totalReacciones ?>)
                                                    </button>
                                                <?php endif; ?>
                                            </div>

                                            <div class="d-flex gap-2 flex-wrap align-items-center">
                                                <?php if (empty($reaccionesGrupos)): ?>
                                                    <span class="text-muted small fst-italic">Aún sin reacciones</span>
                                                <?php else: ?>
                                                    <?php foreach ($reaccionesGrupos as $rg): ?>
                                                        <span class="badge bg-light text-dark border px-2 py-1 fs-6">
                                                            <?= $rg['reaccion'] ?> <span class="fw-bold ms-1 text-secondary" style="font-size: 0.75rem;"><?= $rg['cantidad'] ?></span>
                                                        </span>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- =======================================================
         6. RESEÑAS
    ============================================================ -->
    <?php elseif ($tabActiva === 'resenas'): ?>
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <h4 class="font-playfair mb-3 color-primary-dark">⭐ Moderación de Reseñas</h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Clienta</th>
                            <th>Calificación</th>
                            <th>Comentario</th>
                            <th>Estado</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($resenas)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-chat-square-heart fs-2 d-block mb-2 text-secondary"></i>
                                    <strong>Sin reseñas aún</strong><br>
                                    <small>Las opiniones enviadas por tus clientas aparecerán aquí para decidir cuáles hacer públicas.</small>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($resenas as $r): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($r['nombre']) ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($r['email']) ?></small>
                                    </td>
                                    <td><span class="text-warning fw-bold fs-6"><?= str_repeat('★', $r['calificacion']) ?></span></td>
                                    <td style="max-width: 320px;"><small><?= htmlspecialchars($r['comentario']) ?></small></td>
                                    <td>
                                        <span class="badge <?= $r['estado'] === 'aprobada' ? 'bg-success-subtle text-success border' : ($r['estado'] === 'rechazada' ? 'bg-danger-subtle text-danger border' : 'bg-warning-subtle text-warning border') ?>">
                                            <?= ucfirst($r['estado']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($r['estado'] !== 'aprobada'): ?>
                                            <a href="index.php?ruta=admin&tab=resenas&aprobar_resena=<?= $r['id'] ?>" class="btn btn-sm btn-success text-white me-1" title="Aprobar"><i class="bi bi-check-lg"></i> Aprobar</a>
                                        <?php endif; ?>
                                        <?php if ($r['estado'] !== 'rechazada'): ?>
                                            <a href="index.php?ruta=admin&tab=resenas&rechazar_resena=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger" title="Rechazar"><i class="bi bi-x-lg"></i> Rechazar</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- =======================================================
         7. FIDELIZACIÓN (TODAS LAS CLIENTAS)
    ============================================================ -->
    <?php elseif ($tabActiva === 'fidelizacion'): ?>
        <div class="row g-4">
            <!-- Configuración Dinámica de Reglas -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h5 class="fw-bold mb-3 color-primary-dark">⚙️ Dinámica del Club Maye Lover</h5>
                    <form method="POST">
                        <input type="hidden" name="btnActualizarFidelizacion" value="1">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Meta de Visitas / Citas para Premio</label>
                            <input type="number" name="metaVisitas" class="form-control" value="<?= (int)$configFidelizacion['meta_visitas'] ?>" min="2" max="20" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Porcentaje de Descuento (%)</label>
                            <input type="number" name="porcentajeDescuento" class="form-control" value="<?= (int)$configFidelizacion['porcentaje_descuento'] ?>" min="5" max="100" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Descripción del Premio</label>
                            <textarea name="premioTexto" class="form-control" rows="3" required><?= htmlspecialchars($configFidelizacion['premio_texto']) ?></textarea>
                        </div>
                        <button type="submit" class="btn-primary-custom w-100 py-2">Guardar Reglas</button>
                    </form>
                </div>
            </div>

            <!-- Progreso de Todas las Clientas -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h5 class="fw-bold mb-3 color-primary-dark">Progreso de Todas las Clientas</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th>Clienta</th>
                                    <th>Citas Finalizadas</th>
                                    <th>Progreso hacia Premio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($usuarios)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">Sin usuarios registrados.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php
                                    $meta = (int)$configFidelizacion['meta_visitas'];
                                    foreach ($usuarios as $u):
                                        $finalizadas = UsuarioModelo::mdlContarCitasFinalizadasUsuario($u['id']);
                                        $avance = $meta > 0 ? ($finalizadas % $meta) : 0;
                                        $porcentaje = $meta > 0 ? (($avance / $meta) * 100) : 0;
                                        $completoCiclo = ($finalizadas > 0 && $avance === 0);
                                    ?>
                                        <tr>
                                            <td>
                                                <strong class="text-dark"><?= htmlspecialchars($u['nombre']) ?></strong>
                                                <?php if ($u['rol'] === 'admin'): ?>
                                                    <span class="badge bg-danger-subtle text-danger border ms-1" style="font-size:0.68rem;">Admin</span>
                                                <?php endif; ?>
                                                <br>
                                                <small class="text-muted"><?= htmlspecialchars($u['telefono'] ?: $u['email']) ?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                                    <?= $finalizadas ?> citas
                                                </span>
                                            </td>
                                            <td style="min-width: 190px;">
                                                <div class="d-flex justify-content-between small fw-semibold mb-1">
                                                    <span><?= $avance ?> / <?= $meta ?></span>
                                                    <span class="<?= $completoCiclo ? 'text-success fw-bold' : 'text-muted' ?>">
                                                        <?= $completoCiclo ? '¡Premio Listo!' : round($porcentaje) . '%' ?>
                                                    </span>
                                                </div>
                                                <div class="progress rounded-pill" style="height: 9px;">
                                                    <div class="progress-bar <?= $completoCiclo ? 'bg-success' : 'bg-primary' ?>" style="width: <?= $completoCiclo ? 100 : $porcentaje ?>%;"></div>
                                                </div>
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

        <!-- =======================================================
         8. CLIENTAS (CON EDICIÓN Y ELIMINACIÓN)
    ============================================================ -->
    <?php elseif ($tabActiva === 'usuarios'): ?>
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <h4 class="font-playfair mb-3 color-primary-dark">👥 Directorio de Clientas</h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Clienta</th>
                            <th>Correo Electrónico</th>
                            <th>Teléfono / WhatsApp</th>
                            <th>Rol</th>
                            <th>Fecha Registro</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($usuarios)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Sin clientas registradas.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($usuarios as $u): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($u['nombre']) ?></strong></td>
                                    <td><?= htmlspecialchars($u['email']) ?></td>
                                    <td><?= htmlspecialchars($u['telefono'] ?: 'N/A') ?></td>
                                    <td>
                                        <span class="badge <?= $u['rol'] == 'admin' ? 'bg-danger-subtle text-danger border' : 'bg-primary-subtle text-primary border' ?>">
                                            <?= ucfirst($u['rol']) ?>
                                        </span>
                                    </td>
                                    <td><small class="text-muted"><?= substr($u['fecha_registro'], 0, 10) ?></small></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary me-1"
                                            onclick="abrirModalEditarClienta('<?= $u['id'] ?>', '<?= htmlspecialchars($u['nombre'], ENT_QUOTES) ?>', '<?= htmlspecialchars($u['telefono'] ?? '', ENT_QUOTES) ?>', '<?= $u['rol'] ?>')"
                                            title="Editar Datos">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <?php if ($u['rol'] !== 'admin'): ?>
                                            <a href="index.php?ruta=admin&tab=usuarios&eliminar_usr=<?= $u['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta clienta? Sus citas registradas también se removerán.')" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL DE EDICIÓN DE CLIENTA -->
        <div class="modal fade" id="modalEditarClienta" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title font-playfair color-primary-dark fw-bold">✏️ Editar Datos de Clienta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body px-4">
                            <input type="hidden" name="btnGuardarEdicionUsuario" value="1">
                            <input type="hidden" name="usuarioId" id="editUsrId">

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Nombre Completo</label>
                                <input type="text" name="usuarioNombre" id="editUsrNombre" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Teléfono / WhatsApp</label>
                                <input type="text" name="usuarioTelefono" id="editUsrTelefono" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Rol en el Sistema</label>
                                <select name="usuarioRol" id="editUsrRol" class="form-select">
                                    <option value="cliente">Cliente</option>
                                    <option value="admin">Administradora</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn-primary-custom px-4 py-2">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

<!-- MODAL: DETALLE DE QUIÉNES REACCIONARON -->
<div class="modal fade" id="modalQuienReacciono" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h6 class="modal-title font-playfair color-primary-dark fw-bold">
                    <i class="bi bi-heart-fill text-danger me-1"></i> Reacciones a la Historia
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-3">
                <ul class="list-group list-group-flush" id="listaDetalleReacciones">
                    <!-- Filas dinámicas generadas por JS -->
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    function abrirModalQuienReacciono(detalles) {
        const contenedor = document.getElementById('listaDetalleReacciones');
        contenedor.innerHTML = '';

        if (!detalles || detalles.length === 0) {
            contenedor.innerHTML = '<li class="list-group-item text-center text-muted small py-3">No hay reacciones registradas.</li>';
            const modal = new bootstrap.Modal(document.getElementById('modalQuienReacciono'));
            modal.show();
            return;
        }

        // Agrupar por ID único de usuario
        const agrupados = {};

        detalles.forEach(item => {
            // Clave única infalible: el ID del usuario en base de datos.
            // Si no tiene usuario_id (visitante anónimo), usamos 'anonimo_' + item.id
            const clave = item.usuario_id ? ('user_' + item.usuario_id) : ('anonimo_' + item.id);

            if (!agrupados[clave]) {
                agrupados[clave] = {
                    cliente: item.cliente,
                    telefono: item.telefono,
                    email: item.email,
                    foto: item.foto,
                    ultimaHora: item.fecha ? item.fecha.substring(11, 16) : '',
                    reacciones: {}
                };
            }

            // Acumular conteo de cada emoji
            agrupados[clave].reacciones[item.reaccion] = (agrupados[clave].reacciones[item.reaccion] || 0) + 1;
        });

        // Renderizar una fila independiente por cada persona
        Object.values(agrupados).forEach(usr => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center px-2 py-2 border-0 border-bottom';

            const iniciales = obtenerIniciales(usr.cliente);
            const fotoValida = usr.foto && usr.foto.trim() !== '' && usr.foto !== 'null';

            const avatarHtml = fotoValida ?
                `<img src="${usr.foto}" class="rounded-circle border shadow-xs" style="width: 40px; height: 40px; object-fit: cover;" onerror="this.outerHTML='<div class=\\'rounded-circle d-flex align-items-center justify-content-center bg-primary-subtle text-primary fw-bold shadow-xs\\' style=\\'width: 40px; height: 40px; font-size: 0.95rem;\\'>${iniciales}</div>'">` :
                `<div class="rounded-circle d-flex align-items-center justify-content-center bg-primary-subtle text-primary fw-bold shadow-xs" style="width: 40px; height: 40px; font-size: 0.95rem; letter-spacing: 0.5px;">${iniciales}</div>`;

            let emojisHtml = '<div class="d-flex gap-1 align-items-center flex-wrap justify-content-end">';
            for (const [emoji, cant] of Object.entries(usr.reacciones)) {
                emojisHtml += `
                <span class="badge bg-light text-dark border px-2 py-1 shadow-2xs d-flex align-items-center gap-1" style="font-size: 0.95rem;">
                    <span>${emoji}</span>
                    ${cant > 1 ? `<span class="fw-bold text-secondary" style="font-size: 0.72rem;">x${cant}</span>` : ''}
                </span>
            `;
            }
            emojisHtml += '</div>';

            li.innerHTML = `
            <div class="d-flex align-items-center gap-3">
                ${avatarHtml}
                <div>
                    <strong class="d-block text-dark small mb-0">${usr.cliente}</strong>
                    <small class="text-muted" style="font-size: 0.73rem;">
                        ${usr.telefono || usr.email || 'Clienta'} · ${usr.ultimaHora}
                    </small>
                </div>
            </div>
            ${emojisHtml}
        `;

            contenedor.appendChild(li);
        });

        const modal = new bootstrap.Modal(document.getElementById('modalQuienReacciono'));
        modal.show();
    }

    function obtenerIniciales(nombre) {
        if (!nombre) return 'C';
        const partes = nombre.trim().split(/\s+/);
        if (partes.length >= 2) {
            return (partes[0][0] + partes[1][0]).toUpperCase();
        }
        return partes[0].substring(0, 2).toUpperCase();
    }
</script>
<!-- MODAL PARA VER FOTO DE REFERENCIA / INSPIRACIÓN EN GRANDE -->
<div class="modal fade" id="modalFotoReferencia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h6 class="modal-title font-playfair color-primary-dark fw-bold" id="modalFotoTitulo">Inspiración de la Clienta</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center p-3">
                <img id="imgReferenciaGrande" src="" class="img-fluid rounded-3 shadow-xs" style="max-height: 70vh; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

<script>
    // Previsualización de imágenes antes de subirlas a la galería
    function previsualizarImagen(input, idImg) {
        const preview = document.getElementById(idImg);
        const placeholder = document.getElementById('txtPlaceholder' + (idImg === 'previewAntes' ? 'Antes' : 'Despues'));
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                if (placeholder) placeholder.style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Previsualización de Historias (Imagen o Video)
    function previsualizarHistoriaAdmin(input) {
        const previewImg = document.getElementById('previewHistoriaImg');
        const previewVid = document.getElementById('previewHistoriaVid');
        const placeholder = document.getElementById('txtStoryPlaceholder');

        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                if (file.type.startsWith('video/')) {
                    previewImg.style.display = 'none';
                    previewVid.src = e.target.result;
                    previewVid.style.display = 'block';
                } else {
                    previewVid.style.display = 'none';
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
                }
                if (placeholder) placeholder.style.display = 'none';
            }
            reader.readAsDataURL(file);
        }
    }

    // Modal para foto de inspiración
    function mostrarFotoModal(rutaFoto, cliente) {
        document.getElementById('imgReferenciaGrande').src = rutaFoto;
        document.getElementById('modalFotoTitulo').textContent = 'Inspiración enviada por ' + cliente;
        const modal = new bootstrap.Modal(document.getElementById('modalFotoReferencia'));
        modal.show();
    }

    // Abrir modal de edición de clienta
    function abrirModalEditarClienta(id, nombre, telefono, rol) {
        document.getElementById('editUsrId').value = id;
        document.getElementById('editUsrNombre').value = nombre;
        document.getElementById('editUsrTelefono').value = telefono;
        document.getElementById('editUsrRol').value = rol;

        const modal = new bootstrap.Modal(document.getElementById('modalEditarClienta'));
        modal.show();
    }

    // Alternar visualización de horarios
    function alternarHorarioMaye() {
        const seccion = document.getElementById('seccionHorariosMaye');
        const texto = document.getElementById('textoToggleHorario');
        const icono = document.getElementById('iconoToggleHorario');

        if (!seccion) return;

        if (seccion.style.display === 'none') {
            seccion.style.display = 'block';
            if (texto) texto.textContent = 'Ocultar Horarios';
            if (icono) {
                icono.classList.remove('bi-eye');
                icono.classList.add('bi-eye-slash');
            }
        } else {
            seccion.style.display = 'none';
            if (texto) texto.textContent = 'Ver Horarios';
            if (icono) {
                icono.classList.remove('bi-eye-slash');
                icono.classList.add('bi-eye');
            }
        }
    }

    // Buscador interactivo en la tabla de citas
    document.addEventListener('DOMContentLoaded', () => {
        const buscador = document.getElementById('buscadorAdmin');
        const tabla = document.getElementById('tablaCitasAdmin');
        if (buscador && tabla) {
            buscador.addEventListener('keyup', () => {
                const term = buscador.value.toLowerCase();
                const filas = tabla.querySelectorAll('tbody tr');
                filas.forEach(fila => {
                    const textoFila = fila.textContent.toLowerCase();
                    fila.style.display = textoFila.includes(term) ? '' : 'none';
                });
            });
        }
    });
</script>