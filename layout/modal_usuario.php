<!-- Modal to generate user account -->
<div class="modal fade" id="modal_update_account" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modal_update_account_tittle">Actualizar cuenta de usuario</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="form_update_user_account">
        <div class="modal-body">

          <div class="row mb-3">
            <div class="col-12">
              <label for="name_user_account" class="form-label">Nombre funcionario</label>
              <input type="text" class="form-control" id="name_user_account" disabled>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-6">
              <label for="user_departament" class="form-label">Departamento</label>
              <input type="text" class="form-control" id="user_departament" disabled>
            </div>

            <div class="col-6">
              <label for="user_account_privilege" class="form-label">Privilegio <span class="text-danger fs-5">*</span></label>
              <select class="form-select" id="user_account_privilege">
              </select>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <label for="privilege_descripcion" class="form-label">Descripción del privilegio</label>
              <textarea class="form-control" id="privilege_descripcion" rows="2" disabled></textarea>
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-lg" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-success btn-lg" id="btn_update_user_account">Registrar</button>
        </div>
      </form>

    </div>
  </div>
</div>