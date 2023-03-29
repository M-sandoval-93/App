<?php

    // SE INCLUYE EL MODELO PARA SER USADO POR EL CONTROLADOR
    require_once '../model/model_session.php';

    // NOTA: AGREGAR PROTECCIÓN ANTE CARACTERES
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];

    // SE INSTANCIA LA CLASE SESSION Y USA EL MÉTODO PARA COMPROBAR EL USUARIO
    $inicio_sesion = new Session();

    print $inicio_sesion->checkUsser($usuario, $clave);

?>