function verificarSesion() {
    fetch("../Backend/Login.php", {
        method: "GET",
        credentials: "include" // Envía cookies de sesión
    })
    .then(response => response.json())
    .then(data => {
        if (data.status !== "success") {
            console.log("Sesión no activa");
            window.location.href = "../Views/login.php"; // Redirige si no hay sesión
        }
    })
    .catch(error => console.error("Error al verificar sesión:", error));
}

$(document).ready(function () {
    cargarForos()
    $("#formularioForo").on("submit", function(e) {
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
            success: function(respuesta) {
                if (respuesta.exito) {
                    $("#formularioForo")[0].reset();
                    mostrarMensaje(respuesta.mensaje, "success");
                    cargarForos();
                } else {
                    mostrarMensaje(respuesta.mensaje, "danger");
                }
            },
            error: function() {
                mostrarMensaje("Error en la comunicación con el servidor", "danger");
            }
        });
    });
    
    function cargarForos() {
        $.ajax({
            url: "../Backend/foro.php?action=read",
            type: "GET",
            dataType: "json",
            success: function(respuesta) {
                if (respuesta.exito) {
                    mostrarForos(respuesta.foros);
                } else {
                    $("#listaForos").html("<p class='text-danger'>Error al cargar foros</p>");
                }
            },
            error: function() {
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
                    <div class="card mb-3 mx-2" style="flex: 1 0 45%; min-width: 250px; max-width: 400px;">
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
                <div class="d-flex align-items-center mx-2 text-primary"> 
                    Página <span id="currentPage" class="text-primary">1</span> de <span id="totalPages class="text-primary">${totalPages}</span>
                </div>
                <button id="nextPage" class="btn btn-outline-primary ms-2">Siguiente →</button>
            `);
            
            // Mostrar el contenedor de paginación
            $("#pagination-container").show();
            
            // Configurar manejadores de eventos
            $("#prevPage").on("click", function() {
                if (currentPage > 1) {
                    currentPage--;
                    showPage(currentPage);
                }
            });
            
            $("#nextPage").on("click", function() {
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
        
        // Añadir estilos CSS necesarios
        if (!$("#foroStyles").length) {
            $("head").append(`
                <style id="foroStyles">
                    #listaForos {
                        width: 80%;
                        display: flex;
                        justify-content: center;
                        flex-wrap: wrap;
                        gap: 95rem;
                    }
                    .card {
                        height:150px;
                        width:80%;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                        border-radius: 8px;
                        overflow: hidden;
                        transition: transform 0.1s ease;
                    }
                    .card:hover{
                        transform: scale(1.1);
                    }
                    .card-header {
                        background-color: #f8f9fa;
                        border-bottom: 1px solid #eaeaea;
                        justify-content: space-between;
                    }
                    .card-header h5 {
                        margin-bottom: 10px;
                        font-weight: 600;
                        color: #1d3557;
                    }
                </style>
            `);
        }
    }

    function mostrarMensaje(mensaje, tipo) {
        let mensajeHTML = `
            <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                ${mensaje}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        $("#mensajeContainer").html(mensajeHTML);
    
        setTimeout(function () {
            $(".alert").alert('close');
        }, 5000);
    }
});
