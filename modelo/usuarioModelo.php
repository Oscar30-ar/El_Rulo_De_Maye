<?php
require_once "conexion.php";

class UsuarioModelo
{

    // ==========================================
    // USUARIOS Y AUTENTICACIÓN
    // ==========================================
    public static function mdlRegistroUsuario($datos)
    {
        $stmt = Conexion::conectar()->prepare("INSERT INTO usuarios(nombre, email, telefono, password, rol) VALUES (:nombre, :email, :telefono, :password, 'cliente')");
        $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
        $stmt->bindParam(":email", $datos["email"], PDO::PARAM_STR);
        $stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
        $stmt->bindParam(":password", $datos["password"], PDO::PARAM_STR);
        return $stmt->execute() ? "ok" : "error";
    }

    public static function mdlMostrarUsuario($item, $valor)
    {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM usuarios WHERE $item = :valor LIMIT 1");
        $stmt->bindParam(":valor", $valor, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function mdlGuardarTokenRecuperacion($id, $token, $expiracion)
    {
        $stmt = Conexion::conectar()->prepare("UPDATE usuarios SET token_recuperacion = :token, token_expiracion = :exp WHERE id = :id");
        $stmt->bindParam(":token", $token, PDO::PARAM_STR);
        $stmt->bindParam(":exp", $expiracion, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function mdlActualizarPassword($id, $passwordHash)
    {
        $stmt = Conexion::conectar()->prepare("UPDATE usuarios SET password = :p, token_recuperacion = NULL, token_expiracion = NULL WHERE id = :id");
        $stmt->bindParam(":p", $passwordHash, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function mdlActualizarPasswordPerfil($id, $passwordHash)
    {
        try {
            $stmt = Conexion::conectar()->prepare("UPDATE usuarios SET password = :password WHERE id = :id");
            $stmt->bindParam(":password", $passwordHash, PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            return $stmt->execute() ? "ok" : "error";
        } catch (PDOException $e) {
            return "error: " . $e->getMessage();
        }
    }

    public static function mdlActualizarPerfil($id, $nombre, $email, $telefono)
    {
        $stmt = Conexion::conectar()->prepare("
            UPDATE usuarios 
            SET nombre = :nombre, email = :email, telefono = :telefono 
            WHERE id = :id
        ");
        $stmt->bindParam(":nombre", $nombre, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":telefono", $telefono, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute() ? "ok" : "error";
    }


    // ==========================================
    // SERVICIOS
    // ==========================================
    public static function mdlObtenerServicios()
    {
        $stmt = Conexion::conectar()->prepare("SELECT s.*, c.nombre AS categoria, c.estado AS categoria_estado FROM servicios s JOIN categorias c ON s.categoria_id = c.id ORDER BY c.id ASC, s.id ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ==========================================
    // CITAS Y DISPONIBILIDAD
    // ==========================================
    public static function mdlObtenerCitasConDuracion($fecha)
    {
        // Solo consideramos citas pendientes o confirmadas.
        // Ignoramos las que ya fueron canceladas o finalizadas.
        $stmt = Conexion::conectar()->prepare("
        SELECT c.hora, s.duracion_minutos 
        FROM citas c
        INNER JOIN servicios s ON c.servicio_id = s.id
        WHERE c.fecha = :fecha 
          AND c.estado NOT IN ('cancelada', 'finalizada')
    ");
        $stmt->bindParam(":fecha", $fecha, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function mdlVerificarDisponibilidad($fecha, $hora, $duracionNuevoServicio)
    {
        $citas = self::mdlObtenerCitasConDuracion($fecha);
        $nuevoInicio = strtotime($hora);
        $nuevoFin = $nuevoInicio + ($duracionNuevoServicio * 60);

        foreach ($citas as $c) {
            $existenteInicio = strtotime($c["hora"]);
            $existenteFin = $existenteInicio + ($c["duracion_minutos"] * 60);

            // Se solapan si el nuevo empieza antes de que termine el existente Y termina después de que empieza el existente
            if ($nuevoInicio < $existenteFin && $nuevoFin > $existenteInicio) {
                return true; // Está ocupado
            }
        }
        return false; // Libre
    }

    public static function mdlCrearCita($datos)
    {
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

    public static function mdlListarCitas($usuario_id = null)
    {
        if ($usuario_id) {
            $stmt = Conexion::conectar()->prepare("
            SELECT c.*, s.nombre AS servicio, s.precio, s.duracion_minutos 
            FROM citas c 
            JOIN servicios s ON c.servicio_id = s.id 
            WHERE c.usuario_id = :id 
            ORDER BY c.fecha DESC, c.hora DESC
        ");
            $stmt->bindParam(":id", $usuario_id, PDO::PARAM_INT);
        } else {
            $stmt = Conexion::conectar()->prepare("
            SELECT c.*, u.nombre AS cliente, u.telefono, s.nombre AS servicio, s.precio, s.duracion_minutos 
            FROM citas c 
            JOIN usuarios u ON c.usuario_id = u.id 
            JOIN servicios s ON c.servicio_id = s.id 
            ORDER BY c.fecha ASC, c.hora ASC
        ");
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function mdlActualizarEstadoCita($id, $estado)
    {
        $stmt = Conexion::conectar()->prepare("UPDATE citas SET estado = :estado WHERE id = :id");
        $stmt->bindParam(":estado", $estado, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute() ? "ok" : "error";
    }

    public static function mdlObtenerHorasOcupadas($fecha)
    {
        $stmt = Conexion::conectar()->prepare("SELECT hora FROM citas WHERE fecha = :fecha AND estado != 'cancelada'");
        $stmt->bindParam(":fecha", $fecha, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // ==========================================
    // HORARIOS DE MAYE
    // ==========================================
    public static function mdlObtenerHorarioDia($diaSemana)
    {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM horarios_atencion WHERE dia_semana = :dia LIMIT 1");
        $stmt->bindParam(":dia", $diaSemana, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function mdlObtenerTodosHorarios()
    {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM horarios_atencion ORDER BY dia_semana ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function mdlActualizarHorarioMaye($dia, $apertura, $cierre, $activo)
    {
        $stmt = Conexion::conectar()->prepare("UPDATE horarios_atencion SET hora_apertura = :a, hora_cierre = :c, activo = :act WHERE dia_semana = :dia");
        $stmt->bindParam(":a", $apertura, PDO::PARAM_STR);
        $stmt->bindParam(":c", $cierre, PDO::PARAM_STR);
        $stmt->bindParam(":act", $activo, PDO::PARAM_INT);
        $stmt->bindParam(":dia", $dia, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function mdlActualizarFotoPerfil($id, $fotoRuta)
    {
        try {
            $stmt = Conexion::conectar()->prepare("UPDATE usuarios SET foto = :foto WHERE id = :id");
            $stmt->bindParam(":foto", $fotoRuta, PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            return $stmt->execute() ? "ok" : "error";
        } catch (PDOException $e) {
            return "error: " . $e->getMessage();
        }
    }

    public static function mdlActualizarPagoCita($id, $pagoEstado)
    {
        try {
            $stmt = Conexion::conectar()->prepare("UPDATE citas SET pago_estado = :pago_estado WHERE id = :id");
            $stmt->bindParam(":pago_estado", $pagoEstado, PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            return $stmt->execute() ? "ok" : "error";
        } catch (PDOException $e) {
            return "error: " . $e->getMessage();
        }
    }

    // ==========================================
    // GESTIÓN TOTAL: CATEGORÍAS
    // ==========================================
    public static function mdlListarCategorias()
    {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM categorias ORDER BY id ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function mdlCrearCategoria($nombre, $estado)
    {
        $stmt = Conexion::conectar()->prepare("INSERT INTO categorias (nombre, estado) VALUES (:n, :e)");
        $stmt->bindParam(":n", $nombre, PDO::PARAM_STR);
        $stmt->bindParam(":e", $estado, PDO::PARAM_STR);
        return $stmt->execute() ? "ok" : "error";
    }

    public static function mdlEliminarCategoria($id)
    {
        $stmt = Conexion::conectar()->prepare("DELETE FROM categorias WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute() ? "ok" : "error";
    }

    // ==========================================
    // GESTIÓN TOTAL: SERVICIOS
    // ==========================================
    public static function mdlCrearServicio($datos)
    {
        $stmt = Conexion::conectar()->prepare("INSERT INTO servicios (categoria_id, nombre, descripcion, precio, duracion_minutos, estado) VALUES (:cat, :nom, :desc, :prec, :dur, :est)");
        $stmt->bindParam(":cat", $datos["categoria_id"], PDO::PARAM_INT);
        $stmt->bindParam(":nom", $datos["nombre"], PDO::PARAM_STR);
        $stmt->bindParam(":desc", $datos["descripcion"], PDO::PARAM_STR);
        $stmt->bindParam(":prec", $datos["precio"], PDO::PARAM_STR);
        $stmt->bindParam(":dur", $datos["duracion_minutos"], PDO::PARAM_INT);
        $stmt->bindParam(":est", $datos["estado"], PDO::PARAM_STR);
        return $stmt->execute() ? "ok" : "error";
    }

    public static function mdlActualizarServicio($datos)
    {
        $stmt = Conexion::conectar()->prepare("UPDATE servicios SET categoria_id = :cat, nombre = :nom, descripcion = :desc, precio = :prec, duracion_minutos = :dur, estado = :est WHERE id = :id");
        $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);
        $stmt->bindParam(":cat", $datos["categoria_id"], PDO::PARAM_INT);
        $stmt->bindParam(":nom", $datos["nombre"], PDO::PARAM_STR);
        $stmt->bindParam(":desc", $datos["descripcion"], PDO::PARAM_STR);
        $stmt->bindParam(":prec", $datos["precio"], PDO::PARAM_STR);
        $stmt->bindParam(":dur", $datos["duracion_minutos"], PDO::PARAM_INT);
        $stmt->bindParam(":est", $datos["estado"], PDO::PARAM_STR);
        return $stmt->execute() ? "ok" : "error";
    }

    public static function mdlEliminarServicio($id)
    {
        $stmt = Conexion::conectar()->prepare("DELETE FROM servicios WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute() ? "ok" : "error";
    }

    // ==========================================
    // GESTIÓN TOTAL: USUARIOS
    // ==========================================
    public static function mdlListarUsuarios()
    {
        $stmt = Conexion::conectar()->prepare("SELECT id, nombre, email, telefono, rol, foto, fecha_registro FROM usuarios ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function mdlCambiarRolUsuario($id, $rol)
    {
        $stmt = Conexion::conectar()->prepare("UPDATE usuarios SET rol = :rol WHERE id = :id");
        $stmt->bindParam(":rol", $rol, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute() ? "ok" : "error";
    }

    public static function mdlEliminarUsuario($id)
    {
        $stmt = Conexion::conectar()->prepare("DELETE FROM usuarios WHERE id = :id AND rol != 'admin'");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute() ? "ok" : "error";
    }

    public static function mdlEditarUsuarioAdmin($id, $nombre, $telefono, $rol)
    {
        try {
            $stmt = Conexion::conectar()->prepare("UPDATE usuarios SET nombre = :n, telefono = :t, rol = :r WHERE id = :id");
            $stmt->bindParam(":n", $nombre, PDO::PARAM_STR);
            $stmt->bindParam(":t", $telefono, PDO::PARAM_STR);
            $stmt->bindParam(":r", $rol, PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            return $stmt->execute() ? "ok" : "error";
        } catch (PDOException $e) {
            return "error: " . $e->getMessage();
        }
    }
    // ==========================================
    // IDEA 3: FIDELIZACIÓN (CITAS FINALIZADAS)
    // ==========================================
    public static function mdlContarCitasFinalizadasUsuario($usuarioId)
    {
        $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) FROM citas WHERE usuario_id = :uid AND estado = 'finalizada'");
        $stmt->bindParam(":uid", $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
    public static function mdlObtenerConfigFidelizacion()
    {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM configuracion_fidelizacion WHERE id = 1 LIMIT 1");
        $stmt->execute();
        $config = $stmt->fetch();
        if (!$config) {
            return [
                "meta_visitas" => 5,
                "premio_texto" => "20% de descuento o exfoliación spa gratis",
                "porcentaje_descuento" => 20
            ];
        }
        return $config;
    }

    public static function mdlActualizarConfigFidelizacion($meta, $premio, $descuento)
    {
        try {
            $stmt = Conexion::conectar()->prepare("UPDATE configuracion_fidelizacion SET meta_visitas = :m, premio_texto = :p, porcentaje_descuento = :d WHERE id = 1");
            $stmt->bindParam(":m", $meta, PDO::PARAM_INT);
            $stmt->bindParam(":p", $premio, PDO::PARAM_STR);
            $stmt->bindParam(":d", $descuento, PDO::PARAM_INT);
            return $stmt->execute() ? "ok" : "error";
        } catch (PDOException $e) {
            return "error: " . $e->getMessage();
        }
    }
    // ==========================================
    // IDEA 1: GALERÍA ANTES Y DESPUÉS
    // ==========================================
    public static function mdlListarGaleriaConCategoria($categoriaId = null)
    {
        if ($categoriaId) {
            $stmt = Conexion::conectar()->prepare("
            SELECT g.*, c.nombre AS categoria 
            FROM galeria g 
            LEFT JOIN categorias c ON g.categoria_id = c.id 
            WHERE g.categoria_id = :cid 
            ORDER BY g.id DESC
        ");
            $stmt->bindParam(":cid", $categoriaId, PDO::PARAM_INT);
        } else {
            $stmt = Conexion::conectar()->prepare("
            SELECT g.*, c.nombre AS categoria 
            FROM galeria g 
            LEFT JOIN categorias c ON g.categoria_id = c.id 
            ORDER BY g.id DESC
        ");
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function mdlCrearGaleria($datos)
    {
        $stmt = Conexion::conectar()->prepare("
        INSERT INTO galeria (titulo, categoria_id, descripcion, foto_antes, foto_despues, estado) 
        VALUES (:t, :cat, :d, :fa, :fd, :e)
    ");
        $stmt->bindParam(":t", $datos["titulo"], PDO::PARAM_STR);
        $stmt->bindParam(":cat", $datos["categoria_id"], PDO::PARAM_INT);
        $stmt->bindParam(":d", $datos["descripcion"], PDO::PARAM_STR);
        $stmt->bindParam(":fa", $datos["foto_antes"], PDO::PARAM_STR);
        $stmt->bindParam(":fd", $datos["foto_despues"], PDO::PARAM_STR);
        $stmt->bindParam(":e", $datos["estado"], PDO::PARAM_STR);
        return $stmt->execute() ? "ok" : "error";
    }

    public static function mdlEliminarGaleria($id)
    {
        $stmt = Conexion::conectar()->prepare("SELECT foto_antes, foto_despues FROM galeria WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $item = $stmt->fetch();
        if ($item) {
            if (file_exists($item["foto_antes"])) unlink($item["foto_antes"]);
            if (file_exists($item["foto_despues"])) unlink($item["foto_despues"]);
        }
        $del = Conexion::conectar()->prepare("DELETE FROM galeria WHERE id = :id");
        $del->bindParam(":id", $id, PDO::PARAM_INT);
        return $del->execute() ? "ok" : "error";
    }

    // ==========================================
    // IDEA 5: RESEÑAS Y TESTIMONIOS MODERABLES
    // ==========================================
    public static function mdlCrearResena($usuarioId, $calificacion, $comentario)
    {
        $stmt = Conexion::conectar()->prepare("INSERT INTO resenas (usuario_id, calificacion, comentario, estado) VALUES (:u, :c, :com, 'pendiente')");
        $stmt->bindParam(":u", $usuarioId, PDO::PARAM_INT);
        $stmt->bindParam(":c", $calificacion, PDO::PARAM_INT);
        $stmt->bindParam(":com", $comentario, PDO::PARAM_STR);
        return $stmt->execute() ? "ok" : "error";
    }

    public static function mdlListarResenasAprobadas()
    {
        $stmt = Conexion::conectar()->prepare("
        SELECT r.*, u.nombre, u.foto 
        FROM resenas r 
        JOIN usuarios u ON r.usuario_id = u.id 
        WHERE r.estado = 'aprobada' 
        ORDER BY r.fecha_creacion DESC LIMIT 6
    ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function mdlListarTodasResenas()
    {
        $stmt = Conexion::conectar()->prepare("
        SELECT r.*, u.nombre, u.email 
        FROM resenas r 
        JOIN usuarios u ON r.usuario_id = u.id 
        ORDER BY r.fecha_creacion DESC
    ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function mdlCambiarEstadoResena($id, $estado)
    {
        $stmt = Conexion::conectar()->prepare("UPDATE resenas SET estado = :e WHERE id = :id");
        $stmt->bindParam(":e", $estado, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute() ? "ok" : "error";
    }

    // ==========================================
    // HISTORIAS ESTILO INSTAGRAM (24 HORAS)
    // ==========================================
    public static function mdlListarHistoriasActivas()
    {
        $stmt = Conexion::conectar()->prepare("
            SELECT h.*, 
                   (SELECT COUNT(*) FROM historia_reacciones r WHERE r.historia_id = h.id) as total_reacciones
            FROM historias h 
            WHERE h.fecha_publicacion >= NOW() - INTERVAL 24 HOUR 
            ORDER BY h.fecha_publicacion ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function mdlCrearHistoria($archivo, $tipo, $pieFoto)
    {
        $stmt = Conexion::conectar()->prepare("
            INSERT INTO historias (archivo, tipo, pie_foto) 
            VALUES (:archivo, :tipo, :pie)
        ");
        $stmt->bindParam(":archivo", $archivo, PDO::PARAM_STR);
        $stmt->bindParam(":tipo", $tipo, PDO::PARAM_STR);
        $stmt->bindParam(":pie", $pieFoto, PDO::PARAM_STR);
        return $stmt->execute() ? "ok" : "error";
    }

    public static function mdlReaccionarHistoria($historiaId, $usuarioId, $reaccion)
    {
        $stmt = Conexion::conectar()->prepare("
            INSERT INTO historia_reacciones (historia_id, usuario_id, reaccion) 
            VALUES (:h, :u, :r)
        ");
        $stmt->bindParam(":h", $historiaId, PDO::PARAM_INT);
        $stmt->bindParam(":u", $usuarioId, PDO::PARAM_INT);
        $stmt->bindParam(":r", $reaccion, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // ==========================================
    // LOOKBOOK / TENDENCIAS
    // ==========================================
    public static function mdlListarTendencias()
    {
        $stmt = Conexion::conectar()->prepare("
            SELECT t.*, s.nombre as servicio_nombre 
            FROM tendencias t 
            LEFT JOIN servicios s ON t.servicio_id = s.id 
            WHERE t.estado = 'visible' 
            ORDER BY t.id DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function mdlCrearTendencia($datos)
    {
        $stmt = Conexion::conectar()->prepare("
            INSERT INTO tendencias (titulo, etiquetas, foto, servicio_id, estado) 
            VALUES (:titulo, :etiquetas, :foto, :servicio_id, 'visible')
        ");
        $stmt->bindParam(":titulo", $datos["titulo"], PDO::PARAM_STR);
        $stmt->bindParam(":etiquetas", $datos["etiquetas"], PDO::PARAM_STR);
        $stmt->bindParam(":foto", $datos["foto"], PDO::PARAM_STR);
        $stmt->bindParam(":servicio_id", $datos["servicio_id"], PDO::PARAM_INT);
        return $stmt->execute() ? "ok" : "error";
    }

    public static function mdlObtenerDetalleReaccionesHistoria($historiaId) {
    $stmt = Conexion::conectar()->prepare("
        SELECT r.id, r.usuario_id, r.reaccion, r.fecha, 
               COALESCE(u.nombre, 'Visitante anónima') AS cliente,
               u.foto, u.email, u.telefono
        FROM historia_reacciones r
        LEFT JOIN usuarios u ON r.usuario_id = u.id
        WHERE r.historia_id = :hid
        ORDER BY r.fecha DESC
    ");
    $stmt->bindParam(":hid", $historiaId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
