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

<body class="d-flex flex-column">

    <nav class="navbar navbar-expand-md navbar-dark">
        <div class="container rounded-4 text-bg-dark contenedor-header px-3 gap-2">

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

            </div>
        </div>
    </nav>

    <main class="flex-fill container">
        <h2 class="text-center titulo">Favoritos</h2>
        <div class="container mt-5">
            <div id="contenedor-favoritos" class="row"></div>
        </div>
    </main>

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
                        <button type="submit" id="uploadImageBtn" class="btn btn-primary" disabled>Actualizar
                            Imagen</button>
                    </div>
                </form>
                <div class="d-flex gap-4">
                    <form id="updateProfileForm" class="flex-fill">
                        <div class="mb-3">
                            <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" class="form-control"
                                placeholder="Nombre">
                        </div>
                        <div class="mb-3">
                            <input type="email" name="email" value="<?= htmlspecialchars($email) ?>"
                                class="form-control" placeholder="Correo">
                        </div>
                        <button type="submit" id="updateProfileBtn" class="btn btn-primary w-100">Actualizar
                            información</button>
                    </form>
                    <form id="changePasswordForm" class="flex-fill">
                        <div class="mb-3">
                            <input type="password" name="new_password" class="form-control"
                                placeholder="Nueva contraseña">
                        </div>
                        <div class="mb-3">
                            <input type="password" name="confirm_password" class="form-control"
                                placeholder="Confirmar contraseña">
                        </div>
                        <button type="submit" id="changePasswordBtn" class="btn btn-secondary w-100">Cambiar
                            contraseña</button>
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
    <script src="../js/favorites.js"></script>
    <script src="../js/viewForum.js"></script>
</body>

</html>