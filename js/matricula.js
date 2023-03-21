import {LibreriaFunciones, generar_dv, spanish } from './librerias/librerias.js';
let datos = 'getMatricula';
let infoRetiro;
let infoSuspension;

// ==================== FUNCIONES INTERNAS ===============================//
// Función para generar tabla expansiba con datos secundarios
function getData(data) {
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
                '<td>' + data.sexo + '</td>' +
            '</tr>' +

            '<tr>' +
                '<td>Fecha matrícula:</td>' +
                '<td>' + data.fecha_matricula + '</td>' +
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

// Función para expandir información secundaria // PASAR A FUNCION GENERICA
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

// Función para obtener cantidad de registros
function getCantidadMatricula() {
    let datos = 'getCantidadMatricula';

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

// Función para obtener el siguiente numero de matrícula a ser asignado
function getNumeroMatricula(inicial, final) {
    datos = 'getNumeroMatricula';

    $.ajax({
        url: "./controller/controller_matricula.php",
        type: "post",
        dataType: "json",
        cache: false,
        data: {datos: datos, inicial: inicial, final: final},
        success: function(data) {
            $('#numero_matricula').val(data);
            $('#numero_matricula').prop('disabled', false);
        }

    }).fail(() => {
        $('#numero_matricula').val('Sin datos !!');
    });



    // $('#numero_matricula').val();
}

// Función para validar el rut del estudiante ingresado para matricular
function validarRutEstudianteMatricula(campo_rut, campo_dv_rut) {
    $(campo_rut).keyup(function(e) {
        e.preventDefault();
        generar_dv(campo_rut, campo_dv_rut);
        getEstudiante($(campo_rut).val());
        LibreriaFunciones.validarNumberRut($(campo_rut), $('#informacion_rut'), 'Rut sin puntos, sin guión y sin dígito verificador');
        comprobarEstudianteMatricula($(campo_rut).val());
    });
}

// Función para validar el rut del apoderado
function validarRutApoderadoMatricula(campo_rut, campo_dv_rut, campo_nombre, texto) {
    $(campo_rut).keyup(function(e) {
        e.preventDefault();
        generar_dv(campo_rut, campo_dv_rut);
        getApoderado($(campo_rut).val(), campo_nombre, texto);
        LibreriaFunciones.validarNumberRut($(campo_rut), $(campo_nombre), texto);
    });
}

// Función para validar el rut del apoderado, cuando este se carga para actualizar
function preValidarRutApoderadoMatricula(campo_rut, campo_dv_rut, campo_nombre, texto) {
    generar_dv(campo_rut, campo_dv_rut);
    getApoderado($(campo_rut).val(), campo_nombre, texto);
}

// Función para comprobar si el estudiante ya se encuentra matriculado
function comprobarEstudianteMatricula(rut) {
    datos = 'getEstudiante';

    if (rut != '' && rut.length > 7 && rut.length < 9) {
        $.ajax({
            url: "./controller/controller_estudiante.php",
            type: "post",
            dataType: "json",
            cache: false,
            data: {datos: datos, rut: rut, tipo: 'existeMatricula'},
            success: function(data) {
                if (data == true) {
                    LibreriaFunciones.alertPopUp('warning', 'El rut ingresado ya se encuentra matriculado !!');
                }
            }
        }).fail(() => {
            LibreriaFunciones.alertPopUp('error', 'Error en la consulta !!');
        });
    } 
}

// Función para obtener el nombre del estudiante registrado
function getEstudiante(rut) {
    datos = 'getEstudiante';

    if (rut != '' && rut.length > 7 && rut.length < 9) {
        $.ajax({
            url: "./controller/controller_estudiante.php",
            type: "post",
            dataType: "json",
            cache: false,
            data: {datos: datos, rut: rut, tipo: 'matricula'},
            success: function(data) {
                let info = 'Sin datos para el rut ingresado !!';
                if (data != null) { info = data; }   
                $('#nombre_estudiante_matricula').val(info);
            }
        }).fail(() => {
            LibreriaFunciones.alertPopUp('error', 'Error en la consulta !!');
        });
    } else {
        $('#nombre_estudiante_matricula').val('');
    }
}

// Función para obtener el nombre del apoderado registrado
function getApoderado(rut, campo_nombre, texto) {
    datos = 'getApoderado';

    if (rut != '' && rut.length >= 7 && rut.length <= 9) {
        $.ajax({
            url: "./controller/controller_apoderado.php",
            type: "post",
            dataType: "json",
            cache: false,
            data: {datos: datos, rut: rut, tipo: 'matricula'},
            success: function(data) {
                let info = 'Sin datos para el rut ingresado !!';
                let value = '0';
                if (data.length > 0) { 
                    info = data[0].nombre_apoderado; 
                    value = data[0].id_apoderado; 
                }

                $(campo_nombre).text(info);
                $(campo_nombre).val(value);
            }
        }).fail(() => {
            LibreriaFunciones.alertPopUp('error', 'Error en la consulta !!');
        });
    } else {
        $(campo_nombre).text(texto);
        $(campo_nombre).val('0');
    }
}

// Función para cargar los cursos de un grado y la proxima matricula de un grado
function loadLetra() {
    $('#grado_curso').change(function() {
        let grado = $(this).val();
        if (grado >= 7 && grado <=8) { getNumeroMatricula(7, 8); }
        if (grado >= 1 && grado <=4) { getNumeroMatricula(1, 4); }
        
        datos = 'loadLetra';
        
        if (grado == '-------') {
            $('#letra_curso').html('<option selected> ------- </option>');
            $('#numero_matricula').val('');
            $('#numero_matricula').prop('disabled', true);
        } else {
            $.ajax({
                url: "./controller/controller_curso.php",
                type: "post",
                dataType: "json",
                cache: false,
                data: {datos: datos, grado: grado},
                success: function(data) {
                    $('#letra_curso').html(data);
                }
            }).fail(() => {
                $('#letra_curso').html('sin datos !!');
            });
        }
    });
}

// Función para cargar los cursos de un grado
function preLoadLetra(letra) {
    let grado = $('#grado_curso').val();
    datos = 'loadLetra';

    if (grado == '-------') {
        $('#letra_curso').html('<option selected> ------- </option>');
    } else {
        $.ajax({
            url: "./controller/controller_curso.php",
            type: "post",
            dataType: "json",
            cache: false,
            data: {datos: datos, grado: grado},
            success: function(data) {
                $('#letra_curso').html(data);
                $('#letra_curso').val($('#letra_curso option:contains("' + letra + '")').val());
            }
        }).fail(() => {
            $('#letra_curso').html('sin datos !!');
        });
    }
}

// Función para comprobar los campos vacios del formulario y rut del estudiante ingresado
function comprobarCamposVacios( objeto) {
    let count = 0;
    for (const [key, value] of Object.entries(objeto)) {
        if ((key != 'id_titular' && key != 'id_suplente' && value == '' || value == '-------')) {
            count += 1;
        }
    }

    // Condición para comprobar que el rut ingresado se encuentra en la base de datos estudiante
    if ($('#nombre_estudiante_matricula').val() == 'Sin datos para el rut ingresado !!') {
        count +=1;
    }

    return count;
}

// Función que obtiene los datos del formulario modal de estudiante
function getDataFormulario() {
    const matricula = {
        matricula: $.trim($('#numero_matricula').val()),
        fecha_matricula: $.trim($('#fecha_matricula').val()),
        rut: $.trim($('#rut_estudiante_matricula').val()),
        id_curso: $.trim($('#letra_curso').val().toUpperCase()),
        id_titular: $('#informacion_titular').val(),
        id_suplente: $('#informacion_suplente').val(),
        grado: $('#grado_curso').val()
    }

    return matricula;
}

// Función para ejecutar una accion al terminar un proceso de registro
function beforeRegistroMatricula(tabla, modal) {
    tabla.ajax.reload(null, false);
    $(modal).modal('hide');
    getCantidadMatricula();
}

// Función para obtener los apoderados de un registro de matricula
function getApoderadosTS(id_matricula) {
    datos = "getApoderadoTS";

    $.ajax({
        url: "./controller/controller_matricula.php",
        type: "post",
        dataType: "json",
        cache: false,
        data: {datos: datos, id_matricula: id_matricula},
        success: function(data) {
            $('#rut_ap_titular').val((data[0].rut_titular == null) ? '' : data[0].rut_titular);
            $('#rut_ap_suplente').val((data[0].rut_suplente == null) ? '' : data[0].rut_suplente);

            preValidarRutApoderadoMatricula('#rut_ap_titular', '#dv_rut_ap_titular', '#informacion_titular', 'Asignar apoderado titular');
            preValidarRutApoderadoMatricula('#rut_ap_suplente', '#dv_rut_ap_suplente', '#informacion_suplente', 'Asignar apoderado suplente');
        }
    }).fail(() => {
        LibreriaFunciones.alertPopUp('error', 'Error al consultar los apoderados');
    });

}



// ================== FUNCÓN PARA TRABAJAR CON MODALES ================== //
// Función para preparar el modal de matrícula
function prepararModalMatricula(modal) {
    $('#form_registro_matricula').trigger('reset');
    $('#numero_matricula').prop('disabled', true);
    
    $('#rut_estudiante_matricula').prop('disabled', false);
    $('#rut_estudiante_matricula').removeClass('is-invalid');
    $('#rut_ap_titular').removeClass('is-invalid');
    $('#rut_ap_suplente').removeClass('is-invalid');

    $('#informacion_rut').removeClass('text-danger');
    $('#informacion_titular').removeClass('text-danger');
    $('#informacion_suplente').removeClass('text-danger');

    $('#informacion_rut').text('Rut sin puntos, sin guión y sin dígito verificador');
    $('#informacion_rut').addClass('form-text');
    $('#informacion_titular').addClass('form-text');
    $('#informacion_suplente').addClass('form-text');

    LibreriaFunciones.autoFocus($('#modal_matricula'), $('#rut_estudiante_matricula'));
    validarRutEstudianteMatricula('#rut_estudiante_matricula', '#dv_rut_estudiante_matricula');
    validarRutApoderadoMatricula('#rut_ap_titular', '#dv_rut_ap_titular', '#informacion_titular', 'Asignar apoderado titular');
    validarRutApoderadoMatricula('#rut_ap_suplente', '#dv_rut_ap_suplente', '#informacion_suplente', 'Asignar apoderado suplente');
    (modal == 'registrar') ? loadLetra() : '';
}

// Función para lanzar el modal matrícula para nuevo registro
function lanzarModalNuevaMatricula() {
    $('#btn_nueva_matricula').click(() => {
        prepararModalMatricula('registrar');

        $('#modal_matricula_tittle').text('REGISTRAR NUEVA MATRÍCULA');
        $('#letra_curso').html('<option selected> ------- </option>');
        $('#btn_registrar_matricula').text('Registrar');
        $('#texto_secundario').text('Registro de matrícula N°');
        $('#fecha_matricula').val(LibreriaFunciones.getFecha());
        $('#informacion_titular').text('Asignar apoderado titular');
        $('#informacion_titular').val('0');
        $('#informacion_suplente').text('Asignar apoderado suplente');
        $('#informacion_suplente').val('0');
    });
}

// Función para lanzar el modal matricula para actualzar registro
function lanzarModalActualizarMatricula(tabla) {
    $('#tabla_matricula_estudiante tbody').on('click', '#btn_editar_matricula', function() {
        let data = tabla.row($(this).parents()).data();
        let rut = data.rut.slice(0, data.rut.length - 2);
        let nombres = data.nombre + ' ' + data.ap_paterno + ' ' + data.ap_materno;

        let grado = (data.curso == null) ? '' : data.curso.slice(0, data.curso.length-1);
        let letra = (data.curso == null) ? '' : data.curso.slice(1, data.curso.length);

        prepararModalMatricula('actualizar');

        // Asignación del contenido
        $('#modal_matricula_tittle').text('ACTUALIZAR MATRÍCULA');
        $('#btn_registrar_matricula').text('Actualizar');
        $('#rut_estudiante_matricula').prop('disabled', true);
        $('#informacion_rut').val(data.id_matricula);

        // Asignación de valores
        $('#rut_estudiante_matricula').val(rut);
        generar_dv('#rut_estudiante_matricula', '#dv_rut_estudiante_matricula');
        $('#nombre_estudiante_matricula').val(nombres.toUpperCase());
        $('#grado_curso').val(grado);

        preLoadLetra(letra);
        $('#grado_curso').change(() => {
            preLoadLetra(letra);
        });

        $('#numero_matricula').val(data.matricula);
        $('#fecha_matricula').val(LibreriaFunciones.textFecha(data.fecha_matricula));

        getApoderadosTS(data.id_matricula);
    });

}

// Función para preparar y lanzar el modal de suspención
function lanzarModalSuspencion(tabla) {
    let calculo = (campo) => {
        campo.change(() => {
            $('#numero_dias_suspencion').text(LibreriaFunciones.restarFechas($('#fecha_inicio_suspencion'), $('#fecha_termino_suspencion')));
        })
    }

    $('#tabla_matricula_estudiante tbody').on('click', '#btn_estado_matricula', function() {
        let data = tabla.row($(this).parents()).data();
        let nombres = data.nombre + ' ' + data.ap_materno + ' ' + data.ap_paterno;

        $('#form_suspender_matricula').trigger('reset');
        $('#numero_dias_suspencion').text('')
        $('#nombre_estudiante_suspencion').val(nombres);
        calculo($('#fecha_inicio_suspencion'));
        calculo($('#fecha_termino_suspencion'));

        infoSuspension = { id_matricula: data.id_matricula }
    });
}

// Función para preparar y lanzar el modal de retiro
function lanzarModalRetiro(tabla) {
    $('#tabla_matricula_estudiante tbody').on('click', '#btn_retiro_matricula', function() {
        let data = tabla.row($(this).parents()).data();
        if (data.nombre_estado == 'Retirado(a)') {
            LibreriaFunciones.alertPopUp('warning', 'La matricula ya registra como retirada !!');
            return false;
        }
        $('#modal_retiro_matricula').modal('show');

        let rut = data.rut.slice(0, data.rut.length - 2);
        let nombres = data.nombre + ' ' + data.ap_paterno + ' ' + data.ap_materno;
        $('#fecha_retiro_estudiante').val(LibreriaFunciones.getFecha());
        $('#rut_estudiante_retirado').val(rut);
        generar_dv('#rut_estudiante_retirado', '#dv_rut_estudiante_retirado');
        $('#nombre_estudiante_retiro').val(nombres);
        $('#curso_estudiante_retirado').val(data.curso);

        infoRetiro = {
            rut: rut,
            id_matricula: data.id_matricula,
        }
    });
}



// ================== MANEJO DE INFORMARCIÓN ================== //
// Función para registrar una nueva matricula
function setMatricula(tabla) {
    $('#btn_registrar_matricula').click((e) => {
        e.preventDefault();
        if ($('#modal_matricula_tittle').text() != 'REGISTRAR NUEVA MATRÍCULA') { return false; }
        if (LibreriaFunciones.comprobarLongitud($('#rut_estudiante_matricula').val(), 7, 9, 'RUT', 'Estudiante') == false) { return false; }
        if (LibreriaFunciones.comprobarLongitud($('#rut_ap_titular').val(), 7, 9, 'RUT', 'Apoderado titular') == false) { return false; }
        if (LibreriaFunciones.comprobarLongitud($('#rut_ap_suplente').val(), 7, 9, 'RUT', 'Apoderado suplente') == false) { return false; }

        datos = 'setMatricula';
        const matricula = getDataFormulario();
        if (comprobarCamposVacios(matricula) >= 1) {
            LibreriaFunciones.alertPopUp('info', 'Faltan datos importantes !!');
            return false;
        }

        $.ajax({
            url: "./controller/controller_matricula.php",
            type: "post",
            dataType: "json",
            data: {datos: datos, matricula: matricula},
            success: (response) => {
                if (response == 'existe') {
                    LibreriaFunciones.alertPopUp('warning', 'El rut ingresado ya se encuentra matriculado');
                    return false;
                }

                if (response == 'matriculaExiste') {
                    LibreriaFunciones.alertPopUp('warning', 'El número de matricula para para el grado ya existe !!');
                    return false;
                }

                if (response == true) {
                    LibreriaFunciones.alertPopUp('success', 'Matrícula registrada !!');
                    beforeRegistroMatricula(tabla, '#modal_matricula');
                    return false;
                }

                LibreriaFunciones.alertPopUp('warning', 'Error de registro !!');
            }
        }).fail(() => {
            LibreriaFunciones.alertPopUp('error', 'Error en la consulta !!');
        });
    });
}

// Función para registrar la suspención de una matricula
function setSuspension(tabla) {
    $('#btn_registrar_suspencion').click(() => {
        datos = 'setSuspension';
        infoSuspension.f_inicio = $('#fecha_inicio_suspencion').val();
        infoSuspension.f_termino = $('#fecha_termino_suspencion').val();
        infoSuspension.motivo = $('#motivo_suspencion').val();

        if (infoSuspension.f_inicio == '' || infoSuspension.f_termino == '') {
            LibreriaFunciones.alertPopUp('info', 'Ingresar las fechas de la suspensión !!');
            return false;
        }

        $.ajax({ // TRABAJAR EN ESTE APARTADO, MODIFICAR CONTROLADOR Y MODAL
            url: "./controller/controller_matricula.php",
            type: "post",
            dataType: "json",
            data: {datos: datos, suspension: infoSuspension },
            success: (response) => {
                if (response == true) {
                    LibreriaFunciones.alertPopUp('success', 'Suspensión registrada con exito !!');
                    beforeRegistroMatricula(tabla, '#modal_suspender_matricula');
                    return false;
                }

                LibreriaFunciones.alertPopUp('warning', 'Error de registro !!');
            }
        }).fail((jqXHR) => {
            if (jqXHR.status == 404) {
                LibreriaFunciones.alertPopUp('warning', 'Acceso restringido !!');
                return false;
            }
            LibreriaFunciones.alertPopUp('error', 'Error en la ejecución !!');
        });

    });
}

// Función para registrar el retiro de una matricula
function setRetiroMatricula(tabla) {
    // Registrar retiro
    $('#btn_registrar_retiro').click(() => {
        datos = "setRetiroMatricula";
        if ($('#fecha_retiro_estudiante').val() == '') {
            LibreriaFunciones.alertPopUp('info', 'Ingresar fecha de retiro !!');
            return false;
        }
        infoRetiro.fecha_retiro = $('#fecha_retiro_estudiante').val();

        $.ajax({
            url: "./controller/controller_matricula.php",
            type: "post",
            dataType: "json",
            data: {datos: datos, retiro: infoRetiro },
            success: (response) => {
                if (response == true) {
                    LibreriaFunciones.alertPopUp('success', 'Retiro efectuado con éxito !!');
                    beforeRegistroMatricula(tabla, '#modal_retiro_matricula');
                    return false;
                }

                LibreriaFunciones.alertPopUp('warning', 'Error de registro !!');
            }
        }).fail((jqXHR) => {
            if (jqXHR.status == 404) {
                LibreriaFunciones.alertPopUp('warning', 'Acceso restringido !!');
                return false;
            }

            LibreriaFunciones.alertPopUp('error', 'Error en la consulta')
        });
    });
}

// Función para actualizar una matricula
function updateMatricula(tabla) {
    $('#btn_registrar_matricula').click((e) => {
        e.preventDefault(); 
        if ($('#modal_matricula_tittle').text() != 'ACTUALIZAR MATRÍCULA') { return false; }
        if (LibreriaFunciones.comprobarLongitud($('#rut_ap_titular').val(), 7, 9, 'RUT', 'Apoderado titular') == false) { return false; }
        if (LibreriaFunciones.comprobarLongitud($('#rut_ap_suplente').val(), 7, 9, 'RUT', 'Apoderado suplente') == false) { return false; }

        datos = 'updateMatricula';
        const matricula = getDataFormulario();
        if (comprobarCamposVacios(matricula) >= 1) {
            LibreriaFunciones.alertPopUp('info', 'Faltan datos importantes !!');
            return false;
        }
        matricula.id_matricula = $('#informacion_rut').val();

        $.ajax({
            url: "./controller/controller_matricula.php",
            type: "post",
            dataType: "json",
            data: {datos: datos, matricula: matricula},
            success: (response) => {
                if (response == true) {
                    LibreriaFunciones.alertPopUp('success', 'Matricula actualizada !!');
                    beforeRegistroMatricula(tabla, '#modal_matricula');
                    return false;
                }

                LibreriaFunciones.alertPopUp('warning', 'Error de registro !!');
            }
        }).fail((jqXHR) => {
            if (jqXHR.status == 404) {
                LibreriaFunciones.alertPopUp('warning', 'Acceso restringido !!');
                return false;
            }

            LibreriaFunciones.alertPopUp('error', 'Error en la consulta !!');
        });
    });
}

// Función para eliminar el registro de una matricula
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
                    data: {datos: datos, id_matricula: id_matricula},
                    success: (response) => {
                        if (response == true) {
                            // LibreriaFunciones.alertPopUp('success', 'Registro eliminado !!');
                            LibreriaFunciones.alertToast('success', 'Registro eliminado !!');
                            beforeRegistroMatricula(tabla);
                            return false;
                        } 
                        
                        // LibreriaFunciones.alertPopUp('warning', 'La matrícula no puede ser eliminada !!');
                        LibreriaFunciones.alertToast('warning', 'La matrícula no puede ser eliminada !!');
                    }
                }).fail((jqXHR) => {
                    if (jqXHR.status == 404) {
                        LibreriaFunciones.alertPopUp('warning', 'Acceso restringido !!');
                        return false;
                    }

                    LibreriaFunciones.alertPopUp('error', 'Error de ejecución !!');
                });
            }
        });
    });
}

// Función para descargar el certificado de alumno regular
function getCertificado(tabla) {
    $('#tabla_matricula_estudiante tbody').on('click', '#btn_certificado', function() {
        let data = tabla.row($(this).parents()).data();
        let id_matricula = data.id_matricula;
        let datos = "getCertificado";

        if (data.nombre_estado == 'Retirado(a)') {
            LibreriaFunciones.alertPopUp('warning', 'El estudiante esta retirado(a) !!');
            return false;
        }

        if (data.nombre_estado == 'Suspendido(a)') {
            LibreriaFunciones.alertPopUp('warning', 'El estudiante esta suspendido(a) !!');
            return false;
        }

        $.ajax({
            url: "./controller/controller_matricula.php",
            type: "post",
            data: {datos: datos, id_matricula: id_matricula},
            xhrFields: { responseType: 'blob' },
            success: (response) => {
                var url = window.URL.createObjectURL(response);
                var a = document.createElement('a');
                a.href = url;
                a.download = 'Certificado Alumno Regular.docx'; // nombre del archivo
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
            }
        }).fail((jqXHR) => {
            if (jqXHR.status == 404) {
                LibreriaFunciones.alertPopUp('warning', 'Acceso restringido !!');
                return false;
            }
            LibreriaFunciones.alertPopUp('error', 'Error de ejecución !!');
        });
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
            dataType: "json",
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
                    let modal = 'data-bs-toggle="modal" data-bs-target="#modal_suspender_matricula"';
                    if (data == 'Activo(a)') { estilo = 'btn-primary'; }
                    if (data == 'Suspendido(a)') { estilo = 'btn-warning'; modal = ''; }
                    if (data == 'Retirado(a)') { estilo = 'btn-danger'; modal = ''; }

                    return `<div class="d-grid col-12 mx-auto">
                                <button class="btn ` + estilo + `" title="Cambiar estado"` + modal + `id="btn_estado_matricula">` + data + `</button>
                            </div>`
                }
            },
            {
                data: null,
                bSortable: false,
                defaultContent:`<button class="btn btn-primary btn-justify px-3" id="btn_editar_matricula" title="Editar matricula" type="button" data-bs-toggle="modal" data-bs-target="#modal_matricula"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-info btn-justify px-3" id="btn_certificado" title="Descargar certificado" type="button"><i class="fas fa-file-download"></i></button>
                                <button class="btn btn-warning btn-justify px-3" id="btn_retiro_matricula" title="Retirar estudiante" type="button"><i class="fas fa-sign-out-alt"></i></button>
                                <button class="btn btn-danger btn-delete px-3" id="btn_delete_matricula" title="Eliminar matricula" type="button"><i class="fas fa-trash-alt"></i></button>`,
                className: "text-center"
            }
        ],
        language: spanish
    });

    expadirData(tabla_matricula);

    lanzarModalNuevaMatricula();
    lanzarModalActualizarMatricula(tabla_matricula);
    lanzarModalSuspencion(tabla_matricula);
    lanzarModalRetiro(tabla_matricula);
    setMatricula(tabla_matricula);
    updateMatricula(tabla_matricula);
    setSuspension(tabla_matricula);
    setRetiroMatricula(tabla_matricula);
    deleteRegistroMatricula(tabla_matricula);

    getCertificado(tabla_matricula);
    exportarMatriculas('#btn_excel', 'xlsx');
    exportarMatriculas('#btn_csv', 'csv');

});




