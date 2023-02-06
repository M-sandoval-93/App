<?php

    // // SE INCLUYE EL MODELO PARA SER USADO POR EL CONTROLADOR
    // if (file_exists('./model/model_datosTablas.php')) {
    //     include_once './model/model_datosTablas.php';

    // } else if (file_exists(include_once '../model/model_datosTablas.php')) {
    //     include_once '../model/model_datosTablas.php';
    // }

    // SE INCLUYE EL MODELO PARA SER USADO POR EL CONTROLADOR
    require_once '../model/model_apoderado.php';

    $type = $_POST['datos']; // SE RECIBE EL TIPO DE ACCIÓN
    $datosApoderados = new Apoderado(); // SE CREA EL OBJETO PARA TRABAJAR CON DATATABLE


    switch ($type) {
        case "mostrar_apoderados":
            print $datosApoderados->consultaApoderados();
            break;

        case "buscar_apoderado":
            $rut = $_POST['rut'];
            print $datosApoderados->consultaApoderado($rut);
            break;

        case "nuevo_apoderado":
            $apoderado = array($_POST['rut'], $_POST['dv_rut'], $_POST['nombres'], $_POST['a_paterno'], $_POST['a_materno'], $_POST['fono']);
            print $datosApoderados->newApoderado($apoderado);
            break;

        case "editar_apoderado":
            $apoderado = array($_POST['rut'], $_POST['nombres'], $_POST['a_paterno'], $_POST['a_materno'], $_POST['fono']);
            print $datosApoderados->updateApoderado($apoderado);
            break;

        case "editar_estado":
            $id = $_POST['id_apoderado'];
            $estado = $_POST['estado'];
            print $datosApoderados->updateEstadoApoderado($id, $estado); 
            break;

        case "eliminar_apoderado":
            $id = $_POST['id_apoderado'];
            print $datosApoderados->deleteApoderado($id);
            break;

        case "getApoderado": // Terminado...
            print $datosApoderados->getApoderado($_POST['rut']);
            break;


    }



    






?>
