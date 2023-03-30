import {LibreriaFunciones, generar_dv, spanish } from './librerias/librerias.js';
let datos = 'getGrado';


// Función para comprobar y actualizar la suspensión de matrículas
function comprobarSuspension() {
    let datos = 'comprobarSuspension';
    $.ajax({
        url: "./controller/controller_suspension.php",
        type: "post",
        dataType: "json",
        data: {datos: datos},
        success: (response) => {
            if (response != true) {
                LibreriaFunciones.alertPopUp('warning', 'No se ha podido actualizar las suspensiones !!');
            }
        }
    }).fail(() => {
        LibreriaFunciones.alertPopUp('error', 'Error en consulta a al base de datos !!');
    });
}

// Función para reloj animado
function digitalClock() {
    const meses = {
        0: "enero",
        1: "febrero",
        2: "marzo",
        3: "abril",
        4: "mayo",
        5: "junio",
        6: "julio",
        7: "agosto",
        8: "septiembre",
        9: "octubre",
        10: "noviembre",
        11: "diciembre"
    }

    const date = new Date();
    const hour = date.getHours() % 12 || 12;

    // hour
    const minute = date.getMinutes().toString().padStart(2, '0');
    const second = date.getSeconds().toString().padStart(2, '0');
    const am_pm = date.getHours() >= 12 ? 'PM' : 'AM';

    //date
    const year = date.getFullYear();
    const month = meses[date.getMonth()];
    const day = date.getDate();

    const timeString = `${hour}:${minute}:${second} ${am_pm}`;
    const dateString = `${day} de ${month} de ${year}`;

    $('#date').text(dateString);
    $('#time').text(timeString);

    requestAnimationFrame(digitalClock);
}

// Función para generar una tabla con los cursos del grado
function getDataCursos(grado, data) {
    let table = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' + 
                    '<th>Curso</th>' +
                    '<th>Cantidad</th>';

    for (let i = 0; i < data.length; i++) {
        table += '<tr>' +
                    '<td>' + grado + ' ' + data[i].letra_grado + '</td>' +
                    '<td>' + data[i].cantidad_estudiante + ' estudiantes</td>' +
                '</tr>';
    }
    table += '</table>';
    return table;
}

// Función para expandir información secundaria // PASAR A FUNCION GENERICA
function expandirGrado(tabla) {
    $('#tabla_grados tbody').on('click', 'td.dt-control', function () {
        let dataRow = tabla.row($(this).parents()).data();
        let tr = $(this).closest('tr');
        let row = tabla.row(tr);
        let datos = 'getLetraPorGrado';

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
        } else {
            $.ajax({
                url: "./controller/controller_curso.php",
                type: "post",
                dataType: "json",
                data: {datos: datos, grado: dataRow.grado},
                success: (response) => {
                    row.child(getDataCursos(dataRow.grado, response)).show();
                }

            }).fail(() => {
                LibreriaFunciones.alertPopUp('error', 'Error en la consulta !!');
            });

            tr.addClass('shown');
        }
    });
}

// Función para obtener la cantidad de estudiante matriculados
function getCantidadMatricula() {
    let datos = 'getCantidadMatricula';

    $.ajax({
        url: "./controller/controller_matricula.php",
        type: "post",
        dataType: "json",
        data: {datos: datos},
        success: (response) => {
            $('#cantidad_estudiante_matriculado').text(response.cantidad_matricula);
        }
    }).fail(() => {
        $('#cantidad_estudiante_matriculado').text('Error !!');
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

  





$(document).ready(function() {
    let tabla_grados = $('#tabla_grados').DataTable({
        ajax: {
            url: "./controller/controller_curso.php",
            type: "post",
            dataType: "json",
            data: {datos: datos}
        },
        columns: [
            {
                className: "dt-control",
                bSortable: false,
                data: null,
                defaultContent: ""
            },
            {
                data: "grado",
                className: "text-center"
            },
            {
                data: "nivel",
                className: "text-center"
            },
            {
                data: "cantidad_estudiante",
                className: "text-center"
            }
        ],
        paging: false,
        searching: false,
        order: [[2, "asc"]],
        language: spanish
    });

    getCantidadMatricula();
    getCantidadFuncionario();

    comprobarSuspension();
    digitalClock();
    expandirGrado(tabla_grados);


});