<?php include_once "./layout/header.php"; ?>

<!-- CONTENIDO PRINCIPAL -->

            <!-- titulo del layout -->
            <div class="row d-flex align-items-center">
                <div class="col-md-6">
                    <!-- titulo del layout -->
                    <div class="titulo_main">
                        <h1 class="titulo_main__titulo">Home</h1>
                        <ul class="titulo_main__sub">
                            <li><a href="home">Home</a></li>
                            <li class="divider">/</li>
                            <li><a href="#" class="active">Liceo Bicentenario Valentín Letelier</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <!-- card -->
                    <div class="caja_tarjeta_3">
                        <div class="tarjeta">
                            <div>
                                <div class="detalle pb-3 pt-2 fw-bolder fs-2" id="date"></div>
                                <div class="numero fw-bolder fs-1" id="time"></div>
                            </div>
                            <div class="icono_tarjeta_2">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CONTENIDO CENTRAL -->
            <div class="row">
                <!-- Tabla de cursos -->
                <div class="col-md-6">
                    <div class="texto_tabla pb-3 fw-bolder fs-2">Lista de cursos por grado</div>
                    <div class="table-responsive">
                        <table id="tabla_grados" class="table table-hover text-nowrap" style="width: 100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Grado</th>
                                    <th>Nivel</th>
                                    <th>Cantidad estudiante</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tarjetas de información -->
                <div class="col-md-6">
                    <div class="row d-flex align-items-center">
                        <!-- Tarjeta cantidad matriculas -->
                        <div class="col-md-12">
                            <div class="caja_tarjeta_3">
                                <div class="tarjeta">
                                    <div>
                                        <div class="numero fw-bolder pt-3 fs-1" id="cantidad_estudiante_matriculado"></div>
                                        <div class="detalle pb-3 fw-bolder fs-3">Estudiantes matriculados</div>
                                    </div>
                                    <div class="icono_tarjeta_2">
                                    <i class="fas fa-user-graduate"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tarjeta cantidad funcionarios -->
                        <div class="col-md-12">
                            <div class="caja_tarjeta_3">
                                <div class="tarjeta">
                                    <div>
                                        <div class="numero fw-bolder pt-3 fs-1" id="cantidad_funcionario"></div>
                                        <div class="detalle pb-3 fw-bolder fs-3">Funcionarios contratados</div>
                                    </div>
                                    <div class="icono_tarjeta_2">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                            </div>
                        </div>






                    </div>

                </div>
            </div>

            

            <!-- LISTA PRINCIPAL //// NOTA, PUEDEN IR LISTA DE ALGUN DETALLE GENERAL O MÁS TARJETAS DE REDIRECCIÓN -->
            <!-- <div class="detalles"> -->
                <!-- LISTA DE SOLICITUDES -->
                <!-- <div class="recepcion_orden">
                    <div class="cabecera">
                        <h2>Orden recibida</h2>
                        <a href="" class="boton">View All</a>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <td>Ordinario</td>
                                <td>Departamento</td>
                                <td>Financiamiento</td>
                                <td>Estado</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>274</td>
                                <td>Informática</td>
                                <td>SEP</td>
                                <td><span class="estado enviado">Enviada</span></td>
                            </tr>
                            <tr>
                                <td>274</td>
                                <td>Informática</td>
                                <td>SEP</td>
                                <td><span class="estado pendiente">pendiente</span></td>
                            </tr>
                            <tr>
                                <td>274</td>
                                <td>Informática</td>
                                <td>SEP</td>
                                <td><span class="estado">Enviada</span></td>
                            </tr>
                            <tr>
                                <td>274</td>
                                <td>Informática</td>
                                <td>SEP</td>
                                <td><span class="estado">Enviada</span></td>
                            </tr>
                            <tr>
                                <td>274</td>
                                <td>Informática</td>
                                <td>SEP</td>
                                <td><span class="estado">Enviada</span></td>
                            </tr>
                            <tr>
                                <td>274</td>
                                <td>Informática</td>
                                <td>SEP</td>
                                <td><span class="estado">Enviada</span></td>
                            </tr>
                            <tr>
                                <td>274</td>
                                <td>Informática</td>
                                <td>SEP</td>
                                <td><span class="estado">Enviada</span></td>
                            </tr>
                            <tr>
                                <td>274</td>
                                <td>Informática</td>
                                <td>SEP</td>
                                <td><span class="estado">Enviada</span></td>
                            </tr>
                            <tr>
                                <td>274</td>
                                <td>Informática</td>
                                <td>SEP</td>
                                <td><span class="estado">Enviada</span></td>
                            </tr>
                            <tr>
                                <td>274</td>
                                <td>Informática</td>
                                <td>SEP</td>
                                <td><span class="estado">Enviada</span></td>
                            </tr>
                            <tr>
                                <td>274</td>
                                <td>Informática</td>
                                <td>SEP</td>
                                <td><span class="estado">Enviada</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div> -->

                <!-- New Cliente -->
                <!-- <div class="cliente_actual">
                    <div class="cabecera">
                        <h2>Lista de usuarios</h2>
                    </div>

                    <table>
                        <tr>
                            <td width="60px"><div class="boton"><img src="./assets/logo_liceo.png" alt=""></div></td>
                            <td><h4>David<br><span>Italy</span></h4></td>
                        </tr>
                        <tr>
                            <td width="60px"><div class="boton"><img src="./assets/logo_liceo.png" alt=""></div></td>
                            <td><h4>David<br><span>Italy</span></h4></td>
                        </tr>
                        <tr>
                            <td width="60px"><div class="boton"><img src="./assets/logo_liceo.png" alt=""></div></td>
                            <td><h4>David<br><span>Italy</span></h4></td>
                        </tr>
                        <tr>
                            <td width="60px"><div class="boton"><img src="./assets/logo_liceo.png" alt=""></div></td>
                            <td><h4>David<br><span>Italy</span></h4></td>
                        </tr>
                        <tr>
                            <td width="60px"><div class="boton"><img src="./assets/logo_liceo.png" alt=""></div></td>
                            <td><h4>David<br><span>Italy</span></h4></td>
                        </tr>
                        <tr>
                            <td width="60px"><div class="boton"><img src="./assets/logo_liceo.png" alt=""></div></td>
                            <td><h4>David<br><span>Italy</span></h4></td>
                        </tr>

                    </table>
                </div>
            </div> 
        </div>
    </div> -->

<!-- CONTENIDO PRINCIPAL -->

<?php   include_once "./layout/footer.php"; ?>

    <!-- script módulo home -->
    <script src="./js/home.js" type="module"></script>

</body>
</html>