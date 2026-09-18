<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Page Title</title>

    <meta name='viewport' content='width=device-width, initial-scale=1'>

    <!-- Boostrap 5.3 -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- cdn jquery v 3.7.1 -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>


    <!-- cdn data tablas .net -->

    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/r-3.0.3/datatables.min.css" rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/r-3.0.3/datatables.min.js"></script>

    <!-- cdn sweetalert2 -->

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Archivos personalizados -->

    <link rel='stylesheet' type='text/css' media='screen' href='vista/css/main.css'>


<nav class="navbar navbar-expand-lg bg-white sticky-top py-2 border-bottom shadow-sm">
    <div class="container-fluid px-lg-5 position-relative d-flex align-items-center justify-content-between">
        
        <!-- Logo e Identidad -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="index.php?ruta=inicio">
            <img src="vista/img/logo_maye.jpeg" alt="Logo" class="logo-nav" onerror="this.style.display='none'">
            <span class="logo-text">El Rulo De Maye</span>
        </a>

        <?php if (isset($_SESSION["iniciarSesion"]) && $_SESSION["iniciarSesion"] === "ok"): ?>
            <!-- CON SESIÓN: Botón Hamburguesa para Móvil -->
            <button class="navbar-toggler border-0 shadow-none position-relative"
                type="button"
                id="btnHamburguesaNavbar"
                onclick="toggleMenuMaye()"
                aria-label="Menu"
                style="z-index: 9999; cursor: pointer;">
                <i class="bi bi-list fs-1 color-primary" id="iconoHamburguesa"></i>
            </button>

            <!-- Menú Desplegable para Usuarias Logueadas -->
            <div class="collapse navbar-collapse" id="navMayeMenu">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-2 gap-lg-3 pt-3 pt-lg-0">
                    <li class="nav-item"><a class="nav-link" href="index.php?ruta=inicio">Inicio</a></li>
                    
                    <?php if ($_SESSION["rol"] === "admin"): ?>
                        <li class="nav-item"><a class="nav-link text-primary fw-bold" href="index.php?ruta=admin">Panel Admin</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="index.php?ruta=citas">Mis Citas</a></li>
                    <?php endif; ?>

                    <li class="nav-item"><a class="nav-link" href="index.php?ruta=perfil"><i class="bi bi-person-circle me-1"></i>Mi Perfil</a></li>
                    <li class="nav-item"><a class="nav-link text-danger fw-bold" href="index.php?ruta=salir">Salir</a></li>
                </ul>
            </div>

        <?php else: ?>
            <!-- SIN SESIÓN: Botón directo visible en móvil y PC, sin hamburguesa -->
            <div class="d-flex align-items-center gap-3">
                <a class="nav-link d-none d-lg-block" href="index.php?ruta=inicio">Inicio</a>
                <a href="index.php?ruta=login" class="btn-primary-custom px-3 px-md-4 py-2 text-decoration-none shadow-xs">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión
                </a>
            </div>
        <?php endif; ?>

    </div>
</nav>

<body>