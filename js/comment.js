$(document).ready(function() {
    let IDusuario;

    Obtener_id(); // Solo llamamos a esto, y cargarComentarios se llama dentro de la función

    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get("id"); // este es el ID del foro
    const foroId = window.usuarioActualForoId;

    function mostrarMensaje(mensaje) {
        console.log(mensaje); 
    }

    // Crear comentario
    $('#commentForm').on('submit', function(e) {
        e.preventDefault();
        const content = $('#commentInput').val().trim();
        if (!content) {
            mostrarMensaje('Escribe un comentario', 'danger');
            return;
        }
        $.ajax({
            url: '../Backend/comment.php?action=create',
            type: 'POST',
            data: JSON.stringify({ foro_id: usuarioActualForoId, content: content }),
            contentType: 'application/json',
            dataType: 'json',
            success: function(res) {
                console.log("Comentarios recibidos:", res.comments);
                if (res.exito) {
                    $('#commentInput').val('');
                    cargarComentarios();
                } else {
                    mostrarMensaje(res.mensaje, 'danger');
                }
            },
            error: function() {
                mostrarMensaje('Error en el servidor', 'danger');
            }
        });
    });

    // Leer comentarios
    function cargarComentarios() {
        console.log("ID del foro desde URL:", id);
        $.ajax({
            url: `../Backend/comment.php`,
            type: 'GET',
            data: { action: "read", foro_id: id },
            dataType: 'json',
            success: function(res) {
                if (res.exito) renderComentarios(res.comments);
                else $('#commentContainer').html('<p class="text-danger">No se pudieron cargar los comentarios</p>');
            },
            error: function() {
                $('#commentContainer').html('<p class="text-danger">Error de comunicación</p>');
            }
        });
    }

    // Obtener ID del usuario y luego cargar comentarios
    function Obtener_id() {
        $.ajax({
            url: `../Backend/foro.php`,
            type: "GET",
            data: { action: 'obtener_id' },
            dataType: "json",
            success: function(data) {
                IDusuario = data.id_usuario;
                console.log("IDusuario obtenido:", IDusuario);

                // Llamamos a cargar comentarios solo cuando ya tenemos el ID
                cargarComentarios();
            },
            error: function() {
                alert('Error en la conexión con el servidor');
            }
        });
    }

    // Renderizar comentarios
    function renderComentarios(list) {
        const container = $('#commentContainer').empty();
        if (!list.length) {
            container.html('<p>No hay comentarios</p>');
            return;
        }
        list.forEach(c => {
            const own = c.user_id === IDusuario;
            const actions = own ? 
                `<span>
                   <a href="#" class="edit-comment" data-id="${c.id}">Editar</a>
                   <a href="#" class="delete-comment" data-id="${c.id}">Eliminar</a>
                 </span>` : '';

            const html = `
                <div class="comment" id="comment-${c.id}">
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
        const id = $(this).data('id');
        const current = $(`#comment-${id} .comment-content`).text();
        const nuevo = prompt('Editar comentario:', current);
        if (nuevo != null && nuevo.trim() !== '' && nuevo !== current) {
            $.ajax({
                url: `../Backend/comment.php?action=update&id=${id}`,
                type: 'PUT',
                data: JSON.stringify({ content: nuevo }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(res) {
                    if (res.exito) cargarComentarios();
                    else mostrarMensaje(res.mensaje, 'danger');
                },
                error: function() {
                    mostrarMensaje('Error en el servidor', 'danger');
                }
            });
        }
    });

    // Eliminar comentario
    $(document).on('click', '.delete-comment', function(e) {
        e.preventDefault();
        if (!confirm('¿Eliminar comentario?')) return;
        const id = $(this).data('id');
        $.ajax({
            url: `../Backend/comment.php?action=delete&id=${id}`,
            type: 'DELETE',
            dataType: 'json',
            success: function(res) {
                if (res.exito) cargarComentarios();
                else mostrarMensaje(res.mensaje, 'danger');
            },
            error: function() {
                mostrarMensaje('Error en el servidor', 'danger');
            }
        });
    });
});
