<section id="modal_form" class="modal" hidden>
    <div class="flex" id="flex">
        <div class="contenido-modal">
            
            <!-- HEADER DEL MODAL -->
            <div class="modal-header flex">
                <h2>Apoderados</h2>
            </div>

            <!-- BODY DEL MODAL -->
            <div class="modal-body">
                <form method="post" class="apoderado">
                    <div class="apoderado-rut">
                        <input type="text" name="rut" id="id_rut">
                        <input type="text" name="dv_rut" id="id_dv_rut">
                    </div>
                    <div class="apoderado-datos">
                        <input type="text" name="nombre" id="apoderado_nombre">
                        <input type="text" name="" id="apoderado_apellido_oaterno">
                    </div>

                    <div class="apoderado-sexo">
                        <input type="radio" name="masculino" id="masculino" value="M">
                        <label for="masculino">Masculino</label>
                        <input type="radio" name="femenino" id="femenino" value="F">
                        <label for="femenino">Femenino</label>
                    </div>
                </form>
            </div>

            <!-- FOOTER DEL MODAL -->
            <div class="modal-footer">
                <button class="btn-m btn-add" id="btn_registrar">Registrar</button>
                <button class="btn-m btn-cancel" id="btn_cancelar">Cancelar</button>
            </div>

        </div>
    </div>
</section>

	

