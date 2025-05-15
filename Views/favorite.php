<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../index.html");
    exit();
}
$img   = $_SESSION["profile_image"] ?? "";
$name  = $_SESSION['name'];
$email = $_SESSION["email"] ?? 'Correo no disponible';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MindVortex</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">

    <!-- Tus estilos -->
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/fav.css">

    <link rel="shortcut icon" href="../Assets/logo.png" type="image/x-icon">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container-fluid px-3">
            <!-- Botón para sidebar -->
            <button class="btn" id="btn">
                <i class='bx bx-menu fs-4'></i>
            </button>

            <!-- Logo de la marca -->
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="../Assets/logo.png" alt="MindVortex Logo" height="38" class="d-inline-block">
                <p class="text-center">MindVortex</p>
            </a>

            <!-- Formulario de búsqueda -->
            <form id="searchForm" class="d-flex mx-auto d-none d-md-flex" style="max-width: 400px;">
                <div class="input-group">
                    <input
                        id="searchInput"
                        type="search"
                        class="form-control rounded-pill"
                        placeholder="Buscar foros..."
                        aria-label="Buscar"
                        autocomplete="off">
                    <button
                        id="searchBtn"
                        type="submit"
                        class="btn btn-link position-absolute top-50 end-0 translate-middle-y me-3">
                        <i class='bx bx-search-alt-2 fs-5 text-secondary'></i>
                    </button>
                </div>
            </form>

            <!-- Botón hamburguesa para pantallas pequeñas -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Enlaces de navegación y menú usuario -->
            <div class="collapse navbar-collapse" id="mainNav">
                <!-- Búsqueda en móvil -->
                <form id="mobileSearchForm" class="d-flex d-md-none mt-2 mb-3 w-100">
                    <div class="input-group">
                        <input
                            type="search"
                            class="form-control rounded-pill"
                            placeholder="Buscar foros..."
                            aria-label="Buscar"
                            autocomplete="off">
                        <button
                            type="submit"
                            class="btn btn-link position-absolute top-50 end-0 translate-middle-y me-3">
                            <i class='bx bx-search-alt-2 fs-5 text-secondary'></i>
                        </button>
                    </div>
                </form>

                <!-- Menú de usuario -->
                <div class="ms-auto">
                    <div class="dropdown">
                        <button class="btn p-1 d-flex align-items-center gap-2 rounded-pill px-2 border" type="button" id="userMenuToggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="../uploads/profile_images/<?php echo $img ?>" alt="user photo" class="rounded-circle bg-light" width="32" height="32">
                            <span class="d-none d-lg-block"><?php echo $name ?></span>
                            <i class='bx bx-chevron-down'></i>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userMenuToggle">
                            <li class="dropdown-header d-flex align-items-center flex-column p-3">
                                <img src="../uploads/profile_images/<?php echo $img ?>" alt="user photo" class="rounded-circle bg-light d-block mb-2" width="64" height="64">
                                <strong class="mb-1"><?php echo $name ?></strong>
                                <small class="text-muted"><?php echo $email ?></small>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item d-flex gap-2 align-items-center" href="#editProfileModal" data-bs-toggle="modal"><i class='bx bx-edit'></i>Editar perfil</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item d-flex gap-2 align-items-center text-danger" href="../Backend/logout.php"><i class='bx bx-log-out'></i>Cerrar sesión</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="d-flex">
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
        <div class="flex-fill contenedor-principal">
            <div class="container">
                <h2 class="fw-bold">Favoritos</h2>
                <div class="container mt-5">
                    <div id="contenedor-favoritos" class="row"></div>
                </div>
            </div>
        </div>

    </div>



    <!-- Modal Editar Perfil -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Editar Perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Formulario para subir imagen -->
                    <form id="uploadImageForm" class="mb-4 text-center">
                        <div class="mb-3">
                            <img id="imagePreview" class="rounded-circle mb-3" src="../uploads/profile_images/<?php echo $img ?>" width="100" height="100" alt="Foto de perfil">
                            <div class="d-flex justify-content-center gap-2">
                                <label class="btn btn-outline-primary">
                                    <input type="file" id="profileImageInput" name="profile_image" accept="image/*" hidden>
                                    Cambiar imagen
                                </label>
                                <button type="submit" id="uploadImageBtn" class="btn btn-primary" disabled>Actualizar</button>
                            </div>
                        </div>
                    </form>

                    <hr>

                    <!-- Formulario de datos de perfil -->
                    <form id="updateProfileForm">
                        <div class="mb-3">
                            <label for="profileName" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="profileName" name="name" value="<?= htmlspecialchars($name) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="profileEmail" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="profileEmail" name="email" value="<?= htmlspecialchars($email) ?>">
                        </div>

                        <h6 class="mt-4">Cambiar contraseña</h6>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">Nueva contraseña</label>
                            <input type="password" class="form-control" id="newPassword" name="new_password" placeholder="Dejar en blanco para mantener la actual">
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirmar contraseña</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirm_password" placeholder="Confirma la nueva contraseña">
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" id="updateProfileBtn" class="btn btn-primary">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
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

    <footer class="footer-img fixed-bottom">
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



    <!-- Scripts -->
    <script src="../js/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/update.js"></script>
    <script src="../js/favorites.js"></script>
    <script src="../js/uploadForum.js"></script>
    <script src="../js/viewForum.js"></script>
    <script src="../js/aside.js"></script>
</body>

</html>