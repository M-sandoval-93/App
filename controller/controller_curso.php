<?php

    // Incluimos el modelo que utilizara el controlador
    require_once '../model/model_curso.php';
    // require_once "../model/model_session.php";


    $type = $_POST['datos']; // Recibimos la acción a realizar por el controlador
    $datosCurso = new Curso(); // Creamos el objeto para trabajar con datatable

    switch ($type) {
        case "loadLetra":
            print $datosCurso->loadLetra($_POST['grado']);
            break;

    
    }



?>