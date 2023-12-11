import { LibreriaFunciones, generar_dv, spanish } from './librerias/librerias.js';
let datos = 'getLicencias'; 






// ==================== DESPLIEGUE DE FUNCIONALIDAD ===============================//
$(document).ready(function() {

    // cargar funcion para mostrar cantidad de licencias del año

    let tabla_licencias = $('#tabla_licencia_funcionario').DataTable({
        ajax: {
            url: "./controller/controller_licenciaFuncionario.php",
            type: "post",
            dataType: "json",
            data: {data: data}
        },
        columns: [
            {
                visible: false,
                data: "id_licencia"
            },
            { data: "rut_funcionario" },
            { data: "nombre_funcionario" },
            { data: "departamento" },
            { data: "fecha_desde" },
            { data: "fecha_hasta" },
            { data: "dia" },
            {
                data: null,
                bSortable: false,
                defaultContent: `<button class="btn btn-primary btn-data" id="" type="button" data-bs-toggle="modal" data-bs-target="#"><i class="fas fa-pencil-alt"></i></button>
                                <button class="btn btn-danger btn-delete" id="" type="button"><i class="fas fa-trash-alt"></i></button>`
            }

        ],
        language: spanish
    });
})