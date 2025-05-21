$(document).ready(function() {
    // Maneja la lógica de envío del formulario de edición
    $("#updateProfileForm").on("submit", function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        console.log(...formData);
        
        // Lógica para verificar que la contraseña coincida
        const newPassword = $("input[name='new_password']").val();
        const confirmPassword = $("input[name='confirm_password']").val();
        
        if (newPassword && newPassword !== confirmPassword) {
            alert("Las contraseñas no coinciden.");
            return;
        }

        // Llama a la API para actualizar los datos del perfil
        $.ajax({
            url: "../Backend/updateProfile.php",  // Cambia esto por el URL real de tu backend
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Perfil actualizado',
                        text: 'Tu perfil ha sido actualizado con éxito.',
                    }).then(() => {
                        // Puedes recargar la página o actualizar la vista
                        /* location.reload(); */
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Hubo un error al actualizar tu perfil.',
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error en la comunicación con el servidor.',
                });
            }
        });
    });

    // Habilitar/Deshabilitar el botón de actualizar imagen cuando se selecciona un archivo
    $("#profileImageInput").on("change", function() {
        const file = this.files[0];
        if (file) {
            $("#uploadImageBtn").prop("disabled", false);
            const reader = new FileReader();
            reader.onload = function(e) {
                $("#imagePreview").attr("src", e.target.result);
            };
            reader.readAsDataURL(file);
        } else {
            $("#uploadImageBtn").prop("disabled", true);
        }
    });
});

$(document).ready(function () {
    $("#profileImageInput").on("change", function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $(".userProfileImage").attr("src", e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });
});

