<?php

    require_once "./config/version.php";

    // SETEO DE ZONA HORARIA
    date_default_timezone_set('America/Santiago');
    // Comienzo de las sesiones 
    session_start();
    $_SESSION['version'] = VERSION;
    

    // COMPROBACIÓN DE INICIO DE SESIÓN Y CONTROL DE VISTAS
    if (isset($_SESSION['usser']['name'])) {
        if (isset($_GET['ruta'])) {
            include_once "./controller/controller_ruta.php";
        } else {
            header("location: home");
        }
    } else {
        include_once "./views/login.php";
    }

?>

