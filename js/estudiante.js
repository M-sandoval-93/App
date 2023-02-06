import {LibreriaFunciones, generar_dv, spanish } from './librerias/librerias.js';
let datos = 'getEstudiantes';

// ==================== FUNCIONES INTERNAS ===============================//

// Función para maquetar datos adicionales del estudiante
function getInfoSecundaria(data) {
    let junaeb = 'SI';
    let fecha_retiro = data.fecha_retiro;

    if (data.junaeb != 1) {
        junaeb = 'NO';
    }

    if (data.fecha_retiro == null) {
        fecha_retiro = 'Estudiante sin fecha de retiro !!';
    }

    return (
        '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
            '<tr>' +
                '<td>Fecha de nacimiento:</td>' +
                '<td>' + data.fecha_nacimiento + '</td>' +
            '</tr>' +
        
            '<tr>' +
                '<td>Beneficio Junaeb:</td>' +
                '<td>' + junaeb + '</td>' +
            '</tr>' +

            '<tr>' +
                '<td>Fecha de retiro:</td>' +
                '<td>' + fecha_retiro + '</td>' +
            '</tr>' +
        '</table>'
    )
}

// Función para obtener la cantidad de registros de estudiantes (matriculados y no matriculados)
function cantidadEstudiante() {
    let datos = 'getCantidadEstudiante';
    let valor = 0;

    $.ajax({
        url: "./controller/controller_estudiante.php",
        type: "post",
        dataType: "json",
        data: {datos: datos},
        success: function(data) {
            if (data != false) {
                valor = data;
            }
            $('#cantidad_estudiante').text(valor);
        }
    }).fail(() => {
        $('#cantidad_estudiante').text('Error !!');
    });
}

// Función para mostrar los datos adicionales del estudiante
function expandInfoEstudiante(tabla) {
    $('#tabla_estudiante tbody').on('click', 'td.dt-control', function () {
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

// Función para eliminar el registro de un estudiante
function deleteRegistroEstudiante(tabla) {
    $('#tabla_estudiante tbody').on('click', '#btn_eliminar_estudiante', function() {
        let data = tabla.row($(this).parents()).data();
        let id_estudiante = data.id_estudiante;

        Swal.fire({
            icon: 'question',
            title: 'Eliminar registro de "' + data.nombres_estudiante + ' ' + data.ap_estudiante + '"',
            showCancelButton: true,
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#2691d9',
            cancelButtonColor: '#adadad'
        }). then(resultado => {
            if (resultado.isConfirmed) {
                datos = "deleteEstudiante";

                $.ajax({
                    url: "./controller/controller_estudiante.php",
                    type: "post",
                    dataType: "json",
                    data: {datos: datos, id_estudiante: id_estudiante},
                    success: function(data) {
                        if (data == false) {
                            LibreriaFunciones.alertPopUpButton('error', 'No se puede elimianar un estudiante matriculado, primero elimine la matrícula !!');
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

function beforeRecord(tabla) {
    tabla.ajax.reload(null, false);
    cantidadEstudiante();
}


// ==================== FUNCIONES INTERNAS ===============================//


$(document).ready(function() {

    cantidadEstudiante();

    let tabla_estudiante = $('#tabla_estudiante').DataTable({
        ajax: {
            url: "./controller/controller_estudiante.php",
            type: "post",
            dataType: "json",
            data: {datos: datos}
        },
        columns: [
            {
                visible: false,
                data: "id_estudiante"
            },
            {
                bSortable: false,
                data: null,
                className: "dt-control",
                defaultContent: ""
            },
            {data: "rut_estudiante"},
            {data: "ap_estudiante"},
            {data: "am_estudiante"},
            {data: "nombres_estudiante"},
            {data: "fecha_ingreso"},
            {
                data: "anio_lectivo",
                mRender: function(data) {
                    let estilo;
                    if (data == 'Matriculado') {
                        estilo = 'text-white bg-success';
                    } else if (data == 'No matriculado') {
                        estilo = 'text-white bg-danger';
                    }

                    return '<p class="text-center rounded-3 mb-0 py-1 ' + estilo + '">' + data + '</p>';
                }
            },
            {
                data: null,
                bSortable: false,
                defaultContent: `<button class="btn btn-primary btn-data" id="btn_editar_estudiante" type="button"><i class="fas fa-pencil-alt"></i></button>
                                <button class="btn btn-danger btn-delete" id="btn_eliminar_estudiante" type="button"><i class="fas fa-trash-alt"></i></button>`
            }
        ],
        order: [[3, 'asc']],
        language: spanish


    });

    expandInfoEstudiante(tabla_estudiante);

    deleteRegistroEstudiante(tabla_estudiante);





});