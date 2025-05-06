$(document).ready(function () {
    // Carga la lista de foros al iniciar y habilita la apertura de un foro al hacer clic
    loadForums();
    bindOpenForum();

    // Maneja el envío del formulario para crear un nuevo foro
    $("#forumForm").on("submit", function (e) {
        e.preventDefault();

        // Obtiene y limpia los valores de título y descripción
        let data = {
            title: $("#title").val().trim(),
            description: $("#description").val().trim()
        };

        // Valida que ambos campos no estén vacíos
        if (data.title === "" || data.description === "") {
            mostrarMensaje("Debes completar todos los campos", "danger");
            return;
        }

        // Llama a la API para crear el foro
        $.ajax({
            url: "../Backend/foro.php?action=create",
            type: "POST",
            data: JSON.stringify(data),
            contentType: "application/json",
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    // Muestra alerta de éxito y recarga la vista
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: "Foro Añadido con éxito",
                        confirmButtonText: "Continuar",
                    }).then(() => {
                        window.location.href = "../Views/dashboard.php";
                    });
                    loadForums();
                } else {
                    // Muestra mensaje de error devuelto por el servidor
                    mostrarMensaje(response.message, "danger");
                }
            },
            error: function () {
                // Muestra mensaje si falla la comunicación con el servidor
                mostrarMensaje("Error en la comunicación con el servidor", "danger");
            }
        });
    });

    // Función para obtener todos los foros desde la API
    function loadForums() {
        $.ajax({
            url: "../Backend/foro.php?action=read",
            type: "GET",
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    // Renderiza los foros en pantalla
                    renderForums(response.forums);
                } else {
                    $("#forumList").html("<p class='text-danger'>Error al cargar foros</p>");
                }
            },
            error: function () {
                $("#forumList").html("<p class='text-danger'>Error de comunicación con el servidor</p>");
            }
        });
    }

    // Función para mostrar los foros con paginación
    function renderForums(forums) {
        // Limpia el contenedor de foros
        $("#forumList").empty();

        // Si no hay foros, informa al usuario
        if (forums.length === 0) {
            $("#forumList").html("<p class='text-warning'>No hay foros disponibles</p>");
            return;
        }

        const cardsPerPage = 4;
        const totalPages = Math.ceil(forums.length / cardsPerPage);
        let currentPage = 1;

        // Muestra la página especificada por parámetro
        function showPage(page) {
            $("#forumList").empty().css({
                display: "flex",
                "flex-wrap": "wrap",
                position: "relative"
            });

            const startIndex = (page - 1) * cardsPerPage;
            const endIndex = Math.min(startIndex + cardsPerPage, forums.length);
            let html = "";

            // Construye cada card de foro
            for (let i = startIndex; i < endIndex; i++) {
                const forum = forums[i];
                let date = new Date(forum.createdAt).toLocaleDateString();

                html += `
                    <div class="card-item card mb-3 mx-2" data-id="${forum.id}" style="flex: 1 0 45%; min-width: 350px; max-width: 500px;">
                        <div class="card-header d-flex justify-content-space-beetwen gap-3">
                            <h5 class="w-50 d-flex flex-wrap">${forum.title}</h5>
                            <div class="w-50 d-flex justify-content-between align-items-center">
                                <div class="user-info d-flex align-items-center">
                                    <div class="avatar me-2">
                                        <img src="../uploads/profile_images/${forum.userImage}"
                                             alt="user photo"
                                             class="bg-secondary d-block"
                                             width="32" height="32"
                                             style="border-radius: 10px;">
                                    </div>
                                    <small class="texto-info-foro"><strong>${forum.userName}</strong></small>
                                    <small class="texto-info-foro">${date}</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <p>${forum.description}</p>
                        </div>
                    </div>
                `;

                // Agrega divisor vertical tras cada par de cards
                if ((i - startIndex + 1) % 2 === 0 && (i - startIndex + 1) < (endIndex - startIndex) && (endIndex - startIndex) !== 3) {
                    html += `<div class="vertical-divider"></div>`;
                }
            }

            // Inserta el HTML generado
            $("#forumList").html(html);
            updatePaginationButtons();
        }

        // Actualiza el estado de los botones de paginación
        function updatePaginationButtons() {
            $("#currentPage").text(currentPage);
            $("#totalPages").text(totalPages);
            $("#prevPage").prop("disabled", currentPage === 1);
            $("#nextPage").prop("disabled", currentPage === totalPages);
        }

        // Configura los controles de paginación si hay más de una página
        if (totalPages > 1) {
            $("#paginationContainer").html(`
                <button id="prevPage" class="btn btn-outline-light me-2">← Anterior</button>
                <div class="d-flex align-items-center justify-content-center mx-2 text-light">
                    <p class="text-pagination">
                        Página <span id="currentPage">1</span> de <span id="totalPages">${totalPages}</span>
                    </p>
                </div>
                <button id="nextPage" class="btn btn-outline-light ms-2">Siguiente →</button>
            `).show();

            // Botón Anterior
            $("#prevPage").on("click", () => {
                if (currentPage > 1) {
                    currentPage--;
                    showPage(currentPage);
                }
            });
            // Botón Siguiente
            $("#nextPage").on("click", () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    showPage(currentPage);
                }
            });
        } else {
            // Oculta la paginación si solo hay una página
            $("#paginationContainer").hide();
        }

        // Muestra la primera página al cargar
        showPage(1);
    }

    // Enlaza el click de los elementos .card-item para abrir el detalle del foro
    function bindOpenForum() {
        $(document).on("click", ".card-item", function (e) {
            e.preventDefault();
            const id = $(this).data("id");
            window.location.replace(`../Views/foro.php?id=${id}`);
        });
    }

    $('#searchForm').on('keypress', function (e) {
        console.log('Tecla presionada:', e.key);

        const term = $('#searchInput').val().trim();

        // Si está vacío, recarga todo
        if (!term) {
            $('#paginationContainer').show();
            loadForums();
            return;
        }

        // Petición AJAX al case 'search'
        $.ajax({
            url: "../Backend/foro.php",
            type: "GET",
            data: {
                action: 'search',
                query: term
            },
            dataType: "json",
            success: function (res) {
                if (res.success) {
                    // Pagina y muestra sólo los resultados de búsqueda
                    renderForums(res.results);
                    $('#paginationContainer').show();
                } else {
                    $('#forumList').html('<p class="text-warning">No se encontraron foros.</p>');
                    $('#paginationContainer').hide();
                }
            },
            error: function () {
                $('#forumList').html('<p class="text-danger">Error de comunicación.</p>');
                $('#paginationContainer').hide();
            }
        });
    });

    // Maneja el toggle de favorito usando el nuevo parámetro forum_id
    $(document).on("click", "#btnFavorite", function () {
        const forumId = $(this).data("id");
        $.ajax({
            url: "../Backend/foro.php",
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
                console.error("AJAX Error – status:", status, error);
                alert("Error al conectar con el servidor.");
            }
        });
    });
});
