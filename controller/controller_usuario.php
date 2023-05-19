<?php

    // Incluimos el modelo que utilizara el controlador
    require_once '../model/model_usuario.php';


    $type = $_POST['datos']; // Recibimos la acción a realizar por el controlador
    $datosUsuario = new Usuario(); // Creamos el objeto para trabajar con datatable

    switch ($type) {
        case "loadPrivilegio":
            print $datosUsuario->loadPrivilegio();
            break;

        case "checkUserAccount":
            print $datosUsuario->checkUserAccount($_POST['id_user']);
            break;


    }

?>