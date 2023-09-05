<?php include_once "./layout/header.php"; ?>

            <!-- titulo del layout -->
            <div class="row d-flex align-items-center">
                <div class="col-md-5">
                    <!-- titulo del layout -->
                    <div class="titulo_main">
                        <h1 class="titulo_main__titulo">Registro licencia funcionarios</h1>
                        <ul class="titulo_main__sub">
                            <li><a href="home">Home</a></li>
                            <li class="divider">/</li>
                            <li><a href="#" class="active">Licencia funcionarios</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-7">
                    <!-- card -->
                    <div class="caja_tarjeta_2 d-flex justify-content-center">
                        <div class="tarjeta">
                            <div class="px-3">
                                <div class="numero d-flex justify-content-center" id="Licencias recibidas"></div>
                                <div class="detalle">Licencias 2023</div>
                            </div>
                            <div class="icono_tarjeta">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de contenido principal -->
            <div class="d-flex justify-content-between mb-4">
<?php if ($_SESSION['usser']['privilege'] != 4 && $_SESSION['usser']['privilege'] != 5) { ?>
                <button type="button" class="btn-lg btn-primary" id="btn_nueva_licencia" data-bs-toggle="modal" data-bs-target="#">
                    <i class="fas fa-user-plus icon"></i>
                </button>
<?php } ?>
                <div class="row g-2">
                    <div class="col-6">
                        <button class="btn-lg btn-success" id="btn_excel" title="Exportar Excel"><i class="fas fa-file-excel icon"></i></button>
                    </div>
                    <div class="col-6">
                        <button class="btn-lg btn-secondary" id="btn_csv" title="Exportar CSV"><i class="fas fa-file-csv icon"></i></button>
                    </div>
                </div>
            </div>


            <div class="table-responsive">
                <table id="tabla_licencia_funcionario" class="table table-hover text-nowrap" style="width: 100%">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Rut</th>
                            <th>Nombre funcionario</th>
                            <th>Departamento</th>
                            <th>Desde</th>
                            <th>Hasta</th>
                            <th>Días</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>


<!-- modal -->
<?php   //include_once "./layout/modal_justificacion.php";   ?>

<!-- script generales del proyecto -->
<?php   include_once "./layout/footer.php"; ?>


    <!-- script layout atrasos -->
    <!-- <script src="./js/justificacion.js?v=<?php  //echo $_SESSION['version'] ?>" type="module"></script> -->


</body>
</html>