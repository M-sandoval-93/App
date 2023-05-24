<?php

    // Incluimos el modelo que utilizara el controlador
    require_once '../model/model_usuario.php';


    $type = $_POST['datos']; // Recibimos la acción a realizar por el controlador
    $datosUsuario = new Usuario(); // Creamos el objeto para trabajar con datatable

    switch ($type) {
        case "getUserAccount":
            print $datosUsuario->getUserAccount();
            break;

        case "getUserAccountAmount":
            print $datosUsuario->getUserAccountAmount();
            break;

        case "loadPrivilegio":
            print $datosUsuario->loadPrivilegio();
            break;

        case "checkUserAccount":
            print $datosUsuario->checkUserAccount($_POST['id_user']);
            break;

        case "setUserAccount":
            $userAccount = json_decode(json_encode($_POST['userAccount'])); // Convertir un objeto js a un objeto PHP
            print $datosUsuario->setUserAccount($userAccount);
            break;

        case "modifyUserAccount":
            print $datosUsuario->modifyUserAccount($_POST['id_userAccount']);
            break;

        case "restKeyAccount":
            print $datosUsuario->restKeyAccount($_POST['id_userAccount'], $_POST['key']);
            break;

        case "deleteUserAccount":
            print $datosUsuario->deleteUserAccount($_POST['id_userAccount']);
            break;


    }

?>