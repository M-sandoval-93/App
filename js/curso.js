$(document).ready(function () {

    // PARA CONSULTA Y CREACIÓN DE LETRAS DEL GRADO ---------- //
    $('.edit').click(function(e) {
        e.preventDefault();
        let grado = $(this).attr('id');
        let funcion = 'consulta'

        $.ajax({
            url: "./controller/controller_curso.php",
            method: "post",
            dataType: "json",
            data: { grado: grado, funcion: funcion},
            success: function(data) {
                // SE VALIDA SI SE HAN CREADO LOS CURSOS PARA EL GRADO
                if (data === false) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Los cursos ya han sido creados !',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }


                // revisar funcionamiento luego de borrar los cursos
                // } else {
                //     // SE CREAN LAS LETRAS DEL GRADO
                //     Swal.fire({
                //         title: 'Hasta que letra se creará el curso ?',
                //         html: `
                //             <form method="post" id="form_crearCurso">
                //                 <input type="text" id="id_letra" placeholder="Hasta que letra es creado?">
                //             </form>`,
                //         focusConfirm: false,
                //         showCancelButton: true,
                //         confirmButtonText: 'Crear',
                //         cancelButtonText: 'Cancelar',
                //         confirmButtonColor: '#2691d9',
                //         cancelButtonColor: '#adadad',
                //         preConfirm: () => {
            
                //             if (!$.trim($('#id_letra').val())) {
                //                 Swal.showValidationMessage('Debe ingresar la letra hasta la cual se crean los cursos');
                //             } else {
                //                 let letra = $.trim($('#id_letra').val());
            
                //                 $.ajax({
                //                     url: './controller/controller_cursos.php',
                //                     type: 'post',
                //                     datatype: 'json',
                //                     data: { grado: grado, letra: letra},
                //                     success: function(data) {
                //                         if (data == 'false') {
                //                             Swal.fire({
                //                                 icon: 'error',
                //                                 title: 'Error al crear el grado',
                //                                 showConfirmButton: false,
                //                                 timer: 1500
                //                             });
                //                         } else {
                //                             Swal.fire({
                //                                 position: 'top-end',
                //                                 icon: 'success',
                //                                 title: 'Se crearon los cursos para el grado ' + grado + '!!',
                //                                 toast: true,
                //                                 showConfirmButton: false,
                //                                 timer: 2000,
                //                                 timerProgressBar: true
                //                             });
                //                         }
                //                     }
                //                 });
                //             }
                //         }
                //     });
                // }
            }
        });
    });
    // PARA CONSULTA Y CREACIÓN DE LETRAS DEL GRADO ---------- //


    // PARA MOSTRAR LAS LETRAS DEL GRADO Y LOS ESTUDIANTES POR CURSO ---------- //
    $('.card').click(function(e) {
        e.preventDefault();

        $('.card .edit').click(function(e) {
            e.stopPropagation();
        });

        let grado = $(this).attr('value');

        console.log("se presionó el card" + grado);
    });
    // PARA MOSTRAR LAS LETRAS DEL GRADO Y LOS ESTUDIANTES POR CURSO ---------- //






});
