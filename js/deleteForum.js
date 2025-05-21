$(document).ready(function() {
    let IDusuario;
    const urlParams = new URLSearchParams(window.location.search);
    const foroId = urlParams.get("id");
    let autorId;

    function verificarAutor(autorID) {
        autorId = autorID;
        if (parseInt(IDusuario) !== parseInt(autorId)) {
            $("#deleteOption, #deleteDivider").addClass("d-none");
        } else {
            $("#deleteOption").removeClass("d-none");
            $("#deleteDivider").removeClass("d-none").addClass("d-block");
        }
    }

    function Obtener_id() {
        $.ajax({
            url: `../Backend/foro.php`,
            type: "GET",
            data: { action: 'get_id' },
            dataType: "json",
            success: function(data) {
                IDusuario = data.userId || data.mensaje;
                console.log("IDusuario obtenido:", IDusuario);

                if (autorId) verificarAutor(autorId); 
            },
            error: function() {
                alert('Error en la conexión con el servidor');
            }
        });
    }

    Obtener_id();

    window.verificarAutorForo = function(autorDelForo) {
        autorId = autorDelForo;
        if (IDusuario) verificarAutor(autorId);
    }

    $("#deleteOption").click(function() {
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
                    url: "../Backend/foro.php",
                    type: "POST",
                    data: {
                        action: "delete",
                        id: foroId
                    },
                    dataType: "json",
                    success: function(respuesta) {
                        if (respuesta.success) {
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
                                text: respuesta.message || "No se pudo eliminar el foro"
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
    
});
