<?php

    require_once '../model/model_licenciaFuncionario.php';          // Se incluye el controlador de session

    $type = $_POST['datos'];                                        // Recibimos la acción a realizar por el controlador
    $datosLicencia = new LicenciaFuncionario();                     // Se instancia la clase de licencias funcionario

    switch ($type) {
        case "getLicencias":
            print $datosLicencia->getLicencias();
            break;

    }



?>