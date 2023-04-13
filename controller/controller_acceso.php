<?php

    if (
        $_GET['ruta'] == 'estudiante' && $_SESSION['usser']['privilege'] == 3 ||
        $_GET['ruta'] == 'estudiante' && $_SESSION['usser']['privilege'] == 4 ||

        $_GET['ruta'] == 'apoderado' && $_SESSION['usser']['privilege'] == 3 ||
        $_GET['ruta'] == 'apoderado' && $_SESSION['usser']['privilege'] == 4 ||

        $_GET['ruta'] == 'funcionario' && $_SESSION['usser']['privilege'] == 3 ||
        $_GET['ruta'] == 'funcionario' && $_SESSION['usser']['privilege'] == 4) {
        header("location: ./home");
    }

?>