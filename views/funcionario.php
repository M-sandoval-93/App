<?php include_once "./layout/header.php"; ?>

            <!-- titulo del layout -->
            <div class="row d-flex align-items-center">
                <div class="col-md-5">
                    <!-- titulo del layout -->
                    <div class="titulo_main">
                        <h1 class="titulo_main__titulo">Registro Funcionarios</h1>
                        <ul class="titulo_main__sub">
                            <li><a href="home">Home</a></li>
                            <li class="divider">/</li>
                            <li><a href="#" class="active">Funcionarios</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-7 "> 
                    <!-- card -->
                    <div class="caja_tarjeta_2 d-flex justify-content-center">
                        <div class="tarjeta">
                            <div>
                                <div class="numero d-flex justify-content-center" id="cantidad_funcionario"></div>
                                <div class="detalle">Cantidad Funcionarios</div>
                            </div>
                            <div class="icono_tarjeta ms-2">
                                <i class="fas fa-user-friends"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- main content table -->
            <div class="d-flex justify-content-between mb-4">
                <button type="button" class="btn-lg btn-primary" id="btn_nuevo_funcionario" title="Nuevo funcionario" data-bs-toggle="modal" data-bs-target="#modal_funcionario">
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
                <table id="tabla_funcionario" class="table table-hover text-nowrap" style="width: 100%">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th></th>
                            <th>Rut</th>
                            <th>Ap Paterno</th>
                            <th>Ap Materno</th>
                            <th>Nombres</th>
                            <th>Tipo funcionario</th>
                            <th>Estado</th>
                            <th>Edición</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

<!-- modal -->
<?php   include_once "./layout/modal_funcionario.php";   ?>

<!-- script generales del proyecto -->
<?php   include_once "./layout/footer.php"; ?>

    <!-- script layout apoderado -->
    <script src="./js/funcionario.js?v=<?php  echo $_SESSION['version'] ?>" type="module"></script>

</body>
</html>