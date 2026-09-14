<?php
class ControladorUsuarios
{

    public function ctrRegistroUsuario()
    {
        if (isset($_POST["regEmail"])) {
            if (!isset($_POST["checkTerminos"])) {
                echo '<div class="alert alert-danger custom-alert">Debes aceptar los Términos y Condiciones para registrarte.</div>';
                return;
            }

            $email = filter_var($_POST["regEmail"], FILTER_SANITIZE_EMAIL);
            $existe = UsuarioModelo::mdlMostrarUsuario("email", $email);

            if ($existe) {
                echo '<div class="alert alert-danger custom-alert">El correo electrónico ya se encuentra registrado.</div>';
                return;
            }

            if (strlen($_POST["regPassword"]) < 6) {
                echo '<div class="alert alert-danger custom-alert">La contraseña debe tener un mínimo de 6 caracteres.</div>';
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
                echo '<script>alert("¡Cuenta creada exitosamente! Inicia sesión."); window.location = "index.php?ruta=login";</script>';
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
                echo '<div class="alert alert-danger custom-alert">Correo o contraseña incorrectos.</div>';
            }
        }
    }

    public function ctrSolicitarCodigoRecuperacion()
    {
        if (isset($_POST["recEmail"])) {
            $email = filter_var($_POST["recEmail"], FILTER_SANITIZE_EMAIL);
            $usuario = UsuarioModelo::mdlMostrarUsuario("email", $email);

            if (!$usuario) {
                echo '<div class="alert alert-danger custom-alert">El correo no coincide con ninguna cuenta registrada.</div>';
                return;
            }

            $codigo = (string)random_int(100000, 999999);
            $expiracion = date("Y-m-d H:i:s", strtotime("+10 minutes"));
            UsuarioModelo::mdlGuardarTokenRecuperacion($usuario["id"], $codigo, $expiracion);

            // Envío SMTP Directo con Contraseña de Aplicación de Gmail
            $smtpHost = "ssl://smtp.gmail.com";
            $smtpPort = 465;
            $gmailUser = "tucorreo@gmail.com";              // COLOCA AQUÍ TU CORREO DE GMAIL
            $gmailAppPass = "abcd efgh ijkl mnop";          // COLOCA AQUÍ TU CONTRASEÑA DE APLICACIÓN DE 16 DÍGITOS

            $asunto = "=?UTF-8?B?" . base64_encode("Código de Verificación - El Rulo De Maye") . "?=";
            $cuerpo = "Hola " . $usuario["nombre"] . ",\r\n\r\nTu código de recuperación es: " . $codigo . "\r\n\r\nVence en exactamente 10 minutos.\r\n\r\nEl Rulo De Maye.";

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

            if (strlen($nuevaPass) < 6) {
                echo '<div class="alert alert-danger custom-alert">La nueva contraseña debe tener al menos 6 caracteres.</div>';
                return;
            }

            $usuario = UsuarioModelo::mdlMostrarUsuario("email", $email);

            if ($usuario && $usuario["token_recuperacion"] === $codigo) {
                $ahora = date("Y-m-d H:i:s");
                if ($ahora > $usuario["token_expiracion"]) {
                    echo '<div class="alert alert-danger custom-alert">El código de 6 dígitos ha expirado (más de 10 minutos). Solicita uno nuevo.</div>';
                    return;
                }

                $passHash = password_hash($nuevaPass, PASSWORD_BCRYPT);
                UsuarioModelo::mdlActualizarPassword($usuario["id"], $passHash);
                unset($_SESSION["email_recuperacion"]);

                echo '<script>alert("¡Contraseña actualizada con éxito! Ya puedes iniciar sesión."); window.location = "index.php?ruta=login";</script>';
            } else {
                echo '<div class="alert alert-danger custom-alert">El código ingresado es incorrecto.</div>';
            }
        }
    }

    public function ctrNuevaCita()
    {
        if (isset($_POST["agendarFecha"])) {
            $fecha = $_POST["agendarFecha"];
            $hora = $_POST["agendarHora"];

            $existeCita = UsuarioModelo::mdlVerificarDisponibilidad($fecha, $hora);
            if ($existeCita) {
                return '<div class="alert alert-danger custom-alert">⚠️ Maye ya tiene una cita reservada a las ' . substr($hora, 0, 5) . ' en esa fecha. Por favor escoge otra hora.</div>';
            }

            $numeroDia = date('N', strtotime($fecha));
            $horarioDia = UsuarioModelo::mdlObtenerHorarioDia($numeroDia);

            if (!$horarioDia || $horarioDia["activo"] == 0) {
                return '<div class="alert alert-warning custom-alert">🌸 Maye no atiende citas el día seleccionado.</div>';
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
                echo '<script>alert("¡Tu cita ha sido reservada con éxito!"); window.location="index.php?ruta=citas";</script>';
            }
        }
        return '';
    }

    public function ctrActualizarPerfil()
    {
        $alerta = "";

        // 1. Guardar Nombre y Teléfono
        if (isset($_POST["btnActualizarDatos"])) {
            $nombre = strip_tags(trim($_POST["perfilNombre"]));
            $telefono = strip_tags(trim($_POST["perfilTelefono"]));

            UsuarioModelo::mdlActualizarPerfil($_SESSION["id"], $nombre, $telefono);
            $_SESSION["nombre"] = $nombre;

            $alerta = '<div class="alert alert-success custom-alert d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Tus datos han sido actualizados con éxito.</span>
                   </div>';
        }

        // 2. Subida / Cambio de Foto de Perfil
        if (isset($_FILES["fotoPerfil"]) && !empty($_FILES["fotoPerfil"]["tmp_name"])) {
            $directorio = "vista/imagenes/usuarios/";
            if (!file_exists($directorio)) {
                mkdir($directorio, 0777, true);
            }

            $usuario = UsuarioModelo::mdlMostrarUsuario("id", $_SESSION["id"]);

            // Borrar foto previa si existe y no es la predeterminada
            if (!empty($usuario["foto"]) && file_exists($usuario["foto"]) && strpos($usuario["foto"], "default.png") === false) {
                unlink($usuario["foto"]);
            }

            $extension = strtolower(pathinfo($_FILES["fotoPerfil"]["name"], PATHINFO_EXTENSION));
            $permitidas = ["jpg", "jpeg", "png", "webp"];

            if (in_array($extension, $permitidas)) {
                $nuevaRuta = $directorio . "user_" . $_SESSION["id"] . "_" . time() . "." . $extension;

                if (move_uploaded_file($_FILES["fotoPerfil"]["tmp_name"], $nuevaRuta)) {
                    UsuarioModelo::mdlActualizarFotoPerfil($_SESSION["id"], $nuevaRuta);
                    $_SESSION["foto"] = $nuevaRuta;

                    $alerta = '<div class="alert alert-success custom-alert d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-camera-fill"></i>
                            <span>Foto de perfil actualizada correctamente.</span>
                           </div>';
                } else {
                    $alerta = '<div class="alert alert-danger custom-alert d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <span>Hubo un problema al subir la imagen al servidor.</span>
                           </div>';
                }
            } else {
                $alerta = '<div class="alert alert-warning custom-alert d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span>Formato no admitido. Usa JPG, PNG o WEBP.</span>
                       </div>';
            }
        }

        // 3. Eliminar Foto de Perfil
        if (isset($_POST["btnEliminarFoto"])) {
            $usuario = UsuarioModelo::mdlMostrarUsuario("id", $_SESSION["id"]);

            if (!empty($usuario["foto"]) && file_exists($usuario["foto"]) && strpos($usuario["foto"], "default.png") === false) {
                unlink($usuario["foto"]);
            }

            UsuarioModelo::mdlActualizarFotoPerfil($_SESSION["id"], null);
            $_SESSION["foto"] = null;

            $alerta = '<div class="alert alert-info custom-alert d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-trash3-fill"></i>
                    <span>Foto de perfil eliminada. Ahora se muestran tus iniciales.</span>
                   </div>';
        }

        return $alerta;
    }

    public function ctrCambiarPasswordPerfil()
    {
        if (isset($_POST["btnCambiarPass"])) {
            $idUsuario = $_SESSION["id"];
            $passActual = $_POST["passActual"];
            $passNueva = $_POST["passNueva"];

            if (strlen($passNueva) < 6) {
                echo '<div class="alert alert-danger custom-alert mt-3">La nueva contraseña debe tener al menos 6 caracteres.</div>';
                return;
            }

            $usuario = UsuarioModelo::mdlMostrarUsuario("id", $idUsuario);

            if (!$usuario || !password_verify($passActual, $usuario["password"])) {
                echo '<div class="alert alert-danger custom-alert mt-3">La contraseña actual ingresada es incorrecta.</div>';
                return;
            }

            $passHash = password_hash($passNueva, PASSWORD_BCRYPT);
            $respuesta = UsuarioModelo::mdlActualizarPasswordPerfil($idUsuario, $passHash);

            if ($respuesta === "ok") {
                echo '<script>alert("¡Contraseña actualizada exitosamente!"); window.location = "index.php?ruta=perfil";</script>';
            } else {
                echo '<div class="alert alert-danger custom-alert mt-3">Error al actualizar la contraseña.</div>';
            }
        }
    }
}
