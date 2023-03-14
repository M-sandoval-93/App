import {LibreriaFunciones, generar_dv, spanish } from './librerias/librerias.js';
let asignatura = new Array();
let datos = 'getJustificaciones';

// ==================== FUNCIONES INTERNAS ===============================//
// Estructura de la tabla con información adicional
function getDataSecundaria(data) {

    return(
        '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
            '<tr>' +
                '<td>Fecha justificación:</td>' +
                '<td>' + data.fecha_justificacion + '</td>' +
            '</tr>' +
        
            '<tr>' +
                '<td>Apoderado:</td>' +
                '<td>' + data.nombre_apoderado + '</td>' +
            '</tr>' +

            '<tr>' +
                '<td>Motivo falta:</td>' +
                '<td>' + data.motivo_falta + '</td>' +
            '</tr>' +

            '<tr>' +
                '<td>Documento presentado:</td>' +
                '<td>' + data.presenta_documento + '</td>' +
            '</tr>' +

            '<tr>' +
                '<td>Pruebas por asignatura:</td>' +
                '<td>' + data.prueba_pendiente + '</td>' +
            '</tr>' +
        '</table>'
    );
}

// Función para expandir información secundaria
function expandirData(tabla) {
    $('#tabla_justificacion_estudiante tbody').on('click', 'td.dt-control', function () {
        let tr = $(this).closest('tr');
        let row = tabla.row(tr);

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
        } else {
            row.child(getDataSecundaria(row.data())).show();
            tr.addClass('shown');
        }
    });
}

// Función para obtener la cantidad de justificaciones anuales
function getCantidadJustificacion() {
    let datos = "getCantidadJustificacion";

    $.ajax({
        url: "./controller/controller_justificacion.php",
        type: "POST",
        dataType: "json",
        data: {datos: datos},
        success: (response) => {
            $('#justificacion_diaria').text(response.cantidad_justificacion);
        }
    }).fail(() => {
        $('#justificacion_diaria').text('Error !!');
    });
}

// Función para obtener los datos del estudiante
function getEstudianteJustificacion(rut, input_nombre, input_curso) {
    let datos = 'getEstudianteJustificacion';

    if (rut != '' && rut.length > 7 && rut.length <= 9) {
        $.ajax({
            url: "./controller/controller_estudiante.php",
            type: "POST",
            dataType: "json",
            cache: false,
            data: {datos: datos, rut: rut},
            success: (response) => {
                if (response != false) {
                    input_nombre.val(response.nombre_estudiante);
                    input_curso.val(response.curso);
                    getApoderadoTS(rut, '#justificacion_apoderado');
                    return false;
                }
                
                input_nombre.val('sin datos');
                input_curso.val('N/A');
                $('#justificacion_apoderado').empty(); 
            }
        }).fail(() => {
            LibreriaFunciones.alertPopUp('error', 'Error en la consulta !!');
        });
    } else {
        input_nombre.val('');
        input_curso.val('');
        $('#justificacion_apoderado').empty();
    }
}

// Función para obtener los apoderados del estudiante
function getApoderadoTS(rut, campo) {
    let datos = 'getApoderadoTS';

    $.ajax({
        url: "./controller/controller_apoderado.php",
        type: "post",
        dataType: "json",
        data: {datos: datos, rut: rut},
        success: (response) => {
            $(campo).html(response);
        }
    });
}

// Función para obtener las asignaturas en las que se deben pruebas
function getPruebaAsignaturas() {
    $('#close_modal_justificacion_asignatura').click(() => {
        $('#justificacion_prueba_pendiente').prop('checked', false);
        $('#group_of_the_check').empty();
        $('#modal_registro_justificacion_falta').modal('show');
        asignatura = [];
    });

    $('#btn_seleccion_asignatura').click(() => {
        $('#group_of_the_check input[type=checkbox]').each(function() {
            if (LibreriaFunciones.comprobarCheck(this)) {
                asignatura.push($(this).val());
            }
        });

        if (asignatura.length == 0) {
            LibreriaFunciones.alertPopUp('warning', 'Seleccione alguna asignatura !!');
            return false;
        }

        $('#modal_justificacion_asignatura').modal('hide');
        $('#group_of_the_check').empty();
        $('#modal_registro_justificacion_falta').modal('show');
    });
}

// Función para obtener los datos del modal justificación
function getDataJustificacion() {
    const justificacion = {
        rut: $.trim($('#justificacion_rut_estudiante').val()),
        fecha_inicio: $.trim($('#justificacion_fecha_inicio').val()),
        fecha_termino: $.trim($('#justificacion_fecha_termino').val()),
        id_apoderado: $.trim($('#justificacion_apoderado').val()),
        motivo: $.trim($('#justificacion_motivo_causa').val().toUpperCase()),
        documento: LibreriaFunciones.comprobarCheck('#justificacion_documento'),
        pruebas: LibreriaFunciones.comprobarCheck('#justificacion_prueba_pendiente')   
    }

    return justificacion;
}

// Función para comprobar los campos vacios del formulario
function comprobarCamposVacios(objeto) {
    let count = 0;
    for (const [key, value] of Object.entries(objeto)) {
        if ((key == 'rut' && value == '') ||
            (key == 'fecha_inicio' && value == '') ||
            (key == 'fecha_termino' && value == '') ||
            (key == 'id_apoderado' && value == 'Seleccionar apoderado' || value == 'Sin apoderados !!')) {
            count += 1;
        }
    }

    return count;
}

// Función para validar el rut del estudiante
function validarRutEstudiante() {
    $('#justificacion_rut_estudiante').keyup((e) => {
        e.preventDefault();
        generar_dv($('#justificacion_rut_estudiante'), $('#justificacion_dv_rut_estudiante'));
        getEstudianteJustificacion($('#justificacion_rut_estudiante').val(), $('#justificacion_nombre_estudiante'), $('#justificacion_curso_estudiante'));
        LibreriaFunciones.validarNumberRut($('#justificacion_rut_estudiante'), true, '');
    });
}

// Función para actualizar la tabla de justificaciones después de una modificación
function beforeRegistroJustificacion(tabla) {
    tabla.ajax.reload(null, false);
    getCantidadJustificacion();
}



// ================== FUNCÓN PARA TRABAJAR CON MODALES ================== //
// Función para preparar el modal de justificación
function prepararModalJustificacion() {
    let fecha_actual = new Date();

    $('#btn_nueva_justificacion').click(() => {
        $('#form_registro_justificacion_falta').trigger('reset');
        $('#justificacion_fecha').val(fecha_actual.toLocaleDateString());
        $('#justificacion_rut_estudiante').removeClass('is-invalid');
        $('#justificacion_prueba_pendiente').prop('disabled', true);
        LibreriaFunciones.autoFocus($('#modal_registro_justificacion_falta'), $('#justificacion_rut_estudiante'));
        asignatura = [];
    });

    $('#justificacion_documento').click(function() {
        if (LibreriaFunciones.comprobarCheck(this)) {
            $('#justificacion_prueba_pendiente').prop('disabled', false);
        } else {
            $('#justificacion_prueba_pendiente').prop('disabled', true);
            $('#justificacion_prueba_pendiente').prop('checked', false);
            asignatura = [];
        }
    });

    prepararModalAsignatura();
    validarRutEstudiante()
}

// Función para prepara el modal de selección de asignaturas
function prepararModalAsignatura() {
    let datos = "getAsignatura";
    
    $('#justificacion_prueba_pendiente').click(function () {
        if (LibreriaFunciones.comprobarCheck(this)) {
            if ($('#justificacion_asignatura_nombre').val() == '' && $('#justificacion_curso_estudiante').val() == '') {
                LibreriaFunciones.alertPopUp('warning', 'Ingresar rut del estudiante !!');
                return false
            }

            $('#form_justificacion_asignatura').trigger('reset')
            $('#justificacion_asignatura_nombre').val($('#justificacion_nombre_estudiante').val());
            $('#justificacion_asignatura_curso').val($('#justificacion_curso_estudiante').val());

            $.ajax({
                url: "./controller/controller_asignatura.php",
                type: "POST",
                dataType: "json",
                data: {datos: datos},
                success: function(response) {
                    $.each(response, (obj, datos) => {
                        $('#group_of_the_check').append(`<div class="col-6">
                                                            <div class="form-check">
                                                                <input type="checkbox" id="check_` + datos['id_asignatura'] + `" class="form-check-input" value="` + datos['id_asignatura'] + `">
                                                                <label for="check_` + datos['id_asignatura'] + `" class="form-check-label">` + datos['asignatura'] + `</label>
                                                            </div>
                                                        </div>`);
                    });
                }
            }).fail(() => {
                $('#group_of_the_check').append('<h2>Error al consultar datos</h2>');
            });
            
            $('#modal_registro_justificacion_falta').modal('hide');
            $('#modal_justificacion_asignatura').modal('show');
        } else {
            asignatura = [];
        }
    });
}



// ================== MANEJO DE INFORMARCIÓN ================== //
// Función para registrar la justificación de un estudiante
function setModalJustificacion(tabla) {
    $('#btn_registrar_justificacion').click((e) => {
        e.preventDefault();
        if (LibreriaFunciones.comprobarLongitud($('#justificacion_rut_estudiante').val(), 7, 9, 'RUT', 'Apoderado') == false) { return false; }

        let datos  = "setJustificacion";
        const justificacion = getDataJustificacion();

        if (comprobarCamposVacios(justificacion) >= 1) {
            LibreriaFunciones.alertPopUp('info', 'Faltan datos importante !!');
            return false;
        }

        $.ajax({
            url: "./controller/controller_justificacion.php",
            type: "post",
            dataType: "json",
            data: {datos: datos, justificacion: justificacion, asignatura: asignatura},
            success:(response) => {
                if (response == true) {
                    LibreriaFunciones.alertPopUp('success', 'Justificación registrada !!');
                    $('#modal_registro_justificacion_falta').modal('hide');
                    beforeRegistroJustificacion(tabla);
                    return false;
                }
                LibreriaFunciones.alertPopUp('error', 'Justificación no almacenada !!!');
            }
        }). fail (() => {
            LibreriaFunciones.alertPopUp('error', 'Error en la operación !!');
        });
    });
}

// Función para eliminar un registro de justificación
function deleteRegistroJustificacion(tabla) {
    $('#tabla_justificacion_estudiante tbody').on('click', '#btn_delete_justificar', function() {
        let data = tabla.row($(this).parents()).data();
        let id_justificacion = data.id_justificacion;

        Swal.fire({
            icon: 'question',
            title: 'Eliminar registro de "' + data.nombre + ' ' + data.ap_paterno + '"',
            showCancelButton: true,
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#2691d9',
            cancelButtonColor: '#adadad'
        }). then(resultado => {
            if (resultado.isConfirmed) {
                datos = "deleteJustificacion";

                $.ajax({
                    url: "./controller/controller_justificacion.php",
                    type: "post",
                    dataType: "json",
                    data: {datos: datos, id_justificacion: id_justificacion},
                    success: function(data) {
                        if (data == true) {
                            // LibreriaFunciones.alertPopUp('success', 'Registro eliminado !!');
                            LibreriaFunciones.alertToast('success', 'Registro eliminado !!');
                            beforeRegistroJustificacion(tabla);
                            return false;
                        }
                        // LibreriaFunciones.alertPopUp('error', 'Registro no eliminado !!');
                        LibreriaFunciones.alertToast('error', 'Registro no eliminado !!');
                    }
                }). fail (() => {
                    LibreriaFunciones.alertPopUp('error', 'Error en la operación !!');
                });
            }
        });
    });
}

// Función para generar un certificado de justificacion
function getCertificadoJustificacion(table) {
    $('#tabla_justificacion_estudiante tbody').on('click', '#btn_download_justificar', function() {
        LibreriaFunciones.alertPopUp('info', 'Función en mantenimiento');

    });
}

// Función para exportar justificaciones
function exportarJustificaciones(btn, ext) {
    let datos = 'exportarJustificaciones';

    $(btn).click((e) => {
        e.preventDefault();

        $.ajax({
            url: "./controller/controller_justificacion.php",
            type: "post",
            dataType: "html",
            cache: false,
            data: {datos: datos, ext: ext},
            success: (data) => {
                let opResult = JSON.parse(data);
                let $a = $("<a>");
    
                $a.attr("href", opResult.data);
                $("body").append($a);
                $a.attr("download", "Registro justificaciones." + ext);
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
    getCantidadJustificacion();

    // LLENAR DATATABLE CON INFORMACIÓN =============================== 
    let tabla_justificacion_estudiante = $('#tabla_justificacion_estudiante').DataTable({
        ajax: {
            url: "./controller/controller_justificacion.php",
            type: "POST",
            dataType: "json",
            data: {datos: datos}
        },
        columns: [
            {
                data: "id_justificacion",
                visible: false,
                searchable: false
            },
            {
                className: "dt-control",
                bSortable: false,
                data: null,
                defaultContent: ""
            },
            {data: "rut"},
            {data: "ap_estudiante"},
            {data: "am_estudiante"},
            {data: "nombres_estudiante"},
            {data: "curso"},
            {data: "fecha_inicio"},
            {data: "fecha_termino"},
            {
                data: null,
                defaultContent: `<button class="btn btn-primary btn-justify px-3" id="btn_download_justificar" title="Descargar certificado" type="button"><i class="fas fa-file-download"></i></button>
                                <button class="btn btn-danger btn-delete px-3" id="btn_delete_justificar" title="Eliminar justificación" type="button"><i class="fas fa-trash-alt"></i></button>`,
                className: "text-center"
            }
        ],
        order: ([]), // para quitar el orden automatico que incluye datatable
        language: spanish
    });

    expandirData(tabla_justificacion_estudiante);

    prepararModalJustificacion();
    getPruebaAsignaturas();
    setModalJustificacion(tabla_justificacion_estudiante);
    deleteRegistroJustificacion(tabla_justificacion_estudiante);

    getCertificadoJustificacion(tabla_justificacion_estudiante);
    exportarJustificaciones('#btn_excel', 'xlsx');
    exportarJustificaciones('#btn_csv', 'csv');




    // Generar PDF con información o DOC
    // Ver como generar un pdf o generar u word
    // Para descargar word link: 
    // Parte 1: https://www.youtube.com/watch?v=ABsk2ajYAGQ
    // Parte 2: https://www.youtube.com/watch?v=3fmsZ6fZz1w

    // Opción para PDF https://www.youtube.com/watch?v=PvI3nbffuqk


    // VER si genero un word o un archivo PDF con toda la estructura necesaria
    // de manera manual, así no requiere de una plantilla. !!!!!!!


});


