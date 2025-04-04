<?php
// Encabezados requeridos
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

session_start(); // Asegúrate de iniciar la sesión antes de acceder a las variables de sesión.
header('Content-Type: application/json');



require_once 'config.php';

class Forum
{
    // Conexión a la base de datos y nombre de la tabla
    private $conn;
    private $table_name = "foros";

    // Propiedades de objeto
    public $id;
    public $titulo;
    public $descripcion;
    public $id_usuario;
    public $fecha_creacion;
    public $nombre_usuario; // Para JOIN con la tabla de usuarios
    public $imagen_usuario; // Para JOIN con la tabla de usuarios

    // Constructor con $db como conexión a la base de datos
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Crear un nuevo foro
    public function create()
    {
        // Sanitizar datos
        $this->titulo = htmlspecialchars(strip_tags($this->titulo));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        $this->id_usuario = htmlspecialchars(strip_tags($this->id_usuario));

        // Consulta para insertar
        $query = "INSERT INTO " . $this->table_name . " 
                  SET titulo = :titulo, 
                      descripcion = :descripcion, 
                      id_usuario = :id_usuario";

        // Preparar consulta
        $stmt = $this->conn->prepare($query);

        // Vincular valores
        $stmt->bindParam(':titulo', $this->titulo);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':id_usuario', $this->id_usuario);

        // Ejecutar consulta
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // Leer todos los foros con información de usuario
    public function readAll()
    {
        // Consulta con JOIN
        $query = "SELECT f.id, f.titulo, f.descripcion, f.fecha_creacion, u.name, 
                 COALESCE(u.profile_image, 'default.jpg') AS profile_image
          FROM " . $this->table_name . " f
          INNER JOIN users u ON f.id_usuario = u.id
          ORDER BY f.fecha_creacion DESC";

        // Preparar consulta
        $stmt = $this->conn->prepare($query);

        // Ejecutar consulta
        $stmt->execute();

        return $stmt;
    }

    // Leer un foro específico
    public function readOne()
    {
        // Consulta para leer un solo foro con información de usuario
        $query = "SELECT f.id, f.titulo, f.descripcion, f.fecha_creacion, 
                         u.name, u.profile_image
                  FROM " . $this->table_name . " f
                  INNER JOIN users u ON f.id_usuario = u.id
                  WHERE f.id = :id";

        // Preparar consulta
        $stmt = $this->conn->prepare($query);

        // Vincular ID
        $stmt->bindParam(':id', $this->id);

        // Ejecutar consulta
        $stmt->execute();

        // Obtener fila
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row['id'];
            $this->titulo = $row['titulo'];
            $this->descripcion = $row['descripcion'];
            $this->fecha_creacion = $row['fecha_creacion'];
            $this->nombre_usuario = $row['name'];
            $this->imagen_usuario = $row['profile_image'];
            return true;
        }

        return false;
    }
}
// Instanciar la base de datos
$database = new Database();
$db = $database->getConnection();

// Instanciar el objeto foro
$forum = new Forum($db);

// Obtener datos enviados
$data = json_decode(file_get_contents("php://input"));

// Respuesta por defecto
$response = array(
    "exito" => false,
    "mensaje" => "Acción no reconocida"
);

// Verificar método de solicitud
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        // Verificar acción solicitada
        if (isset($_GET['action']) && $_GET['action'] === 'create') {
            

            // Verificar datos recibidos
            if (!empty($data->titulo) && !empty($data->descripcion)) {
                // Asignar valores a propiedades del foro
                $forum->titulo = $data->titulo;
                $forum->descripcion = $data->descripcion;
                $forum->id_usuario = $_SESSION['id'];

                // Crear foro
                if ($forum->create()) {
                    $response = array(
                        "exito" => true,
                        "mensaje" => "Foro creado correctamente",
                        "id" => $forum->id
                    );
                } else {
                    $response = array(
                        "exito" => false,
                        "mensaje" => "Error al crear el foro"
                    );
                }
            } else {
                $response = array(
                    "exito" => false,
                    "mensaje" => "El título y la descripción son obligatorios"
                );
            }
        }
        break;

    case 'GET':
        // Verificar acción solicitada
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'read':
                    // Leer todos los foros
                    $stmt = $forum->readAll();
                    $num = $stmt->rowCount();

                    // Verificar si hay foros
                    if ($num > 0) {
                        $forums_arr = array();
                        $forums_arr["foros"] = array();

                        // Obtener resultados
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            extract($row);

                            $forum_item = array(
                                "id" => $id,
                                "titulo" => $titulo,
                                "descripcion" => $descripcion,
                                "fecha_creacion" => $fecha_creacion,
                                "nombre_usuario" => $name,
                                "imagen_usuario"=>$profile_image
                            );

                            array_push($forums_arr["foros"], $forum_item);
                        }

                        $response = array(
                            "exito" => true,
                            "foros" => $forums_arr["foros"]
                        );
                    } else {
                        $response = array(
                            "exito" => true,
                            "foros" => array()
                        );
                    }
                    break;

                case 'read_one':
                    // Verificar ID
                    if (isset($_GET['id'])) {
                        $forum->id = $_GET['id'];

                        // Leer un foro específico
                        if ($forum->readOne()) {
                            $response = array(
                                "exito" => true,
                                "foro" => array(
                                    "id" => $forum->id,
                                    "titulo" => $forum->titulo,
                                    "descripcion" => $forum->descripcion,
                                    "fecha_creacion" => $forum->fecha_creacion,
                                    "nombre_usuario" => $forum->nombre_usuario,
                                    "imagen_usuario" => $forum->imagen_usuario
                                )
                            );
                        } else {
                            $response = array(
                                "exito" => false,
                                "mensaje" => "Foro no encontrado"
                            );
                        }
                    } else {
                        $response = array(
                            "exito" => false,
                            "mensaje" => "ID no especificado"
                        );
                    }
                    break;
            }
        }
        break;
}

// Enviar respuesta
echo json_encode($response);
?>