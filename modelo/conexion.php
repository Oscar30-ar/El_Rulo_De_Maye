<?php

class Conexion
{
    public static function conectar()
    {
        $nombreServidor = "localhost";
        $usuariosServidor = "root";
        $baseDatos = "el_rulo_de_maye";
        $password = "";

        try {
            $conexion = new PDO(
                'mysql:host=' . $nombreServidor . ';dbname=' . $baseDatos . ';charset=utf8mb4',
                $usuariosServidor,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ]
            );
            return $conexion;
        } catch (Exception $e) {
            die("Error en la conexión: " . $e->getMessage());
        }
    }
}
