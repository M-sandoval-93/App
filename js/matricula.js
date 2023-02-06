import {LibreriaFunciones, generar_dv, spanish } from './librerias/librerias.js';
let datos = 'getMatricula';

// ==================== FUNCIONES INTERNAS ===============================//
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

function deleteRegistroMatricula(tabla) { // trabajando .....
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
                        // actualizar datos de la página, por medio de función before !!!

                    }
                }).fail(() => {
                    LibreriaFunciones.alertPopUp('error', 'Error de ejecución !!');
                });
            }
        });
    });
}

function beforeregistroMatricula(tabla) {
    tabla.ajax.reload(null, false);
    // agregar función cantidad matricula y cantidad retiros
}


// ==================== FUNCIONES INTERNAS ===============================//

$(document).ready(function() {
    // considerar la funciones para la cantidad de matriculas activas 

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
                // orderable: false,
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
                    if (data == 'activo') {
                        estilo = 'text-white bg-success';
                    } else if (data == 'suspención') {
                        estilo = 'text-dark bg-warning';
                    } else if (data == 'retiro') {
                        estilo = 'text-white bg-danger';
                    }

                    return '<p class="text-center rounded-3 mb-0 p-1 ' + estilo + '">' + data + '</p>';
                }
            },
            {
                data: null,
                bSortable: false,
                defaultContent:`<button class="btn btn-primary btn-justify px-3" id="btn_edit_matricula" title="Editar matricula" type="button"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-warning btn-justify px-3" id="btn_retiro_matricula" title="Retirar estudiante" type="button"><i class="fas fa-sign-out-alt"></i></button>
                                <button class="btn btn-danger btn-delete px-3" id="btn_delete_matricula" title="Eliminar matricula" type="button"><i class="fas fa-trash-alt"></i></button>`,
                className: "text-center"
            }
        ],
        language: spanish
    });

    expadirData(tabla_matricula);

    deleteRegistroMatricula(tabla_matricula);

    // prueba de modal
    $('#btn_nueva_matricula').click(function () {
        console.log('prueba');
    });






});




