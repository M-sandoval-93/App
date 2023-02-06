<?php
    /* HEADER */
    include_once "./layout/header.php";
?>
  
  <main>
            <h1 class="title">Funcionarios</h1>
            <ul class="breadcrumbs">
                <li><a href="#">Inasistencias</a></li>
                <li class="divider">/</li>
                <li><a href="#" class="active">Dashboard</a></li>
            </ul>

            <div class="nuevo_registro">
                <button class="btn btn-s btn-data" type="button" id="btn_nueva_inasistencia"><i class="fas fa-user-plus icon"></i></button>
                <button type="button" class="btn btn-primary">Primary</button>
            </div>

            <table id="inasistencias_funcionarios" class="display table tabl-hover text-nowrap" style="width: 100%">
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

            <?php
            // SE INCLUYEN LOS MODALES A SER UTILIZADOS
            include_once "./layout/modal_inasistenciaF.php";
            include_once "./layout/modal_funcionario.php";
            ?>

        </main>
        <!-- MAIN -->
    <!--  PRINCIPAL -->


    </section>
    <!-- NAVBAR -->


    <script src="./Pluggins/jQuery/jquery-3.6.0.min.js"></script>
    <script src="./Pluggins/DataTables/datatables.min.js"></script>
    <script src="./Pluggins/SweetAlert2/sweetalert2.all.min.js"></script>

    <script src="./js/dashboard.js"></script>
    <script src="./js/inasistenciaF.js" type="module"></script>
    
</body>
</html>