<?php

if ($_GET['ruta'] == 'home' ||
    $_GET['ruta'] == 'matricula' ||
    $_GET['ruta'] == 'retraso' ||
    $_GET['ruta'] == 'justificacion' ||
    $_GET['ruta'] == 'suspension' ||
    $_GET['ruta'] == 'estudiante' ||
    $_GET['ruta'] == 'apoderado' ||
    $_GET['ruta'] == 'funcionario' ||
    $_GET['ruta'] == 'login' ||
    $_GET['ruta'] == 'mantenimiento') {
        include_once "views/".$_GET['ruta'].".php";
} else {
    require_once "views/404.php";
}

?>






