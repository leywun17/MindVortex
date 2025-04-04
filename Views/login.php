
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="../css/style_registro.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
</head>
<body>
    <form class="form-container" id="loginForm">
        <h1>Inicio de sesión</h1>
        <p>¡Bienvenido/a de nuevo! Ingresa tus datos a continuación para acceder a tu cuenta!</p>
        <br>
        <div class="input-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required placeholder="Email">
        </div>
        <br>
        <div class="input-group">
            <label for="password">Contraseña:</label>
            <input type="password" name="password" id="password" required placeholder="Contraseña">
        </div>
        <br>

        <button class="sign" type="submit">Iniciar sesion</button>

        <p class="signup">No tienes una cuenta?
            <a rel="noopener noreferrer" href="../Views/register.php" class="">Registrate</a>
        </p>
    </form>
    <script src="../js/jquery-3.7.1.min.js"></script>
    <script src="../js/login.js"></script>
</body>
</html>

