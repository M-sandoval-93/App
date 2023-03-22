import {LibreriaFunciones, generar_dv, spanish } from './librerias/librerias.js';
let datos = 'getEstudiantes';

// ==================== FUNCIONES INTERNAS ===============================//

// Función para maquetar datos adicionales del estudiante
function getInfoSecundaria(data) {
    let junaeb = 'SI';
    let sexo = 'Masculino';
    let fecha_retiro = data.fecha_retiro;

    if (data.junaeb != 1) {
        junaeb = 'NO';
    }

    if (data.sexo == 'F') {
        sexo = 'Femenino';
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
                '<td>Sexo estudiante:</td>' +
                '<td>' + sexo + '</td>' +
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
function cantidadEstudiante(contexto = false) {
    let datos = 'getCantidadEstudiante';

    $.ajax({
        url: "./controller/controller_estudiante.php",
        type: "post",
        dataType: "json",
        data: {datos: datos},
        success: (response) => {
            if (contexto == true) {
                $('#cantidad_nuevo_registro').text(response.cantidad_estudiante + 1);
                return false;
            }

            $('#cantidad_estudiante').text(response.cantidad_estudiante);
        }
    }).fail(() => {
        if (contexto == true) {
            $('#cantidad_nuevo_registro').text('Error !!');
            return false;
        }

        $('#cantidad_estudiante').text('Error !!');
    });
}

// Función para mostrar los datos adicionales del estudiante
function expandInfoSecundaria(tabla) {
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

 // Función para generar acción al terminar algún proceso de edición de datos
function beforeRecord(tabla) {
    tabla.ajax.reload(null, false);
    cantidadEstudiante();
}

// Función para validar el rut ingresado 
function validarRutEstudiante() {
    $('#rut_estudiante').keyup(function(e) {
        e.preventDefault();
        generar_dv('#rut_estudiante', '#dv_rut_estudiante');
        comprobarEstudiante($('#rut_estudiante').val());

        LibreriaFunciones.validarNumberRut($('#rut_estudiante'), $('#informacion_rut'), 'Rut sin puntos, sin guión y sin dígito verificador');  
    });
}

// Función para comprobar si el rut ingresado ya existe en la bbdd
function comprobarEstudiante(rut) {
    datos = 'getEstudiante';

    if (rut != '' && rut.length > 7 && rut.length < 9) {
        $.ajax({
            url: "./controller/controller_estudiante.php",
            type: "post",
            dataType: "json",
            cache: false,
            data: {datos: datos, rut: rut, tipo: 'existe'},
            success: function(data) {
                if (data == true) {
                    LibreriaFunciones.alertPopUp('warning', 'El rut ingresado ya existe en la base de datos de los estudiantes'); 
                }
            }
        }).fail(() => {
            LibreriaFunciones.alertPopUp('error', 'Error en la consulta !!');
        });
    } 
}

// Función que obtiene los datos del formulario modal de estudiante
function getDataFormulario() {
    const estudiante = {
        rut: $.trim($('#rut_estudiante').val()),
        dv_rut: $.trim($('#dv_rut_estudiante').val().toUpperCase()),
        nombres: $.trim($('#nombre_estudiante').val().toUpperCase()),
        ap: $.trim($('#ap_estudiante').val().toUpperCase()),
        am: $.trim($('#am_estudiante').val().toUpperCase()),
        n_social: $.trim($('#n_social_estudiante').val().toUpperCase()), 
        sexo: $.trim($('#sexo_estudiante').val()),
        f_nacimiento: $.trim($('#fecha_nacimiento').val()),
        f_ingreso: $.trim($('#fecha_ingreso_estudiante').val()),
        junaeb: $.trim($('#beneficio_junaeb').val())
    }
    return estudiante;
}

// Función para comprobar los campos vacios del formulario
function comprobarCamposVacios(exclusion, objeto) {
    let count = 0;
    for (const [key, value] of Object.entries(objeto)) {
        if (key != exclusion && value == '') {
            count += 1;
        }
    }
    return count;
}



// ================== FUNCÓN PARA TRABAJAR CON MODALES ================== //
// Función generica para restablecer el formato del modal
function prepararModal() {
    $('#form_registro_estudiante').trigger('reset');
    $('#rut_estudiante').removeClass('is-invalid');
    $('#informacion_rut').removeClass('text-danger');
    $('#informacion_rut').text('Rut sin puntos, sin guión y sin dígito verificador');
    $('#informacion_rut').addClass('form-text');
    LibreriaFunciones.autoFocus($('#modal_estudiante'), $('#rut_estudiante'));
    validarRutEstudiante();
}

// Función para lanzar el modal de nuevo estudiante
function lanzarModalNuevoEstudiante() {
    $('#btn_nuevo_estudiante').click(() => {
        prepararModal();
        $('#modal_estudiante_tittle').text('REGISTRAR ESTUDIANTE');
        $('#btn_registrar_estudiante').text('Registrar');
        $('#texto_secundario').text('Nuevo registro N°');
        $('#fecha_ingreso_estudiante').val(LibreriaFunciones.getFecha());
        cantidadEstudiante(true);
    });
}

// Función para lanzar el modal empleado para actualizar un estudiante
function lanzarModalupdateEstudiante(tabla) {
    $('#tabla_estudiante tbody').on('click', '#btn_editar_estudiante', function() {
        let data = tabla.row($(this).parents()).data();
        prepararModal();

        // Trabajar datos principales para asignar al formulario
        let rut = data.rut_estudiante.slice(0, data.rut_estudiante.length - 2);
        let n_social = '';
        let nombres = data.nombres_estudiante;

        if (nombres.includes('(')) {
            n_social = data.nombres_estudiante.match(/\((.*)\)/).pop();
            nombres = nombres.replace(/\s*\(.*?\)\s*/g, '');   
        }

        // Asignación del contenido
        $('#modal_estudiante_tittle').text('UPDATE ESTUDIANTE');
        $('#btn_registrar_estudiante').text('Actualizar');
        $('#texto_secundario').text('ID del estudiante N°');
        $('#cantidad_nuevo_registro').text(data.id_estudiante);

        // Asignación de valores
        $('#rut_estudiante').val(rut);
        generar_dv('#rut_estudiante', '#dv_rut_estudiante');
        $('#nombre_estudiante').val(nombres.toUpperCase());
        $('#ap_estudiante').val(data.ap_estudiante.toUpperCase());
        $('#am_estudiante').val(data.am_estudiante.toUpperCase());
        $('#n_social_estudiante').val(n_social.toUpperCase()); // Valor que puede ser nulo !!
        $('#sexo_estudiante').val(data.sexo);
        $('#fecha_nacimiento').val(LibreriaFunciones.textFecha(data.fecha_nacimiento));
        $('#fecha_ingreso_estudiante').val(LibreriaFunciones.textFecha(data.fecha_ingreso));
        $('#beneficio_junaeb').val(data.junaeb);

    });
}



// ================== MANEJO DE INFORMARCIÓN ================== //
// Función para registrar un nuevo estudiante 
function setEstudiante(tabla) {
    $('#btn_registrar_estudiante').click((e) => {
        e.preventDefault();
        if ($('#modal_estudiante_tittle').text() != 'REGISTRAR ESTUDIANTE') { return false; }
        if (LibreriaFunciones.comprobarLongitud($('#rut_estudiante').val(), 7, 9, 'RUT', 'Estudiante') == false) { return false; }

        datos = 'setEstudiante';
        const estudiante = getDataFormulario();
        if (comprobarCamposVacios('n_social', estudiante) >= 1) {
            LibreriaFunciones.alertPopUp('info', 'Faltan datos importantes !!');
            return false;
        }

        $.ajax({
            url: "./controller/controller_estudiante.php",
            type: "post",
            dataType: "json",
            data: {datos: datos, estudiante: estudiante},
            success: (response) => {
                if (response == 'existe') {
                    LibreriaFunciones.alertPopUp('warning', 'El rut ingresado ya existe en la base de datos de los estudiantes');
                    return false;
                }

                if (response == true) {
                    LibreriaFunciones.alertPopUp('success', 'Estudiante registrado !!');
                    $('#modal_estudiante').modal('hide');
                    beforeRecord(tabla);
                    return false;
                }

                LibreriaFunciones.alertPopUp('warning', 'No se registro el estudiante !!');
            }
        }).fail(() => {
            LibreriaFunciones.alertPopUp('error', 'Error en la consulta !!');
        });
    });
}

// Función para actualizar un registro estudiante
function updateEstudiante(tabla) {
    $('#btn_registrar_estudiante').click((e) => {
        e.preventDefault();
        if ($('#modal_estudiante_tittle').text() != 'UPDATE ESTUDIANTE') {
            return false;
        }

        datos = 'updateEstudiante';
        let estudiante = getDataFormulario();
        if (comprobarCamposVacios('n_social', estudiante) >= 1) {
            LibreriaFunciones.alertPopUp('info', 'Faltan datos importantes !!');
            return false;
        }
        estudiante.id_estudiante = $('#cantidad_nuevo_registro').text(); // Se agrega una nueva propiedad al objeto con el id de estudiante

        $.ajax({
            url: "./controller/controller_estudiante.php",
            type: "post",
            dataType: "json",
            data: {datos: datos, estudiante: estudiante},
            success: (response) => {
                if (response == true) {
                    LibreriaFunciones.alertPopUp('success', 'Estudiante actualizado !!');
                    $('#modal_estudiante').modal('hide');
                    beforeRecord(tabla);
                    return false;
                }

                LibreriaFunciones.alertPopUp('warning', 'No se actualizó el estudiante !!');
            }
        }).fail(() => {
            LibreriaFunciones.alertPopUp('error', 'Error en la consulta !!');
        });



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
                    success: (response) => {
                        if (response == true) {
                            // LibreriaFunciones.alertPopUp('success', 'Registro eliminado !!');
                            LibreriaFunciones.alertToast('success', 'Registro eliminado !!');
                            beforeRecord(tabla);
                            return false;
                        }

                        LibreriaFunciones.alertPopUpButton('error', 'No se puede eliminar el registro, por la integridad de los datos !!');
                    }
                }).fail(() => {
                    LibreriaFunciones.alertPopUp('error', 'Error en la operación  !!');
                });
            }
        });
    });
}

// Función para generar documento
function exportarEstudiantes(btn, ext) { // Terminado y revisado !!
    let datos = 'exportarEstudiantes';

    $(btn).click((e) => {
        e.preventDefault();

        $.ajax({
            url: "./controller/controller_estudiante.php",
            type: "post",
            dataType: "html",
            cache: false,
            data: {datos: datos, ext: ext},
            success: (data) => {
                let opResult = JSON.parse(data);
                let $a = $("<a>");
    
                $a.attr("href", opResult.data);
                $("body").append($a);
                $a.attr("download", "Registro estudiante." + ext);
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
                    if (data == 'Matriculado') { estilo = 'bg-success'; }
                    if (data == 'No matriculado') { estilo = 'bg-danger'; }
                    if (data == 'Año anterior') { estilo = 'bg-secondary'; }
                    return '<p class="text-center text-white rounded-3 mb-0 py-1 ' + estilo + '">' + data + '</p>';
                }
            },
            {
                data: null,
                bSortable: false,
                defaultContent: `<button class="btn btn-primary btn-data" id="btn_editar_estudiante" type="button" data-bs-toggle="modal" data-bs-target="#modal_estudiante"><i class="fas fa-pencil-alt"></i></button>
                                <button class="btn btn-danger btn-delete" id="btn_eliminar_estudiante" type="button"><i class="fas fa-trash-alt"></i></button>`
            }
        ],
        order: [[3, 'asc']],
        language: spanish
    });

    expandInfoSecundaria(tabla_estudiante);

    lanzarModalNuevoEstudiante();
    lanzarModalupdateEstudiante(tabla_estudiante);
    setEstudiante(tabla_estudiante);
    updateEstudiante(tabla_estudiante);
    deleteRegistroEstudiante(tabla_estudiante);

    exportarEstudiantes('#btn_excel', 'xlsx');
    exportarEstudiantes('#btn_csv', 'csv');

});
