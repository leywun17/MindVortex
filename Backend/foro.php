<?php
// CORS and JSON response headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

session_start();

// Respond to CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'config.php';

class Forum
{
    private $conn;
    private $tableName = "forums";

    public $id;
    public $title;
    public $description;
    public $userId;
    public $createdAt;
    public $userName;
    public $userImage;
    public $image; // nueva propiedad


    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Create new forum
    public function create()
    {
        $this->createdAt = date('Y-m-d');
        // Sanitize input
        $this->title       = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->userId      = htmlspecialchars(strip_tags($this->userId));
        $this->image       = htmlspecialchars(strip_tags($this->image));

        $query = "INSERT INTO {$this->tableName} (title, description, userId, createdAt, image)
                VALUES (:title, :description, :userId, :createdAt, :image)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':title',       $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':userId',      $this->userId);
        $stmt->bindParam(':createdAt',   $this->createdAt);
        $stmt->bindParam(':image',       $this->image);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Read all forums
    public function readAll()
    {
        $query = "SELECT
                f.id,
                f.title,
                f.description,
                DATE(f.createdAt) AS createdAt,
                u.userName,
                COALESCE(u.userImage,'../uploads/profile_images/default.png') AS userImage,
                f.image
            FROM {$this->tableName} f
            INNER JOIN users u ON f.userId = u.id
            ORDER BY f.createdAt DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function update()
    {
        $query = "UPDATE {$this->tableName}
                SET title       = :title,
                    description = :description
              WHERE id = :id AND userId = :userId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':title',       $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':id',          $this->id,        PDO::PARAM_INT);
        $stmt->bindParam(':userId',      $this->userId,    PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Read one forum by ID
    public function readOne()
    {
        $query = "SELECT
                        f.id,
                        f.title,
                        f.description,
                        DATE(f.createdAt) AS createdAt,
                        f.userId,
                        u.userName,
                        COALESCE(u.userImage,'default.png') AS userImage,
                        f.image
                    FROM {$this->tableName} f
                    INNER JOIN users u ON f.userId = u.id
                    WHERE f.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->title       = $row['title'];
            $this->description = $row['description'];
            $this->createdAt   = $row['createdAt'];
            $this->userId      = $row['userId'];
            $this->userName    = $row['userName'];
            $this->userImage   = $row['userImage'];
            $this->image = $row['image'] ?? null;
            return true;
        }
        return false;
    }

    // Delete forum and its comments
    public function delete()
    {
        try {
            $this->conn->beginTransaction();

            // Delete comments for this forum
            $sql1 = "DELETE FROM comments WHERE forum_id = :id";
            $stmt1 = $this->conn->prepare($sql1);
            $stmt1->bindParam(':id', $this->id);
            $stmt1->execute();

            // Delete the forum itself
            $sql2 = "DELETE FROM {$this->tableName} WHERE id = :id";
            $stmt2 = $this->conn->prepare($sql2);
            $stmt2->bindParam(':id', $this->id);
            $stmt2->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // Toggle favorite for a user
    public function toggleFavorite($userId, $forumId)
    {
        // Check if already favorited
        $check = "SELECT 1 FROM forum_favorite WHERE id_usuario = :userId AND id_foro = :forumId";
        $stmt = $this->conn->prepare($check);
        $stmt->bindParam(':userId',  $userId);
        $stmt->bindParam(':forumId', $forumId);
        $stmt->execute();

        if ($stmt->fetch()) {
            // Remove favorite
            $del = "DELETE FROM forum_favorite WHERE id_usuario = :userId AND id_foro = :forumId";
            $stmtDel = $this->conn->prepare($del);
            $stmtDel->bindParam(':userId',  $userId);
            $stmtDel->bindParam(':forumId', $forumId);
            return $stmtDel->execute();
        } else {
            // Add favorite
            $add = "INSERT INTO forum_favorite (id_usuario, id_foro) VALUES (:userId, :forumId)";
            $stmtAdd = $this->conn->prepare($add);
            $stmtAdd->bindParam(':userId',  $userId);
            $stmtAdd->bindParam(':forumId', $forumId);
            return $stmtAdd->execute();
        }
    }

    // Get favorites for a user
    public function getFavorites($userId)
    {
        $query = "SELECT f.id, f.title, f.description, DATE(f.createdAt) AS createdAt, u.userName, COALESCE(u.userImage, 'default.png') AS userImage, TRUE AS isFavorite FROM forums f INNER JOIN forum_favorite ff ON ff.id_foro = f.id AND ff.id_usuario = :userId INNER JOIN users u ON f.userId = u.id ORDER BY ff.fecha_agregado DESC;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt;
    }


    // Read forums by a specific user
    public function readByUser($userId)
    {
        $query = "SELECT
                        f.id,
                        f.title,
                        f.description,
                        DATE(f.createdAt) AS createdAt,
                        u.userName,
                        COALESCE(u.userImage,'default.png') AS userImage
                    FROM {$this->tableName} f
                    INNER JOIN users u ON f.userId = u.id
                    WHERE f.userId = :userId
                    ORDER BY f.createdAt DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt;
    }

    // Read replies made by a user
    public function readRepliesByUser($userId)
    {
        $query = "SELECT
                        c.id,
                        f.title,
                        c.content,
                        DATE(c.created_at) AS createdAt
                    FROM comments c
                    INNER JOIN {$this->tableName} f ON c.forum_id = f.id
                    WHERE c.user_id = :userId
                    ORDER BY c.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Busca foros por título o descripción
     *
     * @param string $term Término de búsqueda
     * @return array Lista de foros que coinciden
     */
    public function search(string $term): array
    {
        // Preparar término con comodines
        $term = '%' . $term . '%';

        $sql = "SELECT
                    f.id,
                    f.title,
                    f.description,
                    DATE(f.createdAt) AS createdAt,
                    u.userName,
                    COALESCE(u.userImage,'default.png') AS userImage
                FROM {$this->tableName} f
                INNER JOIN users u ON f.userId = u.id
                WHERE f.title LIKE :term OR f.description LIKE :term
                ORDER BY f.createdAt DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':term', $term, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Instantiate database and model
$database = new Database();
$db       = $database->getConnection();
$forum    = new Forum($db);

// Default response
$response = [
    "success" => false,
    "message" => "Acción no reconocida"
];

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        $action = $_POST['action'] ?? $_GET['action'] ?? '';
        $data   = json_decode(file_get_contents("php://input"));

        switch ($action) {
            case 'create':
                if (!empty($_POST['title']) && !empty($_POST['description'])) {
                    $forum->title       = $_POST['title'];
                    $forum->description = $_POST['description'];
                    $forum->userId      = $_SESSION['id'];

                    // Manejo de imagen
                    $uploadDir = '../uploads/forum_images/';
                    $imageName = null;

                    if (isset($_FILES['forumImage']) && $_FILES['forumImage']['error'] === UPLOAD_ERR_OK) {
                        $fileTmpPath = $_FILES['forumImage']['tmp_name'];
                        $fileName = $_FILES['forumImage']['name'];
                        $fileSize = $_FILES['forumImage']['size'];
                        $fileType = $_FILES['forumImage']['type'];
                        $fileNameCmps = explode(".", $fileName);
                        $fileExtension = strtolower(end($fileNameCmps));

                        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                        if (in_array($fileExtension, $allowedExtensions)) {
                            // Crear un nombre único para la imagen
                            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                            $destPath = $uploadDir . $newFileName;

                            if (!file_exists($uploadDir)) {
                                mkdir($uploadDir, 0755, true);
                            }

                            if (move_uploaded_file($fileTmpPath, $destPath)) {
                                $imageName = $newFileName;
                            } else {
                                $response["message"] = "Error al mover la imagen subida.";
                                echo json_encode($response);
                                exit;
                            }
                        } else {
                            $response["message"] = "Tipo de imagen no permitido.";
                            echo json_encode($response);
                            exit;
                        }
                    }

                    $forum->image = $imageName;

                    if ($forum->create()) {
                        $response = [
                            "success" => true,
                            "message" => "Foro creado correctamente",
                            "id"      => $forum->id
                        ];
                    } else {
                        $response["message"] = "Error al crear el foro";
                    }
                } else {
                    $response["message"] = "El título y la descripción son obligatorios";
                }
                break;


            case 'update':
                // Validaciones mínimas
                if (isset($_POST['id'], $_POST['title'], $_POST['description'])) {
                    $forum->id          = intval($_POST['id']);
                    $forum->title       = $_POST['title'];
                    $forum->description = $_POST['description'];
                    $forum->userId      = $_SESSION['id'];

                    // Primero lees para verificar que existe y que el usuario sea el dueño
                    if ($forum->readOne() && $forum->userId === $_SESSION['id']) {
                        if ($forum->update()) {
                            $response = [
                                "success" => true,
                                "message" => "Foro actualizado correctamente"
                            ];
                        } else {
                            $response["message"] = "No se pudo actualizar el foro";
                        }
                    } else {
                        $response["message"] = "No tienes permiso o no existe el foro";
                    }
                } else {
                    $response["message"] = "Faltan parámetros para actualizar";
                }
                break;


            case 'delete':
                if (isset($_POST['id'])) {
                    $forum->id = $_POST['id'];
                    if ($forum->readOne() && $_SESSION['id'] == $forum->userId) {
                        if ($forum->delete()) {
                            $response = [
                                "success" => true,
                                "message" => "Foro eliminado correctamente"
                            ];
                        } else {
                            $response["message"] = "No se pudo eliminar el foro";
                        }
                    } else {
                        $response["message"] = "No tienes permiso o el foro no existe";
                    }
                } else {
                    $response["message"] = "ID no especificado";
                }
                break;

            case 'toggle_favorite':
                $forumId = intval($_POST['forum_id'] ?? 0);
                if ($forumId && isset($_SESSION['id'])) {
                    if ($forum->toggleFavorite($_SESSION['id'], $forumId)) {
                        $response = [
                            "success" => true,
                            "message" => "Estado de favorito actualizado"
                        ];
                    } else {
                        $response["message"] = "Error al actualizar favorito";
                    }
                } else {
                    $response["message"] = "ID de foro o usuario no válidos";
                }
                break;
        }
        break;

    case 'GET':
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'read':
                    $stmt   = $forum->readAll();
                    $forums = [];
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $forums[] = $row;
                    }
                    $response = ["success" => true, "forums" => $forums];
                    break;

                case 'read_one':
                    if (isset($_GET['id'])) {
                        $forum->id = $_GET['id'];
                        if ($forum->readOne()) {
                            $response = [
                                "success" => true,
                                "forum"   => [
                                    "id"          => $forum->id,
                                    "title"       => $forum->title,
                                    "description" => $forum->description,
                                    "createdAt"   => $forum->createdAt,
                                    "userName"    => $forum->userName,
                                    "userImage"   => $forum->userImage,
                                    "image"       => '../uploads/forum_images/' . $forum->image,
                                    "userId"      => $forum->userId,
                                    "isFavorite"  => false
                                ]
                            ];
                        } else {
                            $response["message"] = "Foro no encontrado";
                        }
                    } else {
                        $response["message"] = "ID no especificado";
                    }
                    break;

                case 'get_id':
                    $response = [
                        "success" => true,
                        "userId"  => $_SESSION['id'] ?? null
                    ];
                    break;

                case 'my_forums':
                    $stmt       = $forum->readByUser($_SESSION['id'] ?? 0);
                    $userForums = [];
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $userForums[] = $row;
                    }
                    $response = ["success" => true, "forums" => $userForums];
                    break;
                case 'search':
                    // Obtenemos término de búsqueda desde la query string
                    $queryTerm = $_GET['query'] ?? '';
                    // Ejecutamos método search de la clase Forum
                    $results = $forum->search($queryTerm);
                    // Devolvemos respuesta JSON
                    $response = [
                        'success' => true,
                        'results' => $results
                    ];
                    break;

                case 'my_favorites':
                    $stmt      = $forum->getFavorites($_SESSION['id'] ?? 0);
                    $favForums = [];
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $favForums[] = $row;
                    }
                    $response = ["success" => true, "favorites" => $favForums];
                    break;

                case 'my_replies':
                    $stmt    = $forum->readRepliesByUser($_SESSION['id'] ?? 0);
                    $replies = [];
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $replies[] = $row;
                    }
                    $response = ["success" => true, "replies" => $replies];
                    break;
            }
        }
        break;
}

// Send JSON response
echo json_encode($response);
