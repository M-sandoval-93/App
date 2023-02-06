<?php
    // Incluimos el modelo que utilizara el controlador
    require_once '../model/model_retraso.php';
    require_once "../model/model_session.php";

    $session = new Session();
    $id_usuario = $session->getId();


    $type = $_POST['datos']; // Recibimos la acción a realizar por el controlador
    $datosRetraso = new RetrasoEstudiante(); // Creamos el objeto para trabajar con datatable

    switch ($type) {
        case "getRetraso": // Terminado y revisado !!
            print $datosRetraso->getRetraso();
            break;

        case "getRetrasoSinJustificar"; // Terminado y revisado !!
            print $datosRetraso->getRetrasoSinJustificar($_POST['rut']);
            break;

        case "getCantidadRetraso": // Terminado y revisado !!
            print $datosRetraso->getCantidadRetraso($_POST['tipo']);
            break;

        case "setRetraso": // Terminado y revisado !!
            print $datosRetraso->setRetraso($_POST['rut']);
            break;

        case "deleteRetraso": // Terminado y revisado !!
            print $datosRetraso->deleteRetraso($_POST['id_retraso']);
            break;

        case "setJustificar": // Terminado y revisado !!
            print $datosRetraso->setJustificar($_POST['id_apoderado'], $_POST['retrasos'], $id_usuario);
            break;

        case "exportarRetraso": // Terminado y revisado !!
            print $datosRetraso->exportarRetraso($_POST['ext']);
            break;


    }

?>