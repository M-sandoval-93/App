<?php

    // we include the model that the controller will use (Incluimos el modelo que utilizara el controlador)
    require_once '../model/mode_inasistenciaFuncionario.php';

    $type = $_POST['data']; // we receive the action to be performed by the controller (Recibimos la acción a realizar por el controlador)
    $absenceData = new Funcionario(); // we create the object to work with dataTable (Creamos el objeto para trabajar con datatable)


?>
