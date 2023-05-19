<?php

    // Incluimos el modelo que utilizara el controlador
    require_once '../model/model_funcionario.php';


    $type = $_POST['datos']; // Recibimos la acción a realizar por el controlador
    $datosFuncionario = new Funcionario(); // Creamos el objeto para trabajar con datatable

    switch ($type) {
        case "getFuncionarios":
            print $datosFuncionario->getFuncionarios();
            break;

        case "getFuncionario":
            print $datosFuncionario->getFuncionario($_POST['rut'], $_POST['tipo']);
            break;

        case "getCantidadFuncionario":
            print $datosFuncionario->getCatidadFuncionario();
            break;

        case "loadTipoFuncionario":
            print $datosFuncionario->loadTipoFuncionario();
            break;

        case "loadDepartamento":
            print $datosFuncionario->loadDepartamento();
            break;

        case "setFuncionario":
            $funcionario = json_decode(json_encode($_POST['funcionario'])); // Convertir un objeto js a un objeto PHP
            print $datosFuncionario->setFuncionario($funcionario);
            break;
        
        case "updateFuncionario":
            $funcionario = json_decode(json_encode($_POST['funcionario'])); // Convertir un objeto js a un objeto PHP
            print $datosFuncionario->updateFuncionario($funcionario);
            break;

        case "deleteFuncionario":
            print $datosFuncionario->deleteFuncionario($_POST['id_funcionario']);
            break;
        
        case "getReporteFuncionario":
            print $datosFuncionario->getReporteFuncionario($_POST['ext']);
            break;
    }

?>