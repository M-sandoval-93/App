<?php   
        if ($_SESSION['usser']['id'] == 3) { // Controlador de acceso
            header("location: ./retraso");
        }
        include_once "./layout/header.php"; 
?>

            <!-- titulo del layout -->
            <div class="row d-flex align-items-center">
                <div class="col-md-5">
                    <!-- titulo del layout -->
                    <div class="titulo_main">
                        <h1 class="titulo_main__titulo">Registro Matricula estudiantes</h1>
                        <ul class="titulo_main__sub">
                            <li><a href="home">Home</a></li>
                            <li class="divider">/</li>
                            <li><a href="#" class="active">Matrícula estudiantes</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-7">
                    <!-- card -->
                    <div class="caja_tarjeta_2">
                        <div class="tarjeta">
                            <div>
                                <div class="numero" id="cantidad_matricula"></div>
                                <div class="detalle">Matriculados</div>
                            </div>
                            <div class="icono_tarjeta">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="tarjeta">
                            <div>
                                <div class="numero" id="cantidad_retiro"></div>
                                <div class="detalle">Retirados</div>
                            </div>
                            <div class="icono_tarjeta">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- main content table -->
            <div class="d-flex justify-content-between mb-4">
                <button type="button" class="btn-lg btn-primary" title="Nuevo registro" id="btn_nueva_matricula" data-bs-toggle="modal" data-bs-target="#modal_matricula">
                    <i class="fas fa-user-plus icon"></i>
                </button>
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
                <table id="tabla_matricula_estudiante" class="table table-hover text-nowrap" style="width: 100%">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th></th>
                            <th>Rut</th>
                            <th>Ap. Paterno</th>
                            <th>Ap. Materno</th>
                            <th>Nombres</th>
                            <th>Curso</th>
                            <th>Estado</th>
                            <!-- <th>Certificado</th> -->
                            <th>Edición</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

<!-- modal -->
<?php   require_once "./layout/modal_matricula.php";   ?>


<!-- script generales del proyecto -->
<?php   require_once "./layout/footer.php"; ?>


    <!-- script layout matriculas -->
    <script src="./js/matricula.js" type="module"></script>


</body>
</html>
