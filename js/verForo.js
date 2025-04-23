$(document).ready(function () {
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get("id");

    if (id) {
        $.ajax({
            url: `../Backend/foro.php`,
            type: "GET",
            data: { action: "read_one", id: id },
            dataType: "json",
            success: function (respuesta) {
                if (respuesta.exito) {
                    const foro = respuesta.foro;

                    $("#titulo").text(foro.titulo);
                    $("#descripcion").text(foro.descripcion);
                    $("#fecha").text(foro.fecha_creacion);
                    $("#autor").text(foro.nombre_usuario);
                    $("#imagenUsuario").attr("src", "../uploads/profile_images/" + foro.imagen_usuario);

                    // Botón para favoritos
                    const botonFavorito = `
                        <button class="btn btn-outline-warning mt-3" id="btn-favorito" data-id="${foro.id}">
                            ⭐ Agregar a Favoritos
                        </button>`;
                    $("#botonFavoritoContenedor").html(botonFavorito);

                    if (window.verificarAutorForo) {
                        window.verificarAutorForo(foro.id_usuario);
                    }
                } else {
                    $("#contenidoForo").html("<p>Foro no encontrado</p>");
                }
            },
            error: function () {
                $("#contenidoForo").html("<p>Error al cargar el foro</p>");
            }
        });
    }

    // Evento con AJAX para agregar/quitar favorito
    $(document).on("click", "#btn-favorito", function () {
        const idForo = $(this).data("id");
        console.log("▶️ idForo:", idForo);
        $.ajax({
            url: `../Backend/foro.php`,
            type: "POST",
            data: {
                action: "toggle_favorito",
                id_foro: idForo
            },
            dataType: "json",
            success: function (data) {
                if (data.exito) {
                    alert(data.mensaje);
                    const btn = $("#btn-favorito");
                    if (btn.hasClass("btn-warning")) {
                        btn.removeClass("btn-warning").addClass("btn-outline-warning").text("⭐ Agregar a Favoritos");
                    } else {
                        btn.removeClass("btn-outline-warning").addClass("btn-warning").text("★ Quitar de Favoritos");
                    }
                } else {
                    /* alert(data.mensaje); */
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
