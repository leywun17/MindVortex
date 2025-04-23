<?php
// Encabezados CORS y JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

session_start();

// Responder preflight CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'config.php';

class Forum
{
    private $conn;
    private $table_name = "foros";

    public $id;
    public $titulo;
    public $descripcion;
    public $id_usuario;
    public $fecha_creacion;
    public $nombre_usuario;
    public $imagen_usuario;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        $this->titulo      = htmlspecialchars(strip_tags($this->titulo));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        $this->id_usuario  = htmlspecialchars(strip_tags($this->id_usuario));

        $query = "INSERT INTO {$this->table_name}
                  SET titulo = :titulo,
                      descripcion = :descripcion,
                      id_usuario = :id_usuario";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':titulo',      $this->titulo);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':id_usuario',  $this->id_usuario);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function readAll()
    {
        $query = "SELECT f.id, f.titulo, f.descripcion, DATE(f.fecha_creacion) AS fecha_creacion,
                         u.name, COALESCE(u.profile_image,'default.jpg') AS profile_image
                  FROM {$this->table_name} f
                  INNER JOIN users u ON f.id_usuario = u.id
                  ORDER BY DATE(f.fecha_creacion) DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne()
    {
        $query = "SELECT f.id, f.titulo, f.descripcion, DATE(f.fecha_creacion) AS fecha_creacion, f.id_usuario,
                         u.name, u.profile_image
                  FROM {$this->table_name} f
                  INNER JOIN users u ON f.id_usuario = u.id
                  WHERE f.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id             = $row['id'];
            $this->id_usuario     = $row['id_usuario'];
            $this->titulo         = $row['titulo'];
            $this->descripcion    = $row['descripcion'];
            $this->fecha_creacion = $row['fecha_creacion'];
            $this->nombre_usuario = $row['name'];
            $this->imagen_usuario = $row['profile_image'];
            return true;
        }
        return false;
    }

    public function delete()
    {
        try {
            // 1) Iniciar transacción
            $this->conn->beginTransaction();
    
            // 2) Borrar comentarios del foro
            $sql1 = "DELETE FROM comentarios WHERE foro_id = :id";
            $stmt1 = $this->conn->prepare($sql1);
            $stmt1->bindParam(':id', $this->id);
            $stmt1->execute();
    
            // 3) Borrar el propio foro
            $sql2 = "DELETE FROM {$this->table_name} WHERE id = :id";
            $stmt2 = $this->conn->prepare($sql2);
            $stmt2->bindParam(':id', $this->id);
            $stmt2->execute();
    
            // 4) Confirmar
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}

// Instanciar DB y objeto
$database = new Database();
$db       = $database->getConnection();
$forum    = new Forum($db);

// Respuesta por defecto
$response = [
    "exito"   => false,
    "mensaje" => "Acción no reconocida"
];

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        // detecto la acción venga por POST o GET
        $action = $_POST['action'] ?? $_GET['action'] ?? '';

        switch ($action) {
            case 'create':
                $data = json_decode(file_get_contents("php://input"));
                if (!empty($data->titulo) && !empty($data->descripcion)) {
                    $forum->titulo      = $data->titulo;
                    $forum->descripcion = $data->descripcion;
                    $forum->id_usuario  = $_SESSION['id'];
                    if ($forum->create()) {
                        $response = [
                            "exito"   => true,
                            "mensaje" => "Foro creado correctamente",
                            "id"      => $forum->id
                        ];
                    } else {
                        $response["mensaje"] = "Error al crear el foro";
                    }
                } else {
                    $response["mensaje"] = "El título y la descripción son obligatorios";
                }
                break;

            case 'delete':
                if (isset($_POST['id'])) {
                    $forum->id = $_POST['id'];
                    if ($forum->readOne()) {
                        if (isset($_SESSION['id']) && $_SESSION['id'] == $forum->id_usuario) {
                            if ($forum->delete()) {
                                $response = [
                                    "exito"   => true,
                                    "mensaje" => "Foro eliminado correctamente"
                                ];
                            } else {
                                $response["mensaje"] = "Error al eliminar el foro";
                            }
                        } else {
                            $response["mensaje"] = "No tienes permiso para eliminar este foro";
                        }
                    } else {
                        $response["mensaje"] = "Foro no encontrado";
                    }
                } else {
                    $response["mensaje"] = "ID no especificado";
                }
                break;

            default:
                // deja $response por defecto
                break;
        }
        break;

    case 'GET':
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'read':
                    $stmt = $forum->readAll();
                    $num  = $stmt->rowCount();
                    $foros = [];
                    if ($num > 0) {
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            extract($row);
                            $foros[] = [
                                "id"             => $id,
                                "titulo"         => $titulo,
                                "descripcion"    => $descripcion,
                                "fecha_creacion" => $fecha_creacion,
                                "nombre_usuario" => $name,
                                "imagen_usuario" => $profile_image
                            ];
                        }
                    }
                    $response = ["exito" => true, "foros" => $foros];
                    break;

                case 'read_one':
                    if (isset($_GET['id'])) {
                        $forum->id = $_GET['id'];
                        if ($forum->readOne()) {
                            $response = [
                                "exito" => true,
                                "foro"  => [
                                    "id"             => $forum->id,
                                    "titulo"         => $forum->titulo,
                                    "descripcion"    => $forum->descripcion,
                                    "fecha_creacion" => $forum->fecha_creacion,
                                    "nombre_usuario" => $forum->nombre_usuario,
                                    "imagen_usuario" => $forum->imagen_usuario,
                                    "id_usuario"     => $forum->id_usuario
                                ]
                            ];
                        } else {
                            $response["mensaje"] = "Foro no encontrado";
                        }
                    } else {
                        $response["mensaje"] = "ID no especificado";
                    }
                    break;

                case 'obtener_id':
                    $response = [
                        "exito"      => true,
                        "id_usuario" => $_SESSION['id']
                    ];
                    break;

                default:
                    break;
            }
        }
        break;

    // no usamos DELETE directo
}

// Enviar JSON de respuesta
echo json_encode($response);
