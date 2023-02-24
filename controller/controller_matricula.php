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
        
        case "deleteMatricula":
            print $datosMatricula->deleteMatricula($_POST['id_matricula']);
            break;

        case "exportarMatriculas":
            print $datosMatricula->exportarMatriculas($_POST['ext']);
            break;
    }



?>
