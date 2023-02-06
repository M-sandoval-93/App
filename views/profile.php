<?php
    /* HEADER */
    include_once "./layout/header.php";
?>
  
   <!--  PRINCIPAL -->
        <!-- MAIN -->
        <main>
            <h1 class="title">Perfil usuario</h1>
            <ul class="breadcrumbs">
                <li><a href="#">Perfil</a></li>
                <li class="divider">/</li>
                <li><a href="#" class="active">Dashboard</a></li>
            </ul>

            <div class="div_profile">
                <div class="profile__content">
                    <form action="#" method="POST" id="form_profile">
                        <input class="modal-inputs" type="text" name="nombre" id="nombre_usuario" placeholder="nombre usuario">

                    </form>
                </div>
            </div>



        </main>
        <!-- MAIN -->
    <!--  PRINCIPAL -->
    

    </section>
    <!-- NAVBAR -->


    <script src="./Pluggins/jQuery/jquery-3.6.0.min.js"></script>
    <script src="./Pluggins/DataTables/datatables.min.js"></script>
    <script src="./Pluggins/SweetAlert2/sweetalert2.all.min.js"></script>

    <!-- <script src="./js/apoderados.js"></script> -->
    <script src="./js/dashboard.js"></script>
    
</body>
</html>