$(document).ready(function () {
  const PAGE_SIZE = 6;

  let forosData = null;
  let respuestasData = null;

  const $forosContainer = $('#tab-foros');
  const $respuestasContainer = $('#tab-respuestas');

  $('.tab-btn').on('click', function () {
    const tab = $(this).data('tab');

    $('.tab-btn').removeClass('active border-bottom border-primary fw-bold');
    $(this).addClass('active border-bottom border-primary fw-bold');

    $('.tab-section').addClass('d-none');
    $(`#tab-${tab}`).removeClass('d-none');

    if (tab === 'foros' && !forosData) loadForos();
    if (tab === 'respuestas' && !respuestasData) loadRespuestas();
  });

  function loadForos() {
    $forosContainer.html('<p class="text-center text-muted">Cargando foros...</p>');
    $.ajax({
      url: '../Backend/foro.php',
      method: 'GET',
      data: { action: 'my_forums' },
      dataType: 'json',
      success: function (res) {
        if (res.success) {
          forosData = res.forums;
          renderPaginated(forosData, $forosContainer, 'foro');
        } else showError($forosContainer, res.mensaje);
      },
      error: () => showError($forosContainer, 'No se pudieron cargar los foros.')
    });
  }

  function loadRespuestas() {
    $respuestasContainer.html('<p class="text-center text-muted">Cargando respuestas...</p>');
    $.ajax({
      url: '../Backend/foro.php',
      method: 'GET',
      data: { action: 'my_replies' },
      dataType: 'json',
      success: function (res) {
        if (res.success) {
          respuestasData = res.replies;
          renderPaginated(respuestasData, $respuestasContainer, 'res');
        } else showError($respuestasContainer, res.mensaje);
      },
      error: () => showError($respuestasContainer, 'No se pudieron cargar las respuestas.')
    });
  }

  function renderPaginated(data, $container, type) {
    $container.empty();
    if (!Array.isArray(data) || data.length === 0) {
      $container.html('<p class="text-center text-muted">No hay información disponible.</p>');
      return;
    }

    let currentPage = 1;
    const totalPages = Math.ceil(data.length / PAGE_SIZE);

    function renderPage(page) {
      $container.empty();
      const start = (page - 1) * PAGE_SIZE;
      const end = Math.min(start + PAGE_SIZE, data.length);

      const $cardGrid = $('<div class="row g-3 mb-3"></div>');

      data.slice(start, end).forEach(item => {
        const card = `
          <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title">${item.title}</h5>
                <p class="card-text text-muted">${type === 'foro' ? item.description : item.content}</p>
                <div class="mt-auto text-end">
                  ${type === 'foro'
            ? `<button class="btn btn-sm btn-outline-primary btn-ir-foro" data-id="${item.id}">Ir al foro</button>`
            : `<button class="btn btn-sm btn-light">⋮</button>`
          }
                </div>
              </div>
            </div>
          </div>`;
        $cardGrid.append(card);
      });

      $container.append($cardGrid);
      $container.append(renderPagination(page, totalPages, data.length, start + 1, end, type));
    }

    renderPage(currentPage);

    $container.off('click').on('click', `.page-${type}-link`, function (e) {
      e.preventDefault();
      const page = parseInt($(this).data('page'));
      if (!isNaN(page)) {
        currentPage = page;
        renderPage(currentPage);
      }
    });

    if (type === 'foro') {
      $container.on('click', '.btn-ir-foro', function () {
        const foroId = $(this).data('id');
        window.location.href = `../Views/foro.php?id=${foroId}`;
      });
    }
  }

  function renderPagination(current, totalPages, totalItems, from, to, type) {
    let html = `
      <div class="text-center text-muted small mb-2">Mostrando ${from}–${to} de ${totalItems}</div>
      <nav><ul class="pagination justify-content-center">`;

    

    for (let i = 1; i <= totalPages; i++) {
      html += `
        <li class="page-item ${i === current ? 'active' : ''}">
          <a class="page-link page-${type}-link" href="#" data-page="${i}">${i}</a>
        </li>`;
    }



    html += '</ul></nav>';
    return html;
  }

  function showError(container, msg) {
    container.html('<p class="text-danger text-center">' + msg + '</p>');
  }

  $('.tab-btn[data-tab="foros"]').trigger('click');
});
