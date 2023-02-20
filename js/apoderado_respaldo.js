
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
function generar_dv() {
    // Traspasar valor a número entero
    let numero = $('#apoderado_rut').val();
    numero = numero.split('.').join('');

    // Valida que sea realmente entero
    if (LibreriaFunciones.validarEntero(numero)) {
        $('#apoderado_dv_rut').val(LibreriaFunciones.dv(numero));

    } else {
        $('#apoderado_dv_rut').val('');
    }

    // Formatear el valor del rut con sus puntos
    $('#apoderado_rut').val(LibreriaFunciones.formatearNumero(numero));
}

function camposVacios() {
    const form = document.getElementById('modal_form_apoderados');
    const inputs = form.querySelectorAll('input[type="text"]');
    let contador = 0;

    inputs.forEach(elemento => {
        if (elemento.value === '') {
            contador = contador + 1;
        }
    });
    return contador;
}
// FUNCIONES ===============================================



$(document).ready(function() {

    // VARIABLES GLOBALES
    let modal = $('#modal_form_apoderados');
    let registrar;
    let id_apoderado;


    // BOTÓN NUEVO APODERADO /==================================
    $('#btn_nuevo_apoderado').click(function(e) {
        e.preventDefault();
        $('#form_apoderados').trigger('reset');
        $('#titulo-modal').text('Registrar nuevo apoderado');
        $('#apoderado_codigo_fono').val('569');
        
        modal.addClass('modal-show');
        $('#apoderado_rut').removeAttr('disabled', 'disable');
        $('#apoderado_dv_rut').attr('disabled', 'disabled');
        $('#apoderado_rut').focus();
        $('#apoderado_rut').keyup(generar_dv);
        $('#apoderado_rut').blur(generar_dv);

        registrar = 'nuevo_apoderado';
    });


    // BOTÓN MODAL REGISTRAR /==================================
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
        if (camposVacios() >= 1) {
            Swal.fire({
                icon: 'warning',
                title: 'Hay campos vacios !!',
                showConfirmButton: false,
                timer: 1500
            });
            return false;
        }

        if (registrar == 'nuevo_apoderado') {
            datos = "nuevo_apoderado";
            $.ajax({
                url: "./controller/controller_apoderado.php",
                method: "post",
                dataType: "json",
                data: {rut: rut, dv_rut: dv_rut, nombres: nombres, a_paterno: a_paterno,
                        a_materno: a_materno, fono: fono, datos: datos},
                success: function(data) {
                    if (data == 'existe') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'El apoderado ya existe !!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        if (data === false) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error al registrar !!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Apoderado registrado !!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            modal.removeClass('modal-show');
                            tabla_apoderados.ajax.reload(null, false);
                        }
                    }
                }
            });

        } else if(registrar == 'editar_apoderado') {
            datos = "editar_apoderado";
            $.ajax({
                url:"./controller/controller_apoderado.php",
                method: "post",
                dataType: "json",
                data: {rut: rut, nombres: nombres, a_paterno: a_paterno, a_materno: a_materno, fono: fono, datos: datos},
                success: function(data) {
                    if (data === false) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al actualizar !!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Datos actualizados !!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        modal.removeClass('modal-show');
                        tabla_apoderados.ajax.reload(null, false);
                    }
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Formulario inválido !!',
                showConfirmButton: false,
                timer: 1500
            });
        }
    });


    // BOTÓN MODAL CANCELAR /===================================
    $('#btn_modal_cancelar_apoderado').click(function(e) {
        e.preventDefault();
        modal.removeClass('modal-show');
    });


    // DATATABLE /==============================================
    datos = 'mostrar_apoderados';
    let tabla_apoderados = $('#apoderados').DataTable({
        "ajax": {
            "url": "./controller/controller_apoderado.php",
            "method": "post",
            "data": {datos: datos}
         },
         "columns": [ // INFORMACIÓN DE COLUMNAS
            {"data": "id_apoderado", "visible": false},
            {"data": "rut_apoderado"}, // CELDA CONVINADA POR CONSULTA SQL
            {"data": "apellido_paterno_apoderado"},
            {"data": "apellido_materno_apoderado"},
            {"data": "nombres_apoderado"},
            {"data": "telefono_apoderado"},
            {
                "data": 'estado_apoderado',
                "bSortable": false,
                "mRender": function(data) {
                    let btn_estado;
                    if (data === true) {
                        btn_estado = `<button class="btn btn-s btn-success" id="btn_editar_estado" type="button"><i class="fas fa-lock-open"></i></button>`;
                    } else {
                        btn_estado = `<button class="btn btn-s btn-lock" id="btn_editar_estado" type="button"><i class="fas fa-lock"></i></button>`;
                    }
                    return btn_estado;
                }
            },
            {"data": null,
                "bSortable": false,
                "defaultContent": // BOTONES
                                `<button class="btn btn-s btn-data" id="btn_editar_apoderado" type="button"><i class="fas fa-pencil-alt"></i></i></button>
                                <button class="btn btn-s btn-delete" id="btn_eliminar_apoderado" type="button"><i class="fas fa-trash-alt"></i></button>`
            }
        ],
        order: [[2, 'asc']],
        "language": spanish
    });


    // EDITAR UN APODERADO /====================================
    $('#apoderados tbody').on('click', '#btn_editar_apoderado', function() {
        let data = tabla_apoderados.row($(this).parents()).data();
        $('#form_apoderados').trigger('reset');
        $('#titulo-modal').text('Editar apoderado');
        $('#apoderado_rut').val(data.rut_apoderado.split('-')[0]);
        $('#apoderado_dv_rut').val(data.rut_apoderado.split('-')[1]);
        $('#apoderado_nombres').val(data.nombres_apoderado);
        $('#apoderado_ap_paterno').val(data.apellido_paterno_apoderado);
        $('#apoderado_ap_materno').val(data.apellido_materno_apoderado);
        $('#apoderado_codigo_fono').val(data.telefono_apoderado.split('-')[0]);
        $('#apoderado_fono').val(data.telefono_apoderado.split('-')[1]);
        

        modal.addClass('modal-show');
        $('#apoderado_rut').attr('disabled', 'disabled');
        $('#apoderado_dv_rut').attr('disabled', 'disabled');
        $('#apoderado_nombres').focus();
        registrar  = 'editar_apoderado';
        id_apoderado = data.id_apoderado;
    });


    // ACTIVAR O DESACTIVAR UN APODERADO /======================
    $('#apoderados tbody').on('click', '#btn_editar_estado', function() {
        let data = tabla_apoderados.row($(this).parents()).data();
        id_apoderado = data.id_apoderado;
        estado = data.estado_apoderado;
        datos = "editar_estado";

        $.ajax({
            url: "./controller/controller_apoderado.php",
            method: "post",
            dataType: "json",
            data: {id_apoderado: id_apoderado, estado: estado, datos: datos},
            success: function(data) {
                if (data === false) {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: 'No se pudo desactivar al apoderado',
                        toast: true,
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                } else {
                    if (estado === true) {
                        Swal.fire({
                            position: 'top-end',
                            icon: 'warning',
                            title: 'Apoderado bloqueado !!',
                            toast: true,
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                    } else {
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: 'Apoderado desbloqueado',
                            toast: true,
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                    }
                    tabla_apoderados.ajax.reload(null, false);
                }
            }
        });
    });


    // ELIMINAR UN APODERADO /==================================
    $('#apoderados tbody').on('click', '#btn_eliminar_apoderado', function() {
        let data  = tabla_apoderados.row($(this).parents()).data();
        id_apoderado = data.id_apoderado;
        nombres = data.nombres_apoderado + " " + data.apellido_paterno_apoderado + " " + data.apellido_materno_apoderado;
        Swal.fire({
            icon: 'question',
            title: 'Se eliminará al apoderado \n "' + nombres + '"',
            showCancelButton: true,
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#2691d9',
            cancelButtonColor: '#adadad'
        }).then(resultado => {
            if (resultado.isConfirmed) {
                datos = "eliminar_apoderado";

                $.ajax({
                    url: './controller/controller_apoderado.php',
                    type: 'post',
                    dataType: 'json',
                    data: {id_apoderado: id_apoderado, datos: datos},
                    success: function(data) {
                        if (data === false) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error al eliminar el apoderado !!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Apoderado eliminado !!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            tabla_apoderados.ajax.reload(null, false);
                        }
                    }
                });
            } 
        });
    });
});



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
