import {LibreriaFunciones, generar_dv, spanish } from './librerias/librerias.js';
let datos = 'getUserAccount';

// ==================== INTERNAL FUNCTIONS ===============================//
// Function to get the amount of user account
function getUserAccountAmount() {
    let datos = 'getUserAccountAmount';

    $.ajax({
        url: "./controller/controller_usuario.php",
        type: "post",
        dataType: "json",
        data: {datos: datos},
        success: (response) => {
            $('#cantidad_usuario').text(response.amount_user);
        }
    }).fail(() => {
        $('#cantidad_usuario').text('Error !!');
    });
}

// Function to load privileges
function loadPrivilege(privelege) {
    return new Promise((resolve, reject) => {
        let datos = 'loadPrivilegio';

        $.ajax({
            url: "./controller/controller_usuario.php",
            type: "post",
            dataType: "json",
            data: {datos: datos},
            success: (response) => {
                $('#user_account_privilege').html(response);
                $('#user_account_privilege').val($('#user_account_privilege option:contains("' + privelege + '")').val());
                resolve();


            },
            error: () => {
                $('#user_account_privilege').html('Sin datos');
                reject();
            }
        });
    });
}

 // Function to generate an action at the end of some  data editing process
 function beforeRecord(tabla, modal = true) {
    tabla.ajax.reload(null, false);
    getUserAccountAmount();
    if (modal == true) {
        $('#modal_update_account').modal('hide');
    }
}

// ================== MODAL HANDLING ================== //

function showUpdateUserAccount(tabla) {
    $('#tabla_usuario tbody').on('click', '#btn_edit_user', function() {
        let data = tabla.row($(this).parents()).data();

        $('#form_update_user_account').trigger('reset');
        $('#name_user_account').val(data.funcionario);
        $('#user_departament').val(data.departamento);
        $('#modal_update_account_tittle').val(data.id_usuario);
        
        loadPrivilege(data.privilegio).then(() => {
            $('#privilege_descripcion').val($('#user_account_privilege option:selected').data("description"));

        }).catch(() => {
            $('#privilege_descripcion').val('Error al cargar la descripción !!');
        });


        $('#user_account_privilege').change(function() {
            let selectOption = $(this).find("option:selected");
            $('#privilege_descripcion').val(selectOption.data("description"));
        });
    });
}



// ================== INFORMATION MAGEMENT ================== //
// Function to update user account privilege
function updatePrivilegeUserAccount(tabla) {
    $('#btn_update_user_account').click(() => {
        let datos = "updatePrivilegeUserAccount";
        let id_account = $('#modal_update_account_tittle').val();
        let id_privilege = $('#user_account_privilege').val();

        $.ajax({
            url: "./controller/controller_usuario.php",
            type: "post",
            dataType: "json",
            data: {datos: datos, id_account: id_account, id_privilege: id_privilege},
            success: (response) => {
                if (response == true) {
                    LibreriaFunciones.alertToast('success', 'Privilegio de la cuenta actualizado !!');
                    beforeRecord(tabla);
                    return false;
                }

                LibreriaFunciones.alertPopUp('warning', 'Privilegio no actualizado !!');
            }
        }).fail(() => {
            LibreriaFunciones.alertPopUp('error', 'Error en la operación !!');
        });
    });
}

// Function to modify the status of a user account
function modifyUserAccount(tabla) {
    $('#tabla_usuario tbody').on('click', '#btn_block_user', function() {
        let data = tabla.row($(this).parents()).data();
        let id_userAccount = data.id_usuario;
        let bloqueo = data.bloqueo;
        
        Swal.fire({
            icon: 'question',
            title: 'Modificar estado de la cuenta de usuario ?',
            showCancelButton: true,
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#2691d9',
            cancelButtonColor: '#adadad'
        }). then(result => {
            if (result.isConfirmed) {
                let datos = "modifyUserAccount"

                $.ajax({
                    url: "./controller/controller_usuario.php",
                    type: "post",
                    dataType: "json",
                    data: {datos: datos, id_userAccount: id_userAccount },
                    success: (response) => {
                        let estado = (bloqueo == 1) ? 'Bloqueada' : 'Desbloqueada';
                        if (response == true) {
                            LibreriaFunciones.alertToast('success', 'Cuenta de usuario ' + estado + '!!');
                            beforeRecord(tabla, false);
                            return false;
                        }

                        LibreriaFunciones.alertPopUp('warning', 'No se puede modificar la cuenta !!');

                    }
                }).fail(() => {
                    LibreriaFunciones.alertPopUp('error', 'Error en la operación !!');
                });
            }
        });
    });
}

// User account password reset function
function restKeyAccount(tabla) {
    $('#tabla_usuario tbody').on('click', '#btn_rest_key', function() {
        let data = tabla.row($(this).parents()).data();
        let id_userAccount = data.id_usuario;
        let key = data.nombre_usuario.slice(0, data.nombre_usuario.length - 2);

        Swal.fire({
            icon: 'question',
            title: 'Restablecer contraseña ?',
            showCancelButton: true,
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#2691d9',
            cancelButtonColor: '#adadad'
        }). then(resultado => {
            if (resultado.isConfirmed) {
                datos = "restKeyAccount";

                $.ajax({
                    url: "./controller/controller_usuario.php",
                    type: "post",
                    dataType: "json",
                    data: {datos: datos, id_userAccount: id_userAccount, key: key},
                    success: (response) => {
                        if (response == true) {
                            LibreriaFunciones.alertToast('success', 'Contraseña restablecida !!');
                            beforeRecord(tabla, false);
                            return false;
                        }

                        LibreriaFunciones.alertPopUpButton('warning', 'La contraseña no se puede restablecer !!');
                    }
                }).fail(() => {
                    LibreriaFunciones.alertPopUp('error', 'Error en la operación !!');
                });
            }
        });
    });
}

// Function to delete a user account
function deleteRegistroFuncionario(tabla) {
    $('#tabla_usuario tbody').on('click', '#btn_delete_user', function() {
        let data = tabla.row($(this).parents()).data();
        let id_userAccount = data.id_usuario;

        Swal.fire({
            icon: 'question',
            title: 'Eliminar cuenta de usuario"',
            showCancelButton: true,
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#2691d9',
            cancelButtonColor: '#adadad'
        }). then(resultado => {
            if (resultado.isConfirmed) {
                datos = "deleteUserAccount";

                $.ajax({
                    url: "./controller/controller_usuario.php",
                    type: "post",
                    dataType: "json",
                    data: {datos: datos, id_userAccount: id_userAccount},
                    success: (response) => {
                        if (response == true) {
                            LibreriaFunciones.alertToast('success', 'Cuenta de usuario eliminada !!');
                            beforeRecord(tabla, false);
                            return false;
                        }

                        LibreriaFunciones.alertPopUpButton('warning', 'El registro no se puede eliminar, se encuentra en uso !!');
                    }
                }).fail(() => {
                    LibreriaFunciones.alertPopUp('error', 'Error en la operación !!');
                });
            }
        });
    });
}





$(document).ready(function() {
    getUserAccountAmount();

    let tabla_usuario = $('#tabla_usuario').DataTable({
        ajax: {
            url: "./controller/controller_usuario.php",
            type: "post",
            dataType: "json",
            data: {datos: datos}
        },
        columns: [
            {
                data: "id_usuario",
                visible: false
            },
            {data: "nombre_usuario"},
            {data: "funcionario"},
            {data: "departamento"},
            {data: "privilegio"},
            {
                data: "estado",
                mRender: (data) => {
                    let estilo = 'bg-warning text-dark';
                    if (data == 'Cuenta activa') { estilo = 'bg-success text-white'; }
                    return '<p class="text-center rounded-3 mb-0 py-1 ' + estilo + '">' + data + '</p>';
                }
            },
            {
                data: "bloqueo",
                bSortable: false,
                mRender: (data) => {
                    let estilo = 'btn-success';
                    let icon = 'fa-lock-open';

                    if (data != 1) {
                        estilo = 'btn-warning';
                        icon = 'fa-lock';
                    }

                    return `<button class="btn btn-primary btn-justify px-3" id="btn_edit_user" title="Editar cuenta de usuario" type="button" data-bs-toggle="modal" data-bs-target="#modal_update_account"><i class="fas fa-edit"></i></button>
                            <button class="btn ` + estilo + ` px-3" id="btn_block_user" title="Modificar estado de la cuenta" type="button"><i class="fas ` + icon + `"></i></button>
                            <button class="btn btn-info px-3" id="btn_rest_key" title="Restablecer clave" type="button"><i class="fas fa-key"></i></button>
                            <button class="btn btn-danger btn-delete px-3" id="btn_delete_user" title="Eliminar cuenta de usuario" type="button"><i class="fas fa-trash-alt"></i></button>`
                    },
                className: "text-center"
            }
        ],
        order: ([]), // para quitar el orden automatico que incluye datatable
        language: spanish
    });


    showUpdateUserAccount(tabla_usuario);

    updatePrivilegeUserAccount(tabla_usuario);
    modifyUserAccount(tabla_usuario);
    restKeyAccount(tabla_usuario);
    deleteRegistroFuncionario(tabla_usuario);




});