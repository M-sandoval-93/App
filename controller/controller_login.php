<?php

    require_once '../model/model_session.php';          // Se incluye el controlador de session

    // Se instancia la variable de sesión
    $inicio_sesion = new Session();
    $type = $_POST['datos']; // Recibimos la acción a realizar por el controlador

    switch ($type) {
        case "login":
            $cuentaUsuario = json_decode(json_encode($_POST['cuentaUsuario'])); // Convertir un objeto js a un objeto php
            print $inicio_sesion->checkUsser($cuentaUsuario);
            break;

        case "newPassword":
            $password = json_decode(json_encode($_POST['password'])); // Convertir un objeto js a un objeto php
            print $inicio_sesion->newPassword($password);
            break;
    }



?>