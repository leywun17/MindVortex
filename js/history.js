$(document).ready(function () {
  let $forosContainer = $('#accordionForos');
  if ($forosContainer.children().length) return;

  // Al abrir el collapse principal, cargar forums sólo una vez
  $('#collapse-historial').one('show.bs.collapse', function () {
    // Mostrar loader
    $forosContainer.html('<p class="text-center text-muted">Cargando foros...</p>');

    // Petición AJAX
    $.ajax({
      url: '../Backend/foro.php',
      method: 'GET',
      dataType: 'json',
      data: { action: 'my_forums' },
      success: function (res) {
        if (res.success) renderNestedForos(res.forums);
        else showError(res.mensaje);
      },
      error: function (xhr, status, error) {
        console.error('Error AJAX:', status, error);
        showError('No se pudieron cargar los foros.');
      }
    });
  });

  /**
   * Genera un accordion dentro de #accordionForos con los foros
   */
  function renderNestedForos(forums) {
    $forosContainer.empty();

    if (!forums || forums.length === 0) {
      $forosContainer.append('<p class="text-center text-muted">No has creado forums aún.</p>');
      return;
    }

    forums.forEach(function (forum) {
      let item = `
            <div class="accordion-item mb-2 foro-item">
              <h2 class="accordion-header" id="heading-foro-${forum.id}">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                  data-bs-target="#collapse-foro-${forum.id}" aria-expanded="false" aria-controls="collapse-foro-${forum.id}">
                  <span class="me-2 text-secondary">?</span>
                  <span class="foro-title">${forum.title}</span>
                </button>
              </h2>
              <div id="collapse-foro-${forum.id}" class="accordion-collapse collapse" aria-labelledby="heading-foro-${forum.id}" data-bs-parent="#accordionForos">
                <div class="accordion-body">
                  <p>${forum.description}</p>
                  <div class="text-end">
                    <!-- Botón para ir al foro -->
                    <button class="btn btn-sm btn-primary btn-ir-foro" data-id="${forum.id}">Ir al foro</button>
                  </div>
                </div>
              </div>
            </div>`;
      $forosContainer.append(item);
    });
  }

  // Delegación de evento: redirige al foro cuando se hace click en el botón
  $forosContainer.on('click', '.btn-ir-foro', function () {
    const foroId = $(this).data('id');
    
    window.location.href = `../Views/foro.php?id=${foroId}`;
  });

  $('#collapse-respuestas').one('show.bs.collapse', function () {
    const $container = $('#accordionRespuestas');
    // Si ya cargamos, no volvemos a hacerlo
    if ($container.children().length) return;

    // Loader
    $container.html('<p class="text-center text-muted">Cargando respuestas...</p>');

    $.ajax({
      url: '../Backend/foro.php',
      method: 'GET',
      data: { action: 'my_replies' },
      dataType: 'json',
      success: function (resp) {
        $container.empty();

        if (resp.success && Array.isArray(resp.replies) && resp.replies.length) {
          resp.replies.forEach((replies, index) => {
            const item = `
              <div class="accordion-item mb-3 foro-item">
                <h2 class="accordion-header" id="res-heading-${index}">
                  <button class="accordion-button collapsed" type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#res-collapse-${index}"
                    aria-expanded="false"
                    aria-controls="res-collapse-${index}">
                    <span class="me-2 text-primary">✔</span>
                    <span class="foro-title">${replies.title}</span>
                  </button>
                </h2>
                <div id="res-collapse-${index}" class="accordion-collapse collapse"
                  aria-labelledby="res-heading-${index}"
                  data-bs-parent="#accordionRespuestas">
                  <div class="accordion-body">
                    <p>${replies.content}</p>
                    <div class="text-end">
                      <button class="btn btn-sm btn-light">⋮</button>
                    </div>
                  </div>
                </div>
              </div>`;
            $container.append(item);
          });
        } else if (resp.exito) {
          $container.html('<p class="text-muted text-center">No hay respuestas registradas.</p>');
        } else {
          $container.html('<p class="text-danger text-center">' + (resp.mensaje || 'Error al cargar.') + '</p>');
        }
      },
      error: function (xhr, status, err) {
        console.error('AJAX Error:', status, err);
        $('#accordionRespuestas').html('<p class="text-danger text-center">Error al cargar respuestas.</p>');
      }
    });
  });

  /**
   * Muestra mensaje de error
   */
  function showError(msg) {
    $forosContainer.empty();
    $forosContainer.append('<p class="text-danger text-center">' + msg + '</p>');
  }
});
