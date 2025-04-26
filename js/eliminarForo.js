$(document).ready(function() {
    let IDusuario;
    const urlParams = new URLSearchParams(window.location.search);
    const foroId = urlParams.get("id");
    let autorId;

    function verificarAutor(autorID) {
        autorId = autorID;
        
        // ✅ Esto solo se debe hacer cuando IDusuario YA ESTÉ DEFINIDO
        if (parseInt(IDusuario) !== parseInt(autorId)) {
            $("#opcionEliminar, #separadorEliminar").addClass("d-none");
        } else {
            $("#opcionEliminar").removeClass("d-none");
            $("#separadorEliminar").removeClass("d-none").addClass("d-block");
        }
    }

    function Obtener_id() {
        $.ajax({
            url: `../Backend/foro.php`,
            type: "GET",
            data: { action: 'get_id' },
            dataType: "json",
            success: function(data) {
                IDusuario = data.userId || data.mensaje; // según cómo venga del backend
                console.log("IDusuario obtenido:", IDusuario);

                // 👇 Si ya tenías el autorId cargado antes, podrías hacer:
                if (autorId) verificarAutor(autorId); 
            },
            error: function() {
                alert('Error en la conexión con el servidor');
            }
        });
    }

    // Llamar al obtener ID del usuario al principio
    Obtener_id();

    // Esta función puede ser llamada luego desde verForo.js cuando ya se sepa el autor
    window.verificarAutorForo = function(autorDelForo) {
        autorId = autorDelForo;
        // ⚠️ Si ya tenés IDusuario, podés comparar ahora
        if (IDusuario) verificarAutor(autorId);
        // Si no, se va a comparar más tarde cuando llegue la respuesta de `Obtener_id()`
    }

    // Manejar clic en la opción eliminar
    $("#opcionEliminar").click(function() {
        if (!foroId) return;
        
        // 1. Mostrar confirmación con SweetAlert
        Swal.fire({
            title: "¿Estás seguro?",
            text: "No podrás revertir esta acción",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                // 2. Hacer la petición AJAX mediante POST
                $.ajax({
                    url: "../Backend/foro.php",
                    type: "POST",
                    data: {
                        action: "delete",
                        id: foroId
                    },
                    dataType: "json",
                    success: function(respuesta) {
                        // 3. Si la respuesta es exitosa, mostrar alerta y redirigir
                        if (respuesta.exito) {
                            Swal.fire({
                                icon: "success",
                                title: "Éxito",
                                text: "Foro eliminado con éxito",
                                confirmButtonText: "Continuar"
                            }).then(() => {
                                window.location.href = "../Views/dashboard.php";
                            });
                        } else {
                            // 4. Si hay un error lógico, mostrar mensaje de error
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: respuesta.mensaje || "No se pudo eliminar el foro"
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        // 5. Si la petición falla, loguear y notificar
                        console.error("Error AJAX:", error);
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Error al procesar la solicitud"
                        });
                    }
                });
            }
        });
    });
    
});
