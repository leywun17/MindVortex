<?php
session_start();

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $img = isset($_SESSION["profile_image"]) ? htmlspecialchars($_SESSION["profile_image"]) : "";
    $name = $_SESSION['name'];
    $email = isset($_SESSION["email"]) ? htmlspecialchars($_SESSION["email"]) : 'Correo no disponible';
    $user_id = isset($_SESSION['id']) ? (int) $_SESSION['id'] : 'null';
} else {
    header("Location: ../index.html");
    exit();
}

// Obtener el ID del foro de la URL
$foro_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MindVortex</title>

    <!-- Iconos de Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css//foro.css">

    <!-- Favicon -->
    <link rel="shortcut icon" href="../Assets/logo.png" type="image/x-icon">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="container-fluid d-grid gap-3 contenedor">
        <!-- Barra de navegación -->
        <div class="row">
            <nav class="navbar navbar-expand-md navbar-dark">
                <div class="container rounded-4 text-bg-dark contenedor-header p-2">

                    <!-- Logo -->
                    <a class="navbar-brand d-flex align-items-center" href="">
                        <img src="../Assets/logo.png" alt="Flowbite Logo" height="38" class="logo-pos">
                    </a>

                    <!-- Menú de usuario -->
                    <div class="d-flex align-items-center order-md-2">
                        <div class="dropdown d-grid gap-3 position-relative">
                            <button class="btn p-1 d-flex align-items-center justify-content-center" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                                <!-- Imagen o icono del usuario -->
                                <img src="../uploads/profile_images/<?php echo $img ?>" alt="user photo" class="rounded-circle bg-light d-block" width="32" height="32">
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end rounded fondo-dropdown" aria-labelledby="userMenu">
                                <li class="dropdown-header d-flex align-items-center flex-column">
                                    <img src="../uploads/profile_images/<?php echo $img ?>" alt="user photo" class="rounded-circle bg-light d-block" width="48" height="48">
                                    <strong><?php echo $name ?></strong>
                                    <small><?php echo $email ?></small>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-white" href="#modalUser" data-bs-toggle="modal">Edit</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-white" href="../Backend/logout.php">Sign out</a></li>
                            </ul>
                        </div>

                        <!-- Botón hamburguesa -->
                        <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarUser">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                    </div>

                    <!-- Menú de navegación -->
                    <div class="collapse navbar-collapse order-md-1" id="navbarUser">
                        <ul class="navbar-nav mx-auto mb-2 mb-md-0 paginas gap-2">
                            <li class="nav-item"><a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">Preguntas</a></li>
                            <li class="nav-item"><a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'favorite.php' ? 'active' : ''; ?>" href="favorite.php">Favoritos</a></li>
                            <li class="nav-item"><a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'history.php' ? 'active' : ''; ?>" href="history.php">Historial</a></li>
                        </ul>

                        <!-- Formulario de búsqueda -->
                        <form class="d-flex me-3">
                            <div class="position-relative w-110">
                                <input type="search" class="form-control" placeholder="Buscar" aria-label="Search">
                                <button type="submit" class="btn btn-link position-absolute top-50 end-0 translate-middle-y me-2">
                                    <i class='bx bx-search-alt-2 icono-busqueda'></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Contenido principal -->
        <div class="container contenedor-principal p-5" id="contenidoForo">
            <!-- Cabecera del foro -->
            <div class="header d-flex justify-content-between gap-3 text-light mb-5">
                <h2 class="titulo-foro" id="titulo"></h2>
                <div class="d-flex gap-3 align-items-center">
                        <img id="imagenUsuario" src="" alt="Foto de usuario" class="rounded-3 bg-light" width="24" height="24">
                        <p><span id="autor"></span></p>
                        <p><span id="fecha"></span></p>
                    </div>
                <div class="dropdown">
                    <button class="btn p-1 d-flex align-items-center justify-content-center" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class='bx bx-dots-vertical-rounded icono-opciones'></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end rounded fondo-dropdown" aria-labelledby="userMenu">
                        <li class="dropdown-header text-center"><i class='bx bx-star text-warning'></i> favoritos</li>
                        <hr class="bg-light">
                        <li id="opcionEliminar" class="dropdown-header text-center d-none opcion-eliminar"> <i class='bx bx-trash text-danger'></i> Eliminar </li>
                        <hr id="separadorEliminar" class="d-none bg-light">
                        <li id="opcionEditar" class="dropdown-header text-center d-none opcion-editar"> <i class='bx bx-pencil text-info'></i> Editar </li>
                        <hr id="separadorEditar" class="d-none bg-light">
                        <li class="dropdown-header text-center"> <i class='bx bx-error text-danger'></i> Reportar </li>
                    </ul>
                    <div id="botonFavoritoContenedor"></div>
                </div>
            </div>

            <!-- Cuerpo del foro -->
            <div class="body d-flex flex-column gap-3 text-light">
                <div class="d-flex justify-content-between align-items-center">
                    <p id="descripcion"></p>
                </div>
                <hr>
                <div class="comentarios">
                    <h3>Comentarios</h3>
                    <div id="commentSection overflow-auto">
                        <div id="commentContainer"></div>
                        <hr>
                        <form id="commentForm">
                            <textarea id="commentInput" class="form-control" placeholder="Escribe tu comentario..."></textarea>
                            <button type="submit" id="boton-publicar" class="btn btn-primary mt-2">Publicar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para editar perfil -->
        <div class="modal fade" id="modalUser" aria-hidden="true" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content modalCuerpo">
                    <div class="modal-header">Editar Perfil</div>
                    <div class="modal-body">
                        <form id="uploadImageForm" class="mb-3">
                            <div class="image-upload-container">
                                <img id="imagePreview" class="rounded-circle">
                                <div class="uploadImageBtn">
                                    <label class="btn btn-secondary">
                                        <input type="file" class="file-input" id="profileImageInput" name="profile_image" accept="image/*" required hidden>
                                        Cambiar Imagen
                                    </label>
                                </div>
                                <button type="submit" id="uploadImageBtn" class="btn btn-primary mt-2">Actualizar Imagen</button>
                            </div>
                        </form>

                        <div class="modal-change d-flex justify-content-center gap-3 flex-wrap">
                            <form id="updateProfileForm" class="actualizar">
                                <input type="text" name="name" value="<?= $name ?>" placeholder="name" class="form-control mb-2">
                                <input type="email" name="email" value="<?= $email ?>" placeholder="email" class="form-control mb-2">
                                <button type="submit" id="updateProfileBtn" class="btn btn-success">Actualizar información</button>
                            </form>
                            <form id="changePasswordForm" class="actualizar">
                                <input type="password" name="new_password" placeholder="Nueva contraseña" class="form-control mb-2">
                                <input type="password" name="confirm_password" placeholder="Confirmar contraseña" class="form-control mb-2">
                                <button type="submit" id="changePasswordBtn" class="btn btn-warning">Cambiar contraseña</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer-img fixed-bottom">
            <img src="../Assets/Mask group.svg" alt="Footer SVG" class="w-100">
        </footer>
    </div>

    <!-- Variables de PHP al JS -->
    <script>
        let usuarioActualForoId = <?php echo json_encode($foro_id); ?>;
    </script>

    <!-- Scripts -->
    <script src="../js/jquery-3.7.1.min.js"></script>
    <script src="../js/verForo.js"></script>
    <script src="../js/eliminarForo.js"></script>
    <script src="../js/update.js"></script>
    <script src="../js/comment.js"></script>
</body>

</html>