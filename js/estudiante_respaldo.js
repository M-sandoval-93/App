// Librería de funciones básicas para validar RUT ==========
let LibreriaFunciones = {
    // Valida el rut con su cadena completa "XXXXXXXX-X"
    validarRut: function(rutCompleto) {
        if (!/^[0-9]+[-|‐]{1}[0-9kK]{1}$/.test( rutCompleto )) {
            return false;
        }
        
        let tmp = rutCompleto.split('-');
        let rut = tmp[0];
        let dvRut = tmp[1];

        if (dvRut == 'K') {
            dvRut = 'K';
        }
        return (LibreriaFunciones.dv(rut) == dvRut);
    },

    // Calcula el dígito verificador
    dv: function(T) {
        let M = 0, S = 1;

        for (;T;T = Math.floor(T/10)) {
            S = (S + T % 10 * (9 - M ++ % 6)) % 11;
        }
        return S?S - 1: 'K';
    },

    // Valida que el número sea un entero
    validarEntero: function(value) {
        let regExPattern = /[0-9]+$/;
        return regExPattern.test(value);
    },

    // Formatea un número con puntos de miles
    formatearNumero: function(val) {
        if (LibreriaFunciones.validarEntero(val)) {
            let retorno = '';
            let value = val.toString().split('').reverse().join('');
            let i = value.length;

            while (i > 0) {
                retorno += ((i%3==0&&i!=value.length)?'':'')+value.substring(i--,i);
                // retorno += ((i%3==0&&i!=value.length)?'.':'')+value.substring(i--,i);  //Para ir agregando el punto
            }
            return retorno;
        }
        return val;
    }
}
// Librería de funciones básicas para validar RUT ==========


// FUNCIONES ===============================================
function generar_dv(rut, dv_rut) {
    // Traspasar valor a número entero
    let numero = $(rut).val();
    numero = numero.split('.').join('');

    // Valida que sea realmente entero
    if (LibreriaFunciones.validarEntero(numero)) {
        $(dv_rut).val(LibreriaFunciones.dv(numero));

    } else {
        $(dv_rut).val('');
    }

    // Formatear el valor del rut con sus puntos
    $(rut).val(LibreriaFunciones.formatearNumero(numero));
}


function buscar_info_apoderado(rut, label, elemento) {
    datos = 'buscar_apoderado';

    if (rut.length <= 7) {
        elemento.attr('hidden', 'hidden');
        label.text('');

    } else if (rut.length >= 7) {
        $.ajax({
            url: "./controller/controller_apoderado.php",
            method: "post",
            dataType: "json",
            data: {rut: rut, datos: datos},
            success: function(data) {
                if (data === false) {
                    label.text('Apoderado sin registros !!');
                    elemento.removeAttr('hidden', 'hidden');
                } else {
                    label.text(data);
                }
            }
        });
    }
}

function comprobarLongitudRut() {
    let contador = 0;

    $('.input-rut').each(function() {
        if ($(this).val().length > 1 && $(this).val().length < 8) {
            contador = contador + 1;
        }
    });

    return contador;
}

function camposVacios() { 
    let contador = 0;
    $('#modal_form_estudiantes input').each(function() {
        if ($(this).val() == '' && $(this).attr('id') != 'estudiante_nombre_social' &&
        $(this).attr('id') != 'apoderado_titular_rut' && $(this).attr('id') != 'apoderado_titular_dv_rut' && 
        $(this).attr('id') != 'apoderado_suplente_rut' && $(this).attr('id') != 'apoderado_suplente_dv_rut') {
            contador = contador + 1;
        }
    });
    return contador;
}

function camposVaciosApodeado() {
    let contador = 0;
    $('#modal_form_apoderados input').each(function() {
        if ($(this).val() == '') {
            contador = contador + 1;
        }
    });
    return contador;
}

function alertPopUp(icon, title) {
    Swal.fire({
        icon: icon,
        title: title,
        showConfirmButton: false,
        timer: 1500
    });
}

function prepareModalApoderado(m1, m2) {
    $('#form_apoderados').trigger('reset');
    $('#titulo-modal').text('Registrar nuevo apoderado');
    $('#apoderado_codigo_fono').val('569');
    $('#apoderado_rut').removeAttr('disabled', 'disable');
    $('#apoderado_dv_rut').attr('disabled', 'disabled');
    $('#apoderado_rut').focus();
    $('#apoderado_rut').keyup(function() {
        generar_dv('#apoderado_rut', '#apoderado_dv_rut');
    });

    m1.removeClass('modal-show');
    m2.addClass('modal-show');
}


// FUNCIONES ===============================================


$(document).ready(function() {
    // VARIABLES GLOBALES
    let modal = $('#modal_form_estudiantes');
    let modal_apoderado = $('#modal_form_apoderados');
    let datos = 'mostrar_estudiantes';
    let registrar;
    let id_estudiante;

    // BOTÓN NUEVO ESTUDIANTE /==================================   LISTO
    $('#btn_nuevo_estudiante').click(function(e) {
        e.preventDefault();
        $('#form_estudiantes').trigger('reset');
        $('#titulo-modal_estudiante').text('Registrar nuevo estudiante');
        
        modal.addClass('modal-show');
        // PREPARAR MODAL
        $('#estudiante_rut').removeAttr('disabled', 'disable');
        $('#estudiante_dv_rut').attr('disabled', 'disabled');
        $('#apoderado_titular_dv_rut').attr('disabled', 'disabled');
        $('#apoderado_suplente_dv_rut').attr('disabled', 'disabled');
        $('#btn_me_agregar_titular').attr('hidden', 'hidden');
        $('#btn_me_agregar_suplente').attr('hidden', 'hidden');
        $("#estudiante_letra").html('');
        $('#estudiante_ap_titular').text('');
        $('#estudiante_ap_suplente').text('');
        // $('#estudiante_fecha_ingreso').focus();

        // VALIDADOR DE RUT ESTUDIANTE
        $('#estudiante_rut').keyup(function() {
            generar_dv('#estudiante_rut', '#estudiante_dv_rut');
        });

        // VALIDADOR DE RUT APODERADO TITULAR
        $('#apoderado_titular_rut').keyup(function() {
            generar_dv('#apoderado_titular_rut', '#apoderado_titular_dv_rut');
            buscar_info_apoderado($(this).val(), $('#estudiante_ap_titular'), $('#btn_me_agregar_titular'));
        });

        // VALIDADOR DE RUT APODERADO TITULAR
        $('#apoderado_suplente_rut').keyup(function() {
            generar_dv('#apoderado_suplente_rut', '#apoderado_suplente_dv_rut');
            buscar_info_apoderado($(this).val(), $('#estudiante_ap_suplente'), $('#btn_me_agregar_suplente'));
        });

        registrar = 'nuevo_estudiante';
    });
    // BOTÓN NUEVO ESTUDIANTE /==================================




    // BOTÓN MODAL REGISTRAR /===================================   TRABAJANDO 
    $('#btn_modal_registrar_estudiante').click(function(e) {
        e.preventDefault();

        // VALIDACIOINES PARA EL FORMULARIO
        if ($('#estudiante_letra').val() == null || $('#estudiante_letra').val() == 'Letra') {
            alertPopUp('warning', 'Seleccionar letra de cursos !!');
            return false;
        } else if (camposVacios() >= 1) {
            alertPopUp('warning', 'Hay campos vacios !!');
            return false;
        } else if (comprobarLongitudRut() >= 1) {
            alertPopUp('warning', 'Revisar los rut ingresados');
            return false;
        } else if ($('#estudiante_ap_titular').text() === 'Apoderado sin registros !!' || $('#estudiante_ap_suplente').text() === 'Apoderado sin registros !!') {
            alertPopUp('warning', 'Agregar al apoderado(a) sin registro !!');
            return false;
        }

        // SE CREAN LAS VARIABLES
        fecha_ingreso = $('#estudiante_fecha_ingreso').val();
        matricula = $('#estudiante_matricula').val();
        rut_e = $('#estudiante_rut').val();
        rut_dv_e = $('#estudiante_dv_rut').val().toUpperCase();
        nombres = $('#estudiante_nombres').val().toUpperCase();
        ap = $('#estudiante_ap_paterno').val().toUpperCase();
        am = $('#estudiante_ap_materno').val().toUpperCase();
        nombre_social = $('#estudiante_nombre_social').val().toUpperCase();
        id_curso = $('#estudiante_letra').val();
        fecha_nacimiento = $('#estudiante_fecha_nacimiento').val();
        sexo = $('#estudiante_sexo').val();
        junaeb = $('#estudiante_junaeb').val();
        rut_at = $('#apoderado_titular_rut').val();
        rut_as = $('#apoderado_suplente_rut').val();


        if (registrar == 'nuevo_estudiante') { //========================== LISTO
            datos = "nuevo_estudiante";

            $.ajax({
                url: "./controller/controller_estudiante.php",
                method: "post",
                dataType: "json",
                data: {datos: datos,
                    fecha_ingreso: fecha_ingreso, matricula: matricula, rut_e: rut_e, rut_dv_e: rut_dv_e, nombres: nombres,
                    ap: ap, am: am, nombre_social: nombre_social, id_curso: id_curso, fecha_nacimiento: fecha_nacimiento, sexo: sexo,
                    junaeb: junaeb, rut_at: rut_at, rut_as: rut_as},
                success: function(data) {
                    if (data == 'existe') {
                        alertPopUp('warning', 'El estudiante ya existe !!');

                    } else {
                        if (data === false) {
                            alertPopUp('error', 'Error al registrar !!');
                        
                        } else {
                            alertPopUp('success', 'Estudiante registrado');
                            modal.removeClass('modal-show');
                            tabla_estudiantes.ajax.reload(null, false);
                        }
                    }
                }
            });


        } else if (registrar == 'editar_estudiante') {  // ACCION QUE SE GENERA AL REGISTRAR HABIENDO PRESIONADO EL BTN EDITAR
            console.log('Editar un estudiante');

            // CONSIDERAR LA ACTUALIZACIÓN DE UN APODERADO, SUPLENTE O TITULAR
            // CONSIDEERAR LA ELIMINCIÓN DE UN APODERADO TITULAR O SUPLENTE
            // VER LA OPCIÓN DE PODER ELIMINARLO DESDE EL DESPLEGABLE DEL ESTUDIANTE
        }



    });
    // BOTÓN MODAL REGISTRAR /===================================


    // BOTÓN MODAL REGISTRAR APODERADO /=========================   TRABAJANDO 
    $('#btn_modal_registrar_apoderado').click(function(e) {
        e.preventDefault();

        // SE CREAN LAS VARIABLES
        rut = $('#apoderado_rut').val();
        dv_rut = $('#apoderado_dv_rut').val().toUpperCase();
        nombres = $('#apoderado_nombres').val().toUpperCase();
        a_paterno = $('#apoderado_ap_paterno').val().toUpperCase();
        a_materno = $('#apoderado_ap_materno').val().toUpperCase();
        fono = $('#apoderado_fono').val();

        // VALIDAR QUE TODOS LOS CAMPOS CONTENGAN INFORMACION
        if (camposVaciosApodeado() >= 1) {
            alertPopUp('warning', 'Hay campos vacios !!');
            return false;
        }

        console.log("listo para agregar el apoderado");

        // datos = "nuevo_apoderado";
        // $.ajax({
        //     url: "./controller/controller_apoderado.php",
        //     method: "post",
        //     dataType: "json",
        //     data: {rut: rut, dv_rut: dv_rut, nombres: nombres, a_paterno: a_paterno,
        //             a_materno: a_materno, fono: fono, datos: datos},
        //     success: function(data) {
        //         if (data === false) {
        //             Swal.fire({
        //                 icon: 'error',
        //                 title: 'Error al registrar !!',
        //                 showConfirmButton: false,
        //                 timer: 1500
        //             });
        //         } else {
        //             Swal.fire({
        //                 icon: 'success',
        //                 title: 'Apoderado registrado !!',
        //                 showConfirmButton: false,
        //                 timer: 1500
        //             });
        //             m1.removeClass('modal-show');
        //         }
        //     }
        // });
    });
    // BOTÓN MODAL REGISTRAR APODERADO /=========================




    // BTN PARA AGREGAR UN APODERADO TITULAR /===================   TRABAJAR EN SECCION
    $('#btn_me_agregar_titular').click(function(e) {
        e.preventDefault();
        prepareModalApoderado(modal, modal_apoderado);
    });


    // BTN PARA AGREGAR UN APODERADO SUPLENTE /==================   TRABAJAR EN SECCION
    $('#btn_me_agregar_suplente').click(function(e) {
        e.preventDefault();
        console.log("agregar suplente");
    });
    // BTN PARA AGREGAR UN APODERADO SUPLENTE /==================








    // BOTÓN MODAL CANCELAR DE ESTUDIANTE Y APODERADO /==========   LISTO
    $('#btn_modal_cancelar_estudiante').click(function(e) {
        e.preventDefault();
        modal.removeClass('modal-show');
    });

    $('#btn_modal_cancelar_apoderado').click(function(e) {
        e.preventDefault();
        modal_apoderado.removeClass('modal-show');
        modal.addClass('modal-show');
    });
    // BOTÓN MODAL CANCELAR DE ESTUDIANTE Y APODERADO /==========







    // FUNCION PARA GENERAR INFORMCION ADICIONAL /===============   LISTO
    function format(d) {
        let sexo;

        if (d.sexo_estudiante == 'F') {
            sexo = 'Femenina';
        } else {
            sexo = 'Masculino';
        }

        return (
            '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
                '<tr>' +
                    '<td>Número matrícula:</td>' +
                    '<td>' + d.numero_matricula + '</td>' +
                '</tr>' +

                '<tr>' +
                    '<td>Nombre social:</td>' +
                    '<td>' + d.nombre_social_estudiante + '</td>' +
                '</tr>' +

                '<tr>' +
                    '<td>Sexo estudiante:</td>' +
                    '<td>' + sexo + '</td>' +
                '</tr>' +

                '<tr>' +
                    '<td>Fecha Nacimiento:</td>' +
                    '<td>' + d.fecha_nacimiento_estudiante + '</td>' +
                '</tr>' +

                '<tr>' +
                    '<td>Apoderado titular:</td>' +
                    '<td>' + d.apoderado_titular + '</td>' +
                '</tr>' +

                '<tr>' +
                    '<td>Apoderado: suplente</td>' +
                    '<td>' + d.apoderado_suplente + '</td>' +
                '</tr>' +
            '</table>'
        );
    }
    // FUNCION PARA GENERAR INFORMCION ADICIONAL /===============




    // DATATABLE /===============================================   LISTO
    let tabla_estudiantes = $('#estudiantes').DataTable({
        // processing: true,  // PARA MOSTRAR CIRCULOS DE PROCESAMIENTO
        ajax: {
            url: "./controller/controller_estudiante.php",
            method: "post",
            dateType: "json",
            data: {datos: datos}
         },
         columns: [ // INFORMACIÓN DE COLUMNAS
            { // BTN PARA EXPANDIR Y CONTRAER TABLA
                className: 'dt-control',
                orderable: false,
                data: null,
                defaultContent: '',
            },
            { // ID DEL ESTUDIANTE QUE NO ES VISIBLE
                data: "id_estudiante",
                visible: false
            },
            {data: "rut_estudiante"}, // CELDA CONVINADA POR CONSULTA SQL
            {data: "apellido_paterno_estudiante"},
            {data: "apellido_materno_estudiante"},
            {data: "nombres_estudiante"},
            {data: "curso"},
            {
                data: 'id_estado',
                bSortable: false,
                mRender: function(data) {
                    let btn_estado;
                    if (data == 1) {
                        btn_estado = `<button class="btn btn-s btn-success" id="btn_editar_estado" type="button"><i class="fas fa-lock-open"></i></button>`;
                    } else if (data == 4) {
                        btn_estado = `<button class="btn btn-s btn-delete" id="btn_editar_estado" type="button"><i class="fas fa-ban"></i></button>`;
                    } else if (data == 4) {
                        btn_estado = `<button class="btn btn-s btn-lock" id="btn_editar_estado" type="button"><i class="fas fa-lock"></i></button>`;
                    }

                    return btn_estado;
                }
            },
            {data: null,
                bSortable: false,
                defaultContent: // BOTONES
                                `<button class="btn btn-s btn-data" id="btn_editar_estudiante" type="button"><i class="fas fa-pencil-alt"></i></button>
                                <button class="btn btn-s btn-delete" id="btn_eliminar_estudiante" type="button"><i class="fas fa-trash-alt"></i></button>`
            }
        ],
        order: [[3, 'asc']],
        language: spanish
    });
    // DATATABLE /===============================================





    // EVENTO PARA EXPANDIR TABLA CUANDO SE PRESIONA BTN /=======   LISTO
    $('#estudiantes tbody').on('click', 'td.dt-control', function () {
        let tr = $(this).closest('tr');
        let row = tabla_estudiantes.row(tr);
 
        if (row.child.isShown()) {
            // ACCIÓN PARA CUANDO SE CONTRAE LA TABLA
            row.child.hide();
            tr.removeClass('shown');

        } else {
            // ACCIÓN PARA CUANDO SE EXPANDE LA TABLA
            row.child(format(row.data())).show();
            tr.addClass('shown');
    
        }
    });
    // EVENTO PARA EXPANDIR TABLA CUANDO SE PRESIONA BTN /=======




    // EDITAR UN ESTUDIANTE /====================================   TRABAJAR SECCION
    // $('#estudiante tbody')
    // EDITAR UN ESTUDIANTE /====================================




    // ESTADOS DE UN ESTUDIANTE /================================    TERMINAR DE TRABAJAR FUNCION
    $('#estudiantes tbody').on('click', '#btn_editar_estado', function() {
        let data = tabla_estudiantes.row($(this).parents()).data();
        id_estudiante = data.id_estudiante;
        estado = data.id_estado;
        datos = "editar_estado";

        if (estado == 4) {
            alertPopUp('error', 'El estudiante esta retirado');
            return false;
        } else {
            console.log("alumno para suspender o retirar");
        }

        // $.ajax({
        //     url: "./controller/controller_estudiante.php",
        //     method: "post",
        //     dataType: "json",
        //     data: {id_estudiante: id_estudiante, estado: estado, datos: datos},
        //     success: function(data) {
        //         if (data === false) {
        //             Swal.fire({
        //                 position: 'top-end',
        //                 icon: 'error',
        //                 title: 'No se pudo desactivar al estudiante',
        //                 toast: true,
        //                 showConfirmButton: false,
        //                 timer: 2000,
        //                 timerProgressBar: true
        //             });
        //         } else {
        //             if (estado == 1) {
        //                 Swal.fire({
        //                     position: 'top-end',
        //                     icon: 'warning',
        //                     title: 'Estudiante Suspendido !!',
        //                     toast: true,
        //                     showConfirmButton: false,
        //                     timer: 2000,
        //                     timerProgressBar: true
        //                 });
        //             } else {
        //                 Swal.fire({
        //                     position: 'top-end',
        //                     icon: 'success',
        //                     title: 'Estudiante reitegrado',
        //                     toast: true,
        //                     showConfirmButton: false,
        //                     timer: 2000,
        //                     timerProgressBar: true
        //                 });
        //             }
        //             tabla_estudiantes.ajax.reload(null, false);
        //         }
        //     }
        // });
    });
    // ESTADOS DE UN ESTUDIANTE /================================    


    // ELIMINAR UN ESTUDIANTE /==================================   LISTO
    $('#estudiantes tbody').on('click', '#btn_eliminar_estudiante', function() {
        let data  = tabla_estudiantes.row($(this).parents()).data();
        id_estudiante = data.id_estudiante;
        nombres = data.nombres_estudiante + " " + data.apellido_paterno_estudiante + " " + data.apellido_materno_estudiante;
        Swal.fire({
            icon: 'question',
            title: 'Se eliminará al estudiante \n "' + nombres + '"',
            showCancelButton: true,
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#2691d9',
            cancelButtonColor: '#adadad'
        }).then(resultado => {
            if (resultado.isConfirmed) {
                datos = "eliminar_estudiante";

                $.ajax({
                    url: './controller/controller_estudiante.php',
                    type: 'post',
                    dataType: 'json',
                    data: {id_estudiante: id_estudiante, datos: datos},
                    success: function(data) {
                        if (data === false) {
                            alertPopUp('error', 'Error al eliminar estudiante !!');
                        } else {
                            alertPopUp('success', 'Estudiante eliminado');
                            tabla_estudiantes.ajax.reload(null, false);
                        }
                    }
                });
            } 
        });
    });


    // CARGAR LETRA DE GRADO EN MODAL /==========================   LISTO
    $('#estudiante_grado').change(function() {
        let grado = $(this).val();
        let funcion = 'cargar_letras';

        if (grado == 'Grado') {
            $("#estudiante_letra").html('');
        } else {
            $.ajax({
                url: './controller/controller_curso.php',
                type: 'post',
                dataType: 'json',
                data: { grado: grado, funcion: funcion},
                success: function(data) {
                    $('#estudiante_letra').html(data);
                }
            });
        }
    });
    
})


let spanish = {
    "aria": {
        "sortAscending": ": orden ascendente",
        "sortDescending": ": orden descendente"
    },
    "autoFill": {
        "cancel": "Cancelar",
        "fill": "Llenar todas las celdas con <i>%d&lt;\\\/i&gt;<\/i>",
        "fillHorizontal": "Llenar celdas horizontalmente",
        "fillVertical": "Llenar celdas verticalmente"
    },
    "buttons": {
        "collection": "Colección <span class=\"ui-button-icon-primary ui-icon ui-icon-triangle-1-s\"><\/span>",
        "colvis": "Visibilidad de la columna",
        "colvisRestore": "Restaurar visibilidad",
        "copy": "Copiar",
        "copyKeys": "Presiona ctrl or u2318 + C para copiar los datos de la tabla al portapapeles.<br \/><br \/>Para cancelar, haz click en este mensaje o presiona esc.",
        "copySuccess": {
            "_": "Copió %ds registros al portapapeles",
            "1": "Copió un registro al portapapeles"
        },
        "copyTitle": "Copiado al portapapeles",
        "csv": "CSV",
        "excel": "Excel",
        "pageLength": {
            "_": "Mostrar %ds registros",
            "-1": "Mostrar todos los registros"
        },
        "pdf": "PDF",
        "print": "Imprimir"
    },
    "datetime": {
        "amPm": [
            "AM",
            "PM"
        ],
        "hours": "Horas",
        "minutes": "Minutos",
        "months": {
            "0": "Enero",
            "1": "Febrero",
            "10": "Noviembre",
            "11": "Diciembre",
            "2": "Marzo",
            "3": "Abril",
            "4": "Mayo",
            "5": "Junio",
            "6": "Julio",
            "7": "Agosto",
            "8": "Septiembre",
            "9": "Octubre"
        },
        "next": "Siguiente",
        "previous": "Anterior",
        "seconds": "Segundos",
        "weekdays": [
            "Dom",
            "Lun",
            "Mar",
            "Mie",
            "Jue",
            "Vie",
            "Sab"
        ]
    },
    "decimal": ",",
    "editor": {
        "close": "Cerrar",
        "create": {
            "button": "Nuevo",
            "submit": "Crear",
            "title": "Crear nuevo registro"
        },
        "edit": {
            "button": "Editar",
            "submit": "Actualizar",
            "title": "Editar registro"
        },
        "error": {
            "system": "Ocurrió un error de sistema (&lt;a target=\"\\\" rel=\"nofollow\" href=\"\\\"&gt;Más información)."
        },
        "multi": {
            "info": "Los elementos seleccionados contienen diferentes valores para esta entrada. Para editar y configurar todos los elementos de esta entrada con el mismo valor, haga clic o toque aquí, de lo contrario, conservarán sus valores individuales.",
            "noMulti": "Esta entrada se puede editar individualmente, pero no como parte de un grupo.",
            "restore": "Deshacer cambios",
            "title": "Múltiples valores"
        },
        "remove": {
            "button": "Eliminar",
            "confirm": {
                "_": "¿Está seguro de que desea eliminar %d registros?",
                "1": "¿Está seguro de que desea eliminar 1 registro?"
            },
            "submit": "Eliminar",
            "title": "Eliminar registro"
        }
    },
    "emptyTable": "Sin registros",
    "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
    "infoEmpty": "Mostrando 0 a 0 de 0 registros",
    "infoFiltered": "(filtrado de _MAX_ registros)",
    "infoThousands": ".",
    "lengthMenu": "Mostrar _MENU_ registros",
    "loadingRecords": "Cargando...",
    "paginate": {
        "first": "Primero",
        "last": "Último",
        "next": "Siguiente",
        "previous": "Anterior"
    },
    "processing": "Procesando...",
    "search": "Buscar:",
    "searchBuilder": {
        "add": "Agregar Condición",
        "button": {
            "_": "Filtros (%d)",
            "0": "Filtrar"
        },
        "clearAll": "Limpiar Todo",
        "condition": "Condición",
        "conditions": {
            "array": {
                "contains": "Contiene",
                "empty": "Vacío",
                "equals": "Igual",
                "not": "Distinto",
                "notEmpty": "No vacío",
                "without": "Sin"
            },
            "date": {
                "after": "Mayor",
                "before": "Menor",
                "between": "Entre",
                "empty": "Vacío",
                "equals": "Igual",
                "not": "Distinto",
                "notBetween": "No entre",
                "notEmpty": "No vacío"
            },
            "number": {
                "between": "Entre",
                "empty": "Vacío",
                "equals": "Igual",
                "gt": "Mayor",
                "gte": "Mayor o igual",
                "lt": "Menor",
                "lte": "Menor o igual",
                "not": "Distinto",
                "notBetween": "No entre",
                "notEmpty": "No vacío"
            },
            "string": {
                "contains": "Contiene",
                "empty": "Vacío",
                "endsWith": "Termina con",
                "equals": "Igual",
                "not": "Distinto",
                "notEmpty": "No vacío",
                "startsWith": "Comienza con"
            }
        },
        "data": "Datos",
        "deleteTitle": "Eliminar regla de filtrado",
        "leftTitle": "Filtros anulados",
        "logicAnd": "Y",
        "logicOr": "O",
        "rightTitle": "Filtro",
        "title": {
            "_": "Filtros (%d)",
            "0": "Filtrar"
        },
        "value": "Valor"
    },
    "searchPanes": {
        "clearMessage": "Limpiar todo",
        "collapse": {
            "_": "Paneles de búsqueda (%d)",
            "0": "Paneles de búsqueda"
        },
        "count": "{total}",
        "countFiltered": "{shown} ({total})",
        "emptyPanes": "Sin paneles de búsqueda",
        "loadMessage": "Cargando paneles de búsqueda...",
        "title": "Filtros activos - %d"
    },
    "select": {
        "cells": {
            "_": "%d celdas seleccionadas",
            "1": "Una celda seleccionada"
        },
        "columns": {
            "_": "%d columnas seleccionadas",
            "1": "Una columna seleccionada"
        }
    },
    "thousands": ".",
    "zeroRecords": "No se encontraron registros"
}



// TRABAJAR EN LAS VALIDADCIONES !!!!!!!
// TRABAJAR EN OPTIMIZAR EL CÓDIGO DE APODERADOS, EN FUNCIÓN DEL DE ESTUDIANTES !!!!!!