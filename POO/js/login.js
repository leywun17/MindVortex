$(document).ready(function () {
    $("#loginForm").submit(function (event) {
        event.preventDefault(); // Evita el envío tradicional del formulario

        // Obtener los valores de los campos de entrada
        var email = $.trim($('input[name="email"]').val()); // Eliminar espacios en blanco al inicio y al final
        var password = $.trim($('input[name="password"]').val());

        // Sanitizar datos antes de enviarlos (opcional, solo como precaución adicional)
        password = encodeURIComponent(password);

        // Llamar a la función para iniciar sesión
        loginUser(email, password);
    });
});

function loginUser(email, password) {
    $.ajax({
        url: "../Backend/Login.php", // Asegúrate de que esta es la ruta correcta
        method: "POST",
        data: { email: email, password: password },
        dataType: "json",
        success: function (data) {
            if (data.status === "success") {
                Swal.fire({
                    icon: "success",
                    title: "Inicio de sesión exitoso",
                    text: "Bienvenido de nuevo",
                    confirmButtonText: "Continuar"
                }).then(() => {
                    window.location.href = "../Views/dashboard.php"; // Cambia a la vista crrecta
                    console.log("DAtos", data);
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: data.message
                });
            }
        },
        error: function () {
            Swal.fire({
                icon: "error",
                title: "Error inesperado",
                text: "No se pudo iniciar sesión. Inténtalo de nuevo."
            });
        }
    });
}