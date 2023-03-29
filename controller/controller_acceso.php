<?php
    // Controlador de acceso
    if ($_SESSION['usser']['privilege'] == 3) { header("location: ./retraso"); }
    if ($_SESSION['usser']['privilege'] == 4) { header("location: ./matricula"); }
?>