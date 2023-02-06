<?php   include_once "./layout/header.php"; ?>

<!-- TITULO DEL LAYOUT -->


            <div class="titulo_main">
                <h1 class="titulo_main__titulo">Inasistencias</h1>
                <ul class="titulo_main__sub">
                    <li><a href="home">Home</a></li>
                    <li class="divider">/</li>
                    <li><a href="#" class="active">Inasistencias</a></li>
                </ul>
            </div>


            <!-- CONTENIDO PRINCIPAL -->
            <div class="d-flex justify-content-between mb-5">
                <button class="btn-lg btn-primary " type="button" id="btn_nueva_inasistencia"><i class="fas fa-user-plus icon"></i></button>
                <div class="row g-2">
                    <div class="col-6">
                        <button class="btn-lg btn-success">excel</button>
                    </div>
                    <div class="col-6">
                        <button class="btn-lg btn-danger">PDF</button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table id="inasistencias_funcionarios" class="table table-hover text-nowrap" style="width: 100%">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th></th>
                            <th>Funcionario</th>
                            <th>Inasistencia</th>
                            <th>Inicio</th>
                            <th>Termino</th>
                            <th>Días</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>


<!-- CONTENIDO PRINCIPAL -->


<!-- SCRIPT GENERALES DEL PROYECTO -->
<?php   include_once "./layout/footer.php"; ?>


    <!-- SCRIPT DE CADA PANTALLA -->
    <script src="./js/inasistenciaF.js" type="module"></script>


</body>
</html>