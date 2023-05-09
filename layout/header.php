<?php
     if (!isset($_SESSION['usser']['name'])) {
        header("location: ../");
    }
    include_once "./controller/controller_acceso.php";      // Controlador de acceso
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- logo -->
    <link rel="shortcut icon" href="./assets/logo_liceo.png">

    <!-- fonts -->
    
    
    <!-- icons -->
    <link rel="stylesheet" href="./Pluggins/Fontawesome-5.15.4/css/all.min.css">
    
    <!-- style normalize -->
    <link rel="stylesheet" href="./css/normalize.css">
    
    <!-- style pluggins -->
    <link rel="stylesheet" href="./Pluggins/Bootstrap-5.0.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./Pluggins/DataTables/datatables.min.css">
    
    <!-- main style -->
    <link rel="stylesheet" href="./css/main.css?v=<?php  echo $_SESSION['version'] ?>">

    <title>Liceo Valentín Letelier</title>
</head>

<body>
    <div class="barra_navegacion">
        <ul>
            <li>
                <a href="#">
                    <span class="icono"><i class="fas fa-school"></i></span>
                </a>
            </li>

            <li>
                <a href="home">
                    <span class="icono"><i class="fas fa-home"></i></i></span>
                    <span class="titulo">home</span>
                </a>
            </li>

<!-- Restricción de acceso -->
<?php
if ($_SESSION['usser']['privilege'] == 1 || $_SESSION['usser']['privilege'] == 2 || $_SESSION['usser']['privilege'] == 3 || 
    $_SESSION['usser']['privilege'] == 4 || $_SESSION['usser']['privilege'] == 5) {
?>
<!-- Restricción de acceso -->

            <li>
                <a href="#">
                    <span class="icono"><i class="fas fa-user-graduate"></i></span>
                    <span class="titulo">Registros</span>
                    <i class="fas fa-angle-right icon icon-right"></i>
                </a>
                <ul class="sub_grupo">
                    <li><a href="retraso"><span class="titulo">Retraso</span></a></li>
                    <li><a href="justificacion"><span class="titulo">Justificacion</span></a></li>
<?php
    if ($_SESSION['usser']['privilege'] != 5) {
?>
                    <li><a href="suspension"><span class="titulo">Suspension</span></a></li>
<?php
    }
?>
                </ul>
            </li>

<!-- Restricción de acceso -->
<?php
}
if ($_SESSION['usser']['privilege'] == 1 || $_SESSION['usser']['privilege'] == 2 || $_SESSION['usser']['privilege'] == 3 || 
    $_SESSION['usser']['privilege'] == 4 || $_SESSION['usser']['privilege'] == 5) {
?>
<!-- Restricción de acceso -->

            <li>
                <a href="#">
                    <span class="icono"><i class="fas fa-graduation-cap"></i></span>
                    <span class="titulo">Escolar</span>
                    <i class="fas fa-angle-right icon icon-right"></i>
                </a>
                <ul class="sub_grupo">
                    <li><a href="matricula"><span class="titulo">Matricula</span></a></li>

<!-- Restricción de acceso -->
<?php
    if ($_SESSION['usser']['privilege'] == 1 || $_SESSION['usser']['privilege'] == 2 || $_SESSION['usser']['privilege'] == 5) {
?>
<!-- Restricción de acceso -->

                    <li><a href="estudiante"><span class="titulo">Estudiante</span></a></li>
                    <li><a href="apoderado"><span class="titulo">Apoderado</span></a></li>

<!-- Restricción de acceso -->
<?php
    }
?>
<!-- Restricción de acceso -->

                </ul>
            </li>

<!-- Restricción de acceso -->
<?php
}
if ($_SESSION['usser']['privilege'] == 1 || $_SESSION['usser']['privilege'] == 2) {
?>
<!-- Restricción de acceso -->

            <li>
                <a href="#">
                    <span class="icono"><i class="fas fa-user-friends"></i></span>
                    <span class="titulo">Personal</span>
                    <i class="fas fa-angle-right icon icon-right"></i>
                </a>
                <ul class="sub_grupo">
                    <li><a href="funcionario"><span class="titulo">Funcionario</span></a></li>
                    <li><a href=""><span class="titulo">Licencia</span></a></li>
                    <li><a href=""><span class="titulo">Permiso</span></a></li>
                </ul>
            </li>

             <li>
                <a href="#">
                    <span class="icono"><i class="fas fa-cogs"></i></span>
                    <span class="titulo">Setting</span>
                    <i class="fas fa-angle-right icon icon-right"></i>
                </a>
                <ul class="sub_grupo">
                    <li><a href=""><span class="titulo">Usuarios</span></a></li>
                </ul>
            </li>

            <li>
                <a href="#">
                    <span class="icono"><i class="fas fa-server"></i></span>
                    <span class="titulo">Mantenimiento</span>
                </a>
            </li>

<!-- Restricción de acceso -->
<?php
}
?>
<!-- Restricción de acceso -->

        </ul>
    </div>

    <!-- Main -->
    <div class="main">
        <div class="barra_superior">
            <div class="toggle_interactive">
                <div class="menu-btn__burger"></div>
            </div>

            <!-- AGREGAR EN BARRA SUPERIOR:
                NOTIFICACIONES
                MENSAJES
                ETC. -->

            <!-- img usuario -->
            <div class="logo_liceo">
                <div>
                    <?php echo "<span class='usser_name'>".$_SESSION['usser']['name']."</span>"; ?>
                </div>
                <img src="./assets/logo_liceo.png" alt="logo_liceo">
                <ul class="link_perfil">
                    <li><a href="#"><i class="fas fa-address-card icon"></i> Profile</a></li>
                    <li><a href="./controller/controller_exit.php"><i class="fas fa-sign-out-alt icon"></i> Logout</a></li>
                </ul>
            </div>
        </div>

        <!-- container para el contenido principal -->
        <div class="my_container">

