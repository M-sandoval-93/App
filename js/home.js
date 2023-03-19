import {LibreriaFunciones, generar_dv, spanish } from './librerias/librerias.js';
let datos = 'comprobarSuspension';


// Función para comprobar y actualizar la suspensión de matrículas
function comprobarSuspension() {
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

// función para reloj animado
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

  





$(document).ready(function() {
    comprobarSuspension();
    digitalClock();
    // requestAnimationFrame(updateClock);

});