function cargarNotificaciones() {
    $.ajax({
        url: '../Backend/comment.php',
        method: 'GET',
        data: { action: 'get_notifications' },
        dataType: 'json',
        success: function (data) {
            const contenedor = $('#contenedor-notificaciones');
            const notifCount = $('#notifCount');
            contenedor.empty();

            if (data.success && data.notifications.length > 0) {
                const noLeidas = data.notifications.filter(n => n.is_read == 0).length;

                if (noLeidas > 0) {
                    notifCount.html(`${noLeidas} <span class="visually-hidden">notificaciones nuevas</span>`).show();
                } else {
                    notifCount.hide();
                }

                data.notifications.forEach(function (notif) {
                    const clases = notif.is_read == 0 ? 'p-2 mb-2 border rounded bg-light' : 'p-2 mb-2 border rounded bg-white text-muted';
                    let boton = '';
                    if (notif.is_read == 0) {
                        boton = `<button class="btn btn-sm btn-outline-primary mt-2" onclick="marcarComoLeida(${notif.id})">Marcar como leída</button>`;
                    }
                    const div = $(`
                        <div class="${clases}">
                          <div class="fw-bold">${notif.type === 'comment' ? 'Nuevo comentario' : notif.type}</div>
                          <div>${notif.message}</div>
                          <small class="text-muted">${new Date(notif.created_at).toLocaleString()}</small>
                          ${boton}
                        </div>
                    `);
                    contenedor.append(div);
                });
            } else {
                notifCount.hide();
                contenedor.html('<p class="text-center text-muted">No hay notificaciones.</p>');
            }
        },
        error: function () {
            $('#notifCount').hide();
            $('#contenedor-notificaciones').html('<p class="text-danger text-center">Error al cargar notificaciones.</p>');
        }
    });
}

function marcarComoLeida(id) {
    $.ajax({
        url: '../Backend/comment.php?action=mark_as_read',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ notification_id: id }),
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                cargarNotificaciones();
            } else {
                Swal.fire('Error', data.message || 'No se pudo marcar como leída', 'error');
            }
        },
        error: function () {
            Swal.fire('Error', 'Error en la petición', 'error');
        }
    });
}

$('#notificationModal').on('show.bs.modal', function () {
    cargarNotificaciones();
});

$(document).ready(function () {
    cargarNotificaciones();
});
