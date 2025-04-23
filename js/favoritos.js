$(document).ready(function () {
    function cargarFavoritos() {
        $.ajax({
            url: '../Backend/foro.php',
            data: {
                action: 'mis_favoritos'
            },
            method: 'GET',
            dataType: 'json',
            success: function (respuesta) {
                const contenedor = $('#contenedor-favoritos');
                contenedor.empty();

                if (respuesta.exito && respuesta.favoritos.length > 0) {
                    respuesta.favoritos.forEach(foro => {
                        const card = `
                <div class="col-md-4 mb-4">
                  <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">${foro.titulo}</h5>
                        <p class="card-text">${foro.descripcion}</p>
                    </div>
                    <div class="card-footer d-flex align-items-center">
                        <img src="../uploads/profile_images/${foro.imagen_usuario}" alt="Usuario" class="rounded-circle me-2" width="32" height="32">
                        <small class="text-muted">${foro.nombre_usuario} | ${foro.fecha_creacion}</small>
                    </div>
                    </div>
                </div>
                `;
                        contenedor.append(card);
                    });
                } else {
                    contenedor.append(`<div class="col-12"><p class="text-muted">No tienes foros en favoritos aún.</p></div>`);
                }
            },
            error: function () {
                alert('Error al cargar los favoritos. Asegúrate de haber iniciado sesión.');
            }
        });
    }

    // Cargar al inicio
    cargarFavoritos();
});