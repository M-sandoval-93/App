<div class="modal fade" id="modal_apoderado" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modal_apoderado_tittle">REGISTRAR APODERADO</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="form_registro_apoderado">
        <div class="modal-body">

          <div class="row g-3">
            <!-- <div class="col-sm-12 col-md-12"> -->
                <div class="col-sm-12 col-md-12 text-center">
                  <div class="text-secondary h3" id="texto_secundario">Nuevo registro de apoderado N°</div>
                  <div class="text-primary h3" id="cantidad_nuevo_registro"></div>
                </div>
            <!-- </div> -->
          </div>

          <div class="row align-items-center mt-3">
            <label for="rut_apoderado" class="form-label">Rut <span class="text-danger fs-5">*</span></label>
            <div class="col-sm-6 col-lg-5">
              <div class="row align-items-center">
                <div class="col-7 rut">
                  <input type="text" class="form-control text-center" id="rut_apoderado" required>
                </div>
                <div class="col-1 not_padding text-center">
                  <span>-</span>
                </div>
                <div class="col-4 dv_rut">
                  <input type="text" class="form-control text-center" id="dv_rut_apoderado" disabled required>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-7">
              <span class="form-text" id="informacion_rut">Rut sin puntos, sin guión y sin dígito verificador</span>
            </div>
          </div>

          <div class="row g-3 mt-4">
            <div class="col-md-4">
              <label for="nombre_apoderado" class="form-label">Nombres apoderado <span class="text-danger fs-5">*</span></label>
              <input type="text" class="form-control" id="nombre_apoderado" required>
            </div>
            <div class="col-md-4">
              <label for="ap_apoderado" class="form-label">Apellido paterno <span class="text-danger fs-5">*</span></label>
              <input type="text" class="form-control" id="ap_apoderado" required>
            </div>
            <div class="col-md-4">
              <label for="am_apoderado" class="form-label">Apellido materno <span class="text-danger fs-5">*</span></label>
              <input type="text" class="form-control" id="am_apoderado" required>
            </div>
          </div>

          <div class="row g-3 mt-4">
            <div class="col-md-4">
              <label for="telefono" class="form-label">Teléfono <span class="text-danger fs-5">*</span></label>
              <div class="col-12">
                <div class="row align-items-center">
                  <div class="col-4 rut">
                    <input type="text" class="form-control text-center" id="codigo_area" disabled value="+ 569">
                  </div>
                  <div class="col-1 not_padding text-center">
                    <span>-</span>
                  </div>
                  <div class="col-7 dv_rut">
                    <input type="text" class="form-control text-center" id="telefono" required>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-md-8">
              <label for="direccion" class="form-label">Dirección <span class="text-danger fs-5">*</span></label>
              <input type="text" class="form-control" id="direccion" required>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-lg" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-success btn-lg" id="btn_registrar_apoderado">Registrar</button>
        </div>
      </form>
    </div>
  </div>
</div>