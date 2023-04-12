<?php

    if (
        $_GET['ruta'] == 'estudiante' && $_SESSION['usser']['id'] == 3 ||
        $_GET['ruta'] == 'estudiante' && $_SESSION['usser']['id'] == 4 ||

        $_GET['ruta'] == 'apoderado' && $_SESSION['usser']['id'] == 3 ||
        $_GET['ruta'] == 'apoderado' && $_SESSION['usser']['id'] == 4 ||

        $_GET['ruta'] == 'funcionario' && $_SESSION['usser']['id'] == 3 ||
        $_GET['ruta'] == 'funcionario' && $_SESSION['usser']['id'] == 4) {
        header("location: ./home");
    }


    // Controlador de acceso
    // if ($_SESSION['usser']['id'] == 3) { 
    //     header("location: ./home"); 
    // }
?>