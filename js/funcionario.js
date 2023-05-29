import {LibreriaFunciones, generar_dv, spanish } from './librerias/librerias.js';
let datos = 'getFuncionarios';

// ==================== FUNCIONES INTERNAS ===============================//
// Función para generar tabla expansiba con datos secundarios
function getData(data) {
    return (
        '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
            '<tr>' +
                '<td>Fecha de nacimiento:</td>' +
                '<td>' + data.fecha_nacimiento + '</td>' +
            '</tr>' +
            '<tr>' +
                '<td>Sexo:</td>' +
                '<td>' + data.sexo + '</td>' +
            '</tr>' +
            '<tr>' +
                '<td>Departamento:</td>' +
                '<td>' + data.departamento + '</td>' +
            '</tr>' +
        '</table>'
    );
}

// Función para expandir información secundaria // PASAR A FUNCION GENERICA
function expadirData(tabla) {
    $('#tabla_funcionario tbody').on('click', 'td.dt-control', function () {
        let tr = $(this).closest('tr');
        let row = tabla.row(tr);

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
        } else {
            row.child(getData(row.data())).show();
            tr.addClass('shown');
        }
    });
}

// Función para obtener la cantidad de funcionarios
function getCantidadFuncionario(contexto = false) {
    let datos = 'getCantidadFuncionario';

    $.ajax({
        url: "./controller/controller_funcionario.php",
        type: "post",
        dataType: "json",
        data: {datos: datos},
        success: (response) => {
            if (contexto == true) {
                $('#cantidad_nuevo_registro').text(response.cantidad_funcionario + 1);
                return false;
            }

            $('#cantidad_funcionario').text(response.cantidad_funcionario);
        }
    }).fail(() => {
        if (contexto == true) {
            $('#cantidad_nuevo_registro').text('Error !!');
            return false;
        }

        $('#cantidad_funcionario').text('Error !!');
    });
}

// Función para cargar lista de tipos de funcionario
function loadTipoFuncionario(funcionario = false) {
    let datos = 'loadTipoFuncionario';

    $.ajax({
        url: "./controller/controller_funcionario.php",
        type: "post",
        dataType: "json",
        data: {datos: datos},
        success: (response) => {
            $('#tipo_funcionario').html(response);
            
            if (funcionario != false) {
                $('#tipo_funcionario').val($('#tipo_funcionario option:contains("'+ funcionario + '")').val());
            }
        }
    }).fail(() => {
        $('#tipo_funcionario').html('Error !!');
    });
}

// Función para cargar los departamentos
function loadDepartamento(departamento = false) {
    let datos = 'loadDepartamento';

    $.ajax({
        url: "./controller/controller_funcionario.php",
        type: "post",
        dataType: "json",
        data: {datos: datos},
        success: (response) => {
            $('#departamento_funcionario').html(response);

            if (departamento != false) {
                $('#departamento_funcionario').val($('#departamento_funcionario option:contains("'+ departamento + '")').val());
            }
        }
    }).fail(() => {
        $('#departamento_funcionario').html('Error !!');
    });
}

function loadPrivilegio() {
    let datos = 'loadPrivilegio';

    $.ajax({
        url: "./controller/controller_usuario.php",
        type: "post",
        dataType: "json",
        data: {datos: datos},
        success: (response) => {
            $('#privilegio_cuenta_usuario').html(response);
        }
    }).fail(() => {
        $('#privilegio_cuenta_usuario').html('Sin datos');
    });
}

// Función para generar un objeto funcionario
function getDataFormulario() {
    const funcionario = {
        rut: $.trim($('#rut_funcionario').val()),
        dv_rut: $.trim($('#dv_rut_funcionario').val().toUpperCase()),
        nombre: $.trim($('#nombre_funcionario').val().toUpperCase()),
        ap: $.trim($('#ap_funcionario').val().toUpperCase()),
        am: $.trim($('#am_funcionario').val().toUpperCase()),
        tipo_funcionario: $.trim($('#tipo_funcionario').val()),
        departamento: $.trim($('#departamento_funcionario').val()),
        sexo: $.trim($('#sexo_funcionario').val()),
        fecha_nacimiento: $.trim($('#fecha_nacimiento_funcionario').val())
    }
    return funcionario;
}

// Función para comprobar si el funcionario existe
function comprobarFuncionario(rut) {
    let datos = 'getFuncionario';

    if (rut != '' && rut.length >= 7 && rut.length <= 9) {
        $.ajax({
            url: "./controller/controller_funcionario.php",
            type: "post",
            dataType: "json",
            cache: false,
            data: {datos: datos, rut: rut, tipo: 'existe'},
            success: function(data) {
                if (data == true) {
                    LibreriaFunciones.alertPopUp('warning', 'El rut ingresado ya existe en la base de datos'); 
                }
            }
        }).fail(() => {
            LibreriaFunciones.alertPopUp('error', 'Error en la consulta !!');
        });
    } 
}

// Función para generar digito verificador y validar RUT
function validarRutFuncionario() {
    $('#rut_funcionario').keyup((e) => {
        e.preventDefault();
        generar_dv('#rut_funcionario', '#dv_rut_funcionario');
        comprobarFuncionario($('#rut_funcionario').val());

        LibreriaFunciones.validarNumberRut($('#rut_funcionario'), $('#informacion_rut'), 'Rut sin puntos, sin guión y sin dígito verificador');
    })
}

// Función para comprobar vampos vacios en un objeto
function comprobarObjetoFuncionario(objeto) {
    let count = 0;
    for (const [key, value] of Object.entries(objeto)) {
        if (value == '') { count +=1; }
    }

    // Condición para comprobar que el rut ingresado se encuentra en la base de datos estudiante
    if ($('#nombre_estudiante_matricula').val() == 'Sin datos para el rut ingresado !!') {
        count +=1;
    }

    return count;
}

function checkUserAccount(id_user) {
    return new Promise((resolve) => {
        let datos = 'checkUserAccount';

        $.ajax({
            url:"./controller/controller_usuario.php",
            type: "post",
            dataType: "json",
            data: {datos: datos, id_user: id_user},
            success: (response) => {
                resolve(response);
            }
        }).fail(() => {
            LibreriaFunciones.alertPopUp('error', 'Error de consulta BBDD');
        });
    });
}

// async function statusBtnUser(row, idFuncionario) {
//     const btnUser = $(row).find('#btn_cuenta_usuario');
//     const response = await checkUserAccount(idFuncionario);

//     if (response) {
//         btnUser.addClass('user-created');
//     }
// }

// async function statusBtnUser(rows) {
//     const id_funcionario = rows.map(row => row.data().id_funcionario);
//     const promises = id_funcionario.map(id_funcionario => checkUserAccount(id_funcionario));

//     const resultados = await Promise.all(promises);

//     rows.each((index, row) => {
//         const btnUser = $(row).find('#btn_cuenta_usuario');
//         if (resultados[index]) {
//             btnUser.addClass('user-created');
//         }
//     });
// }


 // Función para generar acción al terminar algún proceso de edición de datos
 function beforeRecord(tabla, modal = true) {
    tabla.ajax.reload(null, false);
    getCantidadFuncionario();
    if (modal == true) {
        $('#modal_funcionario').modal('hide');
    }
}



// ================== MANEJO DE MODAL ================== //
// Función para preparar el modal funcionario
function prepararModal() {
    $('#form_registro_funcionario').trigger('reset');
    $('#rut_funcionario').removeClass('is-invalid');
    $('#informacion_rut').removeClass('text-danger');
    $('#informacion_rut').text('Rut sin puntos, sin guión y sin dígito verificador');
    $('#informacion_rut').addClass('form-text');
    LibreriaFunciones.autoFocus($('#modal_funcionario'), $('#rut_funcionario'));
    validarRutFuncionario();
}

// Función para mostar el modal para un nuevo funcionario
function showModalNewFuncionario() {
    $('#btn_nuevo_funcionario').click(() => {
        prepararModal();
        loadTipoFuncionario();
        loadDepartamento();
        $('#modal_funcionario_title').text('REGISTRAR NUEVO FUNCIONARIO');
        $('#btn_funcionario_registrar').text('Registrar');
        $('#texto_secundario').text('Nuevo registro N°');
        getCantidadFuncionario(true);
    });
}

// Función para mostrar un modal para actualizar los datos de un funcionario
function showModalUpdateFuncionario(tabla) {
    $('#tabla_funcionario tbody').on('click', '#btn_editar_funcionario', function() {
        let data = tabla.row($(this).parents()).data();
        prepararModal();
        loadTipoFuncionario(data.tipo_funcionario);
        loadDepartamento(data.departamento);

        // Trabajar datos principales para asignar al formulario
        let rut = data.rut_funcionario.slice(0, data.rut_funcionario.length - 2);

        // Asignación del contenido
        $('#modal_funcionario_title').text('ACTUALIZAR REGISTRO FUNCIONARIO');
        $('#btn_funcionario_registrar').text('Actualizar');
        $('#texto_secundario').text('ID del funcionario N°');
        $('#cantidad_nuevo_registro').text(data.id_funcionario);

        // Asignación de valores
        $('#rut_funcionario').val(rut);
        generar_dv('#rut_funcionario', '#dv_rut_funcionario');
        $('#nombre_funcionario').val(data.nombres_funcionario.toUpperCase());
        $('#ap_funcionario').val(data.ap_funcionario.toUpperCase());
        $('#am_funcionario').val(data.am_funcionario.toUpperCase());
        $('#sexo_funcionario').val(data.sexo.slice(0, 1));
        $('#fecha_nacimiento_funcionario').val(LibreriaFunciones.textFecha(data.fecha_nacimiento));

    });
}

// Función para cargar los privilegios de una cuenta de usuario
function showModalCreateUsuario(tabla) {
    $('#tabla_funcionario tbody').on('click', '#btn_cuenta_usuario', async function() {
        let data = tabla.row($(this).parents()).data();
        let checkUser = await checkUserAccount(data.id_funcionario);

        if (checkUser) {
            LibreriaFunciones.alertPopUpButton('info', 'El funcionario ya tienen una cuenta de usuario activa !!');
            return false; 
        }
        
        $('#modal_generar_usuario').modal('show');

        $('#form_generar_usuario').trigger('reset');
        $('#rut_funcionario_usuario').val(data.rut_funcionario);
        $('#nombre_funcionario_usuario').val(data.nombres_funcionario + ' ' + data.ap_funcionario);
        $('#departamento_funcionario_usuario').val(data.departamento);
        $('#modal_generar_usuario_tittle').val(data.id_funcionario);

        loadPrivilegio()

        $('#privilegio_cuenta_usuario').change(function() {
            let selectOption = $(this).find("option:selected");
            $('#descripcion_privilegio_usuario').val(selectOption.data("description"));
        });
    });
}


// ================== MANEJO DE INFORMARCIÓN ================== //
// Función para registrar un nuevo funcionrio
function setFuncionario(tabla) {
    $('#btn_funcionario_registrar').click((e) => {
        e.preventDefault();
        if ($('#modal_funcionario_title').text() != 'REGISTRAR NUEVO FUNCIONARIO') { return false; }
        if (LibreriaFunciones.comprobarLongitud($('#rut_funcionario').val(), 7, 9, 'RUT', 'Funcionrio') == false) { return false; }

        let datos = 'setFuncionario';
        const funcionario = getDataFormulario();
        if (comprobarObjetoFuncionario(funcionario) >= 1) {
            LibreriaFunciones.alertPopUp('info', 'Faltan datos importantes !!');
            return false;
        }

        $.ajax({
            url: "./controller/controller_funcionario.php",
            type: "post",
            dataType: "json",
            data: {datos: datos, funcionario: funcionario},
            success: (response) => {
                if (response == 'existe') {
                    LibreriaFunciones.alertPopUp('warning', 'El rut ingresado ya existe en la base de datos');
                    return false;
                }

                if (response == true) {
                    LibreriaFunciones.alertPopUp('success', 'Funcionario registrado !!');
                    beforeRecord(tabla)
                    return false;
                }

                LibreriaFunciones.alertPopUp('warning', 'No se registro el estudiante !!');
            }
        }).fail(() => {
            LibreriaFunciones.alertPopUp('error', 'Error en la consulta !!');
        });
    });
}

// Función para actualiar los datos de un funcionario
function updateFuncionario(tabla) {
    $('#btn_funcionario_registrar').click((e) => {
        e.preventDefault();
        if ($('#modal_funcionario_title').text() != 'ACTUALIZAR REGISTRO FUNCIONARIO') { return false; }
        if (LibreriaFunciones.comprobarLongitud($('#rut_funcionario').val(), 7, 9, 'RUT', 'Funcionrio') == false) { return false; }

        let datos = "updateFuncionario";
        const funcionario = getDataFormulario();
        if (comprobarObjetoFuncionario(funcionario) >= 1) {
            LibreriaFunciones.alertPopUp('info', 'Faltan datos importantes !!');
            return false;
        }

        funcionario.id_funcionario = $('#cantidad_nuevo_registro').text();  // Se agrega una nueva propiedad al objeto con el id de estudiante

        $.ajax({
            url: "./controller/controller_funcionario.php",
            type: "post",
            dataType: "json",
            data: {datos: datos, funcionario: funcionario},
            success: (response) => {
                if (response == true) {
                    LibreriaFunciones.alertPopUp('success', 'Datos actualizados !!');
                    beforeRecord(tabla)
                    return false;
                }

                LibreriaFunciones.alertPopUp('warning', 'No se actualizaron los datos !!');
            }
        }).fail(() => {
            LibreriaFunciones.alertPopUp('error', 'Error en la actualización !!');
        });

    });
    
}

// Función para eliminar el registro de un funcionario
function deleteRegistroFuncionario(tabla) {
    $('#tabla_funcionario tbody').on('click', '#btn_delete_funcionario', function() {
        let data = tabla.row($(this).parents()).data();
        let id_funcionario = data.id_funcionario;


        Swal.fire({
            icon: 'question',
            title: 'Eliminar registro de "' + data.nombres_funcionario + ' ' + data.ap_funcionario + '"',
            showCancelButton: true,
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#2691d9',
            cancelButtonColor: '#adadad'
        }). then(resultado => {
            if (resultado.isConfirmed) {
                datos = "deleteFuncionario";

                $.ajax({
                    url: "./controller/controller_funcionario.php",
                    type: "post",
                    dataType: "json",
                    data: {datos: datos, id_funcionario: id_funcionario},
                    success: (response) => {
                        if (response == true) {
                            LibreriaFunciones.alertToast('success', 'Registro eliminado !!');
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

// Función para crear cuenta de usuario
function setUserAccount(tabla) { // seguir trabajando !!
    $('#btn_generar_usuario').click(() => {
        let datos = "setUserAccount";
        const userAccount = {
            id_funcionario: $('#modal_generar_usuario_tittle').val(),
            usuario: $('#rut_funcionario_usuario').val(),
            clave: $('#rut_funcionario_usuario').val().slice(0, $('#rut_funcionario_usuario').val().length -2),
            id_privilegio:$('#privilegio_cuenta_usuario').val()
        }

        if ($('#privilegio_cuenta_usuario').val() == 0) { 
            LibreriaFunciones.alertPopUp('info', 'Seleccionar privilegio');
            return false;
        }

        $.ajax({
            url: "./controller/controller_usuario.php",
            type: "post",
            dataType: "json",
            data: {datos: datos, userAccount: userAccount},
            success: (response) => {
                if (response == true) {
                    LibreriaFunciones.alertPopUp('success', 'Cuenta de usuario creada !!');
                    beforeRecord(tabla);
                    $('#modal_generar_usuario').modal('hide');
                    return false;
                }

                LibreriaFunciones.alertPopUp('warning', 'Cuenta no creada !!');
            }
        }).fail(() => {
            LibreriaFunciones.alertPopUp('error', 'Error al crear cuenta !!');
        });
    });
}

// Función para generar documento
function getReporteFuncionario(btn, ext) {
    let datos = 'getReporteFuncionario';

    $(btn).click((e) => {
        e.preventDefault();

        $.ajax({
            url: "./controller/controller_funcionario.php",
            type: "post",
            dataType: "html",
            cache: false,
            data: {datos: datos, ext: ext},
            success: (data) => {
                let opResult = JSON.parse(data);
                let $a = $("<a>");
    
                $a.attr("href", opResult.data);
                $("body").append($a);
                $a.attr("download", "Registro funcionario." + ext);
                $a[0].click();
                $a.remove();
            }
        }). fail(() => {
            LibreriaFunciones.alertPopUp('error', 'Error al generar documento');
        });
    });
}




// ==================== FUNCIONES INTERNAS ===============================//

$(document).ready(function() {
    // Función para obtener la cantidad de funcionarios
    getCantidadFuncionario();

    let tabla_funcionario = $('#tabla_funcionario').DataTable({
        ajax: {
            url: "./controller/controller_funcionario.php",
            type: "post",
            dataType: "json",
            data: {datos: datos}
        },
        columns: [
            {
                data: "id_funcionario",
                visible: false
            },
            {
                className: "dt-control",
                bSortable: false,
                data: null,
                defaultContent: ""
            },
            {data: "rut_funcionario"},
            {data: "ap_funcionario"},
            {data: "am_funcionario"},
            {data: "nombres_funcionario"},
            {data: "tipo_funcionario"},
            {
                data: "estado",
                mRender: (data) => {
                    let estilo;
                    if (data == 'Activo(a)') { estilo = 'bg-success'; }
                    if (data == 'Licencia') { estilo = 'bg-danger'; }
                    if (data == 'Permiso') { estilo = 'bg-warning'; }
                    return '<p class="text-center text-white rounded-3 mb-0 py-1 ' + estilo + '">' + data + '</p>';
                }
            },
            {
                data: "cuenta_creada",
                bSortable: false,
                mRender: (data) => {
                    let estilo = 'btn-warning';
                    if (data == 1) { estilo = 'btn-success'; }
                    return `<button class="btn btn-primary btn-justify px-3" id="btn_editar_funcionario" title="Editar funcionario" type="button" data-bs-toggle="modal" data-bs-target="#modal_funcionario"><i class="fas fa-edit"></i></button>
                            <button class="btn ` + estilo + ` px-3" id="btn_cuenta_usuario" title="Generar cuenta usuario" type="button"><i class="fas fa-user-cog"></i></button>
                            <button class="btn btn-danger btn-delete px-3" id="btn_delete_funcionario" title="Eliminar funcionario" type="button"><i class="fas fa-trash-alt"></i></button>`
                    },
                className: "text-center"
            }
        ],
        language: spanish
        // createdRow: function(row, data, dataIndex) {
        //     let id_funcionario = data.id_funcionario;
        //     statusBtnUser(row, id_funcionario);
        // }

    });

    expadirData(tabla_funcionario);


    showModalNewFuncionario();
    showModalUpdateFuncionario(tabla_funcionario);
    showModalCreateUsuario(tabla_funcionario);

    setFuncionario(tabla_funcionario);
    updateFuncionario(tabla_funcionario);
    setUserAccount(tabla_funcionario);
    deleteRegistroFuncionario(tabla_funcionario);


    getReporteFuncionario('#btn_excel', 'xlsx');
    getReporteFuncionario('#btn_csv', 'csv');


});