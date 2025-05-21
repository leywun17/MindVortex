$(document).ready(function() {
    $("#updateProfileForm").on("submit", function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        console.log(...formData);
        
        const newPassword = $("input[name='new_password']").val();
        const confirmPassword = $("input[name='confirm_password']").val();
        
        if (newPassword && newPassword !== confirmPassword) {
            alert("Las contraseñas no coinciden.");
            return;
        }

        $.ajax({
            url: "../Backend/updateProfile.php",  
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
                        location.reload();
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

