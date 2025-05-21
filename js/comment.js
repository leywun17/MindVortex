$(document).ready(function () {
    let userId;                                
    const params = new URLSearchParams(window.location.search);
    const forumId = params.get('id');           

    obtenerUserId().then(() => loadComments());

    function mostrarMensaje(msg) {
        console.log(msg);
    }

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

    function renderizarComentarios(comments, container = $('#commentContainer'), level = 0) {
        container.empty();

        if (!comments.length && level === 0) {
            return container.html('<p class="text-muted">Sé el primero en comentar</p>');
        }

        comments.forEach(c => {
            const isOwn = c.user_id === userId;
            const isReply = level > 0 || c.parent_id !== null;
            const margin = level * 40;
            const borderClass = level > 0 ? 'border-start ps-3' : '';
            console.log(c.author_image)

            const actions = !isReply ? `
            <div class="dropdown">
                ${isOwn ? `
                <button class="btn btn-sm btn-link" data-bs-toggle="dropdown">
                    <i class="bx bx-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item edit-comment" href="#" data-id="${c.id}">Editar</a></li>
                    <li><a class="dropdown-item delete-comment" href="#" data-id="${c.id}">Eliminar</a></li>
                </ul>` : ''}
                <button class="btn btn-link btn-sm reply-button" id="reply-button" data-id="${c.id}">
                    <i class="bx bx-reply"></i>
                </button>
            </div>
        ` : '';

            const html = `
            <div class="card mb-3 ${borderClass}" id="comment-${c.id}" style="margin-left: ${margin}px">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <img src="${c.author_image}" 
                                 class="rounded-circle" 
                                 width="40" 
                                 height="40" 
                                 alt="${c.author_name}"
                                 onerror="this.src='../../uploads/profile_images/default.jpg'">
                            <div>
                                <h6 class="mb-0">${c.author_name}</h6>
                                <small class="text-muted">${timeAgo(c.created_at)}</small>
                            </div>
                        </div>
                        ${actions}
                    </div>
                    <p class="card-text">${c.content}</p>
                    ${c.replies?.length ? `
                    <div class="replies-container mt-3 ms-4">
                        <!-- Respuestas irán aquí -->
                    </div>` : ''}
                </div>
            </div>
        `;

            const $comment = $(html);
            container.append($comment);

            if (c.replies && c.replies.length > 0) {
                const $repliesContainer = $comment.find('.replies-container');
                renderizarComentarios(c.replies, $repliesContainer, level + 1);
            }
        });
    }

    function timeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.round((now - date) / 1000);

        const intervals = {
            año: 31536000,
            mes: 2592000,
            día: 86400,
            hora: 3600,
            minuto: 60
        };

        for (const [unit, secondsInUnit] of Object.entries(intervals)) {
            const interval = Math.floor(seconds / secondsInUnit);
            if (interval >= 1) {
                return `hace ${interval} ${unit}${interval !== 1 ? 's' : ''}`;
            }
        }
        return 'hace unos segundos';
    }

    $(document).on('click', '.reply-button', function (e) {
        e.preventDefault();
        const $button = $(this);
        const parentId = $button.data('id');
        const $card = $button.closest('.card');

        let $repliesContainer = $card.find('.replies-container');
        if (!$repliesContainer.length) {
            $repliesContainer = $('<div class="replies-container mt-3 ms-4"></div>');
            $card.find('.card-body').append($repliesContainer);
        }

        $('.reply-form').remove();

        const formHTML = `
        <div class="reply-form mt-3 p-2 bg-light rounded">
            <div class="input-group">
                <textarea class="form-control autosize" 
                        rows="2"
                        placeholder="Escribe tu respuesta..."
                        style="resize: none; min-height: 80px"></textarea>
                <div class="input-group-append">
                    <button class="btn btn-primary send-reply" type="button">
                        <i class="bx bx-send"></i>
                    </button>
                </div>
            </div>
            <div class="mt-2">
                <button class="btn btn-sm btn-link text-danger cancel-reply">
                    <i class="bx bx-x"></i> Cancelar
                </button>
            </div>
        </div>`;

        $repliesContainer.prepend(formHTML).show();
        $repliesContainer.find('textarea').trigger('focus');
    });


    $(document).on('click', '.cancel-reply', function (e) {
        e.preventDefault();
        $(this).closest('.reply-form').remove();
    });

    $(document).on('click', '.send-reply', function () {
        const $form = $(this).closest('.reply-form');
        const parentId = $(this).closest('.card').attr('id').split('-')[1];
        const content = $form.find('textarea').val().trim();
        const $repliesContainer = $form.closest('.replies-container');

        if (!content) {
            mostrarMensaje('Escribe una respuesta');
            return;
        }

        $.ajax({
            url: '../Backend/comment.php?action=reply',
            type: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify({
                parent_id: parentId,
                content: content,
                forum_id: forumId
            }),
            success: res => {
                if (res.success) {
                    
                    loadComments();
                }
            },
            error: () => mostrarMensaje('Error en el servidor')
        });
    });

    $('#commentTrigger').click(function (e) {
        if (!$(e.target).closest('#commentForm').length) {
            $('.comment-prompt').addClass('d-none');
            $('#commentForm').removeClass('d-none');
            $('#commentInput').focus();
        }
    });

    $('#commentForm').on('click', '.btn-cancel', function () {
        $('#commentForm').addClass('d-none');
        $('.comment-prompt').removeClass('d-none');
        $('#commentInput').val('');
    });

    $('#commentInput').on('input', function () {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    
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
