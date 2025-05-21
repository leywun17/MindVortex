$(document).ready(function () {
    $("#loginForm").submit(function (event) {
        event.preventDefault();
        
        let email = $.trim($('input[name="email"]').val()); 
        let password = $.trim($('input[name="password"]').val());

        password = encodeURIComponent(password);

        loginUser(email, password);
    });
});

function loginUser(email, password) {
    $.ajax({
        url: "../Backend/Login.php", 
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
                    window.location.href = "../Views/dashboard.php";
                    console.log("DAtos", data);
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: data.message
                });
                console.log("DAtos", data);
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