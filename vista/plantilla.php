<?php
$ruta = $_GET["ruta"] ?? "inicio";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>El Rulo De Maye | Salón de Belleza & Spa</title>

    <!-- Bootstrap 5 CSS & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Estilos Personalizados -->
    <link rel="stylesheet" href="vista/css/main.css">
</head>
<body>

    <!-- Cabecera -->
    <?php include "vista/modulos/1cabesera.php"; ?>

    <!-- Contenido Dinámico -->
    <main>
        <?php
        $rutasValidas = ["inicio", "servicios", "citas", "login", "registro", "recuperar", "perfil", "terminos", "admin", "salir"];
        if (in_array($ruta, $rutasValidas)) {
            if (($ruta == "citas" || $ruta == "admin" || $ruta == "perfil") && !isset($_SESSION["iniciarSesion"])) {
                include "vista/modulos/login.php";
            } elseif ($ruta == "admin" && $_SESSION["rol"] !== "admin") {
                echo '<div class="container my-5 text-center"><h3>Acceso restringido para administradores.</h3></div>';
            } else {
                include "vista/modulos/" . $ruta . ".php";
            }
        } else {
            include "vista/modulos/inicio.php";
        }
        ?>
    </main>

    <!-- Botón Flotante de WhatsApp -->
    <a href="https://wa.me/573001234567?text=Hola%20Maye!%20Deseo%20agendar%20o%20preguntar%20por%20un%20servicio" 
       class="btn-whatsapp-float" 
       target="_blank" 
       rel="noopener noreferrer" 
       title="Chatear con Maye">
        <i class="bi bi-whatsapp"></i>
    </a>

    <!-- Pie de página -->
    <?php include "vista/modulos/zpie.php"; ?>

    <!-- Scripts de Bootstrap y Lógica Principal -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="vista/js/main.js"></script>
</body>
</html>