$(document).ready(function () {
    let userId;                                 // ID del usuario autenticado
    const params = new URLSearchParams(window.location.search);
    const forumId = params.get('id');           // ID del foro actual

    obtenerUserId().then(() => loadComments());

    // Mostrar mensajes (console/UI)
    function mostrarMensaje(msg) {
        console.log(msg);
    }

    // Evento submit: crear comentario
    $('#commentForm').submit(e => {
        e.preventDefault();
        const content = $('#commentInput').val().trim();
        if (!content) return mostrarMensaje('Escribe un comentario');

        $.ajax({
            url: '../Backend/comment.php?action=create',
            type: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify({ forum_id: forumId, content }),
            success: res => {
                if (res.success) {
                    $('#commentInput').val('');
                    loadComments();
                } else mostrarMensaje(res.message);
            },
            error: () => mostrarMensaje('Error en el servidor')
        });
    });

    // Cargar lista de comentarios
    function loadComments() {
        $.ajax({
            url: '../Backend/comment.php',
            type: 'GET',
            data: { action: 'read', forum_id: forumId },
            dataType: 'json',
            success: res => {
                if (res.success) renderizarComentarios(res.comments);
                else $('#commentContainer').html('<p class="text-danger">' + res.message + '</p>');
            },
            error: () => $('#commentContainer').html('<p class="text-danger">Error de comunicación</p>')
        });
    }

    // Obtener ID de usuario autenticado
    function obtenerUserId() {
        return $.ajax({
            url: '../Backend/comment.php',
            type: 'GET',
            data: { action: 'get_id' },
            dataType: 'json'
        }).done(res => {
            userId = res.user_id;
        }).fail(() => alert('Error al obtener ID'));
    }

    // Renderizar comentarios en DOM
    function renderizarComentarios(list) {
        const container = $('#commentContainer').empty();
        if (!list.length) return container.html('<p>No hay comentarios</p>');

        list.forEach(c => {
            const isOwn = c.user_id === userId;
            const actions = isOwn
                ? `<span class="dropdown">
                        <!-- Tres puntos como icono -->
                        <button class="btn btn-link" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-dots-vertical-rounded"></i> <!-- Icono de tres puntos -->
                        </button>
                        
                        <!-- Menú desplegable con las opciones -->
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item edit-comment d-flex align-items-center gap-2" href="#" data-id="${c.id}"><i class='bx bx-edit text-dark'></i>Editar</a></li>
                            <li><a class="dropdown-item delete-comment d-flex align-items-center gap-2" href="#" data-id="${c.id}"><i class='bx bx-trash text-danger'></i>Eliminar</a></li>
                        </ul>
                    </span>`
                : '';

            const html = `
                <div class="comment mb-3 gap-2" id="comment-${c.id}">
                    <div class="d-flex justify-content-end align-items-center user-info">
                        <span class='d-flex align-items-center gap-2'>
                            <img class='img' src='../uploads/profile_images/${c.author_image}'>
                            <strong>${c.author_name}</strong> 
                            ${actions}
                        </span>
                    </div>
                    <p class="comment-content">${c.content}</p>
                </div>
            `;
            container.append(html);
        });
    }

    // Editar comentario
    $(document).on('click', '.edit-comment', function (e) {
        e.preventDefault();
        const commentId = $(this).data('id');
        const current = $(`#comment-${commentId} .comment-content`).text();

        Swal.fire({
            title: 'Editar comentario',
            input: 'textarea',
            inputValue: current,
            showCancelButton: true,
            confirmButtonText: 'Guardar',
            cancelButtonText: 'Cancelar',
            inputValidator: value => {
                if (!value || value.trim() === '') {
                    return 'Debes escribir un comentario';
                }
            }
        }).then(result => {
            if (result.isConfirmed) {
                const updated = result.value;
                if (updated !== current) {
                    $.ajax({
                        url: `../Backend/comment.php?action=update&id=${commentId}`,
                        type: 'PUT',
                        data: JSON.stringify({ content: updated }),
                        contentType: 'application/json',
                        dataType: 'json',
                        success: function (res) {
                            if (res.success) {
                                Swal.fire('¡Actualizado!', 'Tu comentario ha sido actualizado.', 'success');
                                loadComments();
                            } else {
                                Swal.fire('Error', res.message || 'No se pudo actualizar el comentario', 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Error', 'Error en el servidor', 'error');
                        }
                    });
                }
            }
        });
    });

    // Eliminar comentario
    $(document).on('click', '.delete-comment', function (e) {
        e.preventDefault();
        const commentId = $(this).data('id');

        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Eliminar comentario?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `../Backend/comment.php?action=delete&id=${commentId}`,
                    type: 'DELETE',
                    dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            Swal.fire('¡Eliminado!', 'El comentario ha sido eliminado.', 'success');
                            loadComments();
                        } else {
                            Swal.fire('Error', res.message || 'No se pudo eliminar el comentario', 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Error en el servidor', 'error');
                    }
                });
            }
        });
    });
});
