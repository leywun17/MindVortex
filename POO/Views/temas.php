
<!-- tema.php - Página que muestra un tema y sus respuestas -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tema - Foro de Discusión</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <div class="container">
        <header>
            <!-- Mismo header que en index.php -->
        </header>
        
        <main>
            <?php
            // Verificar que se proporcionó un ID de tema
            if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                echo "<p>Tema no encontrado. <a href='index.php'>Volver al inicio</a></p>";
                exit;
            }
            
            $tema_id = (int)$_GET['id'];
            
            require_once 'config/conexion.php';
            require_once 'clases/Foro.php';
            
            $foro = new Foro();
            $tema = $foro->obtener_tema($tema_id);
            
            if(!$tema) {
                echo "<p>Tema no encontrado. <a href='index.php'>Volver al inicio</a></p>";
                exit;
            }
            
            // Procesar nueva respuesta si se ha enviado
            if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
                $contenido = trim($_POST['contenido']);
                
                if(!empty($contenido)) {
                    $post_id = $foro->responder_tema($tema_id, $contenido);
                    
                    if($post_id) {
                        // Recargar la página para mostrar la nueva respuesta
                        header("Location: tema.php?id=$tema_id#post-$post_id");
                        exit;
                    }
                }
            }
            ?>
            
            <div class="tema-detalle">
                <div class="tema-cabecera">
                    <h2><?php echo htmlspecialchars($tema['titulo']); ?></h2>
                    <p class="meta">
                        Categoría: <?php echo htmlspecialchars($tema['categoria']); ?> | 
                        Creado: <?php echo date('d/m/Y H:i', strtotime($tema['fecha_creacion'])); ?>
                    </p>
                </div>
                
                <div class="posts">
                    <?php foreach($tema['posts'] as $index => $post): ?>
                    <div class="post" id="post-<?php echo $post['id']; ?>">
                        <div class="post-usuario">
                            <div class="usuario-info">
                                <p class="nombre"><?php echo htmlspecialchars($post['autor']); ?></p>
                                <p class="fecha"><?php echo date('d/m/Y H:i', strtotime($post['fecha_creacion'])); ?></p>
                            </div>
                        </div>
                        
                        <div class="post-contenido">
                            <?php echo nl2br(htmlspecialchars($post['contenido'])); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Formulario para responder -->
                <?php if(isset($_SESSION['user_id'])): ?>
                <div class="responder">
                    <h3>Responder</h3>
                    <form method="post" class="formulario">
                        <div class="campo">
                            <textarea name="contenido" rows="5" required></textarea>
                        </div>
                        <div class="campo">
                            <button type="submit" class="boton">Enviar Respuesta</button>
                        </div>
                    </form>
                </div>
                <?php else: ?>
                <div class="responder">
                    <p>Debes <a href="login.php">iniciar sesión</a> para responder.</p>
                </div>
                <?php endif; ?>
            </div>
        </main>
        
        <footer>
            <!-- Mismo footer que en index.php -->
        </footer>
    </div>
</body>
</html>