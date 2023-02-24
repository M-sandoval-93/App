import {LibreriaFunciones, generar_dv, spanish } from './librerias/librerias.js';
let datos = 'getMatricula';

// ==================== FUNCIONES INTERNAS ===============================//
// Función para generar tabla expansiba con datos secundarios
function getData(data) {
    let sexo = 'Masculino';

    if (data.sexo == 'F') {
        sexo = 'femenina';
    }

    let apoderado = (ap) => {
        let apoderado_estudiante = 'Apoderado no asignado';
        if (ap != null) {
            apoderado_estudiante = ap;
        }
        return apoderado_estudiante
    }

    return (
        '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
            '<tr>' +
                '<td>Fecha de nacimiento:</td>' +
                '<td>' + data.fecha_nacimiento + '</td>' +
            '</tr>' +

            '<tr>' +
                '<td>Sexo estudiante:</td>' +
                '<td>' + sexo + '</td>' +
            '</tr>' +

            '<tr>' +
                '<td>Número matrícula:</td>' +
                '<td> N° ' + data.matricula + '</td>' +
            '</tr>' +

            '<tr>' +
                '<td>Apoderado titular:</td>' +
                '<td>' + apoderado(data.apoderado_titular) + '</td>' +
            '</tr>' +

            '<tr>' +
                '<td>Apoderado suplente:</td>' +
                '<td>' + apoderado(data.apoderado_suplente) + '</td>' +
            '</tr>' +

            '<tr>' +
                '<td>Fecha ingreso:</td>' +
                '<td>' + data.fecha_ingreso + '</td>' +
            '</tr>' +
        '</table>'
    );
}

// Función para expandir información secundaria
function expadirData(tabla) {
    $('#tabla_matricula_estudiante tbody').on('click', 'td.dt-control', function () {
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

// Función para obtener cantidad de registros en diferentes contextos
function getCantidadMatricula() {
    let datos = 'getCantidadMatricula';
    let valor = 0;

    $.ajax({
        url: "./controller/controller_matricula.php",
        type: "post",
        dataType: "json",
        data: {datos: datos},
        success: (data) => {
            $('#cantidad_matricula').text(data.cantidad_matricula);
            $('#cantidad_retiro').text(data.cantidad_retiro);
        }
    }).fail(() => {
        $('#cantidad_matricula').text('Error !!');
        $('#cantidad_retiro').text('Error !!');
    });
}

// Función para preparar el modal de matrícula
function prepararModalNuevaMatricula() {
    $('#form_registro_matricula').trigger('reset');
    $('#rut_estudiante_matricula').removeClass('is-invalid');
    // $('#rut_ap_titular').removeClass('is-invalid');
    // $('#rut_ap_suplente').removeClass('is-invalid');

    $('#informacion_rut').removeClass('text-danger');
    // $('#informacion_titular').removeClass('text-danger');
    // $('#informacion_suplente').removeClass('text-danger');

    $('#informacion_rut').text('Rut sin puntos, sin guión y sin dígito verificador');
    $('#informacion_rut').addClass('form-text');
    // $('#informacion_titular').text('Rut sin puntos, sin guión y sin dígito verificador');
    // $('#informacion_titular').addClass('form-text');
    // $('#informacion_suplente').text('Rut sin puntos, sin guión y sin dígito verificador');
    // $('#informacion_suplente').addClass('form-text');
    LibreriaFunciones.autoFocus($('#modal_matricula'), $('#rut_estudiante_matricula'));
}

// Función para lanzar el modal de nueva matrícula
function lanzarModalNuevaMatricula() {
    $('#btn_nueva_matricula').click(() => {
        prepararModalNuevaMatricula();
        $('#modal_matricula_tittle').text('REGISTRAR NUEVA MATRÍCULA');
        $('#btn_registrar_matricula').text('Registrar');
        $('#texto_secundario').text('Registro de matrícula N°');
        $('#fecha_matricula').val(LibreriaFunciones.getFecha());
        $('#informacion_titular').text('Asignar apoderado titular');
        $('#informacion_suplente').text('Asignar apoderado suplente');
    });
}

// Función para validar el rut ingresado 
function validarRutEstudiante() {
    $('#rut_estudiante_matricula').keyup(function(e) {
        e.preventDefault();
        generar_dv('#rut_estudiante_matricula', '#dv_rut_estudiante_matricula');
        // comprobarEstudiante($('#rut_estudiante_matricula').val());

        LibreriaFunciones.validarNumberRut($('#rut_estudiante_matricula'), $('#informacion_rut'));

        // crear funcion o modificar para que sirva con la informacion mostrada en los apoderados
        // ver si dejo lo que tengo o simplemente cambio el texto para que sea generico y agrego en la cabecera un "ASIGNACIÓN DE APODERADOS EN UN RECUADRO" REVISAR !!!!
    });
}





function deleteRegistroMatricula(tabla) {
    $('#tabla_matricula_estudiante tbody').on('click', '#btn_delete_matricula', function() {
        let data = tabla.row($(this).parents()).data();
        let id_matricula = data.id_matricula;
        Swal.fire({
            icon:'question',
            title: 'Eliminar matricula N° ' + data.matricula,
            showCancelButton: true,
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#2691d9',
            cancelButtonColor: '#adadad'
        }). then(resultado => {
            if (resultado.isConfirmed) {
                datos = "deleteMatricula";

                $.ajax({
                    url: "./controller/controller_matricula.php",
                    type: "post",
                    dataType: "json",
                    data: {datos: datos, id_matricual: id_matricula},
                    success: (data) => {
                        if (data == false) {
                            LibreriaFunciones.alertPopUp('error', 'Registro no eliminado !!');
                            return false;
                        }

                        LibreriaFunciones.alertPopUp('success', 'Registro eliminado !!');
                        beforeRegistroMatricula(tabla);
                    }
                }).fail(() => {
                    LibreriaFunciones.alertPopUp('error', 'Error de ejecución !!');
                });
            }
        });
    });
}

function beforeRegistroMatricula(tabla) {
    tabla.ajax.reload(null, false);
    getCantidadMatricula();
}


// Sección de suspención
function prepararModalEstado(tabla) {
    $('#tabla_matricula_estudiante tbody').on('click', '#btn_estado_matricula', function() {
        let data = tabla.row($(this).parents()).data();

        if ($(this).text() == 'Activo(a)') {
            $('#btn_activar_matricula').prop('disabled', true);
            $('#btn_suspender_matricula').prop('disabled', false);
        } else {
            $('#btn_suspender_matricula').prop('disabled', true);
            $('#btn_activar_matricula').prop('disabled', false);
        }

        prepararModalSuspencion();

    });
}

function prepararModalSuspencion() {
    $('#btn_suspender_matricula').click(() => {
        $('#modal_suspender_matricula').modal('show');
        $('#modal_matricula_estado').modal('hide');
    });
}


// Función para generar documento
function exportarMatriculas(btn, ext) {
    let datos = 'exportarMatriculas';

    $(btn).click((e) => {
        e.preventDefault();

        $.ajax({
            url: "./controller/controller_matricula.php",
            type: "post",
            dataType: "html",
            cache: false,
            data: {datos: datos, ext: ext},
            success: (data) => {
                let opResult = JSON.parse(data);
                let $a = $("<a>");
    
                $a.attr("href", opResult.data);
                $("body").append($a);
                $a.attr("download", "Registro matricula." + ext);
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

    getCantidadMatricula();

    let tabla_matricula = $('#tabla_matricula_estudiante').DataTable({
        ajax: {
            url: "./controller/controller_matricula.php",
            type: "post",
            dateType: "json",
            data: {datos: datos}
        },
        columns: [
            {
                data: "id_matricula",
                visible: false
            },
            {
                className: "dt-control",
                bSortable: false,
                data: null,
                defaultContent: ""
            },
            {data: "rut"},
            {data: "ap_paterno"},
            {data: "ap_materno"},
            {data: "nombre"},
            {data: "curso"},
            {
                data: "nombre_estado",
                // bSortable: false,
                mRender: function(data) {
                    let estilo;
                    let modal = 'data-bs-toggle="modal" data-bs-target="#modal_matricula_estado"';
                    if (data == 'Activo(a)') { estilo = 'btn-primary'; }
                    if (data == 'Suspendido(a)') { estilo = 'btn-warning'; }
                    if (data == 'Retirado(a)') { estilo = 'btn-danger'; modal = ""; }

                    return `<div class="d-grid col-10 mx-auto">
                                <button class="btn ` + estilo + `" title="Cambiar estado"` + modal + ` id="btn_estado_matricula">` + data + `</button>
                            </div>`
                }
            },
            {
                data: null,
                bSortable: false,
                defaultContent:`<button class="btn btn-primary btn-justify px-3" id="btn_edit_matricula" title="Editar matricula" type="button" data-bs-toggle="modal" data-bs-target="#modal_matricula"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-warning btn-justify px-3" id="btn_retiro_matricula" title="Retirar estudiante" type="button" data-bs-toggle="modal" data-bs-target="#modal_retiro_matricula"><i class="fas fa-sign-out-alt"></i></button>
                                <button class="btn btn-danger btn-delete px-3" id="btn_delete_matricula" title="Eliminar matricula" type="button"><i class="fas fa-trash-alt"></i></button>`,
                className: "text-center"
            }
        ],
        language: spanish
    });

    expadirData(tabla_matricula);

    lanzarModalNuevaMatricula();

    prepararModalEstado(tabla_matricula);

    deleteRegistroMatricula(tabla_matricula);

    // desabilitar boton interior dependiendo del estado de la matricula, al presionarlo
    // Trabajar en boton para el retiro de un estudiante, considerando un modal con la fecha y posible motivo
    // Trabajar en modal para editar una matrícula

    exportarMatriculas('#btn_excel', 'xlsx');
    exportarMatriculas('#btn_csv', 'csv');


    validarRutEstudiante();








});




