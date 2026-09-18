<?php

// ========================================================
// SERVICIO DE CORREO NATIVO (EL RULO DE MAYE)
// ========================================================
class CorreoServicio
{

    public static function enviarCodigoRecuperacion($correoDestino, $nombreUsuario, $codigo)
    {

        $correoEmisor = "elrulodemaye@gmail.com";
        $claveApp     = "ztsu rarh yrkf ifok"; // Tu contraseña de aplicación de Gmail

        $asunto = "=?UTF-8?B?" . base64_encode("🌸 Tu código de seguridad - El Rulo De Maye") . "?=";

        // Diseño HTML elegante para El Rulo De Maye
        $cuerpoHTML = '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; background-color: #fcf8f8; margin: 0; padding: 20px; color: #333333; }
                .container { max-width: 500px; margin: 0 auto; background-color: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(212,139,148,0.15); border: 1px solid #f6dfe2; }
                .header { background: linear-gradient(135deg, #fff0f3 0%, #ffffff 100%); padding: 30px 20px; text-align: center; border-bottom: 1px solid #f6dfe2; }
                .header h1 { margin: 0; font-family: Georgia, serif; font-size: 26px; color: #b86b75; font-weight: 700; }
                .header p { margin: 5px 0 0; font-size: 11px; color: #888888; text-transform: uppercase; letter-spacing: 2px; }
                .content { padding: 35px 30px; text-align: center; }
                .greeting { font-size: 18px; font-weight: 600; margin-bottom: 12px; color: #2d2d2d; }
                .text { font-size: 14px; line-height: 1.6; color: #666666; margin-bottom: 25px; }
                .code-box { background: #fff7f8; border: 2px dashed #d48b94; border-radius: 14px; padding: 18px; margin: 25px 0; }
                .code { font-size: 36px; font-weight: 800; letter-spacing: 8px; color: #b86b75; margin: 0; }
                .expiry { font-size: 12px; color: #d9534f; margin-top: 8px; font-weight: bold; }
                .footer { background-color: #fafafa; padding: 20px; text-align: center; font-size: 12px; color: #999999; border-top: 1px solid #f0f0f0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>El Rulo De Maye</h1>
                    <p>Salón de Belleza & Nail Spa</p>
                </div>
                <div class="content">
                    <div class="greeting">¡Hola, ' . htmlspecialchars($nombreUsuario) . '! 💅</div>
                    <p class="text">Recibimos una solicitud para restablecer la contraseña de tu cuenta. Utiliza el siguiente código de verificación:</p>
                    
                    <div class="code-box">
                        <div class="code">' . $codigo . '</div>
                        <div class="expiry">⏳ Este código vence en 10 minutos</div>
                    </div>

                    <p class="text" style="font-size: 12px; margin-bottom: 0;">Si no solicitaste este cambio, puedes ignorar este mensaje. Tu cuenta seguirá protegida.</p>
                </div>
                <div class="footer">
                    &copy; ' . date("Y") . ' El Rulo De Maye. Todos los derechos reservados.<br>
                    Arte, delicadeza y cuidado exclusivo ✨
                </div>
            </div>
        </body>
        </html>';

        return self::enviarSMTPGmail($correoEmisor, $claveApp, $correoDestino, $asunto, $cuerpoHTML);
    }

    private static function enviarSMTPGmail($emisor, $passwordApp, $destinatario, $asunto, $html)
    {
        $host = "ssl://smtp.gmail.com";
        $port = 465;
        $timeout = 15;

        // Contexto SSL para entornos locales (XAMPP)
        $contexto = stream_context_create([
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
                "allow_self_signed" => true
            ]
        ]);

        $socket = @stream_socket_client($host . ":" . $port, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $contexto);
        if (!$socket) {
            $headers  = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
            $headers .= "From: El Rulo De Maye <" . $emisor . ">\r\n";
            return @mail($destinatario, $asunto, $html, $headers);
        }

        self::leerRespuesta($socket);

        fputs($socket, "EHLO " . gethostname() . "\r\n");
        self::leerRespuesta($socket);

        fputs($socket, "AUTH LOGIN\r\n");
        self::leerRespuesta($socket);

        fputs($socket, base64_encode($emisor) . "\r\n");
        self::leerRespuesta($socket);

        fputs($socket, base64_encode(str_replace(' ', '', $passwordApp)) . "\r\n");
        self::leerRespuesta($socket);

        fputs($socket, "MAIL FROM: <" . $emisor . ">\r\n");
        self::leerRespuesta($socket);

        fputs($socket, "RCPT TO: <" . $destinatario . ">\r\n");
        self::leerRespuesta($socket);

        fputs($socket, "DATA\r\n");
        self::leerRespuesta($socket);

        $mensajeHeaders  = "MIME-Version: 1.0\r\n";
        $mensajeHeaders .= "Content-Type: text/html; charset=UTF-8\r\n";
        $mensajeHeaders .= "From: \"El Rulo De Maye\" <" . $emisor . ">\r\n";
        $mensajeHeaders .= "To: <" . $destinatario . ">\r\n";
        $mensajeHeaders .= "Subject: " . $asunto . "\r\n\r\n";
        $mensajeHeaders .= $html . "\r\n.\r\n";

        fputs($socket, $mensajeHeaders);
        self::leerRespuesta($socket);

        fputs($socket, "QUIT\r\n");
        fclose($socket);

        return true;
    }

    private static function leerRespuesta($socket)
    {
        $respuesta = "";
        while ($linea = fgets($socket, 515)) {
            $respuesta .= $linea;
            if (substr($linea, 3, 1) == " ") {
                break;
            }
        }
        return $respuesta;
    }
}


// ========================================================
// CONTROLADOR DE USUARIOS
// ========================================================
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
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "¡Cuenta creada!",
                        text: "Tu registro fue exitoso. Inicia sesión para continuar.",
                        confirmButtonColor: "#d48b94"
                    }).then(() => {
                        window.location = "index.php?ruta=login";
                    });
                </script>';
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
            $email = filter_var(trim(strtolower($_POST["recEmail"])), FILTER_SANITIZE_EMAIL);
            $usuario = UsuarioModelo::mdlMostrarUsuario("email", $email);

            if ($usuario) {
                $codigo = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
                $expiracion = date("Y-m-d H:i:s", strtotime("+10 minutes"));

                UsuarioModelo::mdlGuardarTokenRecuperacion($usuario["id"], $codigo, $expiracion);
                CorreoServicio::enviarCodigoRecuperacion($email, $usuario["nombre"], $codigo);

                $_SESSION["email_recuperacion"] = $email;

                echo '<script>
                    Swal.fire({
                        icon: "info",
                        title: "¡Código Enviado!",
                        html: "Hemos enviado el código de 6 dígitos a <b>' . $email . '</b>.<br><small class=\"text-danger fw-bold\">⏳ Tienes 10 minutos para utilizarlo.</small>",
                        confirmButtonColor: "#d48b94"
                    });
                </script>';
            } else {
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "Correo no registrado",
                        text: "No existe ninguna cuenta asociada a este correo electrónico.",
                        confirmButtonColor: "#d48b94"
                    });
                </script>';
            }
        }
    }

    public function ctrCambiarPasswordConCodigo()
    {
        if (isset($_POST["codigo6"]) && isset($_SESSION["email_recuperacion"])) {
            $email = $_SESSION["email_recuperacion"];
            $codigo = trim($_POST["codigo6"]);
            $nuevaPass = $_POST["nuevaPassword"];

            if (strlen($nuevaPass) < 6) {
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "Contraseña corta",
                        text: "La nueva contraseña debe tener mínimo 6 caracteres.",
                        confirmButtonColor: "#d48b94"
                    });
                </script>';
                return;
            }

            $usuario = UsuarioModelo::mdlMostrarUsuario("email", $email);

            if ($usuario && $usuario["token_recuperacion"] === $codigo) {
                $ahora = date("Y-m-d H:i:s");
                if ($ahora > $usuario["token_expiracion"]) {
                    echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "Código expirado",
                            text: "El código de 6 dígitos ha vencido. Por favor solicita uno nuevo.",
                            confirmButtonColor: "#d48b94"
                        });
                    </script>';
                    return;
                }

                $passHash = password_hash($nuevaPass, PASSWORD_BCRYPT);
                UsuarioModelo::mdlActualizarPassword($usuario["id"], $passHash);
                unset($_SESSION["email_recuperacion"]);

                // Alerta SweetAlert2 elegante acorde a la estética
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "¡Contraseña Actualizada!",
                        text: "Tu contraseña ha sido restablecida con éxito. Inicia sesión con tu nueva clave.",
                        confirmButtonColor: "#b86b75"
                    }).then(() => {
                        window.location = "index.php?ruta=login";
                    });
                </script>';
            } else {
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "Código incorrecto",
                        text: "El código de verificación ingresado no es válido.",
                        confirmButtonColor: "#d48b94"
                    });
                </script>';
            }
        }
    }

    public function ctrNuevaCita()
    {
        if (isset($_POST["agendarFecha"])) {
            $servicioId = (int)$_POST["agendarServicio"];
            $fecha = $_POST["agendarFecha"];
            $hora = $_POST["agendarHora"];

            $stmtSrv = Conexion::conectar()->prepare("SELECT nombre, precio, duracion_minutos FROM servicios WHERE id = :id LIMIT 1");
            $stmtSrv->bindParam(":id", $servicioId, PDO::PARAM_INT);
            $stmtSrv->execute();
            $servicio = $stmtSrv->fetch();
            $duracionMinutos = $servicio ? (int)$servicio["duracion_minutos"] : 60;

            $numeroDia = date('N', strtotime($fecha));
            $horarioDia = UsuarioModelo::mdlObtenerHorarioDia($numeroDia);

            if (!$horarioDia || $horarioDia["activo"] == 0) {
                return '<div class="alert alert-warning custom-alert">🌸 Maye no atiende en la fecha seleccionada. Por favor escoge otro día.</div>';
            }

            $ocupado = UsuarioModelo::mdlVerificarDisponibilidad($fecha, $hora, $duracionMinutos);
            if ($ocupado) {
                return '<div class="alert alert-danger custom-alert">⚠️ El turno a las ' . substr($hora, 0, 5) . ' se cruza con otra cita agendada considerando su duración (' . $duracionMinutos . ' min). Por favor elige otro horario.</div>';
            }

            $metodoPago = $_POST["metodoPago"] ?? 'efectivo';
            $pagoEstado = 'pendiente';
            $refPago = ($metodoPago === 'nequi_daviplata') ? 'TRANSF-' . strtoupper(uniqid()) : null;

            // En ctrNuevaCita() dentro de ControladorUsuarios:
            // ...
            $fotoRef = null;
            if (isset($_FILES["fotoReferencia"]) && !empty($_FILES["fotoReferencia"]["tmp_name"])) {
                $dirRef = "vista/imagenes/referencias/";
                if (!file_exists($dirRef)) {
                    mkdir($dirRef, 0777, true);
                }
                $ext = strtolower(pathinfo($_FILES["fotoReferencia"]["name"], PATHINFO_EXTENSION));
                if (in_array($ext, ["jpg", "jpeg", "png", "webp"])) {
                    $fotoRef = $dirRef . "ref_" . time() . "_" . uniqid() . "." . $ext;
                    move_uploaded_file($_FILES["fotoReferencia"]["tmp_name"], $fotoRef);
                }
            }

            $datos = [
                "usuario_id" => $_SESSION["id"],
                "servicio_id" => $servicioId,
                "fecha" => $fecha,
                "hora" => $hora,
                "pago_estado" => $pagoEstado,
                "referencia_pago" => $refPago,
                "notas" => strip_tags($_POST["agendarNotas"] ?? ''),
                "foto_referencia" => $fotoRef // <- Se añade al arreglo
            ];

            if (UsuarioModelo::mdlCrearCita($datos) === "ok") {
                if ($metodoPago === 'nequi_daviplata') {
                    $nombreSrv = urlencode($servicio["nombre"] ?? "Servicio");
                    $precioSrv = $servicio["precio"] ?? 0;
                    echo '<script>
                        window.location = "index.php?ruta=citas&pago_directo=1&srv=' . $nombreSrv . '&val=' . $precioSrv . '";
                    </script>';
                } else {
                    echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "¡Cita Reservada!",
                            text: "Tu cita ha sido agendada con éxito. Recuerda pagar en el salón al finalizar tu atención.",
                            confirmButtonColor: "#d48b94"
                        }).then(() => {
                            window.location = "index.php?ruta=citas";
                        });
                    </script>';
                }
            } else {
                return '<div class="alert alert-danger custom-alert">Ocurrió un error al procesar tu reserva. Intenta de nuevo.</div>';
            }
        }
        return '';
    }

    public function ctrActualizarPerfil()
    {
        $alerta = "";

        // 1. SUBIDA DIRECTA DE FOTO (Auto-submit al seleccionar archivo)
        if (isset($_FILES["fotoPerfil"]) && !empty($_FILES["fotoPerfil"]["tmp_name"])) {
            $directorio = "vista/imagenes/usuarios/";
            if (!file_exists($directorio)) {
                mkdir($directorio, 0777, true);
            }

            // Eliminar foto física previa si existía
            $usuarioActual = UsuarioModelo::mdlMostrarUsuario("id", $_SESSION["id"]);
            if (!empty($usuarioActual["foto"]) && file_exists($usuarioActual["foto"])) {
                @unlink($usuarioActual["foto"]);
            }

            $extension = strtolower(pathinfo($_FILES["fotoPerfil"]["name"], PATHINFO_EXTENSION));
            $permitidas = ["jpg", "jpeg", "png", "webp"];

            if (in_array($extension, $permitidas)) {
                $nuevaRuta = $directorio . "user_" . $_SESSION["id"] . "_" . time() . "." . $extension;
                if (move_uploaded_file($_FILES["fotoPerfil"]["tmp_name"], $nuevaRuta)) {
                    UsuarioModelo::mdlActualizarFotoPerfil($_SESSION["id"], $nuevaRuta);
                    $_SESSION["foto"] = $nuevaRuta;
                }
            }

            echo '<script>window.location = "index.php?ruta=perfil";</script>';
            exit();
        }

        // 2. ELIMINAR FOTO DE PERFIL
        if (isset($_POST["btnEliminarFoto"])) {
            $usuario = UsuarioModelo::mdlMostrarUsuario("id", $_SESSION["id"]);

            if (!empty($usuario["foto"]) && file_exists($usuario["foto"])) {
                @unlink($usuario["foto"]);
            }

            UsuarioModelo::mdlActualizarFotoPerfil($_SESSION["id"], null);
            $_SESSION["foto"] = null;

            echo '<script>
                Swal.fire({
                    icon: "success",
                    title: "Foto eliminada",
                    text: "Tu foto de perfil se ha eliminado exitosamente.",
                    confirmButtonColor: "#d48b94"
                }).then(() => {
                    window.location = "index.php?ruta=perfil";
                });
            </script>';
            exit();
        }

        // 3. GUARDAR NOMBRE, CORREO Y TELÉFONO
        if (isset($_POST["btnActualizarDatos"])) {
            $nombre = strip_tags(trim($_POST["perfilNombre"]));
            $email = filter_var(trim(strtolower($_POST["perfilEmail"])), FILTER_SANITIZE_EMAIL);
            $telefono = strip_tags(trim($_POST["perfilTelefono"]));

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "Correo inválido",
                        text: "Por favor ingresa un correo electrónico válido.",
                        confirmButtonColor: "#d48b94"
                    });
                </script>';
                return "";
            }

            // Validar unicidad del correo si cambió
            if ($email !== $_SESSION["email"]) {
                $existe = UsuarioModelo::mdlMostrarUsuario("email", $email);
                if ($existe && $existe["id"] != $_SESSION["id"]) {
                    echo '<script>
                        Swal.fire({
                            icon: "warning",
                            title: "Correo ya registrado",
                            text: "Este correo electrónico ya pertenece a otra cuenta.",
                            confirmButtonColor: "#d48b94"
                        });
                    </script>';
                    return "";
                }
            }

            // Guardar cambios de texto
            UsuarioModelo::mdlActualizarPerfil($_SESSION["id"], $nombre, $email, $telefono);
            $_SESSION["nombre"] = $nombre;
            $_SESSION["email"] = $email;

            echo '<script>
                Swal.fire({
                    icon: "success",
                    title: "¡Perfil Actualizado!",
                    text: "Tus datos personales se han guardado con éxito.",
                    confirmButtonColor: "#d48b94"
                }).then(() => {
                    window.location = "index.php?ruta=perfil";
                });
            </script>';
            return "";
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
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "¡Contraseña Actualizada!",
                        text: "Tu contraseña ha sido cambiada correctamente.",
                        confirmButtonColor: "#d48b94"
                    }).then(() => {
                        window.location = "index.php?ruta=perfil";
                    });
                </script>';
            } else {
                echo '<div class="alert alert-danger custom-alert mt-3">Error al actualizar la contraseña.</div>';
            }
        }
    }

    public function ctrGestionAdmin()
    {
        if (isset($_POST["btnGuardarCategoria"])) {
            $nombre = strip_tags(trim($_POST["catNombre"]));
            $estado = $_POST["catEstado"];
            UsuarioModelo::mdlCrearCategoria($nombre, $estado);
            echo '<script>window.location="index.php?ruta=admin&tab=categorias";</script>';
            exit();
        }

        if (isset($_POST["btnGuardarServicio"])) {
            $datos = [
                "id" => !empty($_POST["srvId"]) ? (int)$_POST["srvId"] : null,
                "categoria_id" => (int)$_POST["srvCategoria"],
                "nombre" => strip_tags($_POST["srvNombre"]),
                "descripcion" => strip_tags($_POST["srvDescripcion"]),
                "precio" => (float)$_POST["srvPrecio"],
                "duracion_minutos" => (int)$_POST["srvDuracion"],
                "estado" => $_POST["srvEstado"]
            ];

            if (!empty($datos["id"])) {
                UsuarioModelo::mdlActualizarServicio($datos);
            } else {
                UsuarioModelo::mdlCrearServicio($datos);
            }
            echo '<script>window.location="index.php?ruta=admin&tab=servicios";</script>';
            exit();
        }

        if (isset($_POST["btnSubirGaleria"])) {
            $dir = "vista/imagenes/galeria/";
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            $fotoAntes = $dir . "antes_" . time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "", basename($_FILES["fotoAntes"]["name"]));
            $fotoDespues = $dir . "despues_" . time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "", basename($_FILES["fotoDespues"]["name"]));

            if (
                move_uploaded_file($_FILES["fotoAntes"]["tmp_name"], $fotoAntes) &&
                move_uploaded_file($_FILES["fotoDespues"]["tmp_name"], $fotoDespues)
            ) {
                $datos = [
                    "titulo" => strip_tags($_POST["galTitulo"]),
                    "categoria_id" => !empty($_POST["galCategoria"]) ? (int)$_POST["galCategoria"] : null,
                    "descripcion" => strip_tags($_POST["galDescripcion"]),
                    "foto_antes" => $fotoAntes,
                    "foto_despues" => $fotoDespues,
                    "estado" => "visible"
                ];
                UsuarioModelo::mdlCrearGaleria($datos);
            }
            echo '<script>window.location="index.php?ruta=admin&tab=galeria";</script>';
            exit();
        }

        // Subir Nueva Historia (24 horas)
        if (isset($_POST["btnSubirHistoria"])) {
            $dir = "vista/imagenes/historias/";
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            $nombreArch = "story_" . time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "", basename($_FILES["archivoHistoria"]["name"]));
            $rutaArch = $dir . $nombreArch;

            if (move_uploaded_file($_FILES["archivoHistoria"]["tmp_name"], $rutaArch)) {
                $tipo = (strpos($_FILES["archivoHistoria"]["type"], 'video') !== false) ? 'video' : 'imagen';
                $pie = strip_tags(trim($_POST["pieFotoHistoria"] ?? ''));
                UsuarioModelo::mdlCrearHistoria($rutaArch, $tipo, $pie);
                echo '<script>window.location="index.php?ruta=admin&tab=historias";</script>';
                exit();
            }
        }

        // Subir Nueva Tendencia al Lookbook
        if (isset($_POST["btnSubirHistoria"])) {
            $dir = "vista/imagenes/historias/";
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            $nombreArch = "story_" . time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "", basename($_FILES["archivoHistoria"]["name"]));
            $rutaArch = $dir . $nombreArch;

            if (move_uploaded_file($_FILES["archivoHistoria"]["tmp_name"], $rutaArch)) {
                $tipo = (strpos($_FILES["archivoHistoria"]["type"], 'video') !== false) ? 'video' : 'imagen';
                $pie = strip_tags(trim($_POST["pieFotoHistoria"] ?? ''));
                UsuarioModelo::mdlCrearHistoria($rutaArch, $tipo, $pie);

                // Redirección normalizada
                echo '<script>window.location = "index.php?ruta=admin&tab=historia";</script>';
                exit();
            }
        }
        if (isset($_POST["btnGuardarEdicionUsuario"])) {
            $idUsr = (int)$_POST["usuarioId"];
            $nombre = strip_tags(trim($_POST["usuarioNombre"]));
            $telefono = strip_tags(trim($_POST["usuarioTelefono"]));
            $rol = $_POST["usuarioRol"];

            UsuarioModelo::mdlEditarUsuarioAdmin($idUsr, $nombre, $telefono, $rol);
            echo '<script>window.location="index.php?ruta=admin&tab=usuarios";</script>';
            exit();
        }

        if (isset($_POST["btnActualizarFidelizacion"])) {
            $meta = (int)$_POST["metaVisitas"];
            $premio = strip_tags(trim($_POST["premioTexto"]));
            $descuento = (int)$_POST["porcentajeDescuento"];
            UsuarioModelo::mdlActualizarConfigFidelizacion($meta, $premio, $descuento);
            echo '<script>window.location="index.php?ruta=admin&tab=fidelizacion";</script>';
            exit();
        }
    }

    public function ctrCrearResenaCliente()
    {
        if (isset($_POST["btnEnviarResena"]) && isset($_SESSION["id"])) {
            $calificacion = (int)$_POST["calificacion"];
            $comentario = strip_tags(trim($_POST["comentarioResena"]));
            if (!empty($comentario)) {
                UsuarioModelo::mdlCrearResena($_SESSION["id"], $calificacion, $comentario);
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "¡Reseña Enviada!",
                        text: "🌸 ¡Gracias por tu opinión! Maye la revisará para publicarla en el salón.",
                        confirmButtonColor: "#d48b94"
                    });
                </script>';
            }
        }
    }
}
