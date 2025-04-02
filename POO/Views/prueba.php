<?php 
session_start();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foro de Discusión</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>Foro de Discusión</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="nuevo-tema.php">Nuevo Tema</a></li>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <li><a href="login.php">Iniciar Sesión</a></li>
                        <li><a href="registro.php">Registrarse</a></li>
                    <?php else: ?>
                        <li><a href="perfil.php">Mi Perfil</a></li>
                        <li><a href="logout.php">Cerrar Sesión</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </header>

        <main>
            <div class="filtros">
                <h3>Categorías</h3>
                <ul>
                    <li><a href="index.php">Todas</a></li>
                    <li><a href="index.php?categoria=general">General</a></li>
                    <li><a href="index.php?categoria=programacion">Programación</a></li>
                    <li><a href="index.php?categoria=diseño">Diseño</a></li>
                    <!-- Añade más categorías según sea necesario -->
                </ul>
            </div>

            <div class="temas-lista">
                <div class="acciones">
                    <a href="nuevo-tema.php" class="boton">Crear Nuevo Tema</a>
                </div>

                <?php
                // Incluir archivos necesarios
                require_once '../Backend/config.php';
                require_once '../Backend/Foro.php';

                // Inicializar el foro
                $foro = new Foro();

                // Obtener categoría si existe
                $categoria = isset($_GET['categoria']) ? $_GET['categoria'] : null;

                // Obtener página actual
                $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

                // Obtener lista de temas
                $temas = $foro->listar_temas($categoria, $pagina);

                if (empty($temas)) {
                    echo "<p>No hay temas disponibles.</p>";
                } else {
                    // Mostrar los temas
                    foreach ($temas as $tema) {
                ?>
                        <div class="tema">
                            <div class="tema-info">
                                <h3><a href="tema.php?id=<?php echo $tema['id']; ?>"><?php echo htmlspecialchars($tema['titulo']); ?></a></h3>
                                <p class="meta">
                                    Publicado por: <?php echo htmlspecialchars($tema['autor']); ?> |
                                    Categoría: <?php echo htmlspecialchars($tema['categoria']); ?> |
                                    Respuestas: <?php echo $tema['num_respuestas'] - 1; ?>
                                </p>
                                <p class="ultima-actividad">
                                    Última actividad: <?php echo date('d/m/Y H:i', strtotime($tema['ultima_actividad'])); ?>
                                </p>
                            </div>
                        </div>
                <?php
                    }

                    // Paginación básica
                    // Aquí irían los enlaces de paginación
                }
                ?>
            </div>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> Mi Foro de Discusión</p>
        </footer>
    </div>
</body>

</html>