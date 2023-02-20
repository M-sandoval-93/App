<?php

    // SE INCLUYE EL MODELO PARA SER USADO POR EL CONTROLADOR
    require_once '../model/model_apoderado.php';

    $type = $_POST['datos']; // Recepción de la acción del controlador
    $datosApoderado = new Apoderado(); // Instancia del objeto estudiante

    switch ($type) {
        case "getApoderados":
            print $datosApoderado->getApoderados();
            break;

        case "getApoderado":
            print $datosApoderado->getApoderado($_POST['rut'], $_POST['tipo']);
            break;

        case "getCantidadApoderado":
            print $datosApoderado->getCantidadApoderado();
            break;

        case "setApoderado":
            $apoderado = json_decode(json_encode($_POST['apoderado'])); // Convertir un objeto js a un objeto PHP
            print $datosApoderado->setApoderado($apoderado);
            break;

        case "updateApoderado":
            $apoderado = json_decode(json_encode($_POST['apoderado'])); // Convertir un objeto js a un objeto PHP
            print $datosApoderado->updateApoderado($apoderado);
            break;

        case "deleteApoderado":
            print $datosApoderado->deleteApoderado($_POST['id_apoderado']);
            break;

        case "exportarApoderados":
            print $datosApoderado->exportarApoderados($_POST['ext']);
            break;




            
    }



    






?>
