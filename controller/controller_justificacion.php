<?php

// Incluimos el modelo que utilizara el controlador
require_once '../model/model_justificacion.php';
require_once "../model/model_session.php";

$session = new Session();
$id_usuario = $session->getId();

$type = $_POST['datos'];
$datosJustificacion = new JustificacionEstudiante();

switch ($type) {
    case "getJustificaciones":
        print $datosJustificacion->getJustificaciones();
        break;

    case "getCantidadJustificacion":
        print $datosJustificacion->getCantidadJustificacion();
        break;

    case "setJustificacion":
        $justificacion = json_decode(json_encode($_POST['justificacion'])); // Convertir un objeto js a un objeto PHP
        print $datosJustificacion->setJustificacion($justificacion, $_POST['asignatura']);
        break;
        
    case "deleteJustificacion":
        print $datosJustificacion->deleteJustificacion($_POST['id_justificacion']);
        break;

    case "getCertificadoJustificacion":
        break;

    case "exportarJustificaciones":
        print $datosJustificacion->exportarJustificaciones($_POST['ext']);
        break;

    // case "getDocument":
    //     print $datosJustificacion->getDocument();
    //     break;



}

// Validar los datos que se traen con PHP
// En caso de haverse saltado las validaciones de jQuery y JavaScript



?>