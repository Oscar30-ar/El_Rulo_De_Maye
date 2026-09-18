<?php
session_start();
require_once "../modelo/conexion.php";
require_once "../modelo/usuarioModelo.php";

if (isset($_POST["historia_id"]) && isset($_POST["emoji"])) {
    $historiaId = (int)$_POST["historia_id"];
    $emoji = $_POST["emoji"];
    $usuarioId = $_SESSION["id"] ?? null;

    UsuarioModelo::mdlReaccionarHistoria($historiaId, $usuarioId, $emoji);
    echo json_encode(["status" => "ok"]);
    exit();
}