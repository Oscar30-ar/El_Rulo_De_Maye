<?php
// Procesar actualización de horario de Maye
if (isset($_POST["actualizarHorarios"])) {
    foreach ($_POST["dias"] as $dia => $datos) {
        $activo = isset($datos["activo"]) ? 1 : 0;
        $apertura = $datos["apertura"];
        $cierre = $datos["cierre"];
        UsuarioModelo::mdlActualizarHorarioMaye($dia, $apertura, $cierre, $activo);
    }
    echo '<div class="alert alert-success custom-alert">¡Tus horarios han sido actualizados con éxito, Maye!</div>';
}

$horarios = UsuarioModelo::mdlObtenerTodosHorarios();
$nombresDias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
?>

<div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
    <h4 class="font-playfair mb-3">⚙️ Configuración de Mi Horario de Atención</h4>
    <p class="text-muted small">Configura la hora en que inicias, terminas o los días que tomas libres.</p>

    <form method="POST">
        <input type="hidden" name="actualizarHorarios" value="1">
        <div class="table-responsive">
            <table class="table table-borderless align-middle">
                <thead>
                    <tr class="table-light small text-uppercase">
                        <th>Día</th>
                        <th>¿Atiendes este día?</th>
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
        <button type="submit" class="btn-primary-custom px-4 py-2 mt-2">Guardar Mi Horario</button>
    </form>
</div>