<?php
require_once "conexion.php";

class UsuarioModelo {

    // ==========================================
    // USUARIOS Y AUTENTICACIÓN
    // ==========================================
    public static function mdlRegistroUsuario($datos) {
        $stmt = Conexion::conectar()->prepare("INSERT INTO usuarios(nombre, email, telefono, password, rol) VALUES (:nombre, :email, :telefono, :password, 'cliente')");
        $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
        $stmt->bindParam(":email", $datos["email"], PDO::PARAM_STR);
        $stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
        $stmt->bindParam(":password", $datos["password"], PDO::PARAM_STR);
        return $stmt->execute() ? "ok" : "error";
    }

    public static function mdlMostrarUsuario($item, $valor) {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM usuarios WHERE $item = :valor LIMIT 1");
        $stmt->bindParam(":valor", $valor, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function mdlGuardarTokenRecuperacion($id, $token, $expiracion) {
        $stmt = Conexion::conectar()->prepare("UPDATE usuarios SET token_recuperacion = :token, token_expiracion = :exp WHERE id = :id");
        $stmt->bindParam(":token", $token, PDO::PARAM_STR);
        $stmt->bindParam(":exp", $expiracion, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function mdlActualizarPassword($id, $passwordHash) {
        $stmt = Conexion::conectar()->prepare("UPDATE usuarios SET password = :p, token_recuperacion = NULL, token_expiracion = NULL WHERE id = :id");
        $stmt->bindParam(":p", $passwordHash, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function mdlActualizarPasswordPerfil($id, $passwordHash) {
        try {
            $stmt = Conexion::conectar()->prepare("UPDATE usuarios SET password = :password WHERE id = :id");
            $stmt->bindParam(":password", $passwordHash, PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            return $stmt->execute() ? "ok" : "error";
        } catch (PDOException $e) {
            return "error: " . $e->getMessage();
        }
    }

    public static function mdlActualizarPerfil($id, $nombre, $telefono, $foto = null) {
        if ($foto) {
            $stmt = Conexion::conectar()->prepare("UPDATE usuarios SET nombre = :n, telefono = :t, foto = :f WHERE id = :id");
            $stmt->bindParam(":f", $foto, PDO::PARAM_STR);
        } else {
            $stmt = Conexion::conectar()->prepare("UPDATE usuarios SET nombre = :n, telefono = :t WHERE id = :id");
        }
        $stmt->bindParam(":n", $nombre, PDO::PARAM_STR);
        $stmt->bindParam(":t", $telefono, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ==========================================
    // SERVICIOS
    // ==========================================
    public static function mdlObtenerServicios() {
        $stmt = Conexion::conectar()->prepare("SELECT s.*, c.nombre AS categoria, c.estado AS categoria_estado FROM servicios s JOIN categorias c ON s.categoria_id = c.id ORDER BY c.id ASC, s.id ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ==========================================
    // CITAS Y DISPONIBILIDAD
    // ==========================================
    public static function mdlVerificarDisponibilidad($fecha, $hora) {
        $stmt = Conexion::conectar()->prepare("SELECT id FROM citas WHERE fecha = :fecha AND hora = :hora AND estado != 'cancelada' LIMIT 1");
        $stmt->bindParam(":fecha", $fecha, PDO::PARAM_STR);
        $stmt->bindParam(":hora", $hora, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function mdlCrearCita($datos) {
        $stmt = Conexion::conectar()->prepare("INSERT INTO citas (usuario_id, servicio_id, fecha, hora, estado, pago_estado, referencia_pago, notas) VALUES (:usuario_id, :servicio_id, :fecha, :hora, 'pendiente', :pago_estado, :referencia_pago, :notas)");
        $stmt->bindParam(":usuario_id", $datos["usuario_id"], PDO::PARAM_INT);
        $stmt->bindParam(":servicio_id", $datos["servicio_id"], PDO::PARAM_INT);
        $stmt->bindParam(":fecha", $datos["fecha"], PDO::PARAM_STR);
        $stmt->bindParam(":hora", $datos["hora"], PDO::PARAM_STR);
        $stmt->bindParam(":pago_estado", $datos["pago_estado"], PDO::PARAM_STR);
        $stmt->bindParam(":referencia_pago", $datos["referencia_pago"], PDO::PARAM_STR);
        $stmt->bindParam(":notas", $datos["notas"], PDO::PARAM_STR);
        return $stmt->execute() ? "ok" : "error";
    }

    public static function mdlListarCitas($usuario_id = null) {
        if ($usuario_id) {
            $stmt = Conexion::conectar()->prepare("SELECT c.*, s.nombre AS servicio, s.precio FROM citas c JOIN servicios s ON c.servicio_id = s.id WHERE c.usuario_id = :id ORDER BY c.fecha DESC, c.hora DESC");
            $stmt->bindParam(":id", $usuario_id, PDO::PARAM_INT);
        } else {
            $stmt = Conexion::conectar()->prepare("SELECT c.*, u.nombre AS cliente, u.telefono, s.nombre AS servicio, s.precio FROM citas c JOIN usuarios u ON c.usuario_id = u.id JOIN servicios s ON c.servicio_id = s.id ORDER BY c.fecha ASC, c.hora ASC");
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function mdlActualizarEstadoCita($id, $estado) {
        $stmt = Conexion::conectar()->prepare("UPDATE citas SET estado = :estado WHERE id = :id");
        $stmt->bindParam(":estado", $estado, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute() ? "ok" : "error";
    }

    public static function mdlObtenerHorasOcupadas($fecha) {
        $stmt = Conexion::conectar()->prepare("SELECT hora FROM citas WHERE fecha = :fecha AND estado != 'cancelada'");
        $stmt->bindParam(":fecha", $fecha, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // ==========================================
    // HORARIOS DE MAYE
    // ==========================================
    public static function mdlObtenerHorarioDia($diaSemana) {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM horarios_atencion WHERE dia_semana = :dia LIMIT 1");
        $stmt->bindParam(":dia", $diaSemana, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function mdlObtenerTodosHorarios() {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM horarios_atencion ORDER BY dia_semana ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function mdlActualizarHorarioMaye($dia, $apertura, $cierre, $activo) {
        $stmt = Conexion::conectar()->prepare("UPDATE horarios_atencion SET hora_apertura = :a, hora_cierre = :c, activo = :act WHERE dia_semana = :dia");
        $stmt->bindParam(":a", $apertura, PDO::PARAM_STR);
        $stmt->bindParam(":c", $cierre, PDO::PARAM_STR);
        $stmt->bindParam(":act", $activo, PDO::PARAM_INT);
        $stmt->bindParam(":dia", $dia, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function mdlActualizarFotoPerfil($id, $fotoRuta) {
    try {
        $stmt = Conexion::conectar()->prepare("UPDATE usuarios SET foto = :foto WHERE id = :id");
        $stmt->bindParam(":foto", $fotoRuta, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute() ? "ok" : "error";
    } catch (PDOException $e) {
        return "error: " . $e->getMessage();
    }
}
}