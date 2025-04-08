$(document).ready(function(){
    const urlParams = new URLSearchParams(window.location.search)
    const id = urlParams.get("id")

    if(id){
        $.ajax({
            url: `../Backend/foro.php?action=read_one&id=${id}`,
            type: "GET",
            dataType: "json",
            success: function(respuesta){
                if(respuesta.exito){
                    const foro = respuesta.foro

                    $("#titulo").text(foro.titulo)
                    $("#descripcion").text(foro.descripcion)
                    $("#fecha").text(new Date(foro.fecha_creacion).toLocaleString())
                    $("#autor").text(foro.nombre_usuario)
                    $("#imagenUsuario").attr("src", "../uploads/profile_images/" + foro.imagen_usuario);

                    if (window.verificarAutorForo) {
                        window.verificarAutorForo(respuesta.foro.id_usuario);
                    }
                }else{
                    $("#contenidoForo").html("<p>foro no encontrado</p>")
                }
            },
            error: function(){
                $("#contenidoForo").html("<p>error al cargar el foro</p>")
            }
        })
    }

})