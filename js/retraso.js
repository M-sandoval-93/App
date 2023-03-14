import { spanish, LibreriaFunciones, generar_dv } from './librerias/librerias.js';
let datos = 'getRetraso'; 

// ==================== FUNCIONES INTERNAS ===============================//
// obtener atrasos diarios y anuales
function getCantidadRetraso() {
    let datos = 'getCantidadRetraso';

    $.ajax({
        url: "./controller/controller_retraso.php",
        type: "post",
        dataType: "json",
        data: {datos: datos},
        success: (data) => {
            $('#retraso_diario').text(data.cantidad_diaria);
            $('#retraso_total').text(data.cantidad_anual);
        }
    }).fail(() => {
        $('#retraso_diario').text('Error !!');
        $('#retraso_total').text('Error !!');
    });
}

// Función para obtener los apoderados del estudiante
function getApoderado(rut, campo) {
    let datos = 'getApoderado';

    $.ajax({
        url: "./controller/controller_apoderado.php",
        type: "post",
        dataType: "json",
        data: {datos: datos, rut: rut, tipo: 'retraso'},
        success: (response) => {
            $(campo).html(response);
        }
    });
}

// traer información del estudiante al ingresar nuevo atraso
function getEstudiante(rut, input_nombre, input_curso) {
    let datos = 'getEstudianteRetraso';

    if (rut != '' && rut.length > 7 && rut.length < 9) {
        $.ajax({
            url: "./controller/controller_estudiante.php",
            type: "post",
            dataType: "json",
            cache: false,
            data: {datos: datos, rut: rut},
            success: (response) => {
                if (response.length > 0) {
                    input_nombre.val(response[0].nombre_estudiante);
                    input_curso.val(response[0].curso);

                    if (response[0].cantidad_retraso >= 1) {
                        $('#alerta_retraso_cantidad').text('Retrasos sin justificar: ' + response[0].cantidad_retraso);
                        $('#alerta_retraso_cantidad').show();
                    }

                    if (response[0].id_estado == 5) {
                        $('#btn_registrar_retraso').prop('disabled', true);
                        $('#alerta_suspencion_activa').text('Estudiante suspendido !!!');
                        $('#alerta_suspencion_activa').show();
                    }

                    if (response[0].id_estado == 4) {
                        $('#btn_registrar_retraso').prop('disabled', true);
                        $('#alerta_suspencion_activa').text('Estudiante retirado !!!');
                        $('#alerta_suspencion_activa').show();
                    }
                    return false;
                }

                input_nombre.val('Sin datos');
                input_curso.val('N/A');
            }
        }).fail(() => {
            LibreriaFunciones.alertPopUp('error', 'Error en la consulta !!');
        });
    } else {
        input_nombre.val('');
        input_curso.val('');
        $('#alerta_retraso_cantidad').hide();
        $('#alerta_suspencion_activa').hide();
        $('#btn_registrar_retraso').prop('disabled', false);
    }
}

// Obtener la información de todos los retrasos sin justificar de un estudiante
function getRetrasoSinJustificar(rut) {
    let datos = 'getRetrasoSinJustificar';
    $('#retraso_sinJustificar').DataTable().destroy();

    $('#retraso_sinJustificar').DataTable({
        searching: false,
        info: false,
        lengthChange: false,
        iDisplayLength: 5,
        ajax: {
            url: "./controller/controller_retraso.php",
            type: "post",
            dateType: "json",	
            data: {datos: datos, rut: rut}
        },
        columns: [
            {data: "fecha_retraso", className: 'text-center'},
            {data: "hora_retraso", className: 'text-center'},
            {data: "id_retraso", className: 'text-center'}
        ],
        columnDefs: [
            {
                target: 2,
                checkboxes: {selectRow: true}
            }
        ],
        select: {style: 'multi'},
        order: ([]), // para quitar el orden automatico que incluye datatable
        language: spanish
    });
}

// Función para validar el rut y obtener los datos del estudiante
function validarRutEstudiante() {
    $('#rut_estudiante_retraso').keyup(function(e) {
        e.preventDefault();
        generar_dv('#rut_estudiante_retraso', '#dv_rut_estudiante_retraso');
        getEstudiante($('#rut_estudiante_retraso').val(), $('#nombre_estudiante_retraso'), $('#curso_estudiante_retraso'));

        LibreriaFunciones.validarNumberRut($('#rut_estudiante_retraso'), $('#informacion_rut'), 'Rut sin puntos, sin guión y sin dígito verificador');  
    });
}

// función para cuando se almacena un registro y se recargan los datos necesarios
function beforeRegistro(tabla, retraso = true) {
    tabla.ajax.reload(null, false);
    getCantidadRetraso();
    if (retraso == true) { prepararModalRetraso(); }
}



// SECCIÓN FUNCIONES DE IMPRESIÓN PARA PUNTO DE TICKET
function getImpresoras(data) {
    connetor_plugin.obtenerImpresoras()
        .then(impresoras => {
            console.log(impresoras);
        })
}

async function imprimir() {
    let nombreImpresora = "Printer";
    let api_key = "123456";
    const conector = new connetor_plugin();

    // conector.fontsize("1");
    // conector.textaling("center");
    // conector.text("LICEO BICENTENARIO");
    // conector.text("VALENTIN LETELIER MADARIAGA");
    // conector.feed("2");
    // conector.fontsize("2");
    // conector.text("Ticket de Entrada");
    // conector.fontsize("1");
    // conector.text("--------------------------------------------");

    conector.fontsize("1")
    conector.textaling("center")
    conector.text("LICEO BICENTENARIO")
    conector.text("VALENTIN LETELIER MADARIAGA")
    conector.feed("2")
    conector.fontsize("2")
    conector.text("Ticket de Entrada")
    conector.fontsize("1")
    conector.text("--------------------------------------------")

    conector.textaling("left")

    // conector.text("Nombre: <?php echo $nombre_alumno; ?>")
    // conector.text("Curso: <?php echo $curso_alumno; ?>   /   Hora Ingreso: <?php echo $hora_atraso; ?>")
    // conector.text("Fecha Ingreso: <?php echo $fecha_atraso; ?>")
    conector.feed("1")

    conector.textaling("center")
    conector.text("--------------------------------------------")
    conector.feed("1")
    conector.fontsize("2")
    conector.text("Registro N°: " + data.id_retraso)

    conector.feed("5")
    conector.cut("0") 

    // agregar contenido de la impresión

    // Antes de la promesa!!
    // console.log("conector");
    const resp = await conector.imprimir(nombreImpresora, api_key);
    if (resp === true) {
        // LibreriaFunciones.alertPopUp('Impresipón efectuada con éxito');
    } else {
        LibreriaFunciones.alertPopUp('warning', 'No se pudo imprimir el ticket !!');
    }
}



// ================== FUNCÓN PARA TRABAJAR CON MODALES ================== //
// Función para preparar el modal de retraso
function prepararModalRetraso() {
    let fecha_hora_actual = new Date();
    
    $('#form_registro_retraso').trigger('reset');
    $('#staticFecha').val(fecha_hora_actual.toLocaleDateString());
    $('#staticHora').val(fecha_hora_actual.toLocaleTimeString());
    $('#btn_registrar_retraso').prop('disabled', false);
    $('#alerta_retraso_cantidad').hide();
    $('#alerta_suspencion_activa').hide();
    $('#rut_estudiante_retraso').removeClass('is-invalid');
    $('#informacion_rut').removeClass('text-danger');
    $('#informacion_rut').text('Rut sin puntos, sin guión y sin dígito verificador');
    $('#informacion_rut').addClass('form-text');
    LibreriaFunciones.autoFocus($('#modal_registro_retraso'), $('#rut_estudiante_retraso'));
    validarRutEstudiante();
}

// Función para lanzar el modal de ingreso de retraso
function lanzarModalRetraso() {
    $('#btn_nuevo_retraso').click(() => {
        prepararModalRetraso();
    });
}

// funcion que prepara el modal de justificación de atrasos
function prepararModalJustificar(data) {
    $('#modal_justificar_retraso').modal('show');
    $('#rut_estudiante_justifica').val(data.rut);
    $('#curso_estudiante_justifica').val(data.curso);
    $('#nombre_estudiante_justifica').val(data.nombre + ' ' + data.ap_paterno + ' ' + data.ap_materno);
}

// Función para lanzar el modar para justificar retrasos
function lanzarModalJustificaciones(tabla) {
    $('#tabla_retraso tbody').on('click', '#btn_justificar_retraso', function() {
        let data = tabla.row($(this).parents()).data();
        let rut = data.rut.slice(0, -2);
        
        prepararModalJustificar(data);
        getApoderado(rut, '#apoderado_justifica');
        getRetrasoSinJustificar(rut);
    });
}



// ================== MANEJO DE INFORMARCIÓN ================== //
// Función para registrar un retraso
function setRetraso(tabla) {
    $('#btn_registrar_retraso').click((e) => {
        e.preventDefault();
        datos = 'setRetraso';
        let rut;

        
        if (LibreriaFunciones.comprobarLongitud($('#rut_estudiante_retraso').val(), 7, 9, 'RUT', 'Estudiante') == false) { return false; }
        if ($('#rut_estudiante_retraso').val() == '' || $('#nombre_estudiante_retraso').val() == 'Sin datos' || $('#nombre_estudiante_retraso').val() == '') {
            LibreriaFunciones.alertPopUp('info', 'Los datos ingresados no son correctos !!');
            return false;
        } 

        rut = $.trim($('#rut_estudiante_retraso').val());

        $.ajax({
            url: "./controller/controller_retraso.php",
            type: "post",
            dataType: "json",
            data: {datos: datos, rut: rut },
            success: (response) => {
                if (response.response == true) {
                    // LibreriaFunciones.alertPopUp('success', 'Retraso N° ' + response.id_retraso + ' registrado !!');
                    Swal.fire({
                        icon: 'success',
                        title: 'Retraso N° ' + response.id_retraso + ' registrado !!',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // console.log("se genera una acción secundaria");
                            imprimir(response);
                        }
                    });
                    beforeRegistro(tabla);
                    return false;
                }

                LibreriaFunciones.alertPopUp('error', 'Retraso no registrado !!');
            }
        }).fail(() => {
            LibreriaFunciones.alertPopUp('error', 'Error en la ejecución !!');
        });


    });
}

// Función para justificar un retraso
function setJustificarRetraso(tabla) {
    $('#btn_justificar_retraso').click(function(e) {
        e.preventDefault();
        let row_selected = $('#retraso_sinJustificar').DataTable().column(2).checkboxes.selected();
        let retrasos = [];
        let id_apoderado = $('#apoderado_justifica').val();
        datos = 'setJustificarRetraso';

        if ($('#apoderado_justifica').val() == 'Sin apoderados !!' || $('#apoderado_justifica').val() == 'Seleccionar apoderado') {
            LibreriaFunciones.alertPopUp('warning', 'Seleccionar apoderado !!');
            return false;
        }

        if (row_selected.length < 1) {
            LibreriaFunciones.alertPopUp('warning', 'Seleccionar retraso !!');
            return false;
        }

        $.each(row_selected, function(index, rowId) {
            retrasos.push(rowId);
        });

        $.ajax({
            url: "./controller/controller_retraso.php",
            type: "post",
            dataType: "json",
            cache: false,
            data: {datos: datos, id_apoderado: id_apoderado, retrasos: retrasos},
            success: (response) => {
                if (response == true) {
                    LibreriaFunciones.alertPopUp('success', 'Retrasos justificados !!');
                    tabla.ajax.reload(null, false);
                    $('#modal_justificar_retraso').modal('hide');
                    return false;
                }

                LibreriaFunciones.alertPopUp('warning', 'Retrasos no justificados !!');
            }
        }).fail(() => {
            LibreriaFunciones.alertPopUp('error', 'Error en la operación !!');
        });    
    });
}

// Función para eliminar registro atraso
function deleteRegistroRetraso(tabla) {
    $('#tabla_retraso tbody').on('click', '#btn_eliminar_retraso', function() {
        let data = tabla.row($(this).parents()).data();
        let id_retraso = data.id_retraso;

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
                    success: function(response) {
                        if (response == true) {
                            // LibreriaFunciones.alertPopUp('success', 'Registro eliminado !!');
                            LibreriaFunciones.alertToast('success', 'Registro eliminado !!');
                            beforeRegistro(tabla);
                            return false;
                        }

                        // LibreriaFunciones.alertPopUp('error', 'Registro no eliminado !!');
                        LibreriaFunciones.alertToast('error', 'Registro no eliminado !!');
                    }
                }).fail(() => {
                    LibreriaFunciones.alertPopUp('error', 'Error en la operación !!');
                });
            }
        });
    });
}

// función para generar documento
function exportarRetraso(btn, ext) {
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



// ==================== FUNCIONES INTERNAS ===============================//
$(document).ready(function() {
    getCantidadRetraso();

    // LLENAR DATATABLE CON INFORMACIÓN =============================== 
    let tabla_retraso = $('#tabla_retraso').DataTable({
        ajax: {
            url: "./controller/controller_retraso.php",
            type: "post",
            dataType: "json",
            data: {datos: datos}
        },
        columns: [
            {   
                data: "id_retraso",
                visible: false,
                // searchable: false
            },
            {data: "rut"},
            {data: "ap_paterno"},
            {data: "ap_materno"},
            {data: "nombre"},
            {data: "curso"},
            {data: "fecha_retraso"},
            {data: "hora_retraso"},
            {
                data: null,
                defaultContent: `<button class="btn btn-primary btn-justify" id="btn_justificar_retraso" title="Justificar retraso" type="button"><i class="fas fa-user-check"></i></button>
                                <button class="btn btn-danger btn-delete" id="btn_eliminar_retraso" title="Eliminar retraso" type="button"><i class="fas fa-trash-alt"></i></button>`,
                className: 'text-center'
            }
        ],
        order: ([]), // para quitar el orden automatico que incluye datatable
        language: spanish
    });

    lanzarModalRetraso();
    setRetraso(tabla_retraso);
    lanzarModalJustificaciones(tabla_retraso);
    setJustificarRetraso(tabla_retraso);
    deleteRegistroRetraso(tabla_retraso);

    exportarRetraso('#btn_excel', 'xlsx');
    exportarRetraso('#btn_csv', 'csv');

    
    // // Btn para generar PDF  --ver si realmente es necesario !!!!
    // $('#btn_pdf').click(function(e) { // En progreso...
    //     e.preventDefault();
    //     LibreriaFunciones.alertPopUp('warning', 'En mantenimiento !!');

    //     // // trabajando para probar fetch !!!!!!!
    //     // const data = new FormData();
    //     // data.append('nombre', 'juan manuel');

    //     // // let data = {nombre: 'juan manuel'};

    //     // fetch('http://localhost/update_atrasos/impresion/', {
    //     //     method: 'POST',
    //     //     body: data
    //     //     // body: JSON.stringify(data)
    //     // })
    //     // .then(response => response.json())
    //     // .then(data => {
    //     //     if (data == true) {
    //     //         console.log('impresion ejecutada');
    //     //     } else {
    //     //         console.log('impresión no ejecutada');
    //     //     }
    //     // });
    // });


});
