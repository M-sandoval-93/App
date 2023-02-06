<?php
    if (!isset($_SESSION['usser']['name'])) {
        header("location: ../");
    }

    /* HEADER */
    include_once "./layout/header.php";

?>
  
   <!--  PRINCIPAL -->
        <!-- MAIN -->
        <h1>seccion mantenimiento</h1>

        <div>
            <div>
                <form action="#" method="post">
                    <div>
                        <span>Seleccionar Archivo</span>
                        <input type="file" name="excel" id="excel" accept=".xls, .xlsx">
                    </div>

                    <div>
                        <input type="submit" name="subir" value="SubirArchivo" id="subir">
                    </div>
                </form>
            </div>
        </div>




        <!-- MAIN -->
    <!--  PRINCIPAL -->


    </section>
    <!-- NAVBAR -->


    <script src="./Pluggins/jQuery/jquery-3.6.0.min.js"></script>
    <script src="./Pluggins/SweetAlert2/sweetalert2.all.min.js"></script>
    <script src="./js/dashboard.js"></script>
    
</body>
</html>