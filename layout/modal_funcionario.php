<!-- Modal funcionario -->
<div class="modal fade" id="modal_funcionario" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modal_funcionario_title">REGISTRAR NUEVO FUNCIONARIO</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" id="btn_close_justificacion"></button>
      </div>

      <form id="form_registro_funcionario">
        <div class="modal-body">

          <div class="row g-3">
            <div class="col-sm-7 col-md-8">
                <div class="col-sm-12 col-md-10 text-center">
                  <div class="text-secondary h3" id="texto_secundario">Nuevo registro N°</div>
                  <div class="text-primary h3" id="cantidad_nuevo_registro"></div>
                </div>
            </div>

            <div class="col-sm-5 col-md-3">
              <label for="fecha_nacimiento_funcionario" class="form-label">Fecha nacimiento <span class="text-danger fs-5">*</span></label>
              <input type="date" id="fecha_nacimiento_funcionario" class="form-control">
            </div>
          </div>

          <div class="row align-items-center mt-3">
            <label for="rut_funcionario" class="form-label">Rut <span class="text-danger fs-5">*</span></label>
            <div class="col-sm-6 col-lg-5">
              <div class="row align-items-center">
                <div class="col-7 rut">
                  <input type="text" class="form-control text-center" id="rut_funcionario" required>
                </div>
                <div class="col-1 not_padding text-center">
                  <span>-</span>
                </div>
                <div class="col-4 dv_rut">
                  <input type="text" class="form-control text-center" id="dv_rut_funcionario" disabled>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-7">
              <span class="form-text" id="informacion_rut">Rut sin puntos, sin guión y sin dígito verificador</span>
            </div>
          </div>

          <div class="row g-3 mt-4">
            <div class="col-md-4">
              <label for="nombre_funcionario" class="form-label">Nombres <span class="text-danger fs-5">*</span></label>
              <input type="text" class="form-control" id="nombre_funcionario">
            </div>
            <div class="col-md-4">
              <label for="ap_funcionario" class="form-label">Apellido paterno <span class="text-danger fs-5">*</span></label>
              <input type="text" class="form-control" id="ap_funcionario">
            </div>
            <div class="col-md-4">
              <label for="am_funcionario" class="form-label">Apellido materno <span class="text-danger fs-5">*</span></label>
              <input type="text" class="form-control" id="am_funcionario">
            </div>
          </div>

          <div class="row g-3 mt-4">
            <div class="col-md-4"> <!-- llenar con ajax -->
              <label for="tipo_funcionario" class="form-label">Tipo funcionario <span class="text-danger fs-5">*</span></label>
              <select class="form-select" id="tipo_funcionario">
                <!-- <option selected> -------------- </option> -->
                <!-- <option value=7> Asistente </option>
                <option value=8> Docente </option>
                <option value=8> Docente directivo </option>
                <option value=1> Paradocente </option> -->
              </select>
            </div>

            <div class="col-md-5"> <!-- llenar con ajax -->
              <label for="departamento_funcionario" class="form-label">Departamento <span class="text-danger fs-5">*</span></label>
              <select class="form-select" id="departamento_funcionario">
                <!-- <option selected> -------------- </option>
                <option value=7> Informática </option>
                <option value=8> UTP </option>
                <option value=8> Inspectoría general </option>
                <option value=1> Orientación </option> -->
              </select>
            </div>

            <div class="col-md-3">
              <label for="sexo_funcionario" class="form-label">Sexo <span class="text-danger fs-5">*</span></label>
              <select class="form-select" id="sexo_funcionario">
                <option selected disabled> -------------- </option>
                <option value="M">Masculino</option>
                <option value="F">Femenina</option>
              </select>
            </div>


          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-lg" data-bs-dismiss="modal" id="btn_funcionario_cancelar">Cancelar</button>
          <button type="button" class="btn btn-success btn-lg" id="btn_funcionario_registrar">Registrar</button>
        </div>

      </form>
    </div>
  </div>
</div>