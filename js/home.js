import {LibreriaFunciones, generar_dv, spanish } from './librerias/librerias.js';
let datos = 'comprobarSuspension';

// Función para comprobar y actualizar la suspensión de matrículas
function comprobarSuspension() {
    
    $.ajax({
        url: "./controller/controller_suspension.php",
        type: "post",
        dataType: "json",
        data: {datos: datos},
        success: function(response) {
            if (response != true) {
                LibreriaFunciones.alertPopUp('warning', 'No se ha podido actualizar las suspensiones !!');
            }
        }
    }).fail(() => {
        LibreriaFunciones.alertPopUp('error', 'Error en consulta a al base de datos !!');
    });
}

$(document).ready(function() {

    comprobarSuspension();

});