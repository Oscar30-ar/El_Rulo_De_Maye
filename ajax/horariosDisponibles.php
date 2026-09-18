<?php
require_once "../modelo/conexion.php";
require_once "../modelo/usuarioModelo.php";

if (isset($_GET["fecha"]) && isset($_GET["servicio_id"])) {
    $fecha = $_GET["fecha"];
    $servicioId = (int)$_GET["servicio_id"];
    $diaSemana = date('N', strtotime($fecha));

    // 1. Obtener horario laboral de Maye para este día
    $horario = UsuarioModelo::mdlObtenerHorarioDia($diaSemana);
    if (!$horario || $horario["activo"] == 0) {
        echo json_encode(["status" => "cerrado", "mensaje" => "Maye no atiende en el día seleccionado."]);
        exit();
    }

    // 2. Obtener la duración del servicio que el cliente quiere agendar
    $stmtServicio = Conexion::conectar()->prepare("SELECT duracion_minutos FROM servicios WHERE id = :id LIMIT 1");
    $stmtServicio->bindParam(":id", $servicioId, PDO::PARAM_INT);
    $stmtServicio->execute();
    $servicioSeleccionado = $stmtServicio->fetch();
    $duracionNuevo = $servicioSeleccionado ? (int)$servicioSeleccionado["duracion_minutos"] : 60;

    // 3. Obtener todas las citas activas del día con sus horas y duraciones
    $citasDelDia = UsuarioModelo::mdlObtenerCitasConDuracion($fecha);

    $horaApertura = strtotime($horario["hora_apertura"]);
    $horaCierre = strtotime($horario["hora_cierre"]);
    $horasDisponibles = [];

    // Evaluamos intervalos cada 30 minutos
    $intervalo = 30 * 60; 

    for ($tiempo = $horaApertura; $tiempo < $horaCierre; $tiempo += $intervalo) {
        $nuevoInicio = $tiempo;
        $nuevoFin = $nuevoInicio + ($duracionNuevo * 60);

        // Si el servicio excede la hora de cierre de Maye, no se puede agendar
        if ($nuevoFin > $horaCierre) {
            continue;
        }

        $ocupado = false;
        foreach ($citasDelDia as $c) {
            $existenteInicio = strtotime($c["hora"]);
            $existenteFin = $existenteInicio + ((int)$c["duracion_minutos"] * 60);

            // Condición de traslape
            if ($nuevoInicio < $existenteFin && $nuevoFin > $existenteInicio) {
                $ocupado = true;
                break;
            }
        }

        $horasDisponibles[] = [
            "valor" => date("H:i:00", $tiempo),
            "texto" => date("h:i A", $tiempo),
            "ocupado" => $ocupado
        ];
    }

    echo json_encode(["status" => "ok", "horas" => $horasDisponibles]);
    exit();
}