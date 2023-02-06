<?php
    /* HEADER */
    include_once "./layout/header.php";
?>
  
   <!--  PRINCIPAL -->
        <!-- MAIN -->
        <main>
            <h1 class="title">Estudiantes</h1>
            <ul class="breadcrumbs">
                <li><a href="#">Estudiantes</a></li>
                <li class="divider">/</li>
                <li><a href="#" class="active">Dashboard</a></li>
            </ul>

            <div class="nuevo_registro">
                <button class="btn btn-s btn-data" type="button" id="btn_nuevo_estudiante"><i class="fas fa-user-plus icon"></i></button>
            </div>

            <table id="estudiantes" class="display table tabl-hover text-nowrap" style="width: 100%">
                <thead>
                    <tr>
                        <th></th>
                        <th>Id</th>
                        <th>Rut</th>
                        <th>Ap. Paterno</th>
                        <th>Ap. Materno</th>
                        <th>Nombres</th>
                        <th>Curso</th>
                        <th>Estado</th>
                        <th>Edición</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

            <?php
            include_once "./layout/modal_estudiante.php";
            include_once "./layout/modal_apoderado.php";
            ?>

        </main>
        <!-- MAIN -->
    <!--  PRINCIPAL -->


    </section>
    <!-- NAVBAR -->


    <!-- <script src="./Pluggins/jQuery/jquery-3.6.0.min.js"></script>
    <script src="./Pluggins/DataTables/datatables.min.js"></script>
    <script src="./Pluggins/SweetAlert2/sweetalert2.all.min.js"></script>

    <script src="./js/dashboard.js"></script>
    <script src="./js/estudiante.js"></script> -->
    
</body>
</html>