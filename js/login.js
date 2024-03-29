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
async function newPassword(id_usuario) {
    let datos = "newPassword";

    const { value: newPassword } = await Swal.fire({
        title: 'Crea tu password personal !!',
        customClass: {
            title: 'titulo_cambio_clave',
        },
        html:
            `<input id="password_1" class="swal2-input" type="password" placeholder="Ingrese password">
            <input id="password_2" class="swal2-input" type="password" placeholder="Ingrese password nuevamente">
            <div class="text-secundary">Contraseña no superior a 10 digitos.</div>`,
        focusConfirm: false,
        showCancelButton: false,
        allowOutsideClick: false,
        confirmButtonText: 'Aceptar',
        confirmButtonColor: '#2c6cff',
        preConfirm: () => {
            const password1 = $.trim($("#password_1").val());
            const password2 = $.trim($("#password_2").val());

            if (password1 !== password2) { Swal.showValidationMessage('La password ingresada no coincide !!'); }

            return { password1, password2 };
        }
    });
    
    newPassword.id_usuario = id_usuario;
      
    $.ajax({
        url: './controller/controller_login.php',
        type: 'post',
        dataType: 'json',
        data: {datos: datos, password: newPassword},
        success: (response) => {
            if (response == false) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Error de creación !!',
                    showConfirmButton: false,
                    timer: 1500
                });
                return false;
            }

            Swal.fire({
                icon: 'success',
                title: 'Clave creada con éxito !!',
                showConfirmButton: false,
                timer: 1500
            });
        }
    }).fail (() => {
        Swal.fire({
            icon: 'error',
            title: 'Error al crear password !!',
            showConfirmButton: false,
            timer: 1500
        });
    });

}

function verifyUsser() {
    $('#id_form_login').submit((e) => {
        e.preventDefault();
        let datos = "login";
        const cuentaUsuario = {
            // Captación de las variables
            usuario: $.trim($("#id_usuario").val()),
            clave: $.trim($("#id_clave").val())
        }

        // Comprobaciones de valor
        if (cuentaUsuario.usuario.length <= 0 || cuentaUsuario.clave.length <=0) {
            Swal.fire({
                icon: 'warning',
                title: 'Faltan datos importantes ..!!',
                allowOutsideClick: false,
                showConfirmButton: false,
                timer: 1500
            });
            return false;
        } 

        $.ajax ({
            url: './controller/controller_login.php',
            type: 'post',
            dataType: 'json',
            data: { datos: datos, cuentaUsuario: cuentaUsuario },
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
                
                if (response.status !== 1) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cuenta de usuario; SUSPENDIDA !!'
                    });
                    return false;
                }

                if (response.fecha_ingreso == null) {
                    newPassword(response.id_usuario);
                    return false;
                }

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
    });
}
/* ------------------------- Funcionaes Internas ---------------------- */



/* ----------------------- backend ---------------------- */
 $(document).ready(function() {

    verifyUsser();

 });


