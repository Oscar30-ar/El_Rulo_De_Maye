<?php
session_start();
require_once "controlador/plantillaControlador.php";
require_once "controlador/usuariosControlador.php";
require_once "modelo/usuarioModelo.php";

$plantilla = new PlantillaControlador();
$plantilla->ctrPlantilla();