<?php

    if (
        // Retraso
        // $_GET['ruta'] == 'retraso' && $_SESSION['usser']['privilege'] == 5 ||

        // Justificación
        // $_GET['ruta'] == 'justificacion' && $_SESSION['usser']['privilege'] == 5 ||

        // Suspensión
        $_GET['ruta'] == 'suspension' && $_SESSION['usser']['privilege'] == 5 ||

        // Matrícula

        // Estudiante
        $_GET['ruta'] == 'estudiante' && $_SESSION['usser']['privilege'] == 3 ||
        $_GET['ruta'] == 'estudiante' && $_SESSION['usser']['privilege'] == 4 ||

        // Apoderado
        $_GET['ruta'] == 'apoderado' && $_SESSION['usser']['privilege'] == 3 ||
        $_GET['ruta'] == 'apoderado' && $_SESSION['usser']['privilege'] == 4 ||

        // Funcionario
        $_GET['ruta'] == 'funcionario' && $_SESSION['usser']['privilege'] == 3 ||
        $_GET['ruta'] == 'funcionario' && $_SESSION['usser']['privilege'] == 4) {

        // Redirección
        header("location: ./home");
    }

?>