<!-- MODAL PARA TRABAJAR EL INGRESO Y ACTUALIZACIÓN DE UNA MATRICULA -->
<div class="modal fade" id="modal_matricula" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modal_matricula_tittle">REGISTRAR MATRÍCULA</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="form_registro_matricula">
        <div class="modal-body">

          <div class="row g-3 align-items-end">
            <div class="col-sm-7 col-md-8">
              <div class="col-sm-12 col-md-10 text-center">
                <label for="numero_matricula" class="text-secondary h3">Registro de matrícula N°</label>
                <div class="row d-flex justify-content-center">
                  <div class="col-sm-4 col-md-3">
                    <input type="text" class="form-control text-center" id="numero_matricula" disabled>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-sm-5 col-md-3">
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

          <div class="row g-3 mt-3">
            <div class="col-md-10">
              <label for="nombres_estudiante" class="form-label">Nombre completo estudiante <span class="text-danger fs-5">*</span></label>
              <input type="text" class="form-control" id="nombre_estudiante_matricula">
            </div>
            <div class="col-md-2">
              <label for="curso_estudiante" class="form-label">Curso <span class="text-danger fs-5">*</span></label>
              <select class="form-select" id="curso_estudiante">
                <option selected disabled> ------- </option>
              </select>
            </div>
          </div>

          <!-- Apoderado titula -->
          <div class="row align-items-center mt-3">
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
            <label for="rut_ap_suplente" class="form-label">Rut titular <span class="text-danger fs-5">*</span></label>
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


<!-- MODAL PARA TRABAJAR LA SUSPENSIÓN DE UNA MATRÍCULA -->
<div class="modal fade" id="modal_matricula_estado" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modal_estudiante_tittle">Cambiar estado</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="d-grid gap-2 py-4 col-10 mx-auto">
        <button class="btn btn-primary" title="Activar" id="btn_activar_matricula">Activar matrícula</button>
        <button class="btn btn-warning" title="Suspender" id="btn_suspender_matricula">suspender matrícula</button>
      </div>

      <!-- <form id="form_registro_estudiante">
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
              <label for="nombre_estudiante">Nombres estudiante <span class="text-danger fs-5">*</span></label>
              <input type="text" class="form-control" id="nombre_estudiante">
            </div>
            <div class="col-md-4">
              <label for="ap_estudiante">Apellido paterno <span class="text-danger fs-5">*</span></label>
              <input type="text" class="form-control" id="ap_estudiante">
            </div>
            <div class="col-md-4">
              <label for="am_estudiante">Apellido materno <span class="text-danger fs-5">*</span></label>
              <input type="text" class="form-control" id="am_estudiante">
            </div>
          </div>

          <div class="row g-3 mt-4">
            <div class="col-md-4">
              <label for="n_social_estudiante">Nombre social</label>
              <input type="text" class="form-control" id="n_social_estudiante">
            </div>
            <div class="col-md-3">
              <label for="sexo_estudiante">Sexo <span class="text-danger fs-5">*</span></label>
              <select class="form-select" id="sexo_estudiante">
                <option selected disabled> ------- </option>
                <option value="M">Masculino</option>
                <option value="F">Femenina</option>
              </select>
            </div>
            <div class="col-sm-6 col-md-3">
              <label for="fecha_nacimiento">Fecha nacimiento <span class="text-danger fs-5">*</span></label>
              <input type="date" class="form-control" id="fecha_nacimiento">
            </div>
            <div class="col-sm-6 col-md-2">
              <label for="beneficio_junaeb">Junaeb <span class="text-danger fs-5">*</span></label>
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
      </form> -->

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
                <div id="numero_dias_suspencion" class="text-primary h3"></div>
              </div>
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



<!-- considerar que cada vez que se ingrese al sistema, se ejecute una funciona una única vez, comprobando el estado de la suspención de los estudiantes -->