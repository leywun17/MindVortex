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
            
                    $("#forumTitle").text(forum.title);
                    $("#forumDescription").text(forum.description);
                    $("#forumDate").text(forum.createdAt);
                    $("#forumAuthor").text(forum.userName);
                    $("#userImage").attr("src", "../uploads/profile_images/" + forum.userImage);
                    console.log(forum.userImage)
            
                    // Dependiendo de si es favorito o no, generamos el botón
                    let favoriteButtonHtml = `
                        <li class="dropdown-item text-center">
                            <button class="btn ${forum.isFavorite ? 'btn' : 'btn'} btn-sm w-100" id="btnFavorite" data-id="${forum.id}">
                                ${forum.isFavorite ? '★ Quitar de Favoritos' : '⭐ Agregar a Favoritos'}
                            </button>
                        </li>
                    `;
            
                    $("#favoritesDropdownMenu").append(favoriteButtonHtml);
            
                    // Verificar autor, si corresponde
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
                    const btn = $("#btnFavorite");

                    // Mostrar el SweetAlert
                    Swal.fire({
                        icon: "success",
                        title: "¡Éxito!",
                        text: response.message,
                        confirmButtonText: "Continuar",
                    });

                    // Actualizar botón
                    if (btn.hasClass("btn-warning")) {
                        btn.removeClass("btn-warning")
                            .addClass("btn-outline-warning")
                            .html("⭐ Agregar a Favoritos");
                    } else {
                        btn.removeClass("btn-outline-warning")
                            .addClass("btn-warning")
                            .html("★ Quitar de Favoritos");
                    }
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: response.message || "No se pudo actualizar favorito.",
                        confirmButtonText: "Ok"
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error – status:", status);
                console.error("HTTP Status Code:", xhr.status);
                console.error("Response Text:", xhr.responseText);
                console.error("Error Thrown:", error);

                Swal.fire({
                    icon: "error",
                    title: "Error de Servidor",
                    text: "Error al conectar con el servidor. Revisa la consola para más detalles.",
                    confirmButtonText: "Ok"
                });
            }
        });
    });
});
