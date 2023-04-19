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
                  <div class="text-primary h3" id="cantidad_nuevo_registro">97</div>
                </div>
            </div>
            <!-- <div class="col-sm-5 col-md-3">
              <label for="fecha_ingreso_estudiante" class="form-label">Fecha ingreso <span class="text-danger fs-5">*</span></label>
              <input type="date" id="fecha_ingreso_estudiante" class="form-control">
            </div> -->
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
              <label for="nombre_estudiante" class="form-label">Nombres funcionario <span class="text-danger fs-5">*</span></label>
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

          <!-- <div class="row align-items-end">
            <div class="col-md-5 col-sm-6">
              <div class="row align-items-center">
                <label for="justificacion_rut_estudiante" class="form-label">Rut estudiante <span class="text-danger fs-5">*</span></label>
                <div class="col-8 rut">
                    <input type="text" class="form-control text-center" id="justificacion_rut_estudiante" required>
                </div>
                <div class="col-1 not_padding text-center">
                    <span>-</span>
                </div>
                <div class="col-3 dv_rut">
                    <input type="text" class="form-control text-center" id="justificacion_dv_rut_estudiante" disabled>
                </div>
              </div>
            </div>

            <div class="col-md-3 col-sm-4">
              <label for="justificacion_fecha" class="form-label">Fecha </label>
              <input type="text" class="form-control text-center" id="justificacion_fecha" disabled>
            </div>
          </div>

          <div class="row mt-4">
            <div class="col-sm-9 col-md-10">
              <label for="justificacion_nombre_estudiante" class="form-label">Nombre estudiante</label>
              <input type="text" class="form-control" id="justificacion_nombre_estudiante" disabled>
            </div>
            <div class="col-sm-3 col-md-2">
              <label for="justificacion_curso_estudiante" class="form-label">Curso</label>
              <input type="text" class="form-control" id="justificacion_curso_estudiante" disabled>
            </div>
          </div>

          <div class="row mt-4">
            <div class="col-sm-5 col-md-3">
              <label for="justificacion_fecha_inicio" class="form-label">Fecha inicio <span class="text-danger fs-5">*</span></label>
              <input type="date" id="justificacion_fecha_inicio" class="form-control">
            </div>

            <div class="col-sm-5 col-md-3">
              <label for="justificacion_fecha_termino" class="form-label">Fecha termino <span class="text-danger fs-5">*</span></label>
              <input type="date" id="justificacion_fecha_termino" class="form-control">
            </div>
          </div>

          <div class="row mt-4">
            <div class="col-sm-12">
              <label for="justificacion_apoderado" class="form-label">Apoderado(a) <span class="text-danger fs-5">*</span></label>
              <select id="justificacion_apoderado" class="form-select"></select>
            </div>
          </div>

          <div class="row mt-4">
            <div class="col-lg-12">
              <label for="justificacion_motivo_causa" class="form-label">Motivo/Causa inasistencia</label>
              <textarea id="justificacion_motivo_causa" class="form-control"></textarea>
            </div>
          </div>

          <div class="row mt-4" id="check">
            <div class="col-6">
              <div class="form-check">
                <input type="checkbox" name="" id="justificacion_documento" class="form-check-input" style="margin-top: 12px;">
                <div class="form-check-label">
                  <select id="justificacion_tipo_documento" class="form-select form-check-label" disabled>
                    <option selected value="0">Presenta documento</option>
                    <option value="1">Certificado médico</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="col-3">
              <div class="form-check mt-2">
                <input type="checkbox" id="justificacion_informacion_verbal" class="form-check-input">
                <label for="justificacion_informacion_verbal" class="form-check-label">Información verbal</label>
              </div>
            </div>

            <div class="col-3">
              <div class="form-check mt-2">
                <input type="checkbox" id="justificacion_prueba_pendiente" class="form-check-input" disabled>
                <label for="justificacion_prueba_pendiente" class="form-check-label">Prueba pendiente</label>
              </div>
            </div>
          </div> -->
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-lg" data-bs-dismiss="modal" id="btn_funcionario_cancelar">Cancelar</button>
          <button type="button" class="btn btn-success btn-lg" id="btn_funcionario_registrar">Registrar</button>
        </div>

      </form>
    </div>
  </div>
</div>