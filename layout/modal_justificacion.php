
<!-- Modal justificacion falta -->
<div class="modal fade" id="modal_registro_justificacion_falta" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modal_justificacion_title">REGISTRAR JUSTIFICACIÓN FALTA</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" id="btn_close_justificacion"></button>
      </div>

      <form id="form_registro_justificacion_falta">
        <div class="modal-body">

          <div class="row align-items-end">
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

          <!-- SECCIÓN APODERADO QUE JUSTIFICA -->
          <div class="row mt-4">
            <div class="col-sm-12">
              <label for="justificacion_apoderado" class="form-label">Apoderado(a) <span class="text-danger fs-5">*</span></label>
              <select id="justificacion_apoderado" class="form-select"></select>
            </div>
          </div>

          <!-- SECCIÓN MOTIVO -->
          <div class="row mt-4">
            <div class="col-lg-12">
              <label for="justificacion_motivo_causa" class="form-label">Motivo/Causa inasistencia</label>
              <textarea id="justificacion_motivo_causa" class="form-control"></textarea>
            </div>
          </div>

          <!-- SECCIÓN TIPO DE DOCUMENTO E INFORMACIÓN VERBAL -->
          <div class="row mt-4" id="check">
            <div class="col-6">
              <div class="form-check">
                <input type="checkbox" id="justificacion_documento" class="form-check-input" style="margin-top: 12px;">
                <div class="form-check-label">
                  <select id="justificacion_tipo_documento" class="form-select form-check-label" disabled>
                    <option selected value="0">Presenta documento</option>
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
                <input type="checkbox" id="justificacion_prueba_pendiente" class="form-check-input">
                <label for="justificacion_prueba_pendiente" class="form-check-label">Prueba pendiente</label>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-lg" data-bs-dismiss="modal" id="btn_cancelar_justificacion">Cancelar</button>
          <button type="button" class="btn btn-success btn-lg" id="btn_registrar_justificacion">Registrar</button>
        </div>

      </form>
    </div>
  </div>
</div>

<!-- Modal pruebas pendientes por asignatura -->
<div class="modal fade" id="modal_justificacion_asignatura" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modal_justificacion_asignatura_title">ASIGNATURAS</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" id="close_modal_justificacion_asignatura"></button>
      </div>

      <form id="form_justificacion_asignatura">
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-md-10 col-sm-9">
              <label for="justificacion_asignatura_nombre" class="form-label">Nombre estudiante</label>
              <input type="text" class="form-control" id="justificacion_asignatura_nombre" disabled>
            </div>
            <div class="col-md-2 col-sm-3">
              <label for="justificacion_asignatura_curso" class="form-label">Curso</label>
              <input type="text" class="form-control" id="justificacion_asignatura_curso" disabled>
            </div>
          </div>
          <div class="row mb-3" id="group_of_the_check">
            <label class="form-label">Seleccionar asignatura <span class="text-danger fs-5">*</span></label>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-primary btn-lg" id="btn_seleccion_asignatura">Seleccionar</button>
        </div> 

      </form>
    </div>
  </div>
</div>





<!-- MODAL PARA TRABAJAR EL PORCENTAJE DE EXIGENCIA -->
<div class="modal fade" id="modal_justificacion_porcentaje_exigencia" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content">

      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="modal_justificacion_porcentaje_exigencia_tittle">PORCENTAJE DE EXIGENCIA EN PRUEBAS</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="form_justificacion_porcentaje_exigencia">
        <div class="modal-body">
          <div class="row mt-2">
            <div class="col-sm-12">
              <label for="select_justificacion_porcentaje_exigencia" class="form-label">Asignar porcentaje de exigencia: <span class="text-danger fs-5">*</span></label>
              <select id="select_justificacion_porcentaje_exigencia" class="form-select">
                <option selected disabled>Seleccionar porcentaje</option>
                <option value="80">80 % de exigencia para rendir pruebas pendientes</option>
                <option value="60">60 % de exigencia para rendir pruebas pendientes</option>
              </select>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-lg" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-success btn-lg" id="btn_registro_porcentaje_exigencia">Registrar</button>
        </div>
      </form>

    </div>
  </div>
</div>






