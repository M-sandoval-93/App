<?php

// Incluimos el modelo que utilizara el controlador
require_once '../model/model_justificacion.php';
require_once "../model/model_session.php";

// Trabajar con las variables de sesión
$session = new Session();
$privilege_usser = $session->getPrivilege();
$id_usser = $session->getId();

$type = $_POST['datos'];
$datosJustificacion = new JustificacionEstudiante();

switch ($type) {
    case "getJustificaciones":
        print $datosJustificacion->getJustificaciones();
        break;

    case "getCantidadJustificacion":
        print $datosJustificacion->getCantidadJustificacion();
        break;

    case "getTipoDocumento":
        print $datosJustificacion->getTipoDocumento();
        break;

    case "setJustificacion":
        $justificacion = json_decode(json_encode($_POST['justificacion'])); // Convertir un objeto js a un objeto PHP
        print $datosJustificacion->setJustificacion($justificacion, isset($_POST['asignatura']) ? $_POST['asignatura'] : null, $id_usser);
        break;
        
    case "deleteJustificacion":
        if ($privilege_usser == 4 || $privilege_usser == 5) {
            http_response_code(404);
            exit();
        }
        
        print $datosJustificacion->deleteJustificacion($_POST['id_justificacion']);
        break;

    case "getCertificadoJustificacion":
        if ($privilege_usser == 4 || $privilege_usser == 5) {
            http_response_code(404);
            exit();
        }

        print $datosJustificacion->getCertificadoJustificacion($_POST['id_justificacion']);
        break;

    case "exportarJustificaciones":
        print $datosJustificacion->exportarJustificaciones($_POST['ext']);
        break;


}

// Validar los datos que se traen con PHP
// En caso de haverse saltado las validaciones de jQuery y JavaScript



?>