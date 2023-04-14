/* ------------------------- style ---------------------- */
const inputs = document.querySelectorAll('.input');

function focusFunc() {
    let parent = this.parentNode.parentNode;
    parent.classList.add('focus');
}

function blurFunc() {
    let parent = this.parentNode.parentNode;
    if (this.value == "") {
        parent.classList.remove('focus');
    }
}

inputs.forEach(input => {
    input.addEventListener('focus', focusFunc);
    input.addEventListener('blur', blurFunc);
});
/* ------------------------- style ---------------------- */


/* ------------------------- Funcionaes Internas ---------------------- */
//Función para cambio de contraseña
async function updatePasword() {

    const { value: password1 } = Swal.fire({
        title: 'Crea tu password personal !!',
        html:
            `<input id="password_1" class="swal2-input" type="password" placeholder="Ingrese password" maxlength="10">
            <input id="password_2" class="swal2-input" type="password" placeholder="Ingrese password nuevamente" maxlength="10">
            <div class="text-secundary">Contraseña no superior a 10 digitos.</div>`,
        focusConfirm: false,
        showCancelButton: false,
        preConfirm: () => {
          const password1 = Swal.getPopup().getElementById('password_1').value;
          const password2 = Swal.getPopup().getElementById('password_2').value;
          if (password1 !== password2) {
            Swal.showValidationMessage('La password ingresada no coincide !!');
          }
          return { password1, password2 };
        },
        didOpen: () => {
          document.getElementById('password_1').focus();
        },
      });
      
    console.log(password1);





    

    // $.ajax ({
    //     url: './controller/controller_login.php',
    //     type: 'post',
    //     dataType: 'json',
    //     data: { datos: 'updatePassword'},
    //     success: (response) => {
    //         if (response.data == false) {
    //             Swal.fire({
    //                 icon: 'error',
    //                 title: 'Usuario o Clave incorrectos',
    //                 showConfirmButton: false,
    //                 timer: 1500
    //             });
    //             return false;
    //         } 

    //         if (response.fecha_ingreso == null) {

    //             return false;
    //         }

    //             // Trabajando cambio de clave
    //             // if (response.fecha_ingreso == null) {
    //             //     console.log("Cambio automático de clave");
    //             //     return false;
    //             // }


    //         Swal.fire({
    //             icon: 'success',
    //             title: 'Ingresando al sistema .....!',
    //             showConfirmButton: false,
    //             timer: 1500
    //         }).then(result => {
    //             window.location.href = 'home';
    //         });

    //     }
    // }).fail (() => {
    //     Swal.fire({
    //         icon: 'error',
    //         title: 'Sin conexion con BBDD .....!',
    //         showConfirmButton: false,
    //         timer: 1500
    //     });
    // });
}
/* ------------------------- Funcionaes Internas ---------------------- */



/* ----------------------- backend ---------------------- */
 $(document).ready(function() {
    $('#id_form_login').submit(function(e) {
        e.preventDefault();

        // Captación de las variables
        let usuario = $.trim($("#id_usuario").val());
        let clave = $.trim($("#id_clave").val());

        // Comprobaciones de valor
        if (usuario.length <= 0 || clave.length <=0) {
            Swal.fire({
                icon: 'warning',
                title: 'Faltan datos importantes ..!!',
                allowOutsideClick: false,
                showConfirmButton: false,
                timer: 1500
            });

        } else {
            $.ajax ({
                url: './controller/controller_login.php',
                type: 'post',
                dataType: 'json',
                data: { usuario: usuario, clave: clave},
                success: (response) => {
                    if (response.data == false) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Usuario o Clave incorrectos',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        return false;
                    } 

                    if (response.fecha_ingreso == null) {
                        updatePasword();
                        return false;
                    }

                        // Trabajando cambio de clave
                        // if (response.fecha_ingreso == null) {
                        //     console.log("Cambio automático de clave");
                        //     return false;
                        // }


                    Swal.fire({
                        icon: 'success',
                        title: 'Ingresando al sistema .....!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(result => {
                        window.location.href = 'home';
                    });

                }
            }).fail (() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Sin conexion con BBDD .....!',
                    showConfirmButton: false,
                    timer: 1500
                });
            });
            $('#id_form_login').trigger('reset');
            $('.input-div').removeClass('focus');
        }
    });
 });


