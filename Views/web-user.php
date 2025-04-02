<?php
session_start();

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $name = isset($_SESSION["name"]) ? htmlspecialchars($_SESSION["name"]) : "Nombre no disponible";

    $img = isset($_SESSION["profile_image"]) ? htmlspecialchars($_SESSION["profile_image"]) : "";

    $email = isset($_SESSION["email"]) ? htmlspecialchars($_SESSION["email"]) : 'Correo no disponible';
    $desc = isset($_SESSION["desc"]) ? htmlspecialchars($_SESSION["desc"]) : "descripcion no disponible";
    //echo $img;
} else {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MindVortex</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.0.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/user.css">
    <link rel="shortcut icon" href="../assets/logo.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    

</head>

<body>
    <div class="sidebar">
        <div class="logo-details">
            <img class="opciones" src="../assets/logo.png" alt="logo">
        </div>
        <br><br><br>
        <ul class="nav-list">
            <li>
                <div class="editar" id="mostrar-info">
                    <i class='bx bx-search-alt-2' style='color:#91c6f7'></i>
                    <span class="links_name">Serach</span>
                </div>
                <span class="tooltip">Search</span>
            </li>

            <li>
                <div class="calc" id="calculadora-inp">
                    <i class='bx bx-question-mark' style='color:#91c6f7'></i>
                    <span class="links_name">Questions</span>
                </div>
                <span class="tooltip">Questions</span>
            </li>
            <li>
                <a>
                    <i class='bx bx-star' style='color:#91c6f7'></i>
                    <span class="links_name">Favorites</span>
                </a>
                <span class="tooltip">Favorites</span>
            </li>
            <li>
                <a>
                    <i class='bx bx-history' style='color:#91c6f7'></i>
                    <span class="links_name">History</span>
                </a>
                <span class="tooltip">History</span>
            </li>
            
        </ul>
        <ul>
            <li>
                <a href=" ../backend/logout.php">
                <i class='bx bx-log-out' style='color:#91c6f7'></i>
                    <span class="links_name">Logout</span>
                </a> 
                <span class="tooltip">Logout</span>
            </li>

            <li style="background-color:rgb(61, 61, 61); border-radius: 50%;">
                <a style="border-radius: 50%;">
                <?php if (!empty($img)): ?>
                    <img src="../uploads/profile_images/<?php  echo $img ?>" alt="Imagen de perfil" id="img-avatar">
                <?php else: ?>
                    <i class='bx bx-user' style='color:#91c6f7; font-size: 24px;'></i>
                <?php endif; ?>
                    <span class="links_name">User</span>
                </a> 
                <span class="tooltip">User</span>
            </li>
        </ul>
    </div>
 
    <div class="profile">
        <h2>Perfil</h2>

        <div class="info-user">
            <div class="first-part">
                <?php if (!empty($img)): ?>
                    <img src="../uploads/profile_images/<?php echo $img ?>" alt="Imagen de perfil" id="img-profile">
                <?php else: ?>
                    <i class='bx bx-user' style='color:#91c6f7; font-size: 24px;'></i>
                <?php endif; ?>
                <ul>
                    <li> nombre: <?= $name ?></li>
                    <li> email: <?= $email ?></li>
                    <li> descripcion: <?= $desc ?></li>
                </ul>
                <button>
                    <i class='bx bx-edit-alt' style="color: #fff;"></i>
                    editar
                </button>
            </div>
            <div class="second-part">
                <div class="amount">
                    <i class='bx bx-question-mark' style='color:#fff; font-size: 40px;'></i>
                    <div  class="cantidad-info">
                        <p>15</p>
                        <p>Preguntas</p>
                    </div>
                </div>

                <div class="amount">
                    <i class='bx bxs-message-square-edit' style="font-size: 40px;"></i>
                    <div  class="cantidad-info">
                        <p>50</p>
                        <p>Respuestas</p>
                    </div>
                </div>

                <div class="amount">
                    <i class='bx bx-star' style="color:#fff;font-size: 40px;"></i>
                    <div class="cantidad-info">
                        <p>70</p>
                        <p>Favoritos</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-overlay ocultar" id="modalOverlay"></div>
        <div class="modal ocultar" id="modal">
            <form id="uploadImageForm">
                <img id="imagePreview" style="display:none">
                <progress id="uploadProgress" value="0" max="100" style="display:none"></progress>
                <input type="file" id="profileImageInput" name="profile_image" accept="image/*">
                <button type="submit" id="uploadImageBtn" disabled>Subir imagen</button>
            </form>
            <div class="modal-change">
                <form id="updateProfileForm">
                    <input type="text" name="name" value="<?= $name ?>" placeholder="name">
                    <input type="email" name="email" value="<?= $email ?>" placeholder="email">
                    <textarea name="descripcion" placeholder="descripcion"><?= $desc ?></textarea>
                    <button type="submit" id="updateProfileBtn">Actualizar información</button>
                </form>
    
                <!-- Formulario de cambio de contraseña -->
                <form id="changePasswordForm">
                    <input type="password" name="current_password" placeholder="Contraseña actual">
                    <input type="password" name="new_password" placeholder="Nueva contraseña">
                    <input type="password" name="confirm_password" placeholder="Confirmar contraseña">
                    <button type="submit" id="changePasswordBtn">Cambiar contraseña</button>
                </form>
            </div>

        </div>
    </div>
    
    <script src="../js/jquery-3.7.1.min.js"></script>
    <script src="../js/update.js"></script>

</body>
</html>