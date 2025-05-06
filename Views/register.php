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
    <div class="rain front-row"></div>
    <div class="rain back-row"></div>
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
    <svg version="1.1" xmlns="http://www.w3.org/2000/svg"
        xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="100%" height="100%" viewBox="0 0 1600 900" preserveAspectRatio="xMidYMax slice">
        <defs>
            <linearGradient id="bg">
                <stop offset="0%" style="stop-color:rgba(130, 158, 249, 0.06)"></stop>
                <stop offset="50%" style="stop-color:rgba(76, 190, 255, 0.6)"></stop>
                <stop offset="100%" style="stop-color:#91C6F7"></stop>
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
    <script src="../js/jquery-3.7.1.min.js"></script>
    <script src="../js/register.js"></script>
    <script src="../js/background.js"></script>
</body>

</html>