
<div class="modal fade" id="modal_estudiante" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modal_estudiante_tittle">REGISTRAR ESTUDIANTE</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="form_registro_estudiante">
        <div class="modal-body">

          <div class="row g-3">
            <div class="col-sm-7 col-md-8">
                <div class="col-sm-12 col-md-10 text-center">
                  <div class="text-secondary h3" id="texto_secundario">Nuevo registro N°</div>
                  <div class="text-primary h3" id="cantidad_nuevo_registro"></div>
                </div>
            </div>
            <div class="col-sm-5 col-md-3">
              <label for="fecha_ingreso_estudiante" class="form-label">Fecha ingreso <span class="text-danger fs-5">*</span></label>
              <input type="date" id="fecha_ingreso_estudiante" class="form-control">
            </div>
          </div>

          <div class="row align-items-center mt-3">
            <label for="rut_estudiante" class="form-label">Rut <span class="text-danger fs-5">*</span></label>
            <div class="col-sm-6 col-lg-5">
              <div class="row align-items-center">
                <div class="col-7 rut">
                  <input type="text" class="form-control text-center" id="rut_estudiante" required>
                </div>
                <div class="col-1 not_padding text-center">
                  <span>-</span>
                </div>
                <div class="col-4 dv_rut">
                  <input type="text" class="form-control text-center" id="dv_rut_estudiante" disabled>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-7">
              <span class="form-text" id="informacion_rut">Rut sin puntos, sin guión y sin dígito verificador</span>
            </div>
          </div>

          <div class="row g-3 mt-4">
            <div class="col-md-4">
              <label for="nombre_estudiante" class="form-label">Nombres estudiante <span class="text-danger fs-5">*</span></label>
              <input type="text" class="form-control" id="nombre_estudiante">
            </div>
            <div class="col-md-4">
              <label for="ap_estudiante" class="form-label">Apellido paterno <span class="text-danger fs-5">*</span></label>
              <input type="text" class="form-control" id="ap_estudiante">
            </div>
            <div class="col-md-4">
              <label for="am_estudiante" class="form-label">Apellido materno <span class="text-danger fs-5">*</span></label>
              <input type="text" class="form-control" id="am_estudiante">
            </div>
          </div>

          <div class="row g-3 mt-4">
            <div class="col-md-4">
              <label for="n_social_estudiante" class="form-label">Nombre social</label>
              <input type="text" class="form-control" id="n_social_estudiante">
            </div>
            <div class="col-md-3">
              <label for="sexo_estudiante" class="form-label">Sexo <span class="text-danger fs-5">*</span></label>
              <select class="form-select" id="sexo_estudiante">
                <option selected disabled> ------- </option>
                <option value="M">Masculino</option>
                <option value="F">Femenina</option>
              </select>
            </div>
            <div class="col-sm-6 col-md-3">
              <label for="fecha_nacimiento" class="form-label">Fecha nacimiento <span class="text-danger fs-5">*</span></label>
              <input type="date" class="form-control" id="fecha_nacimiento">
            </div>
            <div class="col-sm-6 col-md-2">
              <label for="beneficio_junaeb" class="form-label">Junaeb <span class="text-danger fs-5">*</span></label>
              <select class="form-select" id="beneficio_junaeb">
                <option selected disabled> --- </option>
                <option value="1">SI</option>
                <option value="2">NO</option>
              </select>
            </div>
          </div>
        </div>


        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-lg" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-success btn-lg" id="btn_registrar_estudiante">Registrar</button>
        </div>
      </form>

    </div>
  </div>
</div>




