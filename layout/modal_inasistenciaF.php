<div id="modal_form_inasistenciaF" class="modal">
    <form action="#" class="modal-content animate inasistenciaF" method="POST" id="form_inasistenciaF">
        <div class="modal-header">
            <h2 id="titulo-modal_inasistenciaF"></h2>
        </div>

        <hr>

        <div class="modal-inputs">
            <div class="section_tipo_inasistencia">
                <label for="tipo_inasistencia">Tipo inasistencia</label>
                <select name="inasistencia" id="tipo_inasistencia"></select>
            </div>

            <div class="section">
                <div class="section_rut_funcionario">
                    <label for="inasistenciaF_rut">Rut Funcionario</label>
                    <input class="input-rut" type="text" id="inasistenciaF_rut" name="rut" maxlength="8">   <!-- REVISAR SI MANTENGO EL LIMITE DE CARACTERES -->
                    <span></span>
                    <input class="input-rut-dv" type="text" id="inasistenciaF_rut_dv" name="rut_dv">
                    <button class="btn" id="btn_agregar_funcionario_ausente"><i class="fas fa-plus-circle"></i></button>
                </div>
                <label class="n_funcionario_inasistencia" id="nombre_inasistenciaF"></label>
            </div>


            <div class="section inasistencia_fechas">
                <div>
                    <label for="inasistenciaF_fecha_inicio">Fecha inicio</label>
                    <input type="date" id="inasistenciaF_fecha_inicio" name="fecha">
                </div>
                <div>
                    <label for="inasistenciaF_fecha_termino">Fecha termino</label>
                    <input type="date" id="inasistenciaF_fecha_termino" name="fecha">
                </div>
                <div>
                    <label for="inasistenciaF_dias">Días</label>
                    <input type="text" id="inasistenciaF_dias" name="dias" disabled>
                </div>
                <div class="check">
                    <label for="check_medio_dia">
                        <input type="checkbox" id="check_medio_dia" name="medio_dia">    
                        1/2 día
                    </label>
                </div>
            </div>

            <div class="section inasistencia_si_reemplazo">
                <label>Cuenta con reemplazo?</label>
                <div class="section_radio">
                    <div>
                        <label for="reemplazo_si">
                            <input type="radio" name="reemplazo" value="1" id="reemplazo_si">
                            Si
                        </label>
                    </div>
    
                    <div>
                        <label for="reemplazo_no">
                            <input type="radio" name="reemplazo" value="2" id="reemplazo_no">
                            No
                        </label>
                    </div>
                </div>
            </div>

            <hr class="section_hidden reemplazo separador_reemplazo">

            <div class="section_hidden reemplazo">
                <div class="section inasistencia_ordinario">
                    <div>
                        <label for="inasistenciaF_ordinario">Número Ordinario</label>
                        <input class="" type="number" id="inasistenciaF_ordinario" name="ordinario">
                    </div>
                </div>
    
                <div class="section">
                    <div class="section_rut_funcionario">
                        <label for="inasistenciaF_reemplazo_rut">Funcionario reemplazante</label>
                        <input class="input-rut" type="text" id="inasistenciaF_reemplazo_rut" name="rut_reempalzo" maxlength="8">  <!-- REVISAR SI MANTENGO EL LIMITE DE CARACTERES -->
                        <span></span>
                        <input class="input-rut-dv" type="text" id="inasistenciaF_reemplazo_rut_dv" name="rut_dv_reemplazo">
                        <button class="btn" id="btn_agregar_funcionario_reemplazo"><i class="fas fa-plus-circle"></i></button>
                    </div>
                    <label id="inasistenciaF_nombre_reemplazo"></label>
                </div>
            </div>

        </div>

        <hr>

        <div class="modal-footer">
            <button class="btn btn-l btn-data" type="submit" id="btn_modal_registrar_insistenciaF">Registrar</button>
            <button class="btn btn-l btn-delete" type="reset" id="btn_modal_cancelar_inaistenciaF">Cancelar</button>
        </div>
    </form>
</div>
