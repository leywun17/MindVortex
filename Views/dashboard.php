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
    <link
        href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">

    <!-- Tus estilos -->
    <link rel="stylesheet" href="../css/dashboard.css">

    <link rel="shortcut icon" href="../Assets/logo.png" type="image/x-icon">
</head>

<body class="d-flex flex-column">

    <nav class="navbar navbar-expand-md navbar-dark">
        <div class="container px-3 py-2">
            <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
                <img src="../Assets/logo.png" alt="Logo" height="38" class="ms-2">
            </a>
            
            <div class="collapse navbar-collapse" id="navbarUser">
                <ul class="navbar-nav mx-auto paginas gap-2">
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
                <form class="d-flex me-3 position-relative">
                    <input
                        class="form-control search-input"
                        type="search"
                        placeholder="Buscar"
                        aria-label="Search">
                    <button class="btn btn-link search-btn" type="submit">
                        <i class='bx bx-search-alt-2'></i>
                    </button>
                </form>
            </div>
            <div class="dropdown">
                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarUser"
                    aria-controls="navbarUser"
                    aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <button
                    class="btn user-btn"
                    id="userMenu"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <?php if ($img): ?>
                        <img
                            src="../uploads/profile_images/<?= htmlspecialchars($img) ?>"
                            alt="user photo"
                            class="rounded-circle user-avatar">
                    <?php else: ?>
                        <i class='bx bx-user user-icon'></i>
                    <?php endif; ?>
                </button>



                <ul class="dropdown-menu dropdown-menu-end user-dropdown" aria-labelledby="userMenu">
                    <li class="dropdown-header text-center">
                        <?php if ($img): ?>
                            <img
                                src="../uploads/profile_images/<?= htmlspecialchars($img) ?>"
                                alt="user photo"
                                class="rounded-circle user-avatar-lg">
                        <?php else: ?>
                            <i class='bx bx-user user-icon-lg'></i>
                        <?php endif; ?>
                        <strong class="d-block"><?= htmlspecialchars($name) ?></strong>
                        <small class="text-muted"><?= htmlspecialchars($email) ?></small>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-light" href="#modalUser" data-bs-toggle="modal">Edit</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-light" href="../Backend/logout.php">Sign out</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="flex-fill container">
        <div class="cuerpo p-3 flex-fill">
            <h2 class="mb-4">Preguntas</h2>
            <div id="listaForos" class="d-flex flex-wrap gap-3 justify-content-center"></div>
            <div id="pagination-container" class="d-flex justify-content-center mt-3"></div>
        </div>
    </main>

    <button
        class="btn add-foro-btn rounded-circle"
        data-bs-toggle="modal"
        data-bs-target="#Agregarforo">
        <i class='bx bx-plus'></i>
    </button>

    <!-- Modal Agregar Foro -->
    <div class="modal fade" id="Agregarforo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4">
                <h5 class="modal-title mb-3">Agregar Pregunta</h5>
                <form id="formularioForo">
                    <div class="mb-3">
                        <input
                            type="text"
                            class="form-control"
                            id="titulo"
                            name="titulo"
                            placeholder="Título"
                            required>
                    </div>
                    <div class="mb-3">
                        <textarea
                            class="form-control"
                            id="descripcion"
                            name="descripcion"
                            rows="5"
                            placeholder="Descripción (mínimo 15 caracteres)"
                            minlength="15"
                            required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Publicar Foro</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Perfil -->
    <div class="modal fade" id="modalUser" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content p-4">
                <h5 class="modal-title mb-3">Editar Perfil</h5>
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
                <div class="d-flex gap-4">
                    <form id="updateProfileForm" class="flex-fill">
                        <div class="mb-3">
                            <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" class="form-control" placeholder="Nombre">
                        </div>
                        <div class="mb-3">
                            <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" class="form-control" placeholder="Correo">
                        </div>
                        <button type="submit" id="updateProfileBtn" class="btn btn-primary w-100">Actualizar información</button>
                    </form>
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

    <footer class="footer-img fixed-bottom">
        <img src="../Assets/Mask group.svg" alt="Footer SVG" class="w-100">
    </footer>

    <!-- Scripts -->
    <script src="../js/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/update.js"></script>
    <script src="../js/subirForo.js"></script>
    <script src="../js/verForo.js"></script>
</body>

</html>