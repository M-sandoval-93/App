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


//   function updateClock() {
//     const now = new Date();
//     const hour = now.getHours() % 12 || 12;
//     const minute = now.getMinutes().toString().padStart(2, '0');
//     const second = now.getSeconds().toString().padStart(2, '0');
//     const ampm = now.getHours() >= 12 ? 'PM' : 'AM';
//     const timeString = `${hour}:${minute}:${second} ${ampm}`;
//     const dateString = now.toLocaleDateString();
    
//     document.getElementById('time').textContent = timeString;
//     document.getElementById('date').textContent = dateString;
    
//     requestAnimationFrame(updateClock);
//   }
// digitalClock() {
//     const now = new Date();
//     const hour = now.getHours() % 12 || 12;
//     const minute = now.getMinutes().toString().padStart(2, '0');
//     const second = now.getSeconds().toString().padStart(2, '0');
//     const am_pm = now.getHours() >= 12 ? 'PM' : 'AM';
//     const timeString = `${hour}:${minute}:${second} ${am_pm}`;
//     const
// }
  





$(document).ready(function() {
    comprobarSuspension();
    // requestAnimationFrame(updateClock);

});