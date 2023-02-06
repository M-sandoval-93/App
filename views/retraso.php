<?php   include_once "./layout/header.php"; ?>

            <!-- titulo del layout -->
            <div class="row d-flex align-items-center">
                <div class="col-md-5">
                    <!-- titulo del layout -->
                    <div class="titulo_main">
                        <h1 class="titulo_main__titulo">Registro Retraso Estudiantes</h1>
                        <ul class="titulo_main__sub">
                            <li><a href="home">Home</a></li>
                            <li class="divider">/</li>
                            <li><a href="#" class="active">Retraso estudiantes</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-7">
                    <!-- card -->
                    <div class="caja_tarjeta_2">
                        <div class="tarjeta">
                            <div>
                                <div class="numero" id="retraso_diario"></div>
                                <div class="detalle">Retrasos durante el día</div>
                            </div>
                            <div class="icono_tarjeta">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="tarjeta">
                            <div>
                                <div class="numero" id="retraso_total"></div>
                                <div class="detalle">Retrasos durante el año</div>
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
                <button type="button" class="btn-lg btn-primary" id="btn_nuevo_retraso" data-bs-toggle="modal" data-bs-target="#modal_registro_retraso">
                    <i class="fas fa-user-plus icon"></i>
                </button>
                <div class="row g-2">
                    <div class="col-4">
                        <button class="btn-lg btn-success" id="btn_excel" title="Exportar Excel"><i class="fas fa-file-excel icon"></i></button>
                    </div>
                    <div class="col-4">
                        <button class="btn-lg btn-secondary" id="btn_csv" title="Exportar CSV"><i class="fas fa-file-csv icon"></i></button>
                    </div>
                    <div class="col-4">
                        <button class="btn-lg btn-danger" id="btn_pdf" title="Exportar PDF"><i class="fas fa-file-pdf icon"></i></button>
                        <!-- <button class="btn-lg btn-danger" id="btn_pdf_atraso" title="Exportar PDF"><i class="fas fa-user-check"></i></button> -->
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table id="tabla_retraso" class="table table-hover text-nowrap" style="width: 100%">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Rut</th>
                            <th>Ap Paterno</th>
                            <th>Ap Materno</th>
                            <th>Nombres</th>
                            <th>Curso</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

<!-- modal -->
<?php   include_once "./layout/modal_retraso.php";   ?>


<!-- script generales del proyecto -->
<?php   include_once "./layout/footer.php"; ?>


    <!-- script layout atrasos -->
    <script src="./js/retraso.js" type="module"></script>


</body>
</html>

