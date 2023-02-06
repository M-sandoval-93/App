<?php

    require_once "../model/model_session.php";
    // include_once "./model/model_session.php";

    $cierre_sesion = new Session();
    $cierre_sesion->closeSession();

    header("location: ../");

?>