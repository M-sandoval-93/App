import { LibreriaFunciones, generar_dv, spanish } from './librerias/librerias.js';
let data = "getAbsenceInformation"; // obtener informacion de inasistencia

// ==================== INTERNAL FUNCTIONS ===============================//



// ================== MODAL MANAGEMENT ================== //



// ================== INFORMATION MANAGEMENT ================== //






// ==================== INTERNAL FUNCTIONS ===============================//


$(document).ready(function() {
    // function to get number of absences   (funcion para conseguir cantidad de inasistencias)

    let tabla_inasistencia_funcionario = $('#tabla_inasistencia_funcionario').DataTable({
        ajax: {
            url: "./controller/controller_inasistenciaFuncionairo.php",
            type: "post",
            dataType: "json",
            data: { data: data }
        },
        colummns: [

        ],
        order: ([]), // to avoid the order generated by dataTable (para evitar el orden generado por dataTable)
        language: spanish
    });
});