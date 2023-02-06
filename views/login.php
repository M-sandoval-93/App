<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Icono de la página -->
    <link rel="shortcut icon" href="./assets/logo_liceo.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,600;1,600&display=swap" rel="stylesheet"> 

    <!-- icons -->
    <!-- <script src="https://kit.fontawesome.com/a81368914c.js"></script> -->
    <link rel="stylesheet" href="./Pluggins/Fontawesome-5.15.4/css/all.min.css">

    <!-- Style -->
    <link rel="stylesheet" href="./css/loggin.css">
    <link rel="stylesheet" href="./css/normalize.css">
    <link rel="stylesheet" href="./Pluggins/SweetAlert2/sweetalert2.min.css">

    <title>Liceo Valentín Letelier</title>
</head>
<body>
    <div class="container">
        <div class="img">
            <img src="./assets/students.svg" alt="">
        </div>

        <div class="login-container">
            <form action="" method="post" id="id_form_login" autocomplete="off">
                <img class="login-container__logo" src="./assets/logo_liceo.png" alt="Logo del liceo">
                <h2>Inicio de Sesión</h2>
                <div class="input-div one">
                    <div class="i">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h5>UsserName</h5>
                        <input class="input" type="text" name="usuario" id="id_usuario" required>
                    </div>
                </div>

                <div class="input-div two">
                    <div class="i">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div>
                        <h5>Password</h5>
                        <input class="input" type="password" name="clave" id="id_clave" required>
                    </div>
                </div>

                <a href="#" class="link_recuperar">Recuperar contraseña</a>

                <input class="btn" type="submit" value="Login">

                <div class="i_redes_sociales">
                    <a href="#"><i class="fab fa-facebook facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram instagram"></i></a>
                    <a href="http://liceovalentinletelier.cl" target="_blank"><i class="fas fa-graduation-cap web"></i></a>
                </div>

            </form>
        </div>
    </div>

    <script src="./Pluggins/jQuery/jquery-3.6.0.min.js"></script>
    <script src="./Pluggins/SweetAlert2/sweetalert2.all.min.js"></script>
    <script src="./js/login.js"></script> 
    
</body>
</html>

