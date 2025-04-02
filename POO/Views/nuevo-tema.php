
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Tema - Foro de Discusión</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <div class="container">
        <header>
            <!-- Mismo header que en index.php -->
        </header>
        
        <main>
            <h2>Crear Nuevo Tema</h2>
            
            <?php
            // Verificar si el usuario está logueado
            session_start();
            if(!isset($_SESSION['user_id'])) {
                echo "<p>Debes iniciar sesión para crear un nuevo tema. <a href='login.php'>Iniciar sesión</a></p>";
            } else {
                // Procesar el formulario si se ha enviado
                if($_SERVER['REQUEST_METHOD'] == 'POST') {
                    require_once 'config/conexion.php';
                    require_once 'clases/Foro.php';
                    
                    $foro = new Foro();
                    
                    $titulo = trim($_POST['titulo']);
                    $contenido = trim($_POST['contenido']);
                    $categoria = $_POST['categoria'];
                    
                    // Validaciones básicas
                    $errores = [];
                    
                    if(empty($titulo)) {
                        $errores[] = "El título es obligatorio";
                    }
                    
                    if(empty($contenido)) {
                        $errores[] = "El contenido es obligatorio";
                    }
                    
                    if(empty($errores)) {
                        $tema_id = $foro->crear_tema($titulo, $contenido, $categoria);
                        
                        if($tema_id) {
                            // Redirigir al tema creado
                            header("Location: tema.php?id=$tema_id");
                            exit;
                        } else {
                            echo "<div class='error'>Error al crear el tema. Por favor, inténtalo de nuevo.</div>";
                        }
                    } else {
                        // Mostrar errores
                        echo "<div class='error'>";
                        foreach($errores as $error) {
                            echo "<p>$error</p>";
                        }
                        echo "</div>";
                    }
                }
            ?>
                
            <form method="post" class="formulario">
                <div class="campo">
                    <label for="titulo">Título:</label>
                    <input type="text" id="titulo" name="titulo" required value="<?php echo isset($_POST['titulo']) ? htmlspecialchars($_POST['titulo']) : ''; ?>">
                </div>
                
                <div class="campo">
                    <label for="categoria">Categoría:</label>
                    <select id="categoria" name="categoria">
                        <option value="general">General</option>
                        <option value="programacion">Programación</option>
                        <option value="diseño">Diseño</option>
                        <!-- Añade más categorías según sea necesario -->
                    </select>
                </div>
                
                <div class="campo">
                    <label for="contenido">Contenido:</label>
                    <textarea id="contenido" name="contenido" rows="10" required><?php echo isset($_POST['contenido']) ? htmlspecialchars($_POST['contenido']) : ''; ?></textarea>
                </div>
                
                <div class="campo">
                    <button type="submit" class="boton">Crear Tema</button>
                </div>
            </form>
            <?php } ?>
        </main>
        
        <footer>
            <!-- Mismo footer que en index.php -->
        </footer>
    </div>
</body>
</html>