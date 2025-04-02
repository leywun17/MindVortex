$(document).ready(function() {
    // Password Change Form Submission
    $('#changePasswordForm').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        // Get form values
        let currentPassword = $('input[name="current_password"]').val();
        let newPassword = $('input[name="new_password"]').val();
        let confirmPassword = $('input[name="confirm_password"]').val();

        // Basic client-side validation
        if (!currentPassword || !newPassword || !confirmPassword) {
            Swal.fire({
                icon: "warning",
                title: "Campos vacíos",
                text: "Por favor, complete todos los campos."
            });
            return;
        }

        if (newPassword !== confirmPassword) {
            Swal.fire({
                icon: "error", 
                title: "Error",
                text: "Las contraseñas nuevas no coinciden."
            });
            return;
        }

        // AJAX request to change password
        $.ajax({
            url: '../Backend/update-user.php', // Updated endpoint
            method: 'POST',
            data: {
                action: 'change_password',
                current_password: currentPassword,
                new_password: newPassword
            },
            dataType: "json",
            beforeSend: function() {
                $('#changePasswordBtn')
                    .html('<i class="fas fa-spinner fa-spin"></i> Cambiando...')
                    .prop('disabled', true);
            },
            success: function(response) {
                if (response.status === "success") {
                    Swal.fire({
                        icon: "success",
                        title: "Contraseña cambiada",
                        text: response.message
                    });
                    $('#changePasswordForm')[0].reset();
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: "error",
                    title: "Error inesperado",
                    text: "No se pudo cambiar la contraseña. Inténtalo de nuevo."
                });
            },
            complete: function() {
                $('#changePasswordBtn')
                    .html('Cambiar contraseña')
                    .prop('disabled', false);
            }
        });
    });

    // Image Upload Form
    $('#profileImageInput').on('change', function() {
        let file = this.files[0];
        let uploadBtn = $('#uploadImageBtn');
        
        // Enable upload button only when a file is selected
        uploadBtn.prop('disabled', !file);

        // Image preview
        if (file) {
            let reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview')
                    .attr('src', e.target.result)
                    .show();
            };
            reader.readAsDataURL(file);
        }
    });

    $('#uploadImageForm').on('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);
        formData.append('action', 'upload_image');

        $.ajax({
            url: '../Backend/update-user.php', // Updated endpoint
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            xhr: function() {
                let xhr = new window.XMLHttpRequest();
                let progressBar = $('#uploadProgress');
                
                // Upload progress tracking
                xhr.upload.addEventListener('progress', function(evt) {
                    if (evt.lengthComputable) {
                        let percentComplete = evt.loaded / evt.total * 100;
                        progressBar
                            .val(percentComplete)
                            .show();
                    }
                }, false);

                return xhr;
            },
            beforeSend: function() {
                $('#uploadImageBtn')
                    .html('<i class="fas fa-spinner fa-spin"></i> Subiendo...')
                    .prop('disabled', true);
            },
            success: function(response) {
                if (response.status === "success") {
                    Swal.fire({
                        icon: "success",
                        title: "Imagen subida",
                        text: response.message,
                        target: 'body', // Se añade directamente al body
                        backdrop: 'rgba(0, 0, 0, 0.4)', // Fondo semitransparente para la alerta
                        customClass: {
                            popup: 'my-swal-popup' // Clase personalizada para el popup
                        }
                    });
                    $('#uploadProgress').hide();
                    $('#imagePreview').hide();
                    $('#uploadImageForm')[0].reset();
                    $('#uploadImageBtn').prop('disabled', true);
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: response.message,
                        target: 'body', // Se añade directamente al body
                        backdrop: 'rgba(0, 0, 0, 0.4)', // Fondo semitransparente para la alerta
                        customClass: {
                            popup: 'my-swal-popup' // Clase personalizada para el popup
                        }
                    });
                    $('#uploadProgress').hide();
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: "error",
                    title: "Error inesperado",
                    text: "No se pudo subir la imagen. Inténtalo de nuevo.",
                    target: 'body', // Se añade directamente al body
                    backdrop: 'rgba(0, 0, 0, 0.4)', // Fondo semitransparente para la alerta
                    customClass: {
                        popup: 'my-swal-popup' // Clase personalizada para el popup
                    }
                });
                $('#uploadProgress').hide();
            },
            complete: function() {
                $('#uploadImageBtn')
                    .html('Subir imagen')
                    .prop('disabled', false);
            }
        });
    });

    // Update Profile Form
    $("#updateProfileForm").submit(function (event) {
        event.preventDefault();

        // Obtener los valores de los campos
        let name = $.trim($('input[name="name"]').val());
        let email = $.trim($('input[name="email"]').val());
        let descripcion = $.trim($('textarea[name="descripcion"]').val());

        if (name === "" || email === "" || descripcion === "") {
            Swal.fire({
                icon: "warning",
                title: "Campos vacíos",
                text: "Por favor completa todos los campos antes de actualizar.",
                target: 'body', // Se añade directamente al body
                backdrop: 'rgba(0, 0, 0, 0.4)', // Fondo semitransparente para la alerta
                customClass: {
                    popup: 'my-swal-popup' // Clase personalizada para el popup
                }
            });
            return;
        }

        // Enviar datos al backend
        updateUserInfo(name, email, descripcion);
    });

    // Función para actualizar información del usuario
    function updateUserInfo(name, email, descripcion) {
        $.ajax({
            url: "../Backend/update-user.php",
            type: "POST",
            data: {
                action: "update_info",
                name: name,
                email: email,
                descripcion: descripcion
            },
            dataType: "json",
            beforeSend: function () {
                $("#updateProfileBtn").html('<i class="fas fa-spinner fa-spin"></i> Actualizando...').prop('disabled', true);
            },
            success: function (response) {
                console.log("Respuesta del servidor:", response); // Log para depuración

                if (response.status === "success") {
                    Swal.fire({
                        icon: "success",
                        title: "Actualización exitosa",
                        text: response.message,
                        confirmButtonText: "OK",
                        target: 'body', // Se añade directamente al body
                        backdrop: 'rgba(0, 0, 0, 0.4)', // Fondo semitransparente para la alerta
                        customClass: {
                            popup: 'my-swal-popup' // Clase personalizada para el popup
                        }
                    }).then(() => {
                        $(".info-user li:nth-child(1)").text("Nombre: " + name);
                        $(".info-user li:nth-child(2)").text("Email: " + email);
                        $(".info-user li:nth-child(3)").text("Descripción: " + descripcion);
                        $("#editProfileModal").modal('hide');
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: response.message,
                        target: 'body', // Se añade directamente al body
                        backdrop: 'rgba(0, 0, 0, 0.4)', // Fondo semitransparente para la alerta
                        customClass: {
                            popup: 'my-swal-popup' // Clase personalizada para el popup
                        }
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error("Error AJAX:", xhr.responseText);
                Swal.fire({
                    icon: "error",
                    title: "Error inesperado",
                    text: "No se pudo actualizar la información. Inténtalo de nuevo.",
                    target: 'body', // Se añade directamente al body
                    backdrop: 'rgba(0, 0, 0, 0.4)', // Fondo semitransparente para la alerta
                    customClass: {
                        popup: 'my-swal-popup' // Clase personalizada para el popup
                    }
                });
            },
            complete: function () {
                $("#updateProfileBtn").html('Actualizar información').prop('disabled', false);
            }
        });
    }
});