<?php
require_once "../modelo/conexion.php";
require_once "../modelo/usuarioModelo.php";

if (isset($_GET["fecha"])) {
    $fecha = $_GET["fecha"];
    $diaSemana = date('N', strtotime($fecha)); // 1 a 7

    $horario = UsuarioModelo::mdlObtenerHorarioDia($diaSemana);

    if (!$horario || $horario["activo"] == 0) {
        echo json_encode(["status" => "cerrado", "mensaje" => "Maye no atiende en este día."]);
        exit();
    }

    // Obtener horas ocupadas en esa fecha
    $horasOcupadas = UsuarioModelo::mdlObtenerHorasOcupadas($fecha);

    $inicio = strtotime($horario["hora_apertura"]);
    $fin = strtotime($horario["hora_cierre"]);
    $horasDisponibles = [];

    // Tramos de 1 hora
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