$(document).ready(function () {
    // Carga la lista de foros al iniciar y habilita la apertura de un foro al hacer clic
    loadForums();
    bindOpenForum();

    // Maneja el envío del formulario para crear un nuevo foro
    $("#forumForm").on("submit", function (e) {

        const title = $("#title").val().trim();
        const description = $("#description").val().trim();

        if (!title || !description) {
            showToast("Debes completar todos los campos", "danger");
            return;
        }

        const formData = new FormData();
        formData.append("title", title);
        formData.append("description", description);

        // Agregar archivo (si existe)
        const fileInput = $("#forumImage")[0];
        if (fileInput.files.length > 0) {
            formData.append("forumImage", fileInput.files[0]);
        }

        // Deshabilitar botón de envío
        $(this).find('button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...');

        $.ajax({
            url: "../Backend/foro.php?action=create",
            type: "POST",
            data: formData,
            processData: false, // Importante para enviar FormData sin procesar
            contentType: false, // Importante para que jQuery no establezca content-type manualmente
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        icon: "success",
                        title: "¡Genial!",
                        text: "Pregunta publicada con éxito",
                        confirmButtonText: "Continuar",
                        confirmButtonColor: "#6366f1"
                    }).then(() => {
                        window.location.href = "../Views/dashboard.php";
                    });
                } else {
                    showToast(response.message, "danger");
                    $('#forumForm').find('button[type="submit"]').prop('disabled', false).text('Publicar');
                }
            },
            error: function () {
                showToast("Error en la comunicación con el servidor", "danger");
                $('#forumForm').find('button[type="submit"]').prop('disabled', false).text('Publicar');
            }
        });
    });

    // Función para mostrar toast de Bootstrap
    function showToast(message, type = "info") {
        // Elimina cualquier toast anterior
        $('.toast').remove();

        // Determina el color según el tipo
        const bgClass = type === 'danger' ? 'bg-danger' :
            type === 'warning' ? 'bg-warning' :
                type === 'success' ? 'bg-success' : 'bg-info';

        // Crea el toast
        const toastHtml = `
            <div class="position-fixed top-0 end-0 p-3" style="z-index: 1070">
                <div class="toast align-items-center ${bgClass} text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        `;

        // Añade al body y muestra
        $('body').append(toastHtml);
        const toastElement = $('.toast');
        const toast = new bootstrap.Toast(toastElement, {
            delay: 3000
        });
        toast.show();
    }

    // Función para obtener todos los foros desde la API
    function loadForums() {
        // Añadir un indicador de carga
        $("#forumList").html(`
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">Cargando preguntas...</p>
            </div>
        `);

        $.ajax({
            url: "../Backend/foro.php?action=read",
            type: "GET",
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    // Renderiza los foros en pantalla
                    renderForums(response.forums);
                } else {
                    console.log(response.success)
                    $("#forumList").html(`
                        <div class="col-12 text-center py-5">
                            <div class="alert alert-danger" role="alert">
                                <i class='bx bx-error-circle me-2'></i> Error al cargar preguntas
                            </div>
                        </div>
                    `);
                }
            },
            error: function () {
                $("#forumList").html(`
                    <div class="col-12 text-center py-5">
                        <div class="alert alert-danger" role="alert">
                            <i class='bx bx-wifi-off me-2'></i> Error de comunicación con el servidor
                        </div>
                    </div>
                `);
            }
        });
    }

    // Función para mostrar los foros con paginación
    function renderForums(forums) {
        // Limpia el contenedor de foros
        $("#forumList").empty();

        // Si no hay foros, informa al usuario
        if (forums.length === 0) {
            $("#forumList").html(`
                <div class="col-12 text-center py-5">
                    <div class="alert alert-warning" role="alert">
                        <i class='bx bx-info-circle me-2'></i> No hay preguntas disponibles
                    </div>
                    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addForumModal">
                        <i class='bx bx-plus me-2'></i> Crea la primera pregunta
                    </button>
                </div>
            `);
            $("#paginationContainer").hide();
            return;
        }

        const cardsPerPage = 6; // Aumentamos a 6 cards por página (2 filas de 3 en pantallas grandes)
        const totalPages = Math.ceil(forums.length / cardsPerPage);
        let currentPage = 1;

        // Muestra la página especificada por parámetro
        function showPage(page) {
            $("#forumList").empty();

            const startIndex = (page - 1) * cardsPerPage;
            const endIndex = Math.min(startIndex + cardsPerPage, forums.length);

            // Construye cada card de foro
            for (let i = startIndex; i < endIndex; i++) {
                const forum = forums[i];
                const date = new Date(forum.createdAt);

                // Formatear fecha relativa
                const timeAgo = formatTimeAgo(date);

                // Crear elemento de columna
                const col = document.createElement('div');
                col.className = 'col-md-6 col-lg-4 mb-4';

                // Crear contenido de la tarjeta
                col.innerHTML = `
                    <div class="card h-100 forum-card" data-id="${forum.id}">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">${forum.title}</h5>
                            <p class="card-text text-muted">${truncateText(forum.description, 120)}</p>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <img src="../uploads/profile_images/${forum.userImage}" 
                                         alt="${forum.userName}" 
                                         class="rounded-circle bg-light me-2" 
                                         width="24" height="24">
                                    <small class="text-muted">${forum.userName}</small>
                                </div>
                                <small class="text-muted">${timeAgo}</small>
                            </div>
                        </div>
                    </div>
                `;

                $("#forumList").append(col);
            }

            // Actualiza los botones de paginación
            updatePaginationButtons();
        }

        // Función para truncar texto largo
        function truncateText(text, maxLength) {
            if (text.length <= maxLength) return text;
            return text.substring(0, maxLength) + '...';
        }

        // Función para formatear fecha relativa
        function formatTimeAgo(date) {
            const now = new Date();
            const diffMs = now - date;
            const diffSecs = Math.floor(diffMs / 1000);
            const diffMins = Math.floor(diffSecs / 60);
            const diffHours = Math.floor(diffMins / 60);
            const diffDays = Math.floor(diffHours / 24);

            if (diffDays > 30) {
                return date.toLocaleDateString();
            } else if (diffDays > 0) {
                return `Hace ${diffDays} día${diffDays > 1 ? 's' : ''}`;
            } else if (diffHours > 0) {
                return `Hace ${diffHours} hora${diffHours > 1 ? 's' : ''}`;
            } else if (diffMins > 0) {
                return `Hace ${diffMins} minuto${diffMins > 1 ? 's' : ''}`;
            } else {
                return 'Justo ahora';
            }
        }

        // Actualiza el estado de los botones de paginación
        function updatePaginationButtons() {
            // Implementación moderna de paginación con Bootstrap 5
            let paginationHTML = `
                <nav aria-label="Navegación de páginas">
                    <ul class="pagination">
                        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                            <a class="page-link" href="#" id="prevPage" aria-label="Anterior">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
            `;

            // Determina qué números de página mostrar
            const maxVisiblePages = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

            // Ajustar si estamos cerca del final
            if (endPage - startPage + 1 < maxVisiblePages) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }

            // Añadir primera página y ellipsis si es necesario
            if (startPage > 1) {
                paginationHTML += `
                    <li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>
                    ${startPage > 2 ? '<li class="page-item disabled"><span class="page-link">...</span></li>' : ''}
                `;
            }

            // Añadir números de página
            for (let i = startPage; i <= endPage; i++) {
                paginationHTML += `
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `;
            }

            // Añadir última página y ellipsis si es necesario
            if (endPage < totalPages) {
                paginationHTML += `
                    ${endPage < totalPages - 1 ? '<li class="page-item disabled"><span class="page-link">...</span></li>' : ''}
                    <li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>
                `;
            }

            paginationHTML += `
                        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                            <a class="page-link" href="#" id="nextPage" aria-label="Siguiente">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            `;

            // Actualizar el contenedor de paginación
            $("#paginationContainer").html(paginationHTML).show();

            // Asignar eventos a los botones de paginación
            $("#prevPage").on("click", function (e) {
                e.preventDefault();
                if (currentPage > 1) {
                    currentPage--;
                    showPage(currentPage);
                }
            });

            $("#nextPage").on("click", function (e) {
                e.preventDefault();
                if (currentPage < totalPages) {
                    currentPage++;
                    showPage(currentPage);
                }
            });

            $(".page-link[data-page]").on("click", function (e) {
                e.preventDefault();
                const page = parseInt($(this).data("page"));
                if (page !== currentPage) {
                    currentPage = page;
                    showPage(currentPage);
                }
            });
        }

        // Verifica si hay necesidad de paginación
        if (totalPages <= 1) {
            $("#paginationContainer").hide();
        }

        // Muestra la primera página al cargar
        showPage(1);
    }

    // Enlaza el click de los elementos .forum-card para abrir el detalle del foro
    function bindOpenForum() {
        $(document).on("click", ".forum-card", function (e) {
            const id = $(this).data("id");
            window.location.href = `../Views/foro.php?id=${id}`;
        });
    }

    // Búsqueda de foros en tiempo real
    let searchTimeout = null;

    $('#searchInput, #mobileSearchInput').on('input', function () {
        const term = $(this).val().trim();

        // Limpia el timeout anterior
        clearTimeout(searchTimeout);

        // Si el término está vacío, vuelve a cargar todos los foros
        if (!term) {
            $('#paginationContainer').show();
            loadForums();
            return;
        }

        // Espera 500ms después de que el usuario deje de teclear
        searchTimeout = setTimeout(function () {
            // Mostrar indicador de búsqueda
            $("#forumList").html(`
                <div class="col-12 text-center py-3">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Buscando...</span>
                    </div>
                    <p class="mt-2">Buscando "${term}"...</p>
                </div>
            `);

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
                    } else {
                        $('#forumList').html(`
                            <div class="col-12 text-center py-4">
                                <i class='bx bx-search-alt text-warning' style="font-size: 3rem;"></i>
                                <p class="mt-3">No se encontraron preguntas para "${term}"</p>
                                <button class="btn btn-outline-primary mt-2" onclick="$('#searchInput, #mobileSearchInput').val(''); loadForums();">
                                    <i class='bx bx-reset me-1'></i> Mostrar todos
                                </button>
                            </div>
                        `);
                        $('#paginationContainer').hide();
                    }
                },
                error: function () {
                    $('#forumList').html(`
                        <div class="col-12 text-center py-4">
                            <div class="alert alert-danger" role="alert">
                                <i class='bx bx-error-circle me-2'></i> Error de comunicación con el servidor
                            </div>
                            <button class="btn btn-outline-primary mt-2" onclick="$('#searchInput, #mobileSearchInput').val(''); loadForums();">
                                <i class='bx bx-reset me-1'></i> Mostrar todos
                            </button>
                        </div>
                    `);
                    $('#paginationContainer').hide();
                }
            });
        }, 500);
    });

    // Manejar el submit del formulario de búsqueda
    $('#searchForm, #mobileSearchForm').on('submit', function (e) {
        e.preventDefault();
        // Disparar el evento input para iniciar la búsqueda
        $(this).find('input[type="search"]').trigger('input');
    });

    // Maneja el toggle de favorito
    $(document).on("click", ".btn-favorite", function (e) {
        e.preventDefault();
        e.stopPropagation(); // Evita que se abra el foro al hacer clic en el botón

        const $btn = $(this);
        const forumId = $btn.data("id");

        // Cambiar estado del botón temporalmente
        $btn.prop('disabled', true);

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
                    // Notificar con Toast en lugar de alert
                    showToast(response.message, "success");

                    // Actualizar UI del botón
                    if ($btn.hasClass("btn-warning")) {
                        $btn.removeClass("btn-warning")
                            .addClass("btn-outline-warning")
                            .html('<i class="bx bx-star"></i>');
                        $btn.attr('title', 'Agregar a Favoritos');
                    } else {
                        $btn.removeClass("btn-outline-warning")
                            .addClass("btn-warning")
                            .html('<i class="bx bxs-star"></i>');
                        $btn.attr('title', 'Quitar de Favoritos');
                    }
                } else {
                    showToast(response.message || "Error al actualizar favoritos", "danger");
                }
                $btn.prop('disabled', false);
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error – status:", status, error);
                showToast("Error de conexión con el servidor", "danger");
                $btn.prop('disabled', false);
            }
        });
    });
});