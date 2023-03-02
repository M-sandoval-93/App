<?php
    // Incluimos el modelo que utilizara el controlador
    require_once '../model/model_suspension.php';
    // require_once "../model/model_session.php";

    $type = $_POST['datos']; // Recibimos la acción a realizar por el controlador
    $datosSuspension = new SuspensionEstudiante(); // Creamos el objeto para trabajar con datatable

    switch ($type) {
        case "getCantidadSuspension":
            print $datosSuspension->getCantidadSuspension();
            break;

        case "getSuspension":
            print $datosSuspension->getSuspension();
            break;

        case "deleteSuspension":
            print $datosSuspension->deleteSuspension($_POST['id_suspension']);
            break;

    }



?>