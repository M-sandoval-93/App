<?php

    // SE INCLUYE EL MODELO PARA SER USADO POR EL CONTROLADOR
    require_once '../model/model_inasistenciaF.php';

    $type = $_POST['datos']; // SE RECIBE EL TIPO DE ACCIÓN
    $datosInasistenciaF = new InasistenciaFuncionario(); // SE CREA EL OBJETO PARA TRABAJAR CON DATATABLE

    switch ($type) {
        case "mostrar_inasistencias":
            print $datosInasistenciaF->consultarInasistenciaF();
            break;

        case "buscar_funcionario":
            $rut = $_POST['rut'];
            print $datosInasistenciaF->buscarFuncionario($rut);
            break;

        case "registrar_inasistencia":
            $inasistencia = array(
                $_POST['tipoI'], $_POST['rutF'], $_POST['fechaI'], $_POST['fechaT'], $_POST['diasI'], 
                ($_POST['ord'] != '') ? $_POST['ord'] : null, ($_POST['rutR'] != '') ? $_POST['rutR'] : null);
            print $datosInasistenciaF->newInaistenciaF($inasistencia);

            // AGREGAR FUNCIÓN PARA ENVIAR CORREO SI LA RESPUESTA ES VERDADERA, VER SI SE AGREGA POR JAVASCRIPT
            break;

        case "getInasistencia":
            $id_inasistencia = $_POST['id_inasistencia'];
            print $datosInasistenciaF->getInasistencia($id_inasistencia);
            break;

        case "getTipoInasistencia":
            print $datosInasistenciaF->getTipoInasistencia();
            break;

        case "editar_inasistencia":
            // eliminar el rut del funcionario que no es necesario
            $inasistencia = array(
                $_POST['tipoI'], $_POST['rutF'], $_POST['fechaI'], $_POST['fechaT'], $_POST['diasI'], 
                ($_POST['ord'] != '') ? $_POST['ord'] : null, ($_POST['rutR'] != '') ? $_POST['rutR'] : null, $_POST['id_inasistencia']);
            print $datosInasistenciaF->updateInasistenciaF($inasistencia);
            break;

        case "eliminar_inasistencia":
            $id_inasistencia = $_POST['id_inasistencia'];
            print $datosInasistenciaF->deleteInasistenciaF($id_inasistencia);
            break;
    }

?>
