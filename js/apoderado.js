import {LibreriaFunciones, generar_dv, spanish } from './librerias/librerias.js';
let datos = 'getApoderados';

// ==================== FUNCIONES INTERNAS ===============================//
// Función para maquetar datos adicionales del estudiante
function getInfoSecundaria(data) {
    return (
        '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
            '<tr>' +
                '<td>Dirección:</td>' +
                '<td>' + data.direccion + '</td>' +
            '</tr>' +
        '</table>'
    )
}

// Función para mostrar los datos adicionales del estudiante
function expandInfoApoderado(tabla) {
    $('#tabla_apoderado tbody').on('click', 'td.dt-control', function () {
        let tr = $(this).closest('tr');
        let row = tabla.row(tr);

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
        } else {
            row.child(getInfoSecundaria(row.data())).show();
            tr.addClass('shown');
        }
    });    
}

// Función para obtener la cantidad de apoderados registrados
function cantidadApoderado(contexto = false) {
    let datos = 'getCantidadApoderado';

    $.ajax({
        url: "./controller/controller_apoderado.php",
        type: "post",
        dataType: "json",
        data: {datos: datos},
        success: (response) => {
            if (contexto == true) {
                $('#cantidad_nuevo_registro').text(response.cantidad_apoderado + 1);
                return false;
            }

            $('#cantidad_apoderado').text(response.cantidad_apoderado);
        }
    }).fail(() => {
        if (contexto == true) {
            $('#cantidad_nuevo_registro').text('Error !!');
            return false;
        }

        $('#cantidad_apoderado').text('Error !!');
    });
}

// Función para eliminar el registro de un estudiante
function deleteRegistroApoderado(tabla) {
    $('#tabla_apoderado tbody').on('click', '#btn_eliminar_apoderado', function() {
        let data = tabla.row($(this).parents()).data();
        let id_apoderado = data.id_apoderado;

        Swal.fire({
            icon: 'question',
            title: 'Eliminar registro de "' + data.nombres_apoderado + ' ' + data.ap_apoderado + '"',
            showCancelButton: true,
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#2691d9',
            cancelButtonColor: '#adadad'
        }). then(resultado => {
            if (resultado.isConfirmed) {
                datos = "deleteApoderado";

                $.ajax({
                    url: "./controller/controller_apoderado.php",
                    type: "post",
                    dataType: "json",
                    data: {datos: datos, id_apoderado: id_apoderado},
                    success: function(data) {
                        if (data == false) {
                            LibreriaFunciones.alertPopUpButton('error', 'No se puede eliminar el registro, por la integridad de los datos !!');
                            return false;
                        }

                        LibreriaFunciones.alertPopUp('success', 'Registro eliminado !!');
                        beforeRecord(tabla);
                    }
                }).fail(() => {
                    LibreriaFunciones.alertPopUp('error', 'Error en la operación  !!');
                });
            }
        });
    });
}
 
// Función para generar acción al terminar algún proceso de edición de datos
function beforeRecord(tabla) {
    tabla.ajax.reload(null, false);
    cantidadApoderado();
}

// Función para generar documento
function exportarApoderados(btn, ext) { // Terminado y revisado !!
    let datos = 'exportarApoderados';

    $(btn).click((e) => {
        e.preventDefault();

        $.ajax({
            url: "./controller/controller_apoderado.php",
            type: "post",
            dataType: "html",
            cache: false,
            data: {datos: datos, ext: ext},
            success: (data) => {
                let opResult = JSON.parse(data);
                let $a = $("<a>");
    
                $a.attr("href", opResult.data);
                $("body").append($a);
                $a.attr("download", "Registro apoderado." + ext);
                $a[0].click();
                $a.remove();
            }
        }). fail(() => {
            LibreriaFunciones.alertPopUp('error', 'Error al generar documento');
        });
    });
}

// Función generica para restablecer el formato del modal
function prepararModal() { // revisar y verificar los campos !!!!! (Agregar al tener listo un modal y lanzarlo)
    $('#form_registro_apoderado').trigger('reset');
    $('#rut_apoderado').removeClass('is-invalid');
    $('#informacion_rut').removeClass('text-danger');
    $('#informacion_rut').text('Rut sin puntos, sin guión y sin dígito verificador');
    $('#informacion_rut').addClass('form-text');
    LibreriaFunciones.autoFocus($('#modal_apoderado'), $('#rut_apoderado'));
    validarRutApoderado();
}

// Función para lanzar modal nuevo apoderado
function lanzarModalNuevoApoderado() {
    $('#btn_nuevo_apoderado').click(() => {
        prepararModal();
        $('#modal_estudiante_tittle').text('REGISTRAR APODERADO');
        $('#btn_registrar_apoderado').text('Registrar');
        $('#texto_secundario').text('Nuevo registro de apoderado N°');
        cantidadApoderado(true);
    });
}

// Función para validar el rut
function validarRutApoderado() {
    $('#rut_apoderado').keyup(function(e) {
        e.preventDefault();
        generar_dv('#rut_apoderado', '#dv_rut_apoderado');
        comprobarApoderado($('#rut_apoderado').val());
        LibreriaFunciones.validarNumberRut($('#rut_apoderado'), $('#informacion_rut'), 'Rut sin puntos, sin guión y sin dígito verificador');
    });
}

// Función para comprobar si el registro exisate en la base de datos
function comprobarApoderado(rut) {
    datos = "getApoderado";

    if (rut != '' && rut.length > 7 && rut.length < 9) {
        $.ajax({
            url: "./controller/controller_apoderado.php",
            type: "post",
            dataType: "json",
            cache: false,
            data: {datos: datos, rut: rut, tipo: 'comprobar'},
            success: function(data) {
                if (data == true) {
                    LibreriaFunciones.alertPopUp('warning', 'El rut ingresado ya existe en la base de datos de los apoderados'); 
                }
            }

        }).fail(() => {
            LibreriaFunciones.alertPopUp('error', 'Error en la consulta !!');
        });
    }
}

// Función para comprobar los campos vacios del formulario
function comprobarCamposVacios(objeto) {
    let count = 0;
    for (const [key, value] of Object.entries(objeto)) {
        if (value == '') {
            count += 1;
        }
    }
    return count;
}

// Función que obtiene los datos del formulario modal de estudiante
function getDataFormulario() {
    const apoderado = {
        rut: $.trim($('#rut_apoderado').val()),
        dv_rut: $.trim($('#dv_rut_apoderado').val().toUpperCase()),
        nombres: $.trim($('#nombre_apoderado').val().toUpperCase()),
        ap: $.trim($('#ap_apoderado').val().toUpperCase()),
        am: $.trim($('#am_apoderado').val().toUpperCase()),
        telefono: $.trim($('#telefono').val().toUpperCase()), 
        direccion: $.trim($('#direccion').val())
    }

    return apoderado;
}

// Función para registrar un nuevo apoderado
function setApoderado(tabla) {
    $('#btn_registrar_apoderado').click((e) => {
        e.preventDefault();
        if ($('#modal_apoderado_tittle').text() != 'REGISTRAR APODERADO') { return false; }
        if (LibreriaFunciones.comprobarLongitud($('#rut_apoderado').val(), 7, 9, 'RUT', 'Apoderado') == false) { return false; }
        if (LibreriaFunciones.comprobarLongitud($('#telefono').val(), 8, 8, 'Teléfono', 'Apoderado') == false) { return false; }
            
        let datos = 'setApoderado';
        const apoderado = getDataFormulario();
        if (comprobarCamposVacios(apoderado) >= 1) {
            LibreriaFunciones.alertPopUp('info', 'Faltan datos importantes !!');
            return false;
        }

        $.ajax({
            url: "./controller/controller_apoderado.php",
            type: "post",
            dataType: "json",
            data: {datos: datos, apoderado: apoderado},
            success: function(data) {
                if (data == 'existe') {
                    LibreriaFunciones.alertPopUp('warning', 'El rut ingresado ya existe en la base de datos de los estudiantes');
                    return false;
                }
                if (data == true) {
                    LibreriaFunciones.alertPopUp('success', 'Apoderadio registrado !!');
                    $('#modal_apoderado').modal('hide');
                    beforeRecord(tabla);
                } else {
                    LibreriaFunciones.alertPopUp('warning', 'No se registro el apoderado !!');
                }
            }
        }).fail(() => {
            LibreriaFunciones.alertPopUp('error', 'Error en la consulta !!');
        });
    });
}

// Función para lanzar el modal empleado para actualizar un estudiante
function lanzarModalupdateApoderado(tabla) {
    $('#tabla_apoderado tbody').on('click', '#btn_editar_apoderado', function() {
        let data = tabla.row($(this).parents()).data();
        prepararModal();

        // Trabajar datos principales para asignar al formulario
        let rut = data.rut_apoderado.slice(0, data.rut_apoderado.length - 2);

        // Asignación del contenido
        $('#modal_apoderado_tittle').text('UPDATE APODERADO');
        $('#btn_registrar_apoderado').text('Actualizar');
        $('#texto_secundario').text('ID del apoderado N°');
        $('#cantidad_nuevo_registro').text(data.id_apoderado);

        // Asignación de valores
        $('#rut_apoderado').val(rut);
        generar_dv('#rut_apoderado', '#dv_rut_apoderado');
        $('#nombre_apoderado').val(data.nombres_apoderado.toUpperCase());
        $('#ap_apoderado').val(data.ap_apoderado.toUpperCase());
        $('#am_apoderado').val(data.am_apoderado.toUpperCase());
        $('#telefono').val(data.telefono);
        $('#direccion').val(data.direccion.toUpperCase());

    });
}

// Función para actualizar el registro de un apoderado
function updateApoderado(tabla) {
    $('#btn_registrar_apoderado').click((e) => {
        e.preventDefault();
        if ($('#modal_apoderado_tittle').text() != 'UPDATE APODERADO') { return false; }
        if (LibreriaFunciones.comprobarLongitud($('#rut_apoderado').val(), 7, 9, 'RUT', 'Apoderado') == false) { return false; }
        if (LibreriaFunciones.comprobarLongitud($('#telefono').val(), 8, 8, 'Teléfono', 'Apoderado') == false) { return false; }

        datos = 'updateApoderado';
        const apoderado = getDataFormulario();
        if (comprobarCamposVacios(apoderado) >= 1) {
            LibreriaFunciones.alertPopUp('info', 'Faltan datos importantes !!');
            return false;
        }
        apoderado.id_apoderado = $('#cantidad_nuevo_registro').text(); // Se agrega una nueva propiedad al objeto con el id de estudiante

        $.ajax({
            url: "./controller/controller_apoderado.php",
            type: "post",
            dataType: "json",
            data: {datos: datos, apoderado: apoderado},
            success: function(data) {
                if (data == true) {
                    LibreriaFunciones.alertPopUp('success', 'Apoderado actualizado !!');
                    $('#modal_apoderado').modal('hide');
                    beforeRecord(tabla);
                } else {
                    LibreriaFunciones.alertPopUp('warning', 'No se actualizó el apoderado !!');
                }
            }
        }).fail(() => {
            LibreriaFunciones.alertPopUp('error', 'Error en la consulta !!');
        });
    });
}

// ==================== FUNCIONES INTERNAS ===============================//

$(document).ready(function() {

    cantidadApoderado();

    let tabla_apoderado = $('#tabla_apoderado').DataTable({
        ajax: {
            url: "./controller/controller_apoderado.php",
            type: "post",
            dataType: "json",
            data: {datos: datos}
        },
        columns: [
            {
                visible: false,
                data: "id_apoderado"
            },
            {
                bSortable: false,
                data: null,
                className: "dt-control",
                defaultContent: ""
            },
            {data: "rut_apoderado"},
            {data: "ap_apoderado"},
            {data: "am_apoderado"},
            {data: "nombres_apoderado"},
            {data: "telefono"},
            {
                data: "asignacion",
                mRender: function(data) {
                    let estilo;
                    if (data == 'ASIGNADO') { estilo = 'bg-success'; }
                    if (data == 'NO ASIGNADO') { estilo = 'bg-danger'; }
                    return '<p class="text-center text-white rounded-3 mb-0 py-1 ' + estilo + '">' + data + '</p>';
                }
            },
            {
                data: null,
                bSortable: false,
                defaultContent: `<button class="btn btn-primary btn-data" id="btn_editar_apoderado" type="button" data-bs-toggle="modal" data-bs-target="#modal_apoderado"><i class="fas fa-pencil-alt"></i></button>
                                <button class="btn btn-danger btn-delete" id="btn_eliminar_apoderado" type="button"><i class="fas fa-trash-alt"></i></button>`,
                className: 'text-center'
            }
        ],
        language: spanish
    });

    expandInfoApoderado(tabla_apoderado);

    lanzarModalNuevoApoderado();
    lanzarModalupdateApoderado(tabla_apoderado);

    setApoderado(tabla_apoderado);
    updateApoderado(tabla_apoderado);

    deleteRegistroApoderado(tabla_apoderado);

    exportarApoderados('#btn_excel', 'xlsx');
    exportarApoderados('#btn_csv', 'csv');

});