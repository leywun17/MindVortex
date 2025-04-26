<?php
// Inicia la sesión y verifica si el usuario está autenticado
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../index.html");
    exit();
}

// Obtiene los datos del usuario desde la sesión
$profileImage = $_SESSION["profile_image"] ?? "";
$userName     = $_SESSION['name'];
$userEmail    = $_SESSION["email"] ?? 'Correo no disponible';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MindVortex</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Librería de íconos Boxicons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <!-- Tipografía Nunito desde Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="../css/dashboard.css">

    <!-- Favicon -->
    <link rel="shortcut icon" href="../Assets/logo.png" type="image/x-icon">
</head>

<body class="d-flex flex-column">

    <!-- Barra de navegación principal -->
    <nav class="navbar navbar-expand-md navbar-dark">
        <div class="container rounded-4 text-bg-dark contenedor-header p-2">

            <!-- Logo de la marca -->
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="../Assets/logo.png" alt="Flowbite Logo" height="38" class="logo-pos">
            </a>

            <!-- Menú de usuario y botón hamburguesa -->
            <div class="d-flex align-items-center order-md-2">
                <div class="dropdown d-grid gap-3 position-relative">
                    <!-- Botón de avatar del usuario -->
                    <button class="btn p-1 d-flex align-items-center justify-content-center" type="button" id="userMenuToggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="../uploads/profile_images/<?php echo $profileImage ?>" alt="user photo" class="rounded-circle bg-light d-block" width="32" height="32">
                    </button>

                    <!-- Menú desplegable de usuario -->
                    <ul class="dropdown-menu dropdown-menu-end rounded fondo-dropdown" aria-labelledby="userMenuToggle">
                        <li class="dropdown-header d-flex align-items-center flex-column">
                            <img src="../uploads/profile_images/<?php echo $profileImage ?>" alt="user photo" class="rounded-circle bg-light d-block" width="48" height="48">
                            <strong><?php echo $userName ?></strong>
                            <small><?php echo $userEmail ?></small>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-white" href="#editProfileModal" data-bs-toggle="modal">Edit</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-white" href="../Backend/logout.php">Sign out</a></li>
                    </ul>
                </div>

                <!-- Botón hamburguesa para pantallas pequeñas -->
                <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <!-- Enlaces de navegación -->
            <div class="collapse navbar-collapse order-md-1" id="mainNav">
                <ul class="navbar-nav mx-auto mb-2 mb-md-0 paginas gap-2">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">Preguntas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'favorite.php' ? 'active' : ''; ?>" href="favorite.php">Favoritos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'history.php' ? 'active' : ''; ?>" href="history.php">Historial</a>
                    </li>
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

    <!-- Contenido principal -->
    <main class="flex-fill container">
        <div class="cuerpo p-3 flex-fill">
            <h2 class="mb-4">Preguntas</h2>
            <div id="forumList" class="d-flex flex-wrap gap-3 justify-content-center"></div>
            <div id="paginationContainer" class="d-flex justify-content-center mt-3"></div>
        </div>
    </main>

    <!-- Botón flotante para agregar foro -->
    <button
        class="btn add-foro-btn rounded-circle"
        data-bs-toggle="modal"
        data-bs-target="#addForumModal">
        <i class='bx bx-plus'></i>
    </button>

    <!-- Modal para agregar una nueva pregunta -->
    <div class="modal fade" id="addForumModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4">
                <h5 class="modal-title mb-3">Agregar Pregunta</h5>
                <form id="forumForm">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="title" name="title" placeholder="Título" required>
                    </div>
                    <div class="mb-3">
                        <textarea class="form-control" id="description" name="description" rows="5" placeholder="Descripción (mínimo 15 caracteres)" minlength="15" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Publicar Foro</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para editar perfil -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content p-4">
                <h5 class="modal-title mb-3">Editar Perfil</h5>

                <!-- Formulario para subir imagen -->
                <form id="uploadImageForm" class="mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <img id="imagePreview" class="rounded-circle user-avatar-lg" src="#" alt="">
                        <label class="btn btn-outline-secondary mb-0">
                            <input type="file" id="profileImageInput" name="profile_image" accept="image/*" hidden>
                            Cambiar imagen
                        </label>
                        <button type="submit" id="uploadImageBtn" class="btn btn-primary" disabled>Actualizar Imagen</button>
                    </div>
                </form>

                <!-- Formularios para editar datos y cambiar contraseña -->
                <div class="d-flex gap-4">
                    <!-- Formulario para actualizar nombre y correo -->
                    <form id="updateProfileForm" class="flex-fill">
                        <div class="mb-3">
                            <input type="text" name="name" value="<?= htmlspecialchars($userName) ?>" class="form-control" placeholder="Nombre">
                        </div>
                        <div class="mb-3">
                            <input type="email" name="email" value="<?= htmlspecialchars($userEmail) ?>" class="form-control" placeholder="Correo">
                        </div>
                        <button type="submit" id="updateProfileBtn" class="btn btn-primary w-100">Actualizar información</button>
                    </form>

                    <!-- Formulario para cambiar contraseña -->
                    <form id="changePasswordForm" class="flex-fill">
                        <div class="mb-3">
                            <input type="password" name="new_password" class="form-control" placeholder="Nueva contraseña">
                        </div>
                        <div class="mb-3">
                            <input type="password" name="confirm_password" class="form-control" placeholder="Confirmar contraseña">
                        </div>
                        <button type="submit" id="changePasswordBtn" class="btn btn-secondary w-100">Cambiar contraseña</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie de página con imagen SVG -->
    <footer class="footer-img fixed-bottom">
        <img src="../Assets/Mask group.svg" alt="Footer SVG" class="w-100">
    </footer>

    <!-- Scripts necesarios -->
    <script src="../js/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/update.js"></script>
    <script src="../js/uploadForum.js"></script>
    <script src="../js/viewForum.js"></script>
</body>

</html>
