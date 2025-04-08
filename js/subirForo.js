$(document).ready(function () {
    cargarForos()
    abrirForo()
    $("#formularioForo").on("submit", function (e) {
        e.preventDefault();

        let datos = {
            titulo: $("#titulo").val().trim(),
            descripcion: $("#descripcion").val().trim()
        };

        if (datos.titulo === "" || datos.descripcion === "") {
            mostrarMensaje("Debes completar todos los campos", "danger");
            return;
        }

        $.ajax({
            url: "../Backend/foro.php?action=create",
            type: "POST",
            data: JSON.stringify(datos),
            contentType: "application/json",
            dataType: "json",
            success: function (respuesta) {
                if (respuesta.exito) {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: "Foro Añadido con exito",
                        confirmButtonText: "Continuar",
                    }).then(() => {
                        window.location.href = "../Views/dashboard.php"; // Cambia a la vista crrecta
                        console.log("DAtos", data);
                    });
                    cargarForos();
                } else {
                    mostrarMensaje(respuesta.mensaje, "danger");
                }
            },
            error: function () {
                mostrarMensaje("Error en la comunicación con el servidor", "danger");
            }
        });
    });

    function cargarForos() {
        $.ajax({
            url: "../Backend/foro.php?action=read",
            type: "GET",
            dataType: "json",
            success: function (respuesta) {
                if (respuesta.exito) {
                    mostrarForos(respuesta.foros);
                } else {
                    $("#listaForos").html("<p class='text-danger'>Error al cargar foros</p>");
                }
            },
            error: function () {
                $("#listaForos").html("<p class='text-danger'>Error de comunicación con el servidor</p>");
            }
        });
    }

    function mostrarForos(foros) {
        // Limpiar el contenido existente
        $("#listaForos").empty();

        // Verificar si hay foros
        if (foros.length === 0) {
            $("#listaForos").html("<p>No hay foros disponibles</p>");
            return;
        }

        // Configuración de paginación
        const cardsPerPage = 4;
        const totalPages = Math.ceil(foros.length / cardsPerPage);
        let currentPage = 1;

        // Función para mostrar una página específica
        function showPage(page) {
            // Limpiar el contenedor
            $("#listaForos").empty();

            // Calcular índices
            const startIndex = (page - 1) * cardsPerPage;
            const endIndex = Math.min(startIndex + cardsPerPage, foros.length);

            // Construir los cards para esta página
            let html = "";
            for (let i = startIndex; i < endIndex; i++) {
                const foro = foros[i];
                let fecha = new Date(foro.fecha_creacion).toLocaleString();

                html += `
                    <div class="card-item card mb-3 mx-2" data-id="${foro.id}" style="flex: 1 0 45%; min-width: 250px; max-width: 400px;">
                        <div class="card-header d-flex justify-content-space-evenly">
                            <h5>${foro.titulo}</h5>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="user-info d-flex align-items-center">
                                    <div class="avatar me-2">
                                        <img src="../uploads/profile_images/${foro.imagen_usuario}" alt="user photo" class="rounded-circle bg-light d-block" width="32" height="32">
                                    </div>
                                    <small class="text-muted"> <strong>${foro.nombre_usuario}</strong></small>
                                </div>
                                <small class="text-muted"> ${fecha}</small>
                            </div>
                        </div>
                        <div class="card-body">
                            <p>${foro.descripcion}</p>
                        </div>
                    </div>
                `;
            }

            // Insertar los cards en el contenedor
            $("#listaForos").html(html);

            // Actualizar estado de los botones de paginación
            updatePaginationButtons();
        }

        // Función para actualizar los botones de paginación
        function updatePaginationButtons() {
            $("#currentPage").text(currentPage);
            $("#totalPages").text(totalPages);
            $("#prevPage").prop("disabled", currentPage === 1);
            $("#nextPage").prop("disabled", currentPage === totalPages);
        }

        // Crear controles de paginación (solo si hay más de una página)
        if (totalPages > 1) {
            // Usar el contenedor de paginación existente
            $("#pagination-container").html(`
                <button id="prevPage" class="btn btn-outline-primary me-2">← Anterior</button>
                <div class="d-flex align-items-center mx-2 text-primary"> <p>
                        Página
                        <span id="currentPage" class="text-primary"> 1 </span> de <span id="totalPages class="text-primary"> ${totalPages} </span>
                    </p>
                </div>
                <button id="nextPage" class="btn btn-outline-primary ms-2">Siguiente →</button>
            `);

            // Mostrar el contenedor de paginación
            $("#pagination-container").show();

            // Configurar manejadores de eventos
            $("#prevPage").on("click", function () {
                if (currentPage > 1) {
                    currentPage--;
                    showPage(currentPage);
                }
            });

            $("#nextPage").on("click", function () {
                if (currentPage < totalPages) {
                    currentPage++;
                    showPage(currentPage);
                }
            });
        } else {
            // Ocultar el contenedor de paginación si solo hay una página
            $("#pagination-container").hide();
        }

        // Mostrar la primera página inicialmente
        showPage(1);

    }

    function abrirForo() {
        $(document).on("click", ".card-item", function (e) {
            e.preventDefault();
            const id = $(this).data("id")
            
            window.location.replace(`../Views/foro.php?id=${id}}`);
        });
    }

});






