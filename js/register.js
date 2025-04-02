$(document).ready(function () {
    $(".sign").on("click", function (event)  {
        console.log("hola");
        event.preventDefault(); // Evita el comportamiento predeterminado del botón
        console.log("Script cargado");

      8  // Obtener los valores de los campos
        let name = $.trim($('input[name="name"]').val());
        let email = $.trim($('input[name="email"]').val());
        let password = $.trim($('input[name="password"]').val());
        let confirmPassword = $.trim($('input[name="confirmPassword"]').val());

        // Validar que los campos no estén vacíos
        if (name === "" || email === "" || password === "" || confirmPassword === "") {
            Swal.fire({
                icon: "warning",
                title: "Campos vacíos",
                text: "Todos los campos son obligatorios."
            });
            return;
        }

        // Validar que las contraseñas coincidan
        if (password !== confirmPassword) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Las contraseñas no coinciden."
            });
            return;
        }

        // Crear objeto FormData para enviar los datos
        let formData = new FormData();
        formData.append("name", name);
        formData.append("email", email);
        formData.append("password", password);
        formData.append("confirmPassword", confirmPassword);

        // Enviar datos mediante AJAX
        registerUser(formData);
    });
});

function registerUser(formData) {
    $.ajax({
        url: "../Backend/Register.php", // Ruta del backend
        method: "POST",
        data: formData,
        processData: false,  // Necesario para FormData
        contentType: false,  // Necesario para FormData
        dataType: "json", // Espera una respuesta en JSON
        success: function (data) {
            if (data.status === "success") {
                Swal.fire({
                    icon: "success",
                    title: "Registro exitoso",
                    text: data.message,
                    confirmButtonText: "Ir al login"
                }).then(() => {
                    window.location.href = "../Views/login.php"; // Redirigir al login
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
