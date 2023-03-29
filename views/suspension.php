<?php   
    include_once "./layout/header.php";

    // Controlador de acceso
    if ($_SESSION['usser']['privilege'] == 5) { header("location: ./matricula"); }
?>

            <!-- titulo del layout -->
            <div class="row d-flex align-items-center">
                <div class="col-md-5">
                    <!-- titulo del layout -->
                    <div class="titulo_main">
                        <h1 class="titulo_main__titulo">Registro Suspenciones</h1>
                        <ul class="titulo_main__sub">
                            <li><a href="home">Home</a></li>
                            <li class="divider">/</li>
                            <li><a href="#" class="active">Suspensión</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-7">
                    <!-- card -->
                    <div class="caja_tarjeta_2">
                        <div class="tarjeta">
                            <div>
                                <div class="numero" id="cantidad_suspension_anual"></div>
                                <div class="detalle">Suspensiones aplicadas</div>
                            </div>
                            <div class="icono_tarjeta">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="tarjeta">
                            <div>
                                <div class="numero" id="cantidad_suspension_activa"></div>
                                <div class="detalle">Suspensiones en curso</div>
                            </div>
                            <div class="icono_tarjeta">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- main content table -->
            <div class="d-flex justify-content-end mb-4">
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
                <table id="tabla_suspension_matricula" class="table table-hover text-nowrap" style="width: 100%">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th></th>
                            <th>Rut</th>
                            <th>Nombres</th>
                            <th>Curso</th>
                            <th>Fecha inicio</th>
                            <th>Fecha término</th>
                            <th>Días</th>
                            <th>Estado</th>
                            <th>Accion</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>


<!-- script generales del proyecto -->
<?php   include_once "./layout/footer.php"; ?>

    <!-- script layout apoderado -->
    <script src="./js/suspension.js" type="module"></script>

</body>
</html>