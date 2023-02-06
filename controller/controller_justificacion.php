<?php

// Incluimos el modelo que utilizara el controlador
require_once '../model/model_justificacion.php';
require_once "../model/model_session.php";

$session = new Session();
$id_usuario = $session->getId();

$type = $_POST['datos'];
$datosJustificacion = new JustificacionEstudiante();

switch ($type) {
    case "showJustificaciones": // Terminado y revisado !!
        print $datosJustificacion->showJustificacion();
        break;

    case "getInfoAdicional": // Terminado y revisado !!
        print $datosJustificacion->infoAdicional($_POST['id_justificacion']);
        break;

    case "getCantidadJustificacion": // Terminado y revisado !!
        print $datosJustificacion->getJustificaciones();
        break;

    case "setJustificacion": // Terminado y revisado !!
        $justificacion = array(
            $_POST['rut'], $_POST['fecha_inicio'], $_POST['fecha_termino'], $_POST['apoderado'], $_POST['motivo'],
            $_POST['documento'], $_POST['pruebas'], (isset($_POST['asignatura'])) ? $_POST['asignatura'] : "false"
        );

        print $datosJustificacion->setJustificacion($justificacion);
        break;
    case "deleteJustificacion": // Terminado y revisado !!
        print $datosJustificacion->deleteJustificacion($_POST['id_justificacion']);
        break;

    case "exportarJustificaciones": // Trabajar !!
        print $datosJustificacion->exportarJustificaciones($_POST['ext']);
        break;

    // case "getDocument":
    //     print $datosJustificacion->getDocument();
    //     break;



}

// Validar los datos que se traen con PHP
// En caso de haverse saltado las validaciones de jQuery y JavaScript



?>