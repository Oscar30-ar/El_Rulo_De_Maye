<?php
class ControladorUsuarios
{

    // 5.2. Registro con validación de correo existente y términos
    public function ctrRegistroUsuario()
    {
        if (isset($_POST["regEmail"])) {
            if (!isset($_POST["checkTerminos"])) {
                echo '<div class="custom-alert error">Debes aceptar los Términos y Condiciones.</div>';
                return;
            }

            $email = filter_var($_POST["regEmail"], FILTER_SANITIZE_EMAIL);
            $existe = UsuarioModelo::mdlMostrarUsuario("email", $email);

            if ($existe) {
                echo '<div class="custom-alert error">El correo ya se encuentra registrado. Intenta iniciar sesión.</div>';
                return;
            }

            $passHash = password_hash($_POST["regPassword"], PASSWORD_BCRYPT);
            $datos = [
                "nombre" => strip_tags($_POST["regNombre"]),
                "email" => $email,
                "telefono" => strip_tags($_POST["regTelefono"]),
                "password" => $passHash
            ];

            if (UsuarioModelo::mdlRegistroUsuario($datos) === "ok") {
                echo '<script>alert("¡Bienvenida! Cuenta creada exitosamente."); window.location="index.php?ruta=login";</script>';
            }
        }
    }

    public function ctrIngresoUsuario()
    {
        if (isset($_POST["ingEmail"])) {
            $usuario = UsuarioModelo::mdlMostrarUsuario("email", $_POST["ingEmail"]);
            if ($usuario && password_verify($_POST["ingPassword"], $usuario["password"])) {
                $_SESSION["iniciarSesion"] = "ok";
                $_SESSION["id"] = $usuario["id"];
                $_SESSION["nombre"] = $usuario["nombre"];
                $_SESSION["email"] = $usuario["email"];
                $_SESSION["rol"] = $usuario["rol"];
                $_SESSION["foto"] = $usuario["foto"];

                if ($usuario["rol"] === "admin") {
                    echo '<script>window.location = "index.php?ruta=admin";</script>';
                } else {
                    echo '<script>window.location = "index.php?ruta=citas";</script>';
                }
            } else {
                echo '<div class="custom-alert error">Correo o contraseña incorrectos.</div>';
            }
        }
    }

    // En controlador/usuariosControlador.php
    public function ctrSolicitarCodigoRecuperacion()
    {
        if (isset($_POST["recEmail"])) {
            $email = filter_var($_POST["recEmail"], FILTER_SANITIZE_EMAIL);
            $usuario = UsuarioModelo::mdlMostrarUsuario("email", $email);

            if (!$usuario) {
                echo '<div class="alert alert-danger custom-alert">El correo no coincide con ninguna cuenta.</div>';
                return;
            }

            $codigo = (string)random_int(100000, 999999);
            $expiracion = date("Y-m-d H:i:s", strtotime("+10 minutes"));
            UsuarioModelo::mdlGuardarTokenRecuperacion($usuario["id"], $codigo, $expiracion);

            // Envío SMTP Directo con Gmail y Contraseña de Aplicación
            $smtpHost = "ssl://smtp.gmail.com";
            $smtpPort = 465;
            $gmailUser = "tucorreo@gmail.com";              // Tu correo Gmail
            $gmailAppPass = "abcd efgh ijkl mnop";          // Contraseña de Aplicación de 16 letras

            $asunto = "=?UTF-8?B?" . base64_encode("Código de Recuperación - El Rulo De Maye") . "?=";
            $cuerpo = "Hola " . $usuario["nombre"] . ",\r\n\r\nTu código de verificación es: " . $codigo . "\r\n\r\nEste código vencerá en 10 minutos.\r\n\r\nEl Rulo De Maye.";

            $socket = @fsockopen($smtpHost, $smtpPort, $errno, $errstr, 15);
            if ($socket) {
                fgets($socket, 515);
                fputs($socket, "EHLO localhost\r\n");
                fgets($socket, 515);
                fputs($socket, "AUTH LOGIN\r\n");
                fgets($socket, 515);
                fputs($socket, base64_encode($gmailUser) . "\r\n");
                fgets($socket, 515);
                fputs($socket, base64_encode(str_replace(' ', '', $gmailAppPass)) . "\r\n");
                fgets($socket, 515);
                fputs($socket, "MAIL FROM: <$gmailUser>\r\n");
                fgets($socket, 515);
                fputs($socket, "RCPT TO: <$email>\r\n");
                fgets($socket, 515);
                fputs($socket, "DATA\r\n");
                fgets($socket, 515);
                fputs($socket, "To: $email\r\nFrom: \"El Rulo De Maye\" <$gmailUser>\r\nSubject: $asunto\r\nContent-Type: text/plain; charset=UTF-8\r\n\r\n$cuerpo\r\n.\r\n");
                fgets($socket, 515);
                fputs($socket, "QUIT\r\n");
                fclose($socket);
            }

            $_SESSION["email_recuperacion"] = $email;
        }
    }

    public function ctrCambiarPasswordConCodigo()
    {
        if (isset($_POST["codigo6"]) && isset($_SESSION["email_recuperacion"])) {
            $email = $_SESSION["email_recuperacion"];
            $codigo = trim($_POST["codigo6"]);
            $nuevaPass = $_POST["nuevaPassword"];

            $usuario = UsuarioModelo::mdlMostrarUsuario("email", $email);

            if ($usuario && $usuario["token_recuperacion"] === $codigo) {
                $ahora = date("Y-m-d H:i:s");
                if ($ahora > $usuario["token_expiracion"]) {
                    echo '<div class="custom-alert error">El código ha vencido (superó los 10 minutos). Solicita uno nuevo.</div>';
                    return;
                }

                $passHash = password_hash($nuevaPass, PASSWORD_BCRYPT);
                UsuarioModelo::mdlActualizarPassword($usuario["id"], $passHash);
                unset($_SESSION["email_recuperacion"]);

                echo '<script>alert("¡Contraseña actualizada con éxito! Ya puedes ingresar."); window.location = "index.php?ruta=login";</script>';
            } else {
                echo '<div class="custom-alert error">Código de 6 dígitos inválido.</div>';
            }
        }
    }

    // 8. Validador de choques de horario con alerta para el cliente
    public function ctrNuevaCita()
    {
        if (isset($_POST["agendarFecha"])) {
            $fecha = $_POST["agendarFecha"];
            $hora = $_POST["agendarHora"];

            // 1. Validar si Maye ya tiene una cita reservada a esa misma hora
            $existeCita = UsuarioModelo::mdlVerificarDisponibilidad($fecha, $hora);
            if ($existeCita) {
                return '<div class="custom-alert error">⚠️ Maye ya tiene un turno agendado a las ' . substr($hora, 0, 5) . ' el día ' . $fecha . '. Por favor escoge otro horario.</div>';
            }

            // 2. Validar si Maye atiende ese día según sus horarios
            $numeroDia = date('N', strtotime($fecha)); // 1 (Lunes) a 7 (Domingo)
            $horarioDia = UsuarioModelo::mdlObtenerHorarioDia($numeroDia);

            if (!$horarioDia || $horarioDia["activo"] == 0) {
                return '<div class="custom-alert info">🌸 Maye no atiende los días domingos o descansos programados.</div>';
            }

            $metodoPago = $_POST["metodoPago"] ?? 'efectivo';
            $pagoEstado = ($metodoPago !== 'efectivo') ? 'pagado' : 'pendiente';
            $refPago = ($pagoEstado === 'pagado') ? 'PAY-' . strtoupper(uniqid()) : null;

            $datos = [
                "usuario_id" => $_SESSION["id"],
                "servicio_id" => $_POST["agendarServicio"],
                "fecha" => $fecha,
                "hora" => $hora,
                "pago_estado" => $pagoEstado,
                "referencia_pago" => $refPago,
                "notas" => strip_tags($_POST["agendarNotas"] ?? '')
            ];

            if (UsuarioModelo::mdlCrearCita($datos) === "ok") {
                echo '<script>alert("¡Cita reservada exitosamente!"); window.location="index.php?ruta=citas";</script>';
            }
        }
        return '';
    }

    // 10. Actualización de perfil y subida de foto
    public function ctrActualizarPerfil()
    {
        if (isset($_POST["perfilNombre"])) {
            $nombre = strip_tags($_POST["perfilNombre"]);
            $telefono = strip_tags($_POST["perfilTelefono"]);
            $fotoRuta = null;

            if (isset($_FILES["fotoPerfil"]) && !empty($_FILES["fotoPerfil"]["tmp_name"])) {
                $dir = "vista/imagenes/usuarios/";
                if (!file_exists($dir)) mkdir($dir, 0777, true);

                $extension = pathinfo($_FILES["fotoPerfil"]["name"], PATHINFO_EXTENSION);
                $fotoRuta = $dir . "user_" . $_SESSION["id"] . "_" . time() . "." . $extension;
                move_uploaded_file($_FILES["fotoPerfil"]["tmp_name"], $fotoRuta);
            }

            UsuarioModelo::mdlActualizarPerfil($_SESSION["id"], $nombre, $telefono, $fotoRuta);
            $_SESSION["nombre"] = $nombre;
            if ($fotoRuta) $_SESSION["foto"] = $fotoRuta;

            echo '<script>window.location="index.php?ruta=perfil";</script>';
        }
    }
    public function ctrCambiarPasswordPerfil()
    {
        if (isset($_POST["btnCambiarPass"])) {
            $idUsuario = $_SESSION["id"];
            $passActual = $_POST["passActual"];
            $passNueva = $_POST["passNueva"];

            // 1. Validar longitud mínima
            if (strlen($passNueva) < 6) {
                echo '<div class="alert alert-danger custom-alert mt-3">La nueva contraseña debe tener al menos 6 caracteres.</div>';
                return;
            }

            // 2. Obtener el usuario actual para verificar el hash existente
            $usuario = UsuarioModelo::mdlMostrarUsuario("id", $idUsuario);

            if (!$usuario || !password_verify($passActual, $usuario["password"])) {
                echo '<div class="alert alert-danger custom-alert mt-3">La contraseña actual no coincide.</div>';
                return;
            }

            // 3. Encriptar y actualizar
            $passHash = password_hash($passNueva, PASSWORD_BCRYPT);
            $respuesta = UsuarioModelo::mdlActualizarPasswordPerfil($idUsuario, $passHash);

            if ($respuesta === "ok") {
                echo '<script>
                alert("¡Contraseña actualizada con éxito!");
                window.location = "index.php?ruta=perfil";
            </script>';
            } else {
                echo '<div class="alert alert-danger custom-alert mt-3">Ocurrió un error al actualizar la contraseña.</div>';
            }
        }
    }
}
