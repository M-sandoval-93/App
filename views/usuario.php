<?php include_once "./layout/header.php"; ?>

            <!-- titulo del layout -->
            <div class="row d-flex align-items-center mb-4">
                <div class="col-md-5">
                    <!-- titulo del layout -->
                    <div class="titulo_main">
                        <h1 class="titulo_main__titulo">Administración cuentas de usuario</h1>
                        <ul class="titulo_main__sub">
                            <li><a href="home">Home</a></li>
                            <li class="divider">/</li>
                            <li><a href="#" class="active">Cuentas de usuario</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-7 "> 
                    <!-- card -->
                    <div class="caja_tarjeta_2 d-flex justify-content-center">
                        <div class="tarjeta">
                            <div>
                                <div class="numero d-flex justify-content-center" id="cantidad_usuario"></div>
                                <div class="detalle">Cantidad de usuarios</div>
                            </div>
                            <div class="icono_tarjeta ms-2">
                                <i class="fas fa-user-friends"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- table structure -->
            <div class="table-responsive">
                <table id="tabla_usuario" class="table table-hover text-nowrap" style="width: 100%">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Nombre cuenta</th>
                            <th>Funcionario</th>
                            <th>Departamento</th>
                            <th>Privilegio</th>
                            <th>Estado</th>
                            <th>Edición</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

<!-- modal -->
<?php   include_once "./layout/modal_usuario.php";   ?>

<!-- script generales del proyecto -->
<?php   include_once "./layout/footer.php"; ?>

    <!-- script layout apoderado -->
    <script src="./js/usuario.js?v=<?php  echo $_SESSION['version'] ?>" type="module"></script>

</body>
</html>