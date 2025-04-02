
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro</title>
    <link rel="stylesheet" href="../css/style_registro.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


</head>

<body>
    <div class="form-container" id="registerContainer">
        <h1>Registrarse</h1>
        <p>¡Bienvenido/a! Únete a nuestra comunidad y accede a contenido exclusivo!</p>

        <div class="input-group">
            <label for="name">Nombre:</label>
            <input type="text" name="name" required placeholder="Nombre">
        </div>
        <br>

        <div class="input-group">
            <label for="email">Email:</label>
            <input type="email" name="email" required placeholder="Email">
        </div>
        <br>

        <div class="input-group">
            <label for="password">Contraseña:</label>
            <input type="password" name="password" required placeholder="Contraseña">
        </div>
        <br>

        <div class="input-group">
            <label for="confirmPassword">Confirmar Contraseña:</label>
            <input type="password" name="confirmPassword" required placeholder="Confirmar Contraseña">
        </div>
        <br>

        <button class="sign" type="submit">Registrar</button>

        <p class="signup">¿Tienes una cuenta?
            <a rel="noopener noreferrer" href="../Views/login.php" class="">Inicia sesión</a>
        </p>
    </div>
    <script src="../js/jquery-3.7.1.min.js"></script>
    <script src="../js/register.js"></script>
</body>

</html>