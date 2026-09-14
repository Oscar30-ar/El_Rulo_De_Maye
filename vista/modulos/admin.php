<?php
if (!isset($_SESSION["iniciarSesion"]) || $_SESSION["rol"] !== "admin") {
    echo '<script>window.location = "index.php?ruta=login";</script>';
    exit();
}

// Guardar Horarios de Maye
if (isset($_POST["actualizarHorarios"])) {
    foreach ($_POST["dias"] as $dia => $datos) {
        $activo = isset($datos["activo"]) ? 1 : 0;
        $apertura = $datos["apertura"];
        $cierre = $datos["cierre"];
        UsuarioModelo::mdlActualizarHorarioMaye($dia, $apertura, $cierre, $activo);
    }
    echo '<div class="alert alert-success custom-alert">¡Tus horarios han sido actualizados con éxito, Maye!</div>';
}

// Acciones sobre citas
if (isset($_GET["accion"]) && isset($_GET["id_cita"])) {
    UsuarioModelo::mdlActualizarEstadoCita($_GET["id_cita"], $_GET["accion"]);
    echo '<script>window.location = "index.php?ruta=admin";</script>';
}

$todasLasCitas = UsuarioModelo::mdlListarCitas();
$horarios = UsuarioModelo::mdlObtenerTodosHorarios();
$nombresDias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
?>

<div class="container my-4">
    <!-- Panel para que Maye elija su horario -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <h4 class="font-playfair mb-3">⚙️ Configurar Mis Horarios de Atención</h4>
        <p class="text-muted small">Define tus horas de inicio y fin para cada día, o activa el descanso para que nadie pueda agendar.</p>

        <form method="POST">
            <input type="hidden" name="actualizarHorarios" value="1">
            <div class="table-responsive">
                <table class="table table-borderless align-middle">
                    <thead>
                        <tr class="table-light small text-uppercase">
                            <th>Día</th>
                            <th>¿Atenderás este día?</th>
                            <th>Hora Apertura</th>
                            <th>Hora Cierre</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($horarios as $h): ?>
                            <tr>
                                <td class="fw-bold"><?= $nombresDias[$h["dia_semana"]] ?></td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="dias[<?= $h["dia_semana"] ?>][activo]" value="1" <?= $h["activo"] ? 'checked' : '' ?>>
                                        <label class="form-check-label small"><?= $h["activo"] ? 'Disponible' : 'Descanso' ?></label>
                                    </div>
                                </td>
                                <td>
                                    <input type="time" class="form-control form-control-sm" name="dias[<?= $h["dia_semana"] ?>][apertura]" value="<?= $h["hora_apertura"] ?>">
                                </td>
                                <td>
                                    <input type="time" class="form-control form-control-sm" name="dias[<?= $h["dia_semana"] ?>][cierre]" value="<?= $h["hora_cierre"] ?>">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <button type="submit" class="btn-primary-custom px-4 py-2 mt-2">Guardar Mis Horarios</button>
        </form>
    </div>

    <!-- Gestión de Citas Globales -->
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
        <h4 class="font-playfair mb-3">📋 Listado de Citas Agendadas</h4>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Cliente</th>
                        <th>Teléfono</th>
                        <th>Servicio</th>
                        <th>Fecha & Hora</th>
                        <th>Estado</th>
                        <th>Pago</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($todasLasCitas)): ?>
                        <tr><td colspan="7" class="text-center py-4">No hay reservas registradas.</td></tr>
                    <?php else: ?>
                        <?php foreach ($todasLasCitas as $c): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($c["cliente"]) ?></strong></td>
                                <td><?= htmlspecialchars($c["telefono"] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($c["servicio"]) ?></td>
                                <td><?= $c["fecha"] ?> - <?= substr($c["hora"], 0, 5) ?></td>
                                <td>
                                    <?php if ($c['estado'] === 'confirmada'): ?>
                                        <span class="badge bg-success-subtle text-success">Confirmada</span>
                                    <?php elseif ($c['estado'] === 'cancelada'): ?>
                                        <span class="badge bg-danger-subtle text-danger">Cancelada</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning">Pendiente</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= ucfirst($c["pago_estado"]) ?></td>
                                <td>
                                    <a href="index.php?ruta=admin&accion=confirmada&id_cita=<?= $c['id'] ?>" class="btn btn-sm btn-outline-success me-1" title="Aprobar"><i class="bi bi-check-lg"></i></a>
                                    <a href="index.php?ruta=admin&accion=cancelada&id_cita=<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger" title="Cancelar"><i class="bi bi-x-lg"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>