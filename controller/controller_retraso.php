<?php
    // Incluimos el modelo que utilizara el controlador
    require_once '../model/model_retraso.php';
    require_once "../model/model_session.php";

    $session = new Session();
    $id_usser = $session->getId();


    $type = $_POST['datos']; // Recibimos la acción a realizar por el controlador
    $datosRetraso = new RetrasoEstudiante(); // Creamos el objeto para trabajar con datatable

    switch ($type) {
        case "getRetraso":
            print $datosRetraso->getRetraso();
            break;

        case "getRetrasoSinJustificar";
            print $datosRetraso->getRetrasoSinJustificar($_POST['rut']);
            break;

        case "getCantidadRetraso":
            print $datosRetraso->getCantidadRetraso();
            break;

        case "setRetraso":
            print $datosRetraso->setRetraso($_POST['rut']);
            break;
            
        case "setJustificarRetraso":
            print $datosRetraso->setJustificarRetraso($_POST['id_apoderado'], $_POST['retrasos'], $id_usser);
            break;

        case "deleteRetraso":
            print $datosRetraso->deleteRetraso($_POST['id_retraso']);
            break;


        case "exportarRetraso":
            print $datosRetraso->exportarRetraso($_POST['ext']);
            break;


    }

?>