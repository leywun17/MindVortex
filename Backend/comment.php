<?php
// Encabezados requeridos
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

session_start();
require_once 'config.php';

class Comment
{
    private $conn;
    private $table_name = "comentarios";

    public $id;
    public $foro_id;
    public $user_id;
    public $content;
    public $created_at;
    public $updated_at;
    public $author_name;
    public $author_image;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Crear comentario
    public function create()
    {
        $this->content  = htmlspecialchars(strip_tags($this->content));
        $this->foro_id  = (int)$this->foro_id;
        $this->user_id  = (int)$this->user_id;

        $query = "INSERT INTO " . $this->table_name . "
                  SET foro_id = :foro_id,
                      user_id  = :user_id,
                      content  = :content";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':foro_id', $this->foro_id);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':content', $this->content);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Leer todos los comentarios de un foro
    public function readByForum()
    {
        $query = "SELECT c.*, u.name as author_name, u.profile_image as author_image
              FROM comentarios c
              JOIN users u ON c.user_id = u.id
              WHERE c.foro_id = :foro_id
              ORDER BY c.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':foro_id', $this->foro_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Leer un comentario específico
    public function readOne()
    {
        $query = "SELECT c.id, c.content, c.user_id, c.created_at, c.updated_at,
                         u.name AS author_name,
                         COALESCE(u.profile_image,'default.jpg') AS author_image
                  FROM " . $this->table_name . " c
                  INNER JOIN users u ON c.user_id = u.id
                  WHERE c.id = :id
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->content      = $row['content'];
            $this->user_id      = $row['user_id'];
            $this->created_at   = $row['created_at'];
            $this->updated_at   = $row['updated_at'];
            $this->author_name  = $row['author_name'];
            $this->author_image = $row['author_image'];
            return true;
        }
        return false;
    }

    // Actualizar comentario
    public function update()
    {
        $this->content = htmlspecialchars(strip_tags($this->content));

        // Verificar autor
        $orig = $this->conn
            ->prepare("SELECT user_id FROM " . $this->table_name . " WHERE id = :id");
        $orig->bindParam(':id', $this->id, PDO::PARAM_INT);
        $orig->execute();
        $row = $orig->fetch(PDO::FETCH_ASSOC);
        if (!$row || $row['user_id'] != $_SESSION['id']) {
            return false;
        }

        $query = "UPDATE " . $this->table_name . "
                  SET content = :content
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':id',      $this->id,    PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Eliminar comentario
    public function delete()
    {
        // Verificar autor
        $orig = $this->conn
            ->prepare("SELECT user_id FROM " . $this->table_name . " WHERE id = :id");
        $orig->bindParam(':id', $this->id, PDO::PARAM_INT);
        $orig->execute();
        $row = $orig->fetch(PDO::FETCH_ASSOC);
        if (!$row || $row['user_id'] != $_SESSION['id']) {
            return false;
        }

        $stmt = $this->conn
            ->prepare("DELETE FROM " . $this->table_name . " WHERE id = :id");
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

// Conexión
$database = new Database();
$db       = $database->getConnection();
$comment  = new Comment($db);

// Leer input
$data = json_decode(file_get_contents("php://input"));

// Respuesta por defecto
$response = ["exito" => false, "mensaje" => "Acción no reconocida"];

// Método y acción
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'POST':
        if ($action === 'create') {
            if (!empty($data->foro_id) && !empty($data->content)) {
                $comment->foro_id  = $data->foro_id;
                $comment->content  = $data->content;
                $comment->user_id  = $_SESSION['id'];
                if ($comment->create()) {
                    $response = [
                        "exito"   => true,
                        "mensaje" => "Comentario creado",
                        "id"     => $comment->id,
                    ];
                } else {
                    $response["mensaje"] = "Error al crear";
                }
            } else {
                $response["mensaje"] = "foro_id y content obligatorios";
            }
        }
        break;

    case 'GET':
        if ($action === 'read') {
            if (isset($_GET['foro_id'])) {
                $comment->foro_id = (int)$_GET['foro_id'];
                $stmt = $comment->readByForum();
                $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $response = ["exito" => true, "comments" => $row];
            } else {
                $response["mensaje"] = "foro_id no especificado";
            }
        } elseif ($action === 'read_one' && isset($_GET['id'])) {
            $comment->id = (int)$_GET['id'];
            if ($comment->readOne()) {
                $response = [
                    "exito"    => true,
                    "comment" => [
                        "id"           => $comment->id,
                        "foro_id"      => $comment->foro_id,
                        "user_id"      => $comment->user_id,
                        "content"      => $comment->content,
                        "created_at"   => $comment->created_at,
                        "updated_at"   => $comment->updated_at,
                        "author_name"  => $comment->author_name,
                        "author_image" => $comment->author_image
                    ]
                ];
            } else {
                $response["mensaje"] = "Comentario no encontrado";
            }
        }
        break;

    case 'PUT':
        if ($action === 'update' && isset($_GET['id'])) {
            $comment->id      = (int)$_GET['id'];
            $comment->content = $data->content ?? '';
            if ($comment->update()) {
                $response = ["exito" => true, "mensaje" => "Comentario actualizado"];
            } else {
                $response["mensaje"] = "Error al actualizar o no autorizado";
            }
        }
        break;

    case 'DELETE':
        if ($action === 'delete' && isset($_GET['id'])) {
            $comment->id = (int)$_GET['id'];
            if ($comment->delete()) {
                $response = ["exito" => true, "mensaje" => "Comentario eliminado"];
            } else {
                $response["mensaje"] = "Error al eliminar o no autorizado";
            }
        }
        break;
}

echo json_encode($response);
