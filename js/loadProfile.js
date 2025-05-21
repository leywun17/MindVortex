$(document).ready(function () {
    function loadUserProfileData() {
        $.ajax({
            url: '../Backend/updateProfile.php?action=get_user',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success && response.user) {
                    const imagePath = `${response.user.userImage || 'default.jpg'}`;

                    $(".userProfileImage").attr("src", imagePath);
                    $(".userNameDisplay").text(response.user.userName);
                    $(".userEmailDisplay").text(response.user.email);
                } else {
                    console.warn("No se pudo cargar la información del usuario.");
                }
            },
            error: function () {
                console.error("Error al obtener los datos del perfil.");
            }
        });
    }

    loadUserProfileData();
});
