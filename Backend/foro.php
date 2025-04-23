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
    private $table_name = "forums";

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
        $query = "SELECT f.id, f.titulo, f.descripcion, DATE(f.fecha_creacion) AS fecha_creacion, f.id_usuario, u.name, u.profile_image
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

    public function toggleFavorito($id_usuario, $id_foro)
    {
        // ¿Ya es favorito?
        $queryCheck = "SELECT 1 FROM forum_favorite WHERE id_usuario = :id_usuario AND id_foro = :id_foro";
        $stmt = $this->conn->prepare($queryCheck);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':id_foro', $id_foro);
        $stmt->execute();

        if ($stmt->fetch()) {
            // Si ya existe, lo quitamos
            $queryDel = "DELETE FROM forum_favorite WHERE id_usuario = :id_usuario AND id_foro = :id_foro";
            $stmtDel = $this->conn->prepare($queryDel);
            $stmtDel->bindParam(':id_usuario', $id_usuario);
            $stmtDel->bindParam(':id_foro', $id_foro);
            return $stmtDel->execute();
        } else {
            // Si no existe, lo agregamos
            $queryAdd = "INSERT INTO forum_favorite (id_usuario, id_foro) VALUES (:id_usuario, :id_foro)";
            $stmtAdd = $this->conn->prepare($queryAdd);
            $stmtAdd->bindParam(':id_usuario', $id_usuario);
            $stmtAdd->bindParam(':id_foro', $id_foro);
            return $stmtAdd->execute();
        }
    }

    public function getFavoritos($id_usuario)
    {
        $query = "SELECT f.id, f.titulo, f.descripcion, DATE(f.fecha_creacion) AS fecha_creacion,
                        u.name, COALESCE(u.profile_image, 'default.jpg') AS profile_image
                FROM forum_favorite ff
                INNER JOIN forums f ON ff.id_foro = f.id
                INNER JOIN users u ON f.id_usuario = u.id
                WHERE ff.id_usuario = :id_usuario
                ORDER BY ff.fecha_agregado DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();
        return $stmt;
    }
    public function readByUser($id_usuario)
    {
        $query = "SELECT f.id, f.titulo, f.descripcion, 
                     DATE(f.fecha_creacion) AS fecha_creacion,
                     COALESCE(u.name,'') AS name,
                     COALESCE(u.profile_image,'default.jpg') AS profile_image
              FROM {$this->table_name} f
              INNER JOIN users u ON f.id_usuario = u.id
              WHERE f.id_usuario = :id_usuario
              ORDER BY f.fecha_creacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();
        return $stmt;
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

            case 'toggle_favorito':
                // Lee directamente de $_POST
                $id_foro = isset($_POST['id_foro']) ? intval($_POST['id_foro']) : null;
                error_log("toggle_favorito llamado con id_foro=" . var_export($_POST['id_foro'], true));

                if ($id_foro && isset($_SESSION['id'])) {
                    if ($forum->toggleFavorito($_SESSION['id'], $id_foro)) {
                        $response = [
                            "exito"   => true,
                            "mensaje" => "Estado de favorito actualizado"
                        ];
                    } else {
                        $response["mensaje"] = "Error al actualizar favorito";
                    }
                } else {
                    $response["mensaje"] = "ID del foro o usuario no válidos. id_foro recibido: "
                        . var_export($_POST['id_foro'], true);
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
                case 'mis_foros':
                    if (isset($_SESSION['id'])) {
                        $stmt = $forum->readByUser($_SESSION['id']);
                        $foros = [];
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $foros[] = [
                                "id"             => $row['id'],
                                "titulo"         => $row['titulo'],
                                "descripcion"    => $row['descripcion'],
                                "fecha_creacion" => $row['fecha_creacion'],
                                "nombre_usuario" => $row['name'],
                                "imagen_usuario" => $row['profile_image']
                            ];
                        }
                        $response = ["exito" => true, "foros" => $foros];
                    } else {
                        $response["mensaje"] = "Usuario no autenticado";
                    }
                    break;

                case 'mis_favoritos':
                    if (isset($_SESSION['id'])) {
                        $stmt = $forum->getFavoritos($_SESSION['id']);
                        $favoritos = [];
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            extract($row);
                            $favoritos[] = [
                                "id"             => $id,
                                "titulo"         => $titulo,
                                "descripcion"    => $descripcion,
                                "fecha_creacion" => $fecha_creacion,
                                "nombre_usuario" => $name,
                                "imagen_usuario" => $profile_image
                            ];
                        }
                        $response = ["exito" => true, "favoritos" => $favoritos];
                    } else {
                        $response["mensaje"] = "Usuario no autenticado";
                    }
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
