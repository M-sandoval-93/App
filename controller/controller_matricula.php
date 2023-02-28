<?php
    // Incluimos el modelo que utilizara el controlador
    require_once '../model/model_matricula.php';
    require_once "../model/model_session.php";

    $type = $_POST['datos']; // Recibimos la acción a realizar por el controlador
    $datosMatricula = new MatriculaEstudiantes(); // Creamos el objeto para trabajar con datatable

    switch ($type) {
        case "getMatricula":
            print $datosMatricula->gerMatricula();
            break;

        case "getCantidadMatricula":
            print $datosMatricula->getCantidadMatricula();
            break;

        case "getNumeroMatricula":
            print $datosMatricula->getNumeroMatricula($_POST['inicial'], $_POST['final']);
            break;

        case "getApoderadoTS":
            print $datosMatricula->getApoderadoTS($_POST['id_matricula']);
            break;

        case "setMatricula":
            $matricula = json_decode(json_encode($_POST['matricula'])); // Convertir un objeto js a un objeto php
            print($datosMatricula->setMatricula($matricula));
            break;

        case "setRetiroMatricula":
            print($datosMatricula->setRetiroMatricula($_POST['rut'], $_POST['id_matricula'], $_POST['fecha']));
            break;

        case "updateMatricula":
            $matricula = json_decode(json_encode($_POST['matricula'])); // Convertir un objeto js a un objeto php
            print($datosMatricula->updateMatricula($matricula));
            break;
        
        case "deleteMatricula":
            print $datosMatricula->deleteMatricula($_POST['id_matricula']);
            break;

        case "exportarMatriculas":
            print $datosMatricula->exportarMatriculas($_POST['ext']);
            break;
    }



?>
