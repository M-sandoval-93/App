<?php

    // Incluimos el modelo que utilizara el controlador
    require_once '../model/model_funcionario.php';


    $type = $_POST['datos']; // Recibimos la acción a realizar por el controlador
    $datosFuncionario = new Funcionario(); // Creamos el objeto para trabajar con datatable

    switch ($type) {
        case "getFuncionario":
            break;

        case "getCantidadFuncionario":
            print $datosFuncionario->getCatidadFuncionario();
            break;



    
    }


?>