<?php

    // SE INCLUYE EL MODELO PARA SER USADO POR EL CONTROLADOR
    include_once '../model/model_estudiante.php';

    $type = $_POST['datos']; // SE RECIBE EL TIPO DE ACCIÓN
    $datoEstudiante = new Estudiante(); // SE CREA EL OBJETO PARA TRABAJAR CON DATATABLE

    switch ($type) {
        case "getEstudiantes":
            print $datoEstudiante->getEstudiantes();
            break;

        case "getEstudiante": // Terminado y revisado !!
            print $datoEstudiante->getEstudiante($_POST['rut'], $_POST['tipo']);
            break;

        case "getCantidadEstudiante":
            print $datoEstudiante->getCantidadEstudiante();
            break;

        case "deleteEstudiante":
            print $datoEstudiante->deleteEstudiante($_POST['id_estudiante']);
            break;
            
        
        
    }




?>