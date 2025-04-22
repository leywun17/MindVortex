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
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="shortcut icon" href="../Assets/logo.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container-fluid d-grid gap-3">
        <div class="row">
            <nav class="navbar navbar-expand-md navbar-dark">
                <div class="container rounded-4 text-bg-dark ps-n2 contenedor-header p-2">
                    <a class="navbar-brand d-flex align-items-center" href="">
                        <img src="../Assets/logo.png" alt="Flowbite Logo" height="38" style="position: relative; left: 15px;">
                    </a>

                    <div class="d-flex align-items-center order-md-2">
                        <div class="dropdown d-grid gap-3 position-relative">
                            <button class="btn p-1 d-flex align-items-center justify-content-center" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false" style="position: relative; right: 5px;">
                                <?php if (!empty($img)): ?>
                                    <img src="../uploads/profile_images/<?php echo $img ?>" alt="user photo" class="rounded-circle bg-light d-block" width="32" height="32">
                                <?php else: ?>
                                    <i class='bx bx-user rounded-circle bg-dark d-block p-2' style='color:#91c6f7; font-size: 24px;'></i>
                                <?php endif; ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end rounded" aria-labelledby="userMenu" style="background-color: #13293D; z-index: 100;">
                                <li class="dropdown-header d-flex align-items-center
                                    flex-column">
                                    <?php if (!empty($img)): ?>
                                        <img src="../uploads/profile_images/<?php echo $img ?>" alt="user photo" class="rounded-circle bg-light d-block" width="48" height="48">
                                    <?php else: ?>
                                        <i class='bx bx-user rounded-circle bg-dark d-block p-2' style='color:#91c6f7; font-size: 24px;'></i>
                                    <?php endif; ?>
                                    <strong><?php echo $name ?></strong>
                                    <small><?php echo $email ?></small>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" style="color: #fff;" href="#modalUser" data-bs-toggle="modal">Edit</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" style="color: #fff;" href="../Backend/logout.php">Sign out</a></li>
                            </ul>
                        </div>
                        <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarUser" aria-controls="navbarUser" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                    </div>

                    <div class="collapse navbar-collapse order-md-1" id="navbarUser">
                        <ul class="navbar-nav mx-auto mb-2 mb-md-0 paginas" style="gap: 10px;">
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


                        <form class="d-flex" style="margin-right: 10px;">
                            <div class="position-relative w-110">
                                <input type="search" class="form-control" placeholder="Buscar" aria-label="Search">
                                <button type="submit" class="btn btn-link position-absolute top-50 end-0 translate-middle-y me-2 text-dark">
                                    <i class='bx bx-search-alt-2' style="color: #91C6F7;"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </nav>
        </div>

        <div class="container contenedor-principal p-5" id="contenidoForo" style="border-radius: 50px; position: relative; height: 80vh;">
            <div class="header d-flex justify-content-between gap-3 text-light mb-5">
                <h2 style="color: #91C6F7; font-weight: bold;" id="titulo"></h2>
                <div class="dropdown">
                    <button class="btn p-1 d-flex align-items-center justify-content-center" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false" style="position: relative; right: 5px;"> <i class='bx bx-dots-vertical-rounded' style="color:#91C6F7; font-size:35px;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end rounded" aria-labelledby="userMenu" style="background-color: #13293D; z-index: 100;">
                        <li class="dropdown-header d-flex align-items-center justify-content-center gap-2" style="font-size: 17px;">
                            <i class='bx bx-star' style='color:#ebff00'></i>
                            favoritos
                        </li>
                        <hr>
                        <li id="opcionEliminar" class="dropdown-header d-flex align-items-center justify-content-center gap-2 d-none" style="font-size: 17px; cursor: pointer;">
                            <i class='bx bx-trash' style='color:#ff0000'></i>
                            Eliminar
                        </li>
                        <hr id="separadorEliminar" class="d-none">

                        <li id="opcionEditar" class="dropdown-header d-flex align-items-center justify-content-center gap-2 d-none" style="font-size: 17px; cursor: pointer;">
                            <i class='bx bx-trash' style='color:#ff0000'></i>
                            Eliminar
                        </li>
                        <hr id="separadorEditar" class="d-none">

                        <li class="dropdown-header d-flex align-items-center justify-content-center gap-2" style="font-size: 17px;">
                            <i class='bx bx-error' style='color:#ff0000'></i>
                            Reportar
                        </li>
                    </ul>
                </div>
            </div>
            <div class="body d-flex gap-3 justify-content-between text-light flex-column">
                <div class="d-flex justify-content-between">
                    <p id="descripcion"></p>
                    <div class="d-flex gap-3">
                        <img id="imagenUsuario" src="" alt="Foto de usuario" class="rounded-3 bg-light d-block" width="24" height="24">
                        <p><span id="autor"></span></p>
                        <p><span id="fecha"></span></p>
                    </div>
                </div>
                <hr>
                <div class="d-flex gap-3">
                    <div class="h3">Comentarios</div>
                    <div id="commentSection">
                        <div id="commentContainer"></div>
                        <form id="commentForm">
                            <textarea id="commentInput" placeholder="Escribe tu comentario..."></textarea>
                            <button type="submit">Publicar</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalUser" aria-hidden="true" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content modalCuerpo d-flex align-items-center justify-content-around">
                    <div class="modal-header d-flex">
                        Editar Perfil
                    </div>
                    <div class="cuerpo-modal gap-3">
                        <form id="uploadImageForm">
                            <div class="image-upload-container gap-3">
                                <div class="">
                                    <img id="imagePreview" style="width: 100px; height: 100px; border: radius 50%;">
                                    <div class="uploadImageBtn" disabled>
                                        <label for="" class="btn-file">
                                            <input type="file" class="file-input" id="profileImageInput" name="profile_image" accept="image/*" required>
                                        </label>
                                        +
                                    </div>
                                </div>
                                <button type="submit" id="uploadImageBtn" disabled>Actualizar Imagen</button>
                            </div>
                        </form>

                        <div class="modal-change d-flex justify-content-center gap-3">
                            <form id="updateProfileForm" class="actualizar">
                                <div class="d-flex gap-3 justify-content-center flex-column">
                                    <input type="text" name="name" value="<?= $name ?>" placeholder="name">
                                    <input type="email" name="email" value="<?= $email ?>" placeholder="email">
                                </div>
                                <button type="submit" id="updateProfileBtn">Actualizar información</button>
                                <i class="fas fa-plus"></i>
                            </form>

                            <form id="changePasswordForm" class="actualizar">
                                <div class="d-flex gap-3 justify-content-center flex-column">
                                    <input type="password" name="new_password" placeholder="Nueva contraseña">
                                    <input type="password" name="confirm_password" placeholder="Confirmar contraseña">
                                </div>
                                <button type="submit" id="changePasswordBtn">Cambiar contraseña</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row d-md-block d-xl-block d-xxl-block ">
        <footer class="container-fluid fixed-bottom z-index-n2 d-flex align-items-center p-0 m-0" style=" z-index: -1; bottom:-43px;">
            <img src="../Assets/Mask group.svg" alt="Footer SVG" class="img-fluid w-100" style="height: 100px; z-index: -1;">
        </footer>
    </div>
    <script>
        let usuarioActualForoId = <?php echo json_encode($foro_id); ?>;
    </script>
    <script src="../js/jquery-3.7.1.min.js"></script>
    <script src="../js/verForo.js"></script>
    <script src="../js/eliminarForo.js"></script>
    <script src="../js/update.js"></script>
    <script src="../js/comment.js"></script>
</body>

</html>