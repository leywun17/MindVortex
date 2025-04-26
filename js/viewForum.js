$(document).ready(function () {
    // Obtiene el parámetro "id" de la URL
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get("id");

    if (id) {
        // Llama a la API para leer un único foro
        $.ajax({
            url: `../Backend/foro.php`,
            type: "GET",
            data: { action: "read_one", id: id },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    const forum = response.forum;

                    // Poblamos los elementos con los nuevos IDs
                    $("#forumTitle").text(forum.title);
                    $("#forumDescription").text(forum.description);
                    $("#forumDate").text(forum.createdAt);
                    $("#forumAuthor").text(forum.userName);
                    $("#userImage").attr("src", "../uploads/profile_images/" + forum.userImage);

                    // Genera botón de favoritos con nuevo ID y data attribute
                    const favButton = `
                        <button class="btn btn-outline-warning mt-3" id="btnFavorite" data-id="${forum.id}">
                            ⭐ Agregar a Favoritos
                        </button>`;
                    $("#favoriteButtonContainer").html(favButton);

                    // Si existe función para verificar autor, la ejecutamos
                    if (window.verificarAutorForo) {
                        window.verificarAutorForo(forum.userId);
                    }
                } else {
                    $("#forumContent").html("<p>Foro no encontrado</p>");
                }
            },
            error: function () {
                $("#forumContent").html("<p>Error al cargar el foro</p>");
            }
        });
    }

    // Maneja toggle de favorito usando delegación de evento y nuevos nombres
    $(document).on("click", "#btnFavorite", function () {
        const forumId = $(this).data("id");
        console.log("▶️ forumId:", forumId);

        $.ajax({
            url: `../Backend/foro.php`,
            type: "POST",
            data: {
                action: "toggle_favorite",
                forum_id: forumId
            },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    alert(response.message);
                    const btn = $("#btnFavorite");
                    if (btn.hasClass("btn-warning")) {
                        btn.removeClass("btn-warning")
                           .addClass("btn-outline-warning")
                           .text("⭐ Agregar a Favoritos");
                    } else {
                        btn.removeClass("btn-outline-warning")
                           .addClass("btn-warning")
                           .text("★ Quitar de Favoritos");
                    }
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error – status:", status);
                console.error("HTTP Status Code:", xhr.status);
                console.error("Response Text:", xhr.responseText);
                console.error("Error Thrown:", error);
                alert("Error al conectar con el servidor. Revisa la consola para más detalles.");
            }
        });
    });
});
