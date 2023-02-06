
import { spanish, LibreriaFunciones, generar_dv } from './librerias/librerias.js';

// ==================== FUNCIONES INTERNAS ===============================//

function camposVacios() {
    let contador = 0;
    let radio = false;

    if ($('#tipo_inasistencia').val() == 'SELECCIONAR TIPO') {
        contador = contador + 1;
    }

    $('#modal_form_inasistenciaF input').each(function() {
        if ($(this).val() == '') {
            contador = contador + 1
        }
    });

    $('input[name=reemplazo]').each(function() {
        if ($(this).is(':checked')) {
            radio = true
        }        
    });

    if (radio == false) {
        contador = contador + 1;
    }

    if ($('input[id=reemplazo_no]').is(':checked')) {
        contador = contador - 3;
    }

    return contador;
}

function format(d) {
    return (
        '<table class="expand_columna" cellpadding="4" cellspacing="2" border="0"">' +
            '<tr>' +
                '<td>NÚMERO DE ORDINARIO: </td>' +
                '<td>' + d.ordinario + '</td>' +
            '</tr>' +
            '<tr>' +
                '<td>FUNCIONARIO REEMPLAZANTE: </td>' +
                '<td>' + d.reemplazante + '</td>' +
            '</tr>' +
        '</table>'
    );
}

function prepararModalInasistencia(modal, titulo) {
    $('#form_inasistenciaF').trigger('reset');
    $('#titulo-modal_inasistenciaF').text(titulo);
    $('#inasistenciaF_rut').removeAttr('disabled', 'disabled');
    $('#inasistenciaF_rut_dv').attr('disabled', 'disabled');
    $('#inasistenciaF_reemplazo_rut_dv').attr('disabled', 'disabled');
    $('#btn_agregar_funcionario_ausente').attr('hidden', 'hidden');
    $('#btn_agregar_funcionario_reemplazo').attr('hidden', 'hidden');
    $('.section .check').attr('hidden', 'hidden');
    $('.reemplazo').addClass('section_hidden');
    $('#nombre_inasistenciaF').text('');
    $('#inasistenciaF_nombre_reemplazo').text('');
    cargarTipoInasistencia();

    modal.addClass('modal-show');
}

function medioDia() {
    $('#modal_form_inasistenciaF').change(function() {
        if (($('#tipo_inasistencia').val() != "1" && $('#tipo_inasistencia').val() != "SELECCIONAR TIPO") && ($('#inasistenciaF_dias').val() == "1" || $('#inasistenciaF_dias').val() == "0.5")) {
            $('.section .check').removeAttr('hidden', 'hidden');
            if ($('#check_medio_dia').prop('checked')) {
                $('#inasistenciaF_dias').val(0.5);
            } else {
                $('#inasistenciaF_dias').val(1);
            }    
        } else {
            $('.section .check').attr('hidden', 'hidden');
            $('#inasistenciaF_dias').val(LibreriaFunciones.restarFechas($('#inasistenciaF_fecha_inicio'), $('#inasistenciaF_fecha_termino')));
        }
    });
}

function validarBuscarRut() {
    $('#inasistenciaF_rut').keyup(function() {
        generar_dv('#inasistenciaF_rut', '#inasistenciaF_rut_dv');
        // LibreriaFunciones.buscar_info_funcionario($(this).val(), $('#nombre_inasistenciaF'), $('#btn_agregar_funcionario_ausente'));  // HABILITAR DESPUES
        LibreriaFunciones.buscar_info_funcionario($(this).val(), $('#nombre_inasistenciaF'));
    });

    $('#inasistenciaF_reemplazo_rut').keyup(function() {
        generar_dv('#inasistenciaF_reemplazo_rut', '#inasistenciaF_reemplazo_rut_dv');
        // LibreriaFunciones.buscar_info_funcionario($(this).val(), $('#inasistenciaF_nombre_reemplazo'), $('#btn_agregar_funcionario_reemplazo')); // HABILITAR DESPUES
        LibreriaFunciones.buscar_info_funcionario($(this).val(), $('#inasistenciaF_nombre_reemplazo'));
    });
}

function seccionReemplazo() {
    $('input[name=reemplazo]').click(function() {
        if ($(this).is(':checked') && $(this).attr('id') == 'reemplazo_si') {
            $('.reemplazo').removeClass('section_hidden');
        } else {
            $('.reemplazo').addClass('section_hidden');
        }
    });
}

function cargarTipoInasistencia() {
    let datos = 'getTipoInasistencia';

    $.ajax({
        url: "./controller/controller_inasistenciaF.php",
        method: "post", 
        dataType: "json",
        data: {datos: datos},
        success: function(data) {
            $('#tipo_inasistencia').html(data);
        }
    });
}

// ==================== FUNCIONES INTERNAS ===============================//

// MODULO CENTRAL =================== >>>>>>>>>
$(document).ready(function() { 
    // ASIGNAR VARIABLES 
    let modal = $('#modal_form_inasistenciaF'); 
    let modal_funcionario = $('#modal_form_funcionario'); 
    let datos = 'mostrar_inasistencias'; 
    let registrar; 
    let id_inasistencia;


    // LLENAR DATATABLE CON INFORMACIÓN =============================== LISTO
    let tabla_inasistencia = $('#inasistencias_funcionarios').DataTable({
        ajax: {
            url: "./controller/controller_inasistenciaF.php",
            method: "post",
            dateType: "json",
            data: {datos: datos}
        },
        columns: [
            {
                data: "id_inasistencia",
                visible: false
            },
            { // BTN PARA EXPANDIR Y CONTRAER TABLA
                orderable: false,
                data: "reemplazante",
                defaultContent: '',
                mRender: function(data) {
                    let btn;
                    if (data != null) {
                        btn = '<button class=" btn-s btn-expand" id="mostrar_reemplazante"><i class="fas fa-plus-circle"></i></button>';
                        return btn;
                    }
                }
            },
            {data: "funcionario"},
            {data: "tipo_inasistencia"},
            {data: "fecha_inicio"},
            {data: "fecha_termino"},
            {data: "dias_inasistencia"},
            {data: null,
                bSortable: false,
                defaultContent: // BOTONES
                                `<button class="btn btn-primary btn-s btn-data" id="btn_modificar_inasistencia" type="button"><i class="fas fa-pencil-alt"></i></button>
                                <button class="btn btn-danger btn-s btn-delete" id="btn_eliminar_inasistencia" type="button"><i class="fas fa-trash-alt"></i></button>`
            }
        ],
        order: [[4, 'asc']],
        language: spanish
    });


    // BTN PARA EXPANDIR Y CONTRAER SECCIÓN DE REEMPLAZANTE ====================== LISTO
    $('#inasistencias_funcionarios tbody').on('click', 'td button#mostrar_reemplazante', function () {
        let tr = $(this).closest('tr');
        let row = tabla_inasistencia.row(tr);
 
        if (row.child.isShown()) {
            // ACCIÓN PARA CUANDO SE CONTRAE LA TABLA
            row.child.hide();
            tr.removeClass('shown');
            $(this).addClass('btn-expand');
            $(this).removeClass('btn-retract');

        } else {
            // ACCIÓN PARA CUANDO SE EXPANDE LA TABLA
            row.child(format(row.data())).show();
            tr.addClass('shown');
            $(this).removeClass('btn-expand');
            $(this).addClass('btn-retract');
            $('.expand_columna').parents('td').css('background-color', 'var(--opacidad)');
        }
    });

    // BTN REGISTRAR DE MODAL ========================================= LISTO
    $('#btn_modal_registrar_insistenciaF').click(function(e) {
        e.preventDefault();

        // VALIDAR CAMPOS VACIOS
        if (camposVacios() >= 1) { 
            LibreriaFunciones.alertPopUp('warning', 'Hay campos vacios !!');
            return false;
        }

        // VALIDAR QUE LOS FUNCIONARIOS ESTEN INGRESADOS
        if ($('#nombre_inasistenciaF').text() == 'Apoderado sin registros !!' || $('#inasistenciaF_nombre_reemplazo').text() == 'Apoderado sin registros !!') {
            LibreriaFunciones.alertPopUp('warning', 'El rut no se encuentra registrado !!');
            return false;
        }

        // OBTENER DATOS PARA REGISTRAR
        let tipoI = $('#tipo_inasistencia').val();
        let rutF = $('#inasistenciaF_rut').val();
        let fechaI = $('#inasistenciaF_fecha_inicio').val();
        let fechaT = $('#inasistenciaF_fecha_termino').val();
        let diasI = $('#inasistenciaF_dias').val();
        let ord = $('#inasistenciaF_ordinario').val();
        let rutR = $('#inasistenciaF_reemplazo_rut').val();

        if (registrar == 'ingresar_inasistenciaF') {
            datos = "registrar_inasistencia";

            $.ajax({
                url: "./controller/controller_inasistenciaF.php",
                method: "post",
                dataType: "json",
                data: {datos: datos, tipoI: tipoI, rutF: rutF, fechaI: fechaI, fechaT: fechaT, diasI: diasI, ord: ord, rutR: rutR},
                success: function(data) {
                    if (data === false) {
                        LibreriaFunciones.alertPopUp('error', 'Error al registrar !!');
                    
                    } else {
                        LibreriaFunciones.alertPopUp('success', 'Inasistencia registrada !!');
                        modal.removeClass('modal-show');
                        tabla_inasistencia.ajax.reload(null, false);
                    }
                }
            });

        } else if(registrar == 'editar_inasistencia') {
            datos = "editar_inasistencia";

            $.ajax({
                url: "./controller/controller_inasistenciaF.php",
                method: "post",
                dataType: "json",
                data: {datos: datos, tipoI: tipoI, rutF: rutF, fechaI: fechaI, fechaT: fechaT, diasI: diasI, ord: ord, rutR: rutR, id_inasistencia: id_inasistencia},
                success: function(data) {
                    if (data == false) {
                        LibreriaFunciones.alertPopUp('info', 'Error al Actualizar registro !!');
                    } else {
                        LibreriaFunciones.alertPopUp('success', 'Registro actualizado !!');
                        modal.removeClass('modal-show');
                        tabla_inasistencia.ajax.reload(null, false);
                    }
                }
            });
        }
    });

    // BTN LANZAR MODAL DE NUEVA INSISTENCIA ========================== LISTO
    $('#btn_nueva_inasistencia').click(function(e) {
        e.preventDefault();
        prepararModalInasistencia(modal, 'Registrar nueva inasistencia');   // Prepara modal
        validarBuscarRut();                                                 // Validar rut y buscar información
        medioDia();                                                         // Habilitar check para marcar medio día
        seccionReemplazo();                                                 // Mostrar u ocultar sección de reemplazo

        
        // CALCULAR DÍAS DE INASISTENCIA 
        $('input[name=fecha]').change(function() {
            $('#inasistenciaF_dias').val(LibreriaFunciones.restarFechas($('#inasistenciaF_fecha_inicio'), $('#inasistenciaF_fecha_termino')));
        });

        registrar = "ingresar_inasistenciaF";
    });

    // BTN OCULTAR MODAL ============================================== LISTO
    $('#btn_modal_cancelar_inaistenciaF').click(function(e) {
        e.preventDefault();
        modal.removeClass('modal-show');
    });

    // BTN LANZAR MODAL PARA EDITAR INAISITENCIA ====================== LISTO
    $('#inasistencias_funcionarios tbody').on('click', '#btn_modificar_inasistencia', function() {
        let data = tabla_inasistencia.row($(this).parents()).data();
        id_inasistencia = data.id_inasistencia;

        prepararModalInasistencia(modal, 'Editar inasistencia');        // Prepara modal
        validarBuscarRut();                                             // Validar rut y buscar información
        medioDia();                                                     // Habilitar check para marcar medio día
        seccionReemplazo();                                             // Mostrar u ocultar sección de reemplazo

        // CALCULAR DÍAS DE INASISTENCIA 
        $('input[name=fecha]').change(function() {
            $('#inasistenciaF_dias').val(LibreriaFunciones.restarFechas($('#inasistenciaF_fecha_inicio'), $('#inasistenciaF_fecha_termino')));
        });

        datos = "getInasistencia"; 
        $.ajax({
            url: './controller/controller_inasistenciaF.php',
            method: 'post',
            dataType: 'json',
            data: {datos: datos, id_inasistencia: data.id_inasistencia},
            success: function (info) {
                // CARGAR INFORMACIÓN AL FORMULARIO
                $('#tipo_inasistencia').val(info[0].inasistencia);
                $('#inasistenciaF_rut').val(info[0].r_funcionario);
                $('#inasistenciaF_fecha_inicio').val(info[0].fecha_inicio);
                $('#inasistenciaF_fecha_termino').val(info[0].fecha_termino);
                $('#inasistenciaF_rut').attr('disabled', 'disabled');

                if (info[0].dias_inasistencia == '0.5') {
                    $('#inasistenciaF_dias').val(info[0].dias_inasistencia);
                    $('#check_medio_dia').prop('checked', true);
                    $('.section .check').removeAttr('hidden', 'hidden');
                } else {
                    $('#inasistenciaF_dias').val(info[0].dias_inasistencia);
                }

                generar_dv('#inasistenciaF_rut', '#inasistenciaF_rut_dv');
                LibreriaFunciones.buscar_info_funcionario($('#inasistenciaF_rut').val(), $('#nombre_inasistenciaF'), $('#btn_agregar_funcionario_ausente'));

                if (info[0].r_reemplazo != null) {
                    $('#reemplazo_si').prop("checked", true);
                    $('#inasistenciaF_ordinario').val(info[0].ordinario);
                    $('#inasistenciaF_reemplazo_rut').val(info[0].r_reemplazo);
                    $('.reemplazo').removeClass('section_hidden');
                    generar_dv('#inasistenciaF_reemplazo_rut', '#inasistenciaF_reemplazo_rut_dv');
                    LibreriaFunciones.buscar_info_funcionario($('#inasistenciaF_reemplazo_rut').val(), $('#inasistenciaF_nombre_reemplazo'), $('#btn_agregar_funcionario_reemplazo'));
                    
                } else {
                    $('#reemplazo_no').prop("checked", true);
                }

            }
        });

        registrar = 'editar_inasistencia';
    });

    // BTN LANZAR MODAL PARA ELIMINAR INAISITENCIA ==================== LISTO 
    $('#inasistencias_funcionarios tbody').on('click', '#btn_eliminar_inasistencia', function() {
        let data = tabla_inasistencia.row($(this).parents()).data();
        id_inasistencia = data.id_inasistencia;
        Swal.fire({
            icon: 'question',
            title: 'Se eliminará el registro de \n "' + data.funcionario + '"',
            showCancelButton: true,
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#2691d9',
            cancelButtonColor: '#adadad'
        }).then(resultado => {
            if (resultado.isConfirmed) {
                datos = "eliminar_inasistencia";

                $.ajax({
                    url: './controller/controller_inasistenciaF.php',
                    type: 'post',
                    dataType: 'json',
                    data: {datos: datos, id_inasistencia: id_inasistencia},
                    success: function(data) {
                        if (data == false) {
                            LibreriaFunciones.alertPopUp('error', 'No se pudo eliminar el registro !!');
                        } else {
                            LibreriaFunciones.alertPopUp('success', 'Registro eliminado !!');
                            tabla_inasistencia.ajax.reload(null, false);
                        }
                    }
                });
            }
        });
    });

    // BTN LANZAR MODAL DE FUNCIONARIO ================================ TRABAJAR !!
    // $('#btn_agregar_funcionario_ausente').click(function(e) {
    //     e.preventDefault();
    //     modal.removeClass('modal-show');
    //     modal_funcionario.addClass('modal-show');
    //     // AGREGAR VALIDACIONES NECESARIAS

    // });

    // BTN LANZAR MODAL DE FUNCIONARIO REEMPLAZANTE =================== TRABAJAR !!
    // $('#btn_agregar_funcionario_reemplazo').click(function(e) {
    //     e.preventDefault();
    //     modal.removeClass('modal-show');
    //     modal_funcionario.addClass('modal-show');
    //     // BLOQUEAR EL TIPO DE FORMULARIO EN DOCENTE REEMPLAZANTE
    // }); 

    // BTN PARA OCULTAR MODAL FUNCIONARIO ============================= TRABAJAR !!


});
