import { spanish, LibreriaFunciones, generar_dv } from './librerias/librerias.js';
let datos = 'getRetraso'; 

// ==================== FUNCIONES INTERNAS ===============================//
// obtener atraso diario y anual
function getCantidadRetraso(tipo, id_campo) { // Terminado y revisado ...
    let datos = 'getCantidadRetraso';
    let valor = 0;

    $.ajax({
        url: "./controller/controller_retraso.php",
        type: "post",
        dataType: "json",
        data: {datos: datos, tipo: tipo},
        success: function(data) {
            if (data != false) {
                valor = data;
            }
            $(id_campo).text(valor);
        }
    }).fail(() => {
        $(id_campo).text('Error !!');
    });
}

// trae información de los atrasos de un estudiante sin justificar
function getRetrasoSinJustificar(rut) { // Terminado y revisado !!
    let datos = 'getRetrasoSinJustificar';
    $('#retraso_sinJustificar').DataTable().destroy();

    $('#retraso_sinJustificar').DataTable({
        searching: false,
        info: false,
        lengthChange: false,
        aaSorting: [], // evitar que se ordenen las filas
        iDisplayLength: 5,
        ajax: {
            url: "./controller/controller_retraso.php",
            type: "post",
            dateType: "json",	
            data: {datos: datos, rut: rut}
        },
        columns: [
            {data: "fecha_atraso", className: 'text-center'},
            {data: "hora_atraso", className: 'text-center'},
            {data: "id_atraso", className: 'text-center'}
        ],
        columnDefs: [
            {
                target: 2,
                checkboxes: {selectRow: true}
            }
        ],
        select: {style: 'multi'},
        language: spanish
    });
}

// traer información del estudiante al ingresar nuevo atraso
function getEstudiante(rut, input_nombre, input_curso) { // Terminado y revisado !!
    let datos = 'getEstudiante';

    if (rut != '' && rut.length > 7 && rut.length < 9) {
        if (input_nombre.val() == '') {
            $.ajax({
                url: "./controller/controller_estudiante.php",
                type: "post",
                dataType: "json",
                cache: false,
                data: {datos: datos, rut: rut, tipo: 'retraso'},
                success: function(info) {
                    if (info != false) {
                        input_nombre.val(info[0].nombre_estudiante);
                        input_curso.val(info[0].curso);

                        if (info[0].cantidad_atraso >= 1) {
                            $('#alerta_retraso_cantidad').text('Retrasos sin justificar: ' + info[0].cantidad_atraso);
                            $('#alerta_retraso_cantidad').show();
                        }

                        if (info[0].id_estado == 5) {
                            $('#registrar_retraso').prop('disabled', true);
                            $('#alerta_suspencion_activa').text('Estudiante suspendido !!!');
                            $('#alerta_suspencion_activa').show();
                        } 

                    } else {
                        input_nombre.val('Sin datos');
                        input_curso.val('N/A');
                    }
                }
            });
        }
    } else {
        input_nombre.val('');
        input_curso.val('');
        $('#alerta_retraso_cantidad').hide();
        $('#alerta_suspencion_activa').hide();
        $('#registrar_retraso').removeAttr('disabled');
    }
}

function prepararModalRetraso() { // Terminado y revisado !!
    let fecha_hora_actual = new Date();
    
    $('#form_registro_retraso').trigger('reset');
    $('#staticFecha').val(fecha_hora_actual.toLocaleDateString());
    $('#staticHora').val(fecha_hora_actual.toLocaleTimeString());
    $('#registrar_retraso').removeAttr('disabled');
    $('#alerta_retraso_cantidad').hide();
    $('#alerta_suspencion_activa').hide();
    $('#rut_estudiante_retraso').removeClass('is-invalid');
    $('#informacion_rut').removeClass('text-danger');
    $('#informacion_rut').text('Rut sin puntos, sin guión y sin dígito verificador');
    $('#informacion_rut').addClass('form-text');
    LibreriaFunciones.autoFocus($('#modal_registro_retraso'), $('#rut_estudiante_retraso'));
}

// Función para lanzar el modal de ingreso de retraso
function lanzarModalRetraso() { // Terminado y revisado !!
    $('#btn_nuevo_retraso').click(() => {
        prepararModalRetraso();
    });
}

// funcion que prepara el modal de justificación de atrasos
function prepararModalJustificar(data) { // Terminado y revisado !!
    $('#modal_justificar_retraso').modal('show');
    $('#rut_estudiante_justifica').val(data.rut);
    $('#curso_estudiante_justifica').val(data.curso);
    $('#nombre_estudiante_justifica').val(data.nombre + ' ' + data.ap_paterno + ' ' + data.ap_materno);
    // $('#marcar_desmarcar_retrasos').removeClass('active');
    // $('#marcar_desmarcar_retrasos').text('Marcar todo');
}

// función para validar el rut y consultar datos del mismo
function validarRut() { // Terminado y revisado !!
    $('#rut_estudiante_retraso').keyup(function(e) {
        e.preventDefault();
        generar_dv('#rut_estudiante_retraso', '#dv_rut_estudiante_retraso');
        getEstudiante($('#rut_estudiante_retraso').val(), $('#nombre_estudiante_retraso'), $('#curso_estudiante_retraso'));

        LibreriaFunciones.validarNumberRut($('#rut_estudiante_retraso'), $('#informacion_rut'), 'Rut sin puntos, sin guión y sin dígito verificador');  
    });
}

// función para cuando se almacena un registro y se recargan los datos necesarios
function beforeRegistro(tabla_retraso) { // Terminado y revisado !!
    tabla_retraso.ajax.reload(null, false);
    getCantidadRetraso('diario', '#retraso_diario');
    getCantidadRetraso('total', '#retraso_total');
    prepararModalRetraso();
}

// función para generar documento
function exportarRetraso(btn, ext) { // Terminado y revisado !!
    let datos = 'exportarRetraso';

    $(btn).click((e) => {
        e.preventDefault();

        $.ajax({
            url: "./controller/controller_retraso.php",
            type: "post",
            dataType: "html",
            cache: false,
            data: {datos: datos, ext: ext},
            success: (data) => {
                let opResult = JSON.parse(data);
                let $a = $("<a>");
    
                $a.attr("href", opResult.data);
                $("body").append($a);
                $a.attr("download", "Registro retrasos." + ext);
                $a[0].click();
                $a.remove();
            }
        }). fail(() => {
            LibreriaFunciones.alertPopUp('error', 'Error al generar documento');
        });
    });
}

function justificarRetraso(tabla_retraso) { // Terminado y revisado !!
    $('#btn_justificar_retraso').click(function(e) {
        e.preventDefault();
        let row_selected = $('#retraso_sinJustificar').DataTable().column(2).checkboxes.selected();
        let atrasos = [];
        let id_apoderado = $('#apoderado_justifica').val();
        datos = 'setJustificar';

        if ($('#apoderado_justifica').val() == 'Sin apoderados !!' || $('#apoderado_justifica').val() == 'Seleccionar apoderado') {
            LibreriaFunciones.alertPopUp('warning', 'Seleccionar apoderado !!');
            return false;
        }

        if (row_selected.length < 1) {
            LibreriaFunciones.alertPopUp('warning', 'Seleccionar retraso !!');
            return false;
        }

        $.each(row_selected, function(index, rowId) {
            atrasos.push(rowId);
        });

        $.ajax({
            url: "./controller/controller_retraso.php",
            type: "post",
            dataType: "json",
            cache: false,
            data: {datos: datos, id_apoderado: id_apoderado, retrasos: retrasos},
            success: function(data) {
                if (data != false) {
                    LibreriaFunciones.alertPopUp('error', 'No registrado !!');
                }

                LibreriaFunciones.alertPopUp('success', 'Retrasos justificados !!');
                tabla_retraso.ajax.reload(null, false);
                $('#modal_justificar_retraso').modal('hide');
            }
        }).fail(() => {
            LibreriaFunciones.alertPopUp('error', 'Error de registro, revisar !!');
        });    
    });
}

function showModalJustificaciones(tabla_retraso) { // Terminado y revisado !!
    $('#tabla_retraso tbody').on('click', '#btn_justificar_retraso', function() {
        let data = tabla_retraso.row($(this).parents()).data();
        let rut = data.rut.slice(0, -2);
        
        prepararModalJustificar(data);
        LibreriaFunciones.loadApoderado(rut, '#apoderado_justifica');
        getRetrasoSinJustificar(rut); // revisar el orden en que se presentan
    });
}

function deleteRegistroRetraso(tabla_retraso) { // Terminado y revisado !!
    $('#tabla_retraso tbody').on('click', '#btn_eliminar_retraso', function() {
        let data = tabla_retraso.row($(this).parents()).data();
        let id_retraso = data.id_atraso;

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
                datos = "deleteRetraso";

                $.ajax({
                    url: "./controller/controller_retraso.php",
                    type: "post",
                    dataType: "json",
                    data: {datos: datos, id_retraso: id_retraso},
                    success: function(data) {
                        if (data == false) {
                            LibreriaFunciones.alertPopUp('error', 'Registro no eliminado !!');
                            return false;
                        }

                        LibreriaFunciones.alertPopUp('success', 'Registro eliminado !!');
                        beforeRegistro(tabla_retraso);
                    }
                }).fail(() => {
                    LibreriaFunciones.alertPopUp('error', 'Error en la operación !!');
                });
            }
        });
    });
}

function setRetraso(tabla_retraso) { // En progreso ... implementar impresión de ticket !!!
    $('#btn_registrar_retraso').click((e) => {
        e.preventDefault();
        datos = 'setRetraso';
        let rut;

        
        if ($('#rut_estudiante_retraso').val() == '' || $('#nombre_estudiante_retraso').val() == 'Sin datos' || $('#nombre_estudiante_retraso').val() == '') {
            LibreriaFunciones.alertPopUp('info', 'Faltan datos importantes');
            return false;
        } // Arreglar comprobación el datos del formulario
        if (LibreriaFunciones.comprobarLongitud($('#rut_estudiante_retraso').val(), 7, 9, 'RUT', 'Apoderado') == false) { return false; }

        

        rut = $.trim($('#rut_estudiante_retraso').val());

        $.ajax({
            url: "./controller/controller_retraso.php",
            type: "post",
            dataType: "json",
            data: {datos: datos, rut: rut },
            success: function(data) {
                if (data == false) {
                    LibreriaFunciones.alertPopUp('error', 'Registro no ingresado !!');
                    return false;
                }

                LibreriaFunciones.alertPopUp('success', 'Registro ingresado !!');
                beforeRegistro(tabla_retraso);
            }
        });
    });
}

// ==================== FUNCIONES INTERNAS ===============================//


$(document).ready(function() {
    // Cantidad de atrasos diarios y total
    getCantidadRetraso('diario', '#retraso_diario');
    getCantidadRetraso('total', '#retraso_total');

    // LLENAR DATATABLE CON INFORMACIÓN =============================== 
    let tabla_retraso = $('#tabla_retraso').DataTable({ // Terminado y revisado !!
        ajax: {
            url: "./controller/controller_retraso.php",
            type: "post",
            dateType: "json",
            data: {datos: datos}
        },
        columns: [
            {   
                data: "id_atraso",
                visible: false,
                // searchable: false
            },
            {data: "rut"},
            {data: "ap_paterno"},
            {data: "ap_materno"},
            {data: "nombre"},
            {data: "curso"},
            {data: "fecha_atraso"},
            {data: "hora_atraso"},
            {
                data: null,
                defaultContent: `<button class="btn btn-primary btn-justify" id="btn_justificar_retraso" title="Justificar atraso" type="button"><i class="fas fa-user-check"></i></button>
                                <button class="btn btn-danger btn-delete" id="btn_eliminar_retraso" title="Eliminar atraso" type="button"><i class="fas fa-trash-alt"></i></button>`,
                className: 'text-center'
            }
        ],
        order: [0, 'desc'],
        language: spanish
    });

    // funcion para preparar el modal antes de ingresar datos
    lanzarModalRetraso();

    // Función para registrar nuevo atraso e imprimir ticket de atraso 
    setRetraso(tabla_retraso);

    // Función para mostrar modal con los atrasos a justificar de estudiante
    showModalJustificaciones(tabla_retraso);

    // Función para justificar los atrasos de un estudiante
    justificarRetraso(tabla_retraso);
    
    // Función para eliminar un registro de atraso
    deleteRegistroRetraso(tabla_retraso);

    // Función para generar y descargar documentos 
    exportarRetraso('#btn_excel', 'xlsx');
    exportarRetraso('#btn_csv', 'csv');

    
    // Btn para generar PDF  --ver si realmente es necesario !!!!
    $('#btn_pdf_atraso').click(function(e) { // En progreso...
        e.preventDefault();
        LibreriaFunciones.alertPopUp('warning', 'En mantenimiento !!');

        // // trabajando para probar fetch !!!!!!!
        // const data = new FormData();
        // data.append('nombre', 'juan manuel');

        // // let data = {nombre: 'juan manuel'};

        // fetch('http://localhost/update_atrasos/impresion/', {
        //     method: 'POST',
        //     body: data
        //     // body: JSON.stringify(data)
        // })
        // .then(response => response.json())
        // .then(data => {
        //     if (data == true) {
        //         console.log('impresion ejecutada');
        //     } else {
        //         console.log('impresión no ejecutada');
        //     }
        // });
    });

    validarRut();

});
