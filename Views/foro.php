<?php
// Inicia la sesión y verifica autenticación
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../index.html");
    exit();
}

// Obtiene datos del usuario con valores por defecto
$img = $_SESSION["profile_image"] ?? "";
$name = $_SESSION['name'] ?? 'Usuario';
$email = $_SESSION["email"] ?? 'Correo no disponible';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MindVortex</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- Google Fonts: Nunito -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Favicon -->
    <link rel="shortcut icon" href="../Assets/logo.png" type="image/x-icon">

    <!-- Hojas de estilo locales -->
    <link rel="stylesheet" href="../css/foro.css">
    <link rel="stylesheet" href="../css/dashboard.css">
</head>

<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container-fluid">
            <button class="btn btn-link text-dark p-0 me-3" id="btn">
                <i class='bx bx-menu fs-4'></i>
            </button>

            <a class="navbar-brand d-flex align-items-center text-dark" href="#">
                <img src="../Assets/logo.png" alt="MindVortex Logo" height="36">
                <p>MindVortex</p>
            </a>

            <!-- Barra de búsqueda desktop -->
            <div class="search-container d-none d-lg-block mx-auto">
                <form id="searchForm" class="w-100">
                    <div class="input-group">
                        <input id="searchInput" type="search" class="form-control rounded-pill"
                            placeholder="Buscar foros..." autocomplete="off">
                        <button id="searchBtn" type="submit" class="btn btn-link">
                            <i class='bx bx-search-alt-2 fs-5'></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Menú usuario -->
            <div class="collapse navbar-collapse" id="mainNav">
                <!-- Barra de búsqueda mobile -->
                <form id="mobileSearchForm" class="d-lg-none mt-3 mb-2 w-100">
                    <div class="input-group">
                        <input type="search" class="form-control rounded-pill" placeholder="Buscar foros..."
                            autocomplete="off">
                        <button type="submit" class="btn btn-link">
                            <i class='bx bx-search-alt-2 fs-5'></i>
                        </button>
                    </div>
                </form>

                <div class="ms-auto">
                    <div class="dropdown">
                        <button class="btn d-flex align-items-center gap-2 rounded-pill px-3 border-0 text-dark"
                            type="button" id="userMenuToggle" data-bs-toggle="dropdown">
                            <img id="navbarUserImage" src="../uploads/profile_images/<?= htmlspecialchars($img) ?>"
                                alt="Usuario" class="rounded-circle" width="32" height="32">
                            <span class="d-none d-lg-block"><?= htmlspecialchars($name) ?></span>
                            <i class='bx bx-chevron-down'></i>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li class="dropdown-header d-flex align-items-center flex-column p-3">
                                <img src="../uploads/profile_images/<?= htmlspecialchars($img) ?>" alt="Usuario"
                                    class="rounded-circle mb-2" width="64" height="64">
                                <strong class="mb-1"><?= htmlspecialchars($name) ?></strong>
                                <small class="text-muted"><?= htmlspecialchars($email) ?></small>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item d-flex gap-2 align-items-center" href="#" data-bs-toggle="modal"
                                    data-bs-target="#editProfileModal">
                                    <i class='bx bx-edit'></i>Editar perfil
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item d-flex gap-2 align-items-center text-danger"
                                    href="../Backend/logout.php">
                                    <i class='bx bx-log-out'></i>Cerrar sesión
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="d-flex">
        <!-- Barra lateral -->
        <div class="sidebar">
            <ul class="nav-list">
                <li class="add-foro-btn">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#addForumModal">
                        <i class='bx bx-plus'></i>
                        <span class="links_name">Nueva pregunta</span>
                    </a>
                    <span class="tooltip">Nueva pregunta</span>
                </li>
                <li>
                    <a href="./dashboard.php" class="active">
                        <i class='bx bx-question-mark'></i>
                        <span class="links_name">Preguntas</span>
                    </a>
                    <span class="tooltip">Preguntas</span>
                </li>
                <li>
                    <a href="./favorite.php">
                        <i class="bx bx-star"></i>
                        <span class="links_name">Favoritos</span>
                    </a>
                    <span class="tooltip">Favoritos</span>
                </li>
                <li>
                    <a href="./history.php">
                        <i class="bx bx-history"></i>
                        <span class="links_name">Historial</span>
                    </a>
                    <span class="tooltip">Historial</span>
                </li>
                <li>
                    <a href="./notificaciones.php">
                        <i class="bx bx-bell"></i>
                        <span class="links_name">Notificaciones</span>
                    </a>
                    <span class="tooltip">Notificaciones</span>
                </li>
            </ul>
        </div>

        <!-- Contenido del foro -->
        <div class="main-content contenedor-principal" id="forumContent">
            <!-- Encabezado del foro -->
            <div class="forum-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="mb-0" id="forumTitle"></h2>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex justify-content-md-end align-items-center mt-3 mt-md-0">
                            <div class="d-flex align-items-center me-3">
                                <img id="userImage" src="" alt="Autor" class="rounded-circle me-2" width="32"
                                    height="32">
                                <div>
                                    <div id="forumAuthor" class="fw-bold"></div>
                                    <div id="forumDate" class="small text-muted"></div>
                                </div>
                            </div>

                            <!-- Menú de opciones -->
                            <div class="dropdown">
                                <button class="btn btn-link text-dark p-0" type="button" id="optionsMenuToggle"
                                    data-bs-toggle="dropdown">
                                    <i class='bx bx-dots-vertical-rounded fs-4'></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">

                                    <li>
                                        <a id="btnEdit" class="dropdown-item d-flex align-items-center" href="#">
                                            <i class='bx bx-edit-alt me-2'></i> Editar
                                        </a>
                                    </li>

                                    <li>
                                        <a id="btnDelete" class="dropdown-item d-flex align-items-center" href="#">
                                            <i class='bx bx-trash me-2 text-danger'></i> Eliminar
                                        </a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item d-flex align-items-center" href="#">
                                            <i class='bx bx-error me-2 text-warning'></i> Reportar
                                        </a>
                                    </li>

                                    <li id="favoriteButtonContainer"></li>

                                </ul>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cuerpo del foro -->
            <div class="forum-body">
                <div class="forum-content mb-4">
                    <p id="forumDescription" class="lead"></p>
                    <div id="forumImageContainer"></div>
                </div>
                <hr>

                <!-- Sección de comentarios -->
                <div class="comments-section mt-5">
                    <h3 class="mb-4">Comentarios</h3>
                    <div id="commentContainer" class="mb-4"></div>

                    <!-- Formulario de comentarios -->
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Tu comentario</h5>
                            <form id="commentForm">
                                <div class="input-group d-flex flex-column">
                                    <textarea id="commentInput" class="form-control" rows="2"
                                        placeholder="Escribe un comentario..." required></textarea>
                                    <br>
                                    <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                                        Publicar
                                        <i class='bx bx-send'></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Footer con animación SVG -->
    <footer>
        <svg version="1.1" xmlns="http://www.w3.org/2000/svg"
            xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="100%" height="100%" viewBox="0 0 1600 900" preserveAspectRatio="xMidYMax slice">
            <defs>
                <linearGradient id="bg">
                    <stop offset="0%" style="stop-color:rgba(130, 158, 249, 0.06)"></stop>
                    <stop offset="50%" style="stop-color:rgba(76, 190, 255, 0.6)"></stop>
                    <stop offset="100%" style="stop-color:#60B5FF"></stop>
                </linearGradient>
                <path id="wave" fill="url(#bg)" d="M-363.852,502.589c0,0,236.988-41.997,505.475,0
            s371.981,38.998,575.971,0s293.985-39.278,505.474,5.859s493.475,48.368,716.963-4.995v560.106H-363.852V502.589z" />
            </defs>
            <g>
                <use xlink:href='#wave' opacity=".3">
                    <animateTransform
                        attributeName="transform"
                        attributeType="XML"
                        type="translate"
                        dur="10s"
                        calcMode="spline"
                        values="270 230; -334 180; 270 230"
                        keyTimes="0; .5; 1"
                        keySplines="0.42, 0, 0.58, 1.0;0.42, 0, 0.58, 1.0"
                        repeatCount="indefinite" />
                </use>
                <use xlink:href='#wave' opacity=".6">
                    <animateTransform
                        attributeName="transform"
                        attributeType="XML"
                        type="translate"
                        dur="8s"
                        calcMode="spline"
                        values="-270 230;243 220;-270 230"
                        keyTimes="0; .6; 1"
                        keySplines="0.42, 0, 0.58, 1.0;0.42, 0, 0.58, 1.0"
                        repeatCount="indefinite" />
                </use>
                <use xlink:href='#wave' opacty=".9">
                    <animateTransform
                        attributeName="transform"
                        attributeType="XML"
                        type="translate"
                        dur="6s"
                        calcMode="spline"
                        values="0 230;-140 200;0 230"
                        keyTimes="0; .4; 1"
                        keySplines="0.42, 0, 0.58, 1.0;0.42, 0, 0.58, 1.0"
                        repeatCount="indefinite" />
                </use>
            </g>
        </svg>
    </footer>

    <!-- Modals -->
    <!-- Modal Editar Perfil -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Editar Perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadImageForm" class="mb-4 text-center">
                        <div class="mb-3">
                            <img id="imagePreview" class="rounded-circle mb-3"
                                src="../uploads/profile_images/<?= htmlspecialchars($img) ?>" width="100" height="100"
                                alt="Foto de perfil">
                            <div class="d-flex justify-content-center gap-2">
                                <label class="btn btn-outline-primary">
                                    <input type="file" id="profileImageInput" name="profile_image" accept="image/*"
                                        hidden>
                                    Cambiar imagen
                                </label>
                                <button type="submit" id="uploadImageBtn" class="btn btn-primary"
                                    disabled>Actualizar</button>
                            </div>
                        </div>
                    </form>

                    <hr>

                    <form id="updateProfileForm">
                        <div class="mb-3">
                            <label for="profileName" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="profileName" name="name"
                                value="<?= htmlspecialchars($name) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="profileEmail" class="form-label">Correo</label>
                            <input type="email" class="form-control" id="profileEmail" name="email"
                                value="<?= htmlspecialchars($email) ?>">
                        </div>

                        <h6 class="mt-4">Cambiar contraseña</h6>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">Nueva contraseña</label>
                            <input type="password" class="form-control" id="newPassword" name="new_password">
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirmar</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirm_password">
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-outline-secondary me-2"
                                data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nueva Pregunta -->
    <div class="modal fade" id="addForumModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Nueva Pregunta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="forumForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="title" class="form-label">Título</label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="Escribe un título descriptivo" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control" id="description" name="description" rows="5" placeholder="Describe tu pregunta con detalles (mínimo 15 caracteres)" minlength="15" required></textarea>
                        </div>
                        <input type="file" id="forumImage" name="forumImage" accept="image/*">
                        <div class="text-end">
                            <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Publicar</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editForumModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Pregunta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editForumForm">
                        <div class="mb-3">
                            <label for="editForumTitle" class="form-label">Título</label>
                            <input type="text" class="form-control" id="editForumTitle" required>
                        </div>
                        <div class="mb-3">
                            <label for="editForumDescription" class="form-label">Descripción</label>
                            <textarea class="form-control" id="editForumDescription" rows="5" required></textarea>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary ms-2">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    */

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/jquery-3.7.1.min.js"></script>
    <script src="../js/viewForum.js"></script>
    <script src="../js/aside.js"></script>
    <script src="../js/uploadForum.js"></script>
    <script src="../js/comment.js"></script>
</body>

</html>