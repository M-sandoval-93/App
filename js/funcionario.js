import {LibreriaFunciones, generar_dv, spanish } from './librerias/librerias.js';
let datos = 'getFuncionarios';

// ==================== FUNCIONES INTERNAS ===============================//
// Función para generar tabla expansiba con datos secundarios
function getData(data) {
    return (
        '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
            '<tr>' +
                '<td>Fecha de nacimiento:</td>' +
                '<td>' + data.fecha_nacimiento + '</td>' +
            '</tr>' +
            '<tr>' +
                '<td>Sexo:</td>' +
                '<td>' + data.sexo + '</td>' +
            '</tr>' +
            '<tr>' +
                '<td>Departamento:</td>' +
                '<td>' + data.departamento + '</td>' +
            '</tr>' +
        '</table>'
    );
}

// Función para expandir información secundaria // PASAR A FUNCION GENERICA
function expadirData(tabla) {
    $('#tabla_funcionario tbody').on('click', 'td.dt-control', function () {
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

// Función para obtener la cantidad de funcionarios
function getCantidadFuncionario(contexto = false) {
    let datos = 'getCantidadFuncionario';

    $.ajax({
        url: "./controller/controller_funcionario.php",
        type: "post",
        dataType: "json",
        data: {datos: datos},
        success: (response) => {
            if (contexto == true) {
                $('#cantidad_nuevo_registro').text(response.cantidad_funcionario + 1);
                return false;
            }

            $('#cantidad_funcionario').text(response.cantidad_funcionario);
        }
    }).fail(() => {
        if (contexto == true) {
            $('#cantidad_nuevo_registro').text('Error !!');
            return false;
        }

        $('#cantidad_funcionario').text('Error !!');
    });
}

// Función para cargar lista de tipos de funcionario
function loadTipoFuncionario() {
    let datos = 'loadTipoFuncionario';

    $.ajax({
        url: "./controller/controller_funcionario.php",
        type: "post",
        dataType: "json",
        data: {datos: datos},
        success: (response) => {
            $('#tipo_funcionario').html(response);

            //Trabajar para utilizar en update
            // $('#letra_curso').html(data);
            // $('#letra_curso').val($('#letra_curso option:contains("' + letra + '")').val());
        }
    }).fail(() => {
        $('#tipo_funcionario').html('Error !!');
    });
}

// Función para cargar los departamentos
function loadDepartamento() {
    let datos = 'loadDepartamento';

    $.ajax({
        url: "./controller/controller_funcionario.php",
        type: "post",
        dataType: "json",
        data: {datos: datos},
        success: (response) => {
            $('#departamento_funcionario').html(response);

            //Trabajar para utilizar en update
            // $('#letra_curso').html(data);
            // $('#letra_curso').val($('#letra_curso option:contains("' + letra + '")').val());
        }
    }).fail(() => {
        $('#departamento_funcionario').html('Error !!');
    });
}

// Función para generar un objeto funcionario
function getDataFormulario() {
    const funcionario = {
        rut: $.trim($('#rut_funcionario').val()),
        dv_rut: $.trim($('#dv_rut_funcionario').val().toUpperCase()),
        nombre: $.trim($('#nombre_funcionario').val().toUpperCase()),
        ap: $.trim($('#ap_funcionario').val().toUpperCase()),
        am: $.trim($('#am_funcionario').val().toUpperCase()),
        tipo_funcionario: $.trim($('#tipo_funcionario').val()),
        departamento: $.trim($('#departamento_funcionario').val()),
        sexo: $.trim($('#sexo_funcionario').val()),
        fecha_nacimiento: $.trim($('#fecha_nacimiento_funcionario').val())
    }
    return funcionario;
}

// Función para comprobar si el funcionario existe
function comprobarFuncionario(rut) {
    let datos = 'getFuncionario';

    if (rut != '' && rut.length >= 7 && rut.length <= 9) {
        $.ajax({
            url: "./controller/controller_funcionario.php",
            type: "post",
            dataType: "json",
            cache: false,
            data: {datos: datos, rut: rut, tipo: 'existe'},
            success: function(data) {
                if (data == true) {
                    LibreriaFunciones.alertPopUp('warning', 'El rut ingresado ya existe en la base de datos'); 
                }
            }
        }).fail(() => {
            LibreriaFunciones.alertPopUp('error', 'Error en la consulta !!');
        });
    } 
}

// Función para generar digito verificador y validar RUT
function validarRutFuncionario() {
    $('#rut_funcionario').keyup((e) => {
        e.preventDefault();
        generar_dv('#rut_funcionario', '#dv_rut_funcionario');
        comprobarFuncionario($('#rut_funcionario').val());

        LibreriaFunciones.validarNumberRut($('#rut_funcionario'), $('#informacion_rut'), 'Rut sin puntos, sin guión y sin dígito verificador');
    })
}

// Función para comprobar vampos vacios en un objeto
function comprobarObjetoFuncionario(objeto) {
    let count = 0;
    for (const [key, value] of Object.entries(objeto)) {
        if (value == '') { count +=1; }
    }

    // Condición para comprobar que el rut ingresado se encuentra en la base de datos estudiante
    if ($('#nombre_estudiante_matricula').val() == 'Sin datos para el rut ingresado !!') {
        count +=1;
    }

    return count;
}

 // Función para generar acción al terminar algún proceso de edición de datos
 function beforeRecord(tabla, modal = true) {
    tabla.ajax.reload(null, false);
    getCantidadFuncionario();
    if (modal == true) {
        $('#modal_funcionario').modal('hide');
    }
}



// ================== MANEJO DE MODAL ================== //
// Función para preparar el modal funcionario
function prepararModal() {
    $('#form_registro_funcionario').trigger('reset');
    $('#rut_funcionario').removeClass('is-invalid');
    $('#informacion_rut').removeClass('text-danger');
    $('#informacion_rut').text('Rut sin puntos, sin guión y sin dígito verificador');
    $('#informacion_rut').addClass('form-text');
    LibreriaFunciones.autoFocus($('#modal_funcionario'), $('#rut_funcionario'));
    validarRutFuncionario();
    loadTipoFuncionario();
    loadDepartamento();
}

// Función para mostar el modal para un nuevo funcionario
function showModalNewFuncionario() {
    $('#btn_nuevo_funcionario').click(() => {
        prepararModal();
        $('#modal_funcionario_title').text('REGISTRAR NUEVO FUNCIONARIO');
        $('#btn_funcionario_registrar').text('Registrar');
        $('#texto_secundario').text('Nuevo registro N°');
        getCantidadFuncionario(true);
    });

}

// Función para mostrar un modal para actualizar los datos de un funcionario
function showModalUpdateFuncionario(tabla) {
    $('#tabla_funcionario tbody').on('click', '#btn_editar_funcionario', function() {
        let data = tabla.row($(this).parents()).data();
        prepararModal();

        // Trabajar datos principales para asignar al formulario
        let rut = data.rut_funcionario.slice(0, data.rut_funcionario.length - 2);

        // Asignación del contenido
        $('#modal_funcionario_title').text('ACTUALIZAR REGISTRO FUNCIONARIO');
        $('#btn_funcionario_registrar').text('Actualizar');
        $('#texto_secundario').text('ID del funcionario N°');
        $('#cantidad_nuevo_registro').text(data.id_funcionario);

        // Asignación de valores
        $('#rut_funcionario').val(rut);
        generar_dv('#rut_funcionario', '#dv_rut_funcionario');
        $('#nombre_funcionario').val(data.nombres_funcionario.toUpperCase());
        $('#ap_funcionario').val(data.ap_funcionario.toUpperCase());
        $('#am_funcionario').val(data.am_funcionario.toUpperCase());
        $('#sexo_funcionario').val(data.sexo.slice(0, 1));
        $('#fecha_nacimiento_funcionario').val(LibreriaFunciones.textFecha(data.fecha_nacimiento));



        // trabajar en esta función ya que no esta dando resultado
        let texto = data.tipo_funcionario;
        let options = $('#tipo_funcionario option');

        options.each(function() {
            if ($(this).text() === texto) {
                $(this).prop('selected', true);
                return false;
            }
        });



        // let option = $('#tipo_funcionario option').filter(function() {
        //     return $(this).text() == texto;
        // });

        // console.log(option);

        // option.prop('selected', true);

    });
}


// let seleccion = $('#tipo_funcionario option:contains(' + data.tipo_funcionario + ')');
// seleccion.prop('selected', true);

// $('#tipo_funcionario option:contains("' + data.tipo_funcionario + '")').prop('selected', true);
// $('#tipo_funcionario option:contains("Asistente")').prop('selected', true);
// $('#tipo_funcionario').val($('#tipo_funcionario option:contains("' + data.tipo_funcionario + '")').val());

// console.log(data.tipo_funcionario);
// console.log($('#tipo_funcionario option:contains("' + data.tipo_funcionario + '")').text());

// $('#letra_curso').val($('#letra_curso option:contains("' + letra + '")').val());


// ================== MANEJO DE INFORMARCIÓN ================== //
// Función para registrar un nuevo funcionrio
function setFuncionario(tabla) {
    $('#btn_funcionario_registrar').click((e) => {
        e.preventDefault();
        if ($('#modal_funcionario_title').text() != 'REGISTRAR NUEVO FUNCIONARIO') { return false; }
        if (LibreriaFunciones.comprobarLongitud($('#rut_funcionario').val(), 7, 9, 'RUT', 'Funcionrio') == false) { return false; }

        let datos = 'setFuncionario';
        const funcionario = getDataFormulario();
        if (comprobarObjetoFuncionario(funcionario) >= 1) {
            LibreriaFunciones.alertPopUp('info', 'Faltan datos importantes !!');
            return false;
        }

        $.ajax({
            url: "./controller/controller_funcionario.php",
            type: "post",
            dataType: "json",
            data: {datos: datos, funcionario: funcionario},
            success: (response) => {
                if (response == 'existe') {
                    LibreriaFunciones.alertPopUp('warning', 'El rut ingresado ya existe en la base de datos');
                    return false;
                }

                if (response == true) {
                    LibreriaFunciones.alertPopUp('success', 'Funcionario registrado !!');
                    beforeRecord(tabla)
                    return false;
                }

                LibreriaFunciones.alertPopUp('warning', 'No se registro el estudiante !!');
            }
        }).fail(() => {
            LibreriaFunciones.alertPopUp('error', 'Error en la consulta !!');
        });


    });
}

function updateFuncionario() {
    
}

// Función para eliminar el registro de un funcionario
function deleteRegistroFuncionario(tabla) {
    $('#tabla_funcionario tbody').on('click', '#btn_delete_funcionario', function() {
        let data = tabla.row($(this).parents()).data();
        let id_funcionario = data.id_funcionario;


        Swal.fire({
            icon: 'question',
            title: 'Eliminar registro de "' + data.nombres_funcionario + ' ' + data.ap_funcionario + '"',
            showCancelButton: true,
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#2691d9',
            cancelButtonColor: '#adadad'
        }). then(resultado => {
            if (resultado.isConfirmed) {
                datos = "deleteFuncionario";

                $.ajax({
                    url: "./controller/controller_funcionario.php",
                    type: "post",
                    dataType: "json",
                    data: {datos: datos, id_funcionario: id_funcionario},
                    success: (response) => {
                        if (response == true) {
                            LibreriaFunciones.alertToast('success', 'Registro eliminado !!');
                            beforeRecord(tabla, false);
                            return false;
                        }

                        LibreriaFunciones.alertPopUpButton('warning', 'El registro no se puede eliminar, se encuentra en uso !!');
                    }
                }).fail(() => {
                    LibreriaFunciones.alertPopUp('error', 'Error en la operación !!');
                });
            }
        });
    });
}


// Función para generar documento
function getReporteFuncionario(btn, ext) {
    let datos = 'getReporteFuncionario';

    $(btn).click((e) => {
        e.preventDefault();

        $.ajax({
            url: "./controller/controller_funcionario.php",
            type: "post",
            dataType: "html",
            cache: false,
            data: {datos: datos, ext: ext},
            success: (data) => {
                let opResult = JSON.parse(data);
                let $a = $("<a>");
    
                $a.attr("href", opResult.data);
                $("body").append($a);
                $a.attr("download", "Registro funcionario." + ext);
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
    // Función para obtener la cantidad de funcionarios
    getCantidadFuncionario();

    let tabla_funcionario = $('#tabla_funcionario').DataTable({
        ajax: {
            url: "./controller/controller_funcionario.php",
            type: "post",
            dataType: "json",
            data: {datos: datos}
        },
        columns: [
            {
                data: "id_funcionario",
                visible: false
            },
            {
                className: "dt-control",
                bSortable: false,
                data: null,
                defaultContent: ""
            },
            {data: "rut_funcionario"},
            {data: "ap_funcionario"},
            {data: "am_funcionario"},
            {data: "nombres_funcionario"},
            {data: "tipo_funcionario"},
            {
                data: "estado",
                mRender: (data) => {
                    let estilo;
                    if (data == 'Activo(a)') { estilo = 'bg-success'; }
                    if (data == 'Licencia') { estilo = 'bg-danger'; }
                    if (data == 'Permiso') { estilo = 'bg-warning'; }
                    return '<p class="text-center text-white rounded-3 mb-0 py-1 ' + estilo + '">' + data + '</p>';
                }
            },
            {
                data: null,
                bSortable: false,
                defaultContent:`<button class="btn btn-primary btn-justify px-3" id="btn_editar_funcionario" title="Editar funcionario" type="button" data-bs-toggle="modal" data-bs-target="#modal_funcionario"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-warning px-3" id="btn_generar_usuario" title="Generar cuenta usuario" type="button"><i class="fas fa-user-cog"></i></button>
                                <button class="btn btn-danger btn-delete px-3" id="btn_delete_funcionario" title="Eliminar funcionario" type="button"><i class="fas fa-trash-alt"></i></button>`,
                className: "text-center"
            }
        ],
        language: spanish

    });

    expadirData(tabla_funcionario);


    showModalNewFuncionario();
    showModalUpdateFuncionario(tabla_funcionario);

    setFuncionario(tabla_funcionario);
    deleteRegistroFuncionario(tabla_funcionario);


    getReporteFuncionario('#btn_excel', 'xlsx');
    getReporteFuncionario('#btn_csv', 'csv');


});