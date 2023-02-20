<?php

    // SE INCLUYE EL MODELO PARA SER USADO POR EL CONTROLADOR
    require_once '../model/model_estudiante.php';

    $type = $_POST['datos']; // Recepción de la acción del controlador
    $datoEstudiante = new Estudiante(); // Instancia del objeto estudiante

    switch ($type) {
        case "getEstudiantes":
            print $datoEstudiante->getEstudiantes();
            break;

        case "getEstudiante":
            print $datoEstudiante->getEstudiante($_POST['rut'], $_POST['tipo']);
            break;

        case "getCantidadEstudiante":
            print $datoEstudiante->getCantidadEstudiante();
            break;

        case "setEstudiante":
            $estudiante = json_decode(json_encode($_POST['estudiante'])); // Convertir un objeto js a un objeto PHP
            print $datoEstudiante->setEstudiante($estudiante);
            break;

        case "updateEstudiante":
            $estudiante = json_decode(json_encode($_POST['estudiante'])); // Convertir un objeto js a un objeto PHP
            print $datoEstudiante->updateEstudiante($estudiante);
            break;

        case "deleteEstudiante":
            print $datoEstudiante->deleteEstudiante($_POST['id_estudiante']);
            break;
            
        case "exportarEstudiantes":
            print $datoEstudiante->exportarEstudiantes($_POST['ext']);
            break;

        
    }




?>
