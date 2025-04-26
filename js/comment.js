$(document).ready(function() {
    let userId;                   // ID del usuario actual
    const forumId = window.currentForumId;  // ID del foro actual

    getUserId(); // Obtiene userId y luego carga comentarios

    // Mensaje de consola o UI
    function mostrarMensaje(mensaje) {
        console.log(mensaje);
    }

    // Crear comentario
    $('#commentForm').on('submit', function(e) {
        e.preventDefault();
        const content = $('#commentInput').val().trim();
        if (!content) {
            mostrarMensaje('Escribe un comentario');
            return;
        }
        $.ajax({
            url: '../Backend/comment.php?action=create',
            type: 'POST',
            data: JSON.stringify({ forum_id: forumId, content: content }),
            contentType: 'application/json',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#commentInput').val('');
                    loadComments();
                } else {
                    mostrarMensaje(response.message);
                }
            },
            error: function() {
                mostrarMensaje('Error en el servidor');
            }
        });
    });

    // Cargar comentarios
    function loadComments() {
        $.ajax({
            url: '../Backend/comment.php',
            type: 'GET',
            data: { action: "read", forum_id: forumId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    renderComments(response.comments);
                } else {
                    $('#commentContainer').html('<p class="text-danger">No se pudieron cargar los comentarios</p>');
                }
            },
            error: function() {
                $('#commentContainer').html('<p class="text-danger">Error de comunicación</p>');
            }
        });
    }

    // Obtener ID de usuario
    function getUserId() {
        $.ajax({
            url: '../Backend/foro.php',
            type: 'GET',
            data: { action: 'get_id' },
            dataType: 'json',
            success: function(response) {
                userId = response.userId;
                loadComments();
            },
            error: function() {
                alert('Error en la conexión con el servidor');
            }
        });
    }

    // Renderizar comentarios
    function renderComments(list) {
        const container = $('#commentContainer').empty();
        if (!list.length) {
            container.html('<p>No hay comentarios</p>');
            return;
        }
        list.forEach(c => {
            const own = c.user_id === userId;
            const actions = own
                ? `<span>
                     <a href="#" class="edit-comment" data-id="${c.id}">Editar</a>
                     <a href="#" class="delete-comment" data-id="${c.id}">Eliminar</a>
                   </span>`
                : '';
            const html = `
                <div class="comment mb-3" id="comment-${c.id}">
                  <div class="d-flex justify-content-between">
                    <span><strong>${c.author_name}</strong> <small>${new Date(c.created_at).toLocaleString()}</small></span>
                    ${actions}
                  </div>
                  <p class="comment-content">${c.content}</p>
                </div>
            `;
            container.append(html);
        });
    }

    // Editar comentario
    $(document).on('click', '.edit-comment', function(e) {
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
                        success: function(res) {
                            if (res.success) {
                                Swal.fire('¡Actualizado!', 'Tu comentario ha sido actualizado.', 'success');
                                loadComments();
                            } else {
                                Swal.fire('Error', res.message || 'No se pudo actualizar el comentario', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Error en el servidor', 'error');
                        }
                    });
                }
            }
        });
    });

    // Eliminar comentario
    $(document).on('click', '.delete-comment', function(e) {
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
                    success: function(res) {
                        if (res.success) {
                            Swal.fire('¡Eliminado!', 'El comentario ha sido eliminado.', 'success');
                            loadComments();
                        } else {
                            Swal.fire('Error', res.message || 'No se pudo eliminar el comentario', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Error en el servidor', 'error');
                    }
                });
            }
        });
    });
});
