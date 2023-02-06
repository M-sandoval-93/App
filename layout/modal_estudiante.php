
<div id="modal_form_estudiantes" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <form action="#" class="modal-content animate estudiantes" method="POST" id="form_estudiantes">
        <div class="modal-header">
            <h2 id="titulo-modal_estudiante"></h2>
        </div>
        <hr>
        <div class="modal-inputs">
            <div class="section section_1">
                <div>
                    <label for="estudiante_fecha_ingreso">Fecha ingreso</label>
                    <input type="date" id="estudiante_fecha_ingreso" name="fecha_ingreso">
                </div>
                <div>
                    <label for="estudiante_matricula">Matricula</label>
                    <input type="text" id="estudiante_matricula" name="matricula">
                </div>
                <div>
                    <label for="estudiante_rut">Rut</label>
                    <input class="input-rut" type="text" id="estudiante_rut" name="rut" maxlength="8">
                    <span></span>
                    <input class="input-rut-dv" type="text" id="estudiante_dv_rut" maxlength="1">
                </div>
            </div>

            <div class="section section_2">
                <div>
                    <label for="estudiante_nombres">Nombres</label>
                    <input type="text" id="estudiante_nombres" name="nombres">
                </div>
                <div>
                    <label for="estudiante_ap_paterno">Apellido Paterno</label>
                    <input type="text" id="estudiante_ap_paterno" name="ap_paterno">
                </div>
                <div>
                    <label for="estudiante_ap_materno">Apellido Materno</label>
                    <input type="text" id="estudiante_ap_materno" name="ap_materno">
                </div>
            </div>
            
            <div class="section section_3">
                <div>
                    <label for="estudiante_nombre_social">Nombre Social</label>
                    <input type="text" id="estudiante_nombre_social" name="nombre_social">
                </div>
                
                <div>
                    <label for="estudiante_grado">Curso</label>
                    <select name="grado" id="estudiante_grado">
                        <option disable selected>Grado</option>
                        <option value="7">Septimo</option>
                        <option value="8">Octavo</option>
                        <option value="1">Primero</option>
                        <option value="2">Segundo</option>
                        <option value="3">Tercero</option>
                        <option value="4">Cuarto</option>
                    </select>
                    <select name="grado" id="estudiante_letra"></select>
                </div>
                
            </div>
           
            <div class="section section_4">
                <div>
                    <label for="estudiante_fecha_nacimiento">Fecha Nacimiento</label>
                    <input type="date" id="estudiante_fecha_nacimiento" name="fecha_nacimiento">
                </div>
                
                <div>
                    <label>Sexo Estudiante</label>
                    <select name="sexo" id="estudiante_sexo">
                        <option value="M" selected>Masculino</option>
                        <option value="F">Femenina</option>
                    </select>
                </div>
                
                <div>
                    <label>Beneficio junaeb</label>
                    <select name="junaeb" id="estudiante_junaeb">
                        <option value="1" selected>SI</option>
                        <option value="2">NO</option>
                    </select>
                </div>
            </div>
            

            <div class="section section_5">
                <div>
                    <label for="apoderado_titula_rut">Apoderado titular</label>
                    <input class="input-rut" type="text" id="apoderado_titular_rut" name="rut_titular" maxlength="8">
                    <span></span>
                    <input class="input-rut-dv" type="text" id="apoderado_titular_dv_rut" maxlength="1">
                    <button class="btn" id="btn_me_agregar_titular"><i class="fas fa-plus-circle"></i></button>
                </div>

                <label id="estudiante_ap_titular"></label>
            </div>

            <div class="section section_6">
                <div>
                    <label for="apoderado_suplente_rut">Apoderado titular</label>
                    <input class="input-rut" type="text" id="apoderado_suplente_rut" name="rut_suplente" maxlength="8">
                    <span></span>
                    <input class="input-rut-dv" type="text" id="apoderado_suplente_dv_rut" maxlength="1">
                    <button class="btn" id="btn_me_agregar_suplente"><i class="fas fa-plus-circle"></i></button>
                </div>
                
                <label id="estudiante_ap_suplente"></label>
            </div>
            
        </div>
        <hr>
        <div class="modal-footer">
            <button class="btn btn-l btn-data" type="submit" id="btn_modal_registrar_estudiante">Registrar</button>
            <button class="btn btn-l btn-delete" type="reset" id="btn_modal_cancelar_estudiante">Cancelar</button>
        </div>
    </form>
</div>
