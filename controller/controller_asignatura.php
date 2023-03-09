<?php
    // Incluimos el modelo que utilizara el controlador
    require_once '../model/model_asignatura.php';
    // include_once "../model/model_session.php";

    // $session = new Session();
    // $id_usuario = $session->getId();


    $type = $_POST['datos']; // Recibimos la acción a realizar por el controlador
    $datosAsignatura = new Asignatura(); 

    switch ($type) {
        case "getAsignatura":
            print $datosAsignatura->getAsignatura();
            break;




    }

?>

