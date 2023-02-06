<div id="modal_form_apoderados" class="modal">
    <form action="#" class="modal-content animate" method="POST" id="form_apoderados">
        <div class="modal-header">
            <h2 id="titulo-modal"></h2>
        </div>
        <hr>
        <div class="modal-inputs">
            <label for="apoderado_rut">Rut</label>
            <input class="input-rut" type="text" id="apoderado_rut" name="rut" maxlength="8">
            <span></span>
            <input class="input-rut-dv" type="text" id="apoderado_dv_rut" maxlength="1">

            <label for="apoderado_nombres">Nombres</label>
            <input type="text" id="apoderado_nombres" name="nombres">

            <label for="apoderado_ap_paterno">Apellido Paterno</label>
            <input type="text" id="apoderado_ap_paterno" name="ap_paterno">

            <label for="apoderado_ap_materno">Apellido Materno</label>
            <input type="text" id="apoderado_ap_materno" name="ap_materno">

            <label for="apoderado_fono">Teléfono</label>
            <input type="text" id="apoderado_codigo_fono" disabled>
            <span></span>
            <input type="text" id="apoderado_fono" name="fono" maxlength="8">
        </div>
        <div class="modal-footer">
            <button class="btn btn-l btn-data" type="submit" id="btn_modal_registrar_apoderado">Registrar</button>
            <button class="btn btn-l btn-delete" type="reset" id="btn_modal_cancelar_apoderado">Cancelar</button>
        </div>
    </form>
</div>