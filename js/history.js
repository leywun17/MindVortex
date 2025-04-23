// assets/js/mis_foros.js
// Maneja la carga de foros cuando se expande el panel de “Historial de Preguntas”

$(document).ready(function() {
    var $forosContainer = $('#accordionForos');

    // Al abrir el collapse principal, cargar foros sólo una vez
    $('#collapse-historial').one('show.bs.collapse', function() {
        // Mostrar loader
        $forosContainer.html('<p class="text-center text-muted">Cargando foros...</p>');

        // Petición AJAX
        $.ajax({
            url: '../Backend/foro.php',
            method: 'GET',
            dataType: 'json',
            data: { action: 'mis_foros' },
            success: function(res) {
                if (res.exito) renderNestedForos(res.foros);
                else showError(res.mensaje);
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', status, error);
                showError('No se pudieron cargar los foros.');
            }
        });
    });

    /**
     * Genera un accordion dentro de #accordionForos con los foros
     */
    function renderNestedForos(foros) {
        $forosContainer.empty();

        if (!foros || foros.length === 0) {
            $forosContainer.append('<p class="text-center text-muted">No has creado foros aún.</p>');
            return;
        }

        foros.forEach(function(foro) {
            var item = `
            <div class="accordion-item mb-2 foro-item">
              <h2 class="accordion-header" id="heading-foro-${foro.id}">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                  data-bs-target="#collapse-foro-${foro.id}" aria-expanded="false" aria-controls="collapse-foro-${foro.id}">
                  <span class="me-2 text-secondary">?</span>
                  <span class="foro-title">${foro.titulo}</span>
                </button>
              </h2>
              <div id="collapse-foro-${foro.id}" class="accordion-collapse collapse" aria-labelledby="heading-foro-${foro.id}" data-bs-parent="#accordionForos">
                <div class="accordion-body">
                  <p>${foro.descripcion}</p>
                  <div class="text-end">
                    <button class="btn btn-sm btn-light">⋮</button>
                  </div>
                </div>
              </div>
            </div>`;
            $forosContainer.append(item);
        });
    }

    /**
     * Muestra mensaje de error
     */
    function showError(msg) {
        $forosContainer.empty();
        $forosContainer.append('<p class="text-danger text-center">'+msg+'</p>');
    }
});
