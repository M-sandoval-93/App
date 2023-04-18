<?php include_once "./layout/header.php"; ?>


            <!-- titulo del layout -->
            <div class="row d-flex align-items-center">
                <div class="col-md-5">
                    <!-- titulo del layout -->
                    <div class="titulo_main">
                        <h1 class="titulo_main__titulo">Registro Justificación Estudiantes</h1>
                        <ul class="titulo_main__sub">
                            <li><a href="home">Home</a></li>
                            <li class="divider">/</li>
                            <li><a href="#" class="active">Justificación estudiantes</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-7">
                    <!-- card -->
                    <div class="caja_tarjeta_2 d-flex justify-content-center">
                        <div class="tarjeta">
                            <div class="px-3">
                                <div class="numero d-flex justify-content-center" id="justificacion_diaria"></div>
                                <div class="detalle">Justificaciones 2023</div>
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
<?php if ($_SESSION['usser']['privilege'] != 4) { ?>
                <button type="button" class="btn-lg btn-primary" id="btn_nueva_justificacion" data-bs-toggle="modal" data-bs-target="#modal_registro_justificacion_falta">
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
                <table id="tabla_justificacion_estudiante" class="table table-hover text-nowrap" style="width: 100%">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th></th>
                            <th>Rut</th>
                            <th>Ap Paterno</th>
                            <th>Ap Materno</th>
                            <th>Nombres</th>
                            <th>Curso</th>
                            <th>Desde</th>
                            <th>Hasta</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>


<!-- modal -->
<?php   include_once "./layout/modal_justificacion.php";   ?>
i

<!-- script generales del proyecto -->
<?php   include_once "./layout/footer.php"; ?>


    <!-- script layout atrasos -->
    <script src="./js/justificacion.js?v=<?php  echo $_SESSION['version'] ?>" type="module"></script>


</body>
</html>