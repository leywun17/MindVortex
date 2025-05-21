$(document).ready(function () {
    $(".sign").on("click", function (event)  {
        console.log("hola");
        event.preventDefault(); 
        console.log("Script cargado");

        let name = $.trim($('input[name="name"]').val());
        let email = $.trim($('input[name="email"]').val());
        let password = $.trim($('input[name="password"]').val());
        let confirmPassword = $.trim($('input[name="confirmPassword"]').val());

        if (name === "" || email === "" || password === "" || confirmPassword === "") {
            Swal.fire({
                icon: "warning",
                title: "Campos vacíos",
                text: "Todos los campos son obligatorios."
            });
            return;
        }

        if (password !== confirmPassword) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Las contraseñas no coinciden."
            });
            return;
        }

        let formData = new FormData();
        formData.append("name", name);
        formData.append("email", email);
        formData.append("password", password);
        formData.append("confirmPassword", confirmPassword);

        registerUser(formData);
    });
});

function registerUser(formData) {
    $.ajax({
        url: "../Backend/Register.php",
        method: "POST",
        data: formData,
        processData: false, 
        contentType: false, 
        dataType: "json",
        success: function (data) {
            if (data.status === "success") {
                Swal.fire({
                    icon: "success",
                    title: "Registro exitoso",
                    text: data.message,
                    confirmButtonText: "Ir al login"
                }).then(() => {
                    window.location.href = "../Views/login.php";
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error en el registro",
                    text: data.message
                });
            }
        },
        error: function () {
            Swal.fire({
                icon: "error",
                title: "Error inesperado",
                text: "No se pudo completar el registro. Inténtalo de nuevo."
            });
        }
    });
}
