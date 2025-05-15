$(document).ready(function () {
    // Constantes y variables globales
    const URL_PARAMS = new URLSearchParams(window.location.search);
    const FORUM_ID = URL_PARAMS.get("id");
    const API_URL = "../Backend/foro.php";
    const USER_ID = window.userId || null;

    // Elementos del DOM
    const $forumContent = $("#forumContent");
    const $editModal = $("#editForumModal");
    const $editTitle = $("#editForumTitle");
    const $editDescription = $("#editForumDescription");

    // Inicialización
    inicializarForo();

    /* Funciones principales ***********************************************/

    function inicializarForo() {
        if (!FORUM_ID) {
            mostrarErrorYRedirigir("ID de foro no especificado");
            return;
        }

        cargarForo();
        configurarEventos();
        inicializarModales();
    }

    function cargarForo() {
        $.ajax({
            url: API_URL,
            type: "GET",
            data: { action: "read_one", id: FORUM_ID },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    mostrarDatosForo(response.forum);
                    console.log(response.forum.userId)
                    verificarPropietario(response.forum.userId);
                } else {
                    mostrarErrorForo("Foro no encontrado");
                }
            },
            error: () => mostrarErrorForo("Error al cargar el foro")
        });
    }

    function configurarEventos() {
        $(document).on("click", "#btnFavorite", manejarFavorito);
        $("#editForumForm").on("submit", actualizarForo);
    }

    function inicializarModales() {
        $editModal.on("show.bs.modal", () => {
            $editTitle.val($("#forumTitle").text());
            $editDescription.val($("#forumDescription").text());
        });
    }

    $("#optionsMenuToggle").next(".dropdown-menu")
        .on("click", "#btnEdit", function (e) {
            e.preventDefault();
            manejarEdicion(e);
        })
        .on("click", "#btnDelete", function (e) {
            e.preventDefault();
            manejarEliminacion(e);
        });

    /* Funciones de visualización *******************************************/

    function mostrarDatosForo(forum) {
        $("#forumTitle").text(forum.title);
        $("#forumDescription").text(forum.description);
        $("#forumDate").text(forum.createdAt);
        $("#forumAuthor").text(forum.userName);
        $("#userImage").attr("src", `../uploads/profile_images/${forum.userImage}`);


        actualizarBotonFavorito(forum.isFavorite);

        if (forum.image) {
            // Asumiendo que tienes un div o contenedor donde mostrar la imagen, por ejemplo:
            $("#forumImageContainer").html(`<img src="${forum.image}" alt="Imagen del foro" class="img-fluid rounded mb-3">`);
        } else {
            $("#forumImageContainer").html(''); // Si no hay imagen, limpiar el contenedor
        }
    }

    function actualizarBotonFavorito(esFavorito) {
        const botonFavorito = `
            <li class="dropdown-item text-center p-0">
                <button class="btn ${esFavorito ? 'btn-warning' : 'btn-outline-warning'} 
                        btn-sm w-100 py-2" 
                        id="btnFavorite" 
                        data-id="${FORUM_ID}">
                    ${esFavorito ? '★ Quitar de Favoritos' : '⭐ Agregar a Favoritos'}
                </button>
            </li>
        `;
        $("#favoriteButtonContainer").html(botonFavorito);
    }

    /* Funciones de interacción ********************************************/

    function manejarFavorito() {
        const forumId = $(this).data("id");

        $.ajax({
            url: API_URL,
            type: "POST",
            data: { action: "toggle_favorite", forum_id: forumId },
            dataType: "json",
            success: (response) => {
                if (response.success) {
                    mostrarNotificacion("success", "¡Éxito!", response.message);
                    actualizarEstadoFavorito();
                } else {
                    mostrarNotificacion("error", "Error", response.message);
                }
            },
            error: manejarErrorAjax
        });
    }

    function manejarEliminacion(e) {
        e.preventDefault();
        confirmarEliminacion();
    }

    function manejarEdicion(e) {
        e.preventDefault();
        $editModal.modal("show");
    }

    function actualizarForo(e) {
        e.preventDefault();
        const nuevosDatos = {
            title: $editTitle.val(),
            description: $editDescription.val()
        };

        $.ajax({
            url: API_URL,
            type: "POST",
            data: {
                action: "update",
                id: FORUM_ID,
                ...nuevosDatos
            },
            dataType: "json",
            success: (response) => {
                if (response.success) {
                    actualizarVistaForo(nuevosDatos);
                    $editModal.modal("hide");
                    mostrarNotificacion("success", "¡Actualizado!", response.message);
                } else {
                    mostrarNotificacion("error", "Error", response.message);
                }
            },
            error: manejarErrorAjax
        });
    }

    /* Funciones de utilidad ***********************************************/

    function verificarPropietario(userId) {
        if (userId === USER_ID) {
            $("#editOption, #deleteOption").removeClass("d-none");
        }
    }

    function actualizarEstadoFavorito() {
        const $boton = $("#btnFavorite");
        const esFavorito = $boton.hasClass("btn-warning");
        $boton.toggleClass("btn-warning btn-outline-warning")
            .text(esFavorito ? '⭐ Agregar a Favoritos' : '★ Quitar de Favoritos');
    }

    function confirmarEliminacion() {
        Swal.fire({
            title: '¿Eliminar foro?',
            text: "¡Esta acción no se puede deshacer!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => result.isConfirmed && eliminarForo());
    }

    function eliminarForo() {
        $.ajax({
            url: API_URL,
            type: "POST",
            data: { action: "delete", id: FORUM_ID },
            dataType: "json",
            success: (response) => {
                if (response.success) {
                    mostrarNotificacion("success", "Eliminado", response.message);
                    setTimeout(() => window.location.href = "./dashboard.php", 1500);
                } else {
                    mostrarNotificacion("error", "Error", response.message);
                }
            },
            error: manejarErrorAjax
        });
    }

    function actualizarVistaForo(datos) {
        $("#forumTitle").text(datos.title);
        $("#forumDescription").text(datos.description);
    }

    function mostrarNotificacion(icono, titulo, mensaje) {
        Swal.fire({
            icon: icono,
            title: titulo,
            text: mensaje,
            confirmButtonText: "Continuar",
            timer: icono === 'success' ? 2000 : null
        });
    }

    function mostrarErrorForo(mensaje) {
        $forumContent.html(`
            <div class="alert alert-danger m-4">
                ${mensaje}. Redirigiendo...
            </div>
        `);
        redirigirDashboard();
    }

    function mostrarErrorYRedirigir(mensaje) {
        mostrarErrorForo(mensaje);
        redirigirDashboard();
    }


    function manejarErrorAjax(xhr) {
        console.error("Error AJAX:", xhr.responseText);
        mostrarNotificacion("error", "Error", "Error de conexión con el servidor");
    }
});