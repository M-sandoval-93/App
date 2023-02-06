<?php

    // SETEO DE ZONA HORARIA
    date_default_timezone_set('America/Santiago');
    session_start();

    // COMPROBACIÓN DE INICIO DE SESIÓN Y CONTROL DE VISTAS
    if (isset($_SESSION['usser']['name'])) {
        if (isset($_GET['ruta'])) {
            include_once "./controller/controller_ruta.php";
        } else {
            // header("location: home");
            header("location: home");
        }
    } else {
        include_once "./views/login.php";
    }

?>

