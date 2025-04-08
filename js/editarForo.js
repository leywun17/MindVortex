$(document).ready(function() {
    // Obtener ID del foro de la URL
    const urlParams = new URLSearchParams(window.location.search);
    const foroId = urlParams.get("id");
    

    let autorId;
    
    // Al cargar los datos del foro, verificar si el usuario actual es el autor
    function verificarAutor(autorID) {
        autorId = autorID;
        
        // Si el usuario actual no es el autor, ocultar la opción de eliminar
        if (parseInt(usuarioActualId) !== parseInt(autorId)) {
            $("#opcionEditar, #separadorEditar").addClass("d-none");
        } else {
            $("#opcionEditar").removeClass("d-none"); // mantiene d-flex
            $("#separadorEditar").removeClass("d-none").addClass("d-block"); // <hr> necesita d-block
        }
    }
    
    
    // Manejar clic en la opción eliminar
    $("#opcionEliminar").click(function() {
        if (!foroId) return;
        
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
                // Proceder con la eliminación
                $.ajax({
                    url: `../Backend/foro.php?action=delete&id=${foroId}`,
                    type: "DELETE",
                    dataType: "json",
                    success: function(respuesta) {
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
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: respuesta.mensaje || "No se pudo eliminar el foro"
                            });
                        }
                    },
                    error: function(xhr, status, error) {
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
    
    // Función para exponer al exterior y ser llamada desde verForo.js
    window.verificarAutorForo = verificarAutor;
});