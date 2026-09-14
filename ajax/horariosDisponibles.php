<?php
require_once "../modelo/conexion.php";
require_once "../modelo/usuarioModelo.php";

if (isset($_GET["fecha"])) {
    $fecha = $_GET["fecha"];
    $diaSemana = date('N', strtotime($fecha)); // 1 (Lunes) a 7 (Domingo)

    $horario = UsuarioModelo::mdlObtenerHorarioDia($diaSemana);

    if (!$horario || $horario["activo"] == 0) {
        echo json_encode(["status" => "cerrado", "mensaje" => "Maye no atiende en el día seleccionado."]);
        exit();
    }

    $horasOcupadas = UsuarioModelo::mdlObtenerHorasOcupadas($fecha);

    $inicio = strtotime($horario["hora_apertura"]);
    $fin = strtotime($horario["hora_cierre"]);
    $horasDisponibles = [];

    // Generar tramos de 1 hora
    while ($inicio < $fin) {
        $horaFormato = date("H:i:00", $inicio);
        $horaTexto = date("h:i A", $inicio);

        $ocupado = in_array($horaFormato, $horasOcupadas);

        $horasDisponibles[] = [
            "valor" => $horaFormato,
            "texto" => $horaTexto,
            "ocupado" => $ocupado
        ];

        $inicio = strtotime("+60 minutes", $inicio);
    }

    echo json_encode(["status" => "ok", "horas" => $horasDisponibles]);
    exit();
}