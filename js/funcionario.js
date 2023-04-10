import {LibreriaFunciones, generar_dv, spanish } from './librerias/librerias.js';
let datos = 'getFuncionario';

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
function getCantidadFuncionario() {
    let datos = 'getCantidadFuncionario';

    $.ajax({
        url: "./controller/controller_funcionario.php",
        type: "post",
        dataType: "json",
        data: {datos: datos},
        success: (response) => {
            $('#cantidad_funcionario').text(response.cantidad_funcionario);
        }
    }).fail(() => {
        $('#cantidad_funcionario').text('Error !!');
    });
}



// ================== MANEJO DE INFORMARCIÓN ================== //

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
                            // beforeRecord(tabla); // habilitar funcion
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
            {data: "estado"},
            {
                data: null,
                bSortable: false,
                defaultContent:`<button class="btn btn-primary btn-justify px-3" id="btn_editar_funcionario" title="Editar matricula" type="button" data-bs-toggle="modal" data-bs-target="#modal_matricula"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-danger btn-delete px-3" id="btn_delete_funcionario" title="Eliminar funcionario" type="button"><i class="fas fa-trash-alt"></i></button>`,
                className: "text-center"
            }
        ],
        language: spanish

    });

    expadirData(tabla_funcionario);


    deleteRegistroFuncionario(tabla_funcionario);


    getReporteFuncionario('#btn_excel', 'xlsx');
    getReporteFuncionario('#btn_csv', 'csv');






});