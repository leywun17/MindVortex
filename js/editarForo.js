$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const foroId = urlParams.get("id");
    

    let autorId;
    
    function verificarAutor(autorID) {
        autorId = autorID;
        
        if (parseInt(usuarioActualId) !== parseInt(autorId)) {
            $("#opcionEditar, #separadorEditar").addClass("d-none");
        } else {
            $("#opcionEditar").removeClass("d-none");
            $("#separadorEditar").removeClass("d-none").addClass("d-block");
        }
    }
    
    
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
    
    window.verificarAutorForo = verificarAutor;
});