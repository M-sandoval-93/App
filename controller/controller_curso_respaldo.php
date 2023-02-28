<?php

    // SE INCLUYE EL MODELO PARA SER USADO POR EL CONTROLADOR
    if (file_exists('./model/model_curso.php')) {
        require_once './model/model_curso.php';

    } else if (file_exists(include_once '../model/model_curso.php')) {
        require_once '../model/model_curso.php';
    }

 
    // SE OBTIENEN LOS DATOS ENVIADOS POR AJAX
    $grado = (isset($_POST['grado'])) ? $_POST['grado'] : '';
    $letra = (isset($_POST['letra'])) ? $_POST['letra'] : '';
    $funcion = $_POST['funcion'];
    $cursos = new Curso();

    switch ($funcion) {
        case 'consulta':
            print $cursos->consultarCurso($grado);
            break;

        case 'crear_curso':
            break;

        case 'cargar_letras':
            print $cursos->cargarLetrasGrado($grado);
            break;
    }


?>