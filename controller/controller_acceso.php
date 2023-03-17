<?php
    // Controlador de acceso
    if ($_SESSION['usser']['id'] == 3) { header("location: ./retraso"); }
    if ($_SESSION['usser']['id'] == 4) { header("location: ./matricula"); }
?>