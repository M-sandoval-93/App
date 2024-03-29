<!-- MODAL PARA TRABAJAR EL INGRESO Y ACTUALIZACIÓN DE UNA MATRICULA -->
<div class="modal fade" id="modal_matricula" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modal_matricula_tittle">REGISTRAR NUEVA MATRÍCULA</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="form_registro_matricula">
        <div class="modal-body">

          <div class="row g-3 align-items-end">
            <div class="col-sm-6 col-md-7">
              <div class="col-sm-12 col-md-10 text-center">
                <label for="numero_matricula" class="text-secondary h3">Registro de matrícula N°</label>
                <div class="row d-flex justify-content-center">
                  <div class="col-sm-5 col-md-4">
                    <input type="number" class="form-control text-center" id="numero_matricula" disabled>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-sm-2 col-md-2">
              <label for="numero_lista" class="form-label">N° lista <span class="text-danger fs-5">*</span></label>
              <input type="number" id="numero_lista" class="form-control">
            </div>

            <div class="col-sm-4 col-md-3">
              <label for="fecha_matricula" class="form-label">Fecha de matrícula <span class="text-danger fs-5">*</span></label>
              <input type="date" id="fecha_matricula" class="form-control">
            </div>
          </div>

          <div class="row align-items-center mt-3">
            <label for="rut_estudiante_matricula" class="form-label">Rut <span class="text-danger fs-5">*</span></label>
            <div class="col-sm-6 col-lg-5">
              <div class="row align-items-center">
                <div class="col-7 rut">
                  <input type="text" class="form-control text-center" id="rut_estudiante_matricula" required>
                </div>
                <div class="col-1 not_padding text-center">
                  <span>-</span>
                </div>
                <div class="col-4 dv_rut">
                  <input type="text" class="form-control text-center" id="dv_rut_estudiante_matricula" disabled>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-7">
              <span class="form-text" id="informacion_rut">Rut sin puntos, sin guión y sin dígito verificador</span>
            </div>
          </div>

          <div class="row g-3 mt-3 align-items-end">
            <div class="col-md-8">
              <label for="nombres_estudiante" class="form-label">Nombre completo estudiante</label>
              <input type="text" class="form-control" id="nombre_estudiante_matricula" disabled>
            </div>
            <div class="col-md-2">
              <label for="grado_curso" class="form-label">Grado <span class="text-danger fs-5">*</span></label>
              <select class="form-select" id="grado_curso">
                <option selected> ------- </option>
                <option value=7> 7° </option>
                <option value=8> 8° </option>
                <option value=1> 1° </option>
                <option value=2> 2° </option>
                <option value=3> 3° </option>
                <option value=4> 4° </option>
              </select>
            </div>
            <div class="col-md-2">
              <label for="letra_curso" class="form-label">Letra <span class="text-danger fs-5">*</span></label>
              <select class="form-select" id="letra_curso">
                <option selected> ------- </option>
              </select>
            </div>
          </div>

          <!-- Apoderado titula -->
          <div class="row align-items-center mt-4">
            <label for="rut_ap_titular" class="form-label">Rut titular <span class="text-danger fs-5">*</span></label>
            <div class="col-sm-6 col-lg-5">
              <div class="row align-items-center">
                <div class="col-7 rut">
                  <input type="text" class="form-control text-center" id="rut_ap_titular" required>
                </div>
                <div class="col-1 not_padding text-center">
                  <span>-</span>
                </div>
                <div class="col-4 dv_rut">
                  <input type="text" class="form-control text-center" id="dv_rut_ap_titular" disabled>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-7">
              <span class="form-text" id="informacion_titular">Asignar apoderado titular</span>
            </div>
          </div>

          <!-- Apoderado suplente -->
          <div class="row align-items-center mt-3">
            <label for="rut_ap_suplente" class="form-label">Rut suplente <span class="text-danger fs-5">*</span></label>
            <div class="col-sm-6 col-lg-5">
              <div class="row align-items-center">
                <div class="col-7 rut">
                  <input type="text" class="form-control text-center" id="rut_ap_suplente" required>
                </div>
                <div class="col-1 not_padding text-center">
                  <span>-</span>
                </div>
                <div class="col-4 dv_rut">
                  <input type="text" class="form-control text-center" id="dv_rut_ap_suplente" disabled>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-7">
              <span class="form-text" id="informacion_suplente">Asignar apoderado suplente</span>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-lg" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-success btn-lg" id="btn_registrar_matricula">Registrar</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- MODAL PARA TRABAJAR LA FECHA DEL CAMBIO DE CURSO -->
<div class="modal fade" id="modal_matricula_fecha_cambio_curso" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">

      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="modal_matricula_fecha_cambio_tittle">FECHA CAMBIO CURSO</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="d-flex justify-content-center p-4">
        <div class="col-10">
          <label for="fecha_cambio_curso" class="form-label">Fecha del cambio</label>
          <input type="date" id="fecha_cambio_curso" class="form-control">
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-danger btn-lg" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success btn-lg" id="btn_guardar_fecha_cambio_curso">Registrar</button>
      </div>

    </div>
  </div>
</div>


<!-- MODAL PARA REGISTRAR LAS FECHAS DE SUSPENSIÓN DE UN ESTUDIANTE -->
<div class="modal fade" id="modal_suspender_matricula" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content">

      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="modal_matricula_tittle">SUSPENDER MATRÍCULA</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="form_suspender_matricula">
        <div class="modal-body">

          <div class="row mb-4">
            <div class="col-12">
              <label for="nombre_estudiante_suspencion" class="form-label">Nombre completo estudiante </label>
              <input type="text" class="form-control" id="nombre_estudiante_suspencion" disabled>
            </div>
          </div>

          <div class="row">
            <div class="col-sm-12 col-md-6 d-grid gap-3 mb-4">
              <div class="col-sm-6 col-md-12">
                <label for="fecha_inicio_suspencion" class="form-label">Fecha inicio suspención <span class="text-danger fs-5">*</span></label>
                <input type="date" id="fecha_inicio_suspencion" class="form-control">
              </div>

              <div class="col-sm-6 col-md-12">
                <label for="fecha_termino_suspencion" class="form-label">Fecha término suspención <span class="text-danger fs-5">*</span></label>
                <input type="date" id="fecha_termino_suspencion" class="form-control">
              </div>
            </div>

            <div class="col-sm-12 col-md-6 mb-4 d-flex align-items-center justify-content-center">
              <div class="text-center">
                <div class="text-secondary h3 text-center">Días de suspención</div>
                <div id="numero_dias_suspencion" class="text-primary h2"></div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <label for="motivo_suspencion" class="form-label">Nombre completo estudiante </label>
              <textarea class="form-control" id="motivo_suspencion"></textarea>
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-lg" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-success btn-lg" id="btn_registrar_suspencion">Suspender</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- MODAL PARA REGISTRAR EL RETIRO DE UNA MATRICULA -->
<div class="modal fade" id="modal_retiro_matricula" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content">

      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="modal_matricula_tittle">RETIRO DE MATRÍCULA</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="form_suspender_matricula">
        <div class="modal-body">


          <div class="row mb-3">
            <div class="col-sm-5 col-md-5">
              <label for="fecha_retiro_estudiante" class="form-label">Fecha de retiro <span class="text-danger fs-5">*</span></label>
              <input type="date" id="fecha_retiro_estudiante" class="form-control">
            </div>
          </div>



          <!-- SECCION RUT Y CURSO -->
          <div class="row">
            <div class="col-sm-9 col-md-9 mb-3">
              <label for="rut_estudiante_retirado" class="form-label">Rut estudiante</label>
              <div class="col-sm-11 col-md-11">
                <div class="row align-items-center">
                  <div class="col-7 rut">
                    <input type="text" class="form-control text-center" id="rut_estudiante_retirado" disabled>
                  </div>
                  <div class="col-1 not_padding text-center">
                    <span>-</span>
                  </div>
                  <div class="col-4 dv_rut">
                    <input type="text" class="form-control text-center" id="dv_rut_estudiante_retirado" disabled>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-sm-3 col-md-3 mb-3">
              <label for="curso_estudiante_retirado" class="form-label">Curso</label>
              <input type="text" class="form-control text-center" id="curso_estudiante_retirado" disabled>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <label for="nombre_estudiante_retiro" class="form-label">Nombre completo estudiante </label>
              <input type="text" class="form-control" id="nombre_estudiante_retiro" disabled>
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-lg" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-success btn-lg" id="btn_registrar_retiro">Retirar</button>
        </div>
      </form>


    </div>
  </div>
</div>


<!-- MODAL PARA EXCEL DE MATRICULA POR FECHA Y POR RETIROS, ALTAS O COMLETO -->
<div class="modal fade" id="modal_descargar_excel_matricula" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content">

      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="modal_matricula_tittle">DESCARGAR INFORMACIÓN MATRÍCULAS</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

        <div class="modal-body">

          <div class="col-12 form-check mb-4" id="check">
            <input class="form-check-input" type="checkbox" id="check_info_matricula_completa">
            <label class="form-check-label" for="check_info_matricula_completa">Descargar información del periodo actual</label>
          </div>

          <div class="row g-2" id="fecha_descarga_matricula">
            <div class="col-sm-12 col-md-6">
              <label for="fecha_inicio_descarga_matricula" class="form-label">Fecha inicio</label>
              <input type="date" id="fecha_inicio_descarga_matricula" class="form-control">
            </div>

            <div class="col-sm-12 col-md-6">
              <label for="fecha_termino_descarga_matricula" class="form-label">Fecha término</label>
              <input type="date" id="fecha_termino_descarga_matricula" class="form-control">
            </div>
          </div>

          <div class="d-grid gap-2 py-4 col-md-11 col-sm-11 mx-auto" id="botones_descarga_info_matricula">
            <div class="row align-items-center gap-1">
              <div class="col-3 d-grid">
                <button class="btn-lg btn-primary" title="Exportar alta de estudiante" id="btn_exportar_altas"><i class="fas fa-file-excel icon"></i></button>
              </div>
              <div class="col-8 d-grid bg-primary rounded-3">
                <span class="p-2 text-white">Exportar -> Altas de matrículas</span>
              </div>

              <div class="col-3 d-grid">
                <button class="btn-lg btn-success" title="Exportar cambio curso" id="btn_exportar_cambios_curso"><i class="fas fa-file-excel icon"></i></button>
              </div>
              <div class="col-8 d-grid bg-success rounded-3">
                <span class="p-2 text-white">Exportar -> Cambios de curso</span>
              </div>

              <div class="col-3 d-grid">
                <button class="btn-lg btn-secondary" title="Exportar cambio apoderado" id="btn_exportar_cambios_apoderado"><i class="fas fa-file-excel icon"></i></button>
              </div>
              <div class="col-8 d-grid bg-secondary rounded-3">
                <span class="p-2 text-white">Exportar -> Cambios de apoderado</span>
              </div>

              <div class="col-3 d-grid">
                <button class="btn-lg btn-danger" title="Exportar retiro de estudianteante" id="btn_exportar_retiros"><i class="fas fa-file-excel icon"></i></button>
              </div>
              <div class="col-8 d-grid bg-danger rounded-3">
                <span class="p-2 text-white">Exportar -> Retiros de matrícula</span>
              </div>

              <div class="col-3 d-grid">
                <button class="btn-lg btn-warning" title="Exportar datos de matrícula" id="btn_exportar_matriculas"><i class="fas fa-file-excel icon"></i></button>
              </div>
              <div class="col-8 d-grid bg-warning rounded-3">
                <span class="p-2 text-dark">Exportar -> Datos de matrícula</span>
              </div>
            </div>
          </div>

        </div>
    </div>
  </div>
</div>



<!-- considerar que cada vez que se ingrese al sistema, se ejecute una funciona una única vez, comprobando el estado de la suspención de los estudiantes -->