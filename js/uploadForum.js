$(document).ready(function () {
    
    loadForums();
    bindOpenForum();

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

        const fileInput = $("#forumImage")[0];
        if (fileInput.files.length > 0) {
            formData.append("forumImage", fileInput.files[0]);
        }

        $(this).find('button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...');

        $.ajax({
            url: "../Backend/foro.php?action=create",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
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

    function showToast(message, type = "info") {
        $('.toast').remove();

        const bgClass = type === 'danger' ? 'bg-danger' :
            type === 'warning' ? 'bg-warning' :
                type === 'success' ? 'bg-success' : 'bg-info';

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

        $('body').append(toastHtml);
        const toastElement = $('.toast');
        const toast = new bootstrap.Toast(toastElement, {
            delay: 3000
        });
        toast.show();
    }

    function loadForums() {
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

    function renderForums(forums) {
        $("#forumList").empty();

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

        const cardsPerPage = 6;
        const totalPages = Math.ceil(forums.length / cardsPerPage);
        let currentPage = 1;

        function showPage(page) {
            $("#forumList").empty();

            const startIndex = (page - 1) * cardsPerPage;
            const endIndex = Math.min(startIndex + cardsPerPage, forums.length);

            for (let i = startIndex; i < endIndex; i++) {
                const forum = forums[i];
                const date = new Date(forum.createdAt);

                const timeAgo = formatTimeAgo(date);

                const col = document.createElement('div');
                col.className = 'col-md-6 col-lg-4 mb-4';
                col.innerHTML = `
                    <div class="card h-100 forum-card" data-id="${forum.id}">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">${forum.title}</h5>
                            <p class="card-text text-muted">${truncateText(forum.description, 120)}</p>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <img src="${forum.userImage}" 
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

            updatePaginationButtons();
        }

        function truncateText(text, maxLength) {
            if (text.length <= maxLength) return text;
            return text.substring(0, maxLength) + '...';
        }

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

        function updatePaginationButtons() {
            let paginationHTML = `
                <nav aria-label="Navegación de páginas">
                    <ul class="pagination">
                        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                        </li>
            `;

            const maxVisiblePages = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

            if (endPage - startPage + 1 < maxVisiblePages) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }

            if (startPage > 1) {
                paginationHTML += `
                    <li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>
                    ${startPage > 2 ? '<li class="page-item disabled"><span class="page-link">...</span></li>' : ''}
                `;
            }

            for (let i = startPage; i <= endPage; i++) {
                paginationHTML += `
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `;
            }

            if (endPage < totalPages) {
                paginationHTML += `
                    ${endPage < totalPages - 1 ? '<li class="page-item disabled"><span class="page-link">...</span></li>' : ''}
                    <li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>
                `;
            }

            

            $("#paginationContainer").html(paginationHTML).show();

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

        if (totalPages <= 1) {
            $("#paginationContainer").hide();
        }

        showPage(1);
    }

    function bindOpenForum() {
        $(document).on("click", ".forum-card", function (e) {
            const id = $(this).data("id");
            window.location.href = `../Views/foro.php?id=${id}`;
        });
    }

    let searchTimeout = null;

    $('#searchInput, #mobileSearchInput').on('input', function () {
        const term = $(this).val().trim();

        clearTimeout(searchTimeout);

        if (!term) {
            $('#paginationContainer').show();
            loadForums();
            return;
        }

        searchTimeout = setTimeout(function () {
            $("#forumList").html(`
                <div class="col-12 text-center py-3">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Buscando...</span>
                    </div>
                    <p class="mt-2">Buscando "${term}"...</p>
                </div>
            `);

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

    $('#searchForm, #mobileSearchForm').on('submit', function (e) {
        e.preventDefault();
        $(this).find('input[type="search"]').trigger('input');
    });

    $(document).on("click", ".btn-favorite", function (e) {
        e.preventDefault();
        e.stopPropagation(); 
        const $btn = $(this);
        const forumId = $btn.data("id");

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
                    showToast(response.message, "success");

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