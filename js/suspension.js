import {LibreriaFunciones, generar_dv, spanish } from './librerias/librerias.js';
let datos = 'getSuspension';


// ==================== FUNCIONES INTERNAS ===============================//
// Función para mostrar datos extras
function getData(data) {
    return (
        '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
            '<tr>' +
                '<td>Motivo suspensión:</td>' +
                '<td>' + data.motivo + '</td>' +
            '</tr>' +
        '</table>'
    );
}

// Función para expandir información secundaria // PASAR A FUNCION GENERICA
function expadirData(tabla) {
    $('#tabla_suspension_matricula tbody').on('click', 'td.dt-control', function () {
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

// Función para obtener cantidad de registros
function getCantidadSuspension() {
    let datos = 'getCantidadSuspension';

    $.ajax({
        url: "./controller/controller_suspension.php",
        type: "post",
        dataType: "json",
        data: {datos: datos},
        success: (data) => {
            $('#cantidad_suspension_anual').text(data.cantidad_anual);
            $('#cantidad_suspension_activa').text(data.cantidad_activa);
        }
    }).fail(() => {
        $('#cantidad_suspension_anual').text('Error !!');
        $('#cantidad_suspension_activa').text('Error !!');
    });
}

// Función para ejecutar una accion al terminar un proceso de registro
function beforeRegistroSuspension(tabla) {
    tabla.ajax.reload(null, false);
    getCantidadSuspension();
}

// Función para generar documento
function exportarSuspension(btn, ext) {
    let datos = 'exportarSuspension';

    $(btn).click((e) => {
        e.preventDefault();

        $.ajax({
            url: "./controller/controller_suspension.php",
            type: "post",
            dataType: "html",
            cache: false,
            data: {datos: datos, ext: ext},
            success: (data) => {
                let opResult = JSON.parse(data);
                let $a = $("<a>");
    
                $a.attr("href", opResult.data);
                $("body").append($a);
                $a.attr("download", "Registro suspensión." + ext);
                $a[0].click();
                $a.remove();
            }
        }). fail(() => {
            LibreriaFunciones.alertPopUp('error', 'Error al generar documento');
        });
    });
}



// ================== MANEJO DE INFORMARCIÓN ================== //
// Función para eliminar el registro de una suspension
function deleteRegistroSuspension(tabla) {
    $('#tabla_suspension_matricula tbody').on('click', '#btn_delete_suspension', function() {
        let data = tabla.row($(this).parents()).data();
        let f_inicio = new Date(LibreriaFunciones.textFecha(data.fecha_inicio));
        let f_termino = new Date(LibreriaFunciones.textFecha(data.fecha_termino));
        let f_actual = new Date();
        f_actual.setDate(f_actual.getDate() - 1);

        if (f_inicio < f_actual && f_termino < f_actual) {
            LibreriaFunciones.alertPopUp('warning', 'No se puede eliminar una suspensión iniciada');
            return false;
        }

        Swal.fire({
            icon:'question',
            title: 'Eliminar suspención "' + data.nombres + '"',
            showCancelButton: true,
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#2691d9',
            cancelButtonColor: '#adadad'
        }). then(resultado => {
            if (resultado.isConfirmed) {
                datos = "deleteSuspension";

                $.ajax({
                    url: "./controller/controller_suspension.php",
                    type: "post",
                    dataType: "json",
                    data: {datos: datos, id_suspension: data.id_suspension},
                    success: (response) => {
                        if (response == true) {
                            LibreriaFunciones.alertPopUp('success', 'Registro eliminado !!');
                            beforeRegistroSuspension(tabla);
                            return false;
                        }

                        LibreriaFunciones.alertPopUp('warning', 'Registro no eliminado !!');
                    }
                }).fail(() => {
                    LibreriaFunciones.alertPopUp('error', 'Error en la ejecución !!');
                });
            }
        });
    });
}



// ==================== FUNCIONES INTERNAS ===============================//
$(document).ready(function() {
    getCantidadSuspension();

    let tabla_suspension = $('#tabla_suspension_matricula').DataTable({
        ajax: {
            url: "./controller/controller_suspension.php",
            type: "post",
            dateType: "json",
            data: {datos: datos}
        },
        columns: [
            // { orderable: false, target: 0 },
            {
                data: "id_suspension",
                visible: false
            },
            {
                className: "dt-control",
                bSortable: false,
                data: null,
                defaultContent: ""
            },
            {data: "rut"},
            {data: "nombres"},
            {data: "curso"},
            {data: "fecha_inicio"},
            {data: "fecha_termino"},
            {data: "dias_suspension"},
            {
                data: "estado",
                mRender: function(data) {
                    let estilo = 'btn-success';
                    if (data == 'Suspensión terminada') { estilo = 'btn-secondary'; }
                    if (data == 'Suspensión a comenzar') { estilo = 'btn-warning'; }

                    return `<div class="d-grid col-12 mx-auto">
                                <span class="btn ` + estilo + `">` + data + `</span>
                            </div>`
                }
            },
            {
                data: null,
                bSortable: false,
                defaultContent: '<button class="btn btn-danger btn-delete px-3" id="btn_delete_suspension" title="Eliminar matricula" type="button"><i class="fas fa-trash-alt"></i></button>',
                className: "text-center"
            }
        ],
        order: ([]), // para quitar el orden automatico que incluye datatable
        language: spanish
    });

    expadirData(tabla_suspension);
    deleteRegistroSuspension(tabla_suspension);

    exportarSuspension('#btn_excel', 'xlsx');
    exportarSuspension('#btn_csv', 'csv');



});