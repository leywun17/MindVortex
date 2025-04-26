$(document).ready(function () {
    // Carga los foros favoritos del usuario
    function loadFavorites() {
        $.ajax({
            url: '../Backend/foro.php',
            method: 'GET',
            dataType: 'json',
            data: { action: 'my_favorites' },
            success: function (response) {
                const container = $('#contenedor-favoritos');
                container.empty();

                // Verificamos la clave 'success' y que haya al menos un favorito
                if (response.success && response.favorites.length > 0) {
                    response.favorites.forEach(forum => {
                        // Construimos la tarjeta con los campos que devuelve el backend
                        const card = `
                          <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm">
                              <div class="card-body">
                                <h5 class="card-title">${forum.title}</h5>
                                <p class="card-text">${forum.description}</p>
                              </div>
                              <div class="card-footer d-flex align-items-center">
                                <img
                                  src="../uploads/profile_images/${forum.userImage}"
                                  alt="Usuario"
                                  class="rounded-circle me-2"
                                  width="32" height="32"
                                >
                                <small class="text-muted">
                                  ${forum.userName} | ${forum.createdAt}
                                </small>
                              </div>
                            </div>
                          </div>
                        `;
                        container.append(card);
                    });
                } else {
                    // Mensaje si no hay favoritos
                    container.append(`
                      <div class="col-12">
                        <p class="text-muted">No tienes foros en favoritos aún.</p>
                      </div>
                    `);
                }
            },
            error: function () {
                alert('Error al cargar los favoritos. Asegúrate de haber iniciado sesión.');
            }
        });
    }

    // Ejecutamos al cargar la página
    loadFavorites();
});
