<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'config.php';

class User
{
    private $conn;
    private $tableName = "users";

    public $id;
    public $userName;
    public $email;
    public $password;
    public $userImage;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function updateProfile()
    {
        $this->userName = htmlspecialchars(strip_tags($this->userName));
        $this->email = htmlspecialchars(strip_tags($this->email));

        $query = "UPDATE {$this->tableName} 
                  SET userName = :userName, email = :email";

        if ($this->password) {
            $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
            $query .= ", password = :password";
        }

        if ($this->userImage) {
            $query .= ", userImage = :userImage";
        }

        $query .= " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':userName', $this->userName);
        $stmt->bindParam(':email', $this->email);
        if ($this->password) {
            $stmt->bindParam(':password', $hashedPassword);
        }
        if ($this->userImage) {
            $stmt->bindParam(':userImage', $this->userImage);
        }
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function getUserById()
    {
        $query = "SELECT id, userName, email, userImage FROM {$this->tableName} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->userName = $row['userName'];
            $this->email = $row['email'];
            $this->userImage = $row['userImage'];
            return true;
        }
        return false;
    }
}

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$response = [
    "success" => false,
    "message" => "Acción no reconocida"
];

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        if (!isset($_SESSION['id'])) {
            $response = [
                "success" => false,
                "message" => "Usuario no autenticado"
            ];
            break;
        }

        $user->id = $_SESSION['id'];

        if (isset($_POST['name']) && isset($_POST['email'])) {
            $user->userName = $_POST['name'];
            $user->email = $_POST['email'];

            if (isset($_POST['password']) && !empty($_POST['password'])) {
                $user->password = $_POST['password'];
            }

            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
                $targetDir = "../uploads/profile_images/";
                $targetFile = $targetDir . basename($_FILES['profile_image']['name']);
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
                    $user->userImage = $targetFile;
                } else {
                    $response = [
                        "success" => false,
                        "message" => "Error al subir la imagen"
                    ];
                    echo json_encode($response);
                    exit;
                }
            }

            if ($user->updateProfile()) {
                $response = [
                    "success" => true,
                    "user" => [
                        "id" => $user->id,
                        "userName" => $user->userName,
                        "email" => $user->email,
                        "userImage" => $user->userImage ?? 'default.jpg'
                    ]
                ];
            } else {
                $response = [
                    "success" => false,
                    "message" => "Error al actualizar el perfil"
                ];
            }
        } else {
            $response = [
                "success" => false,
                "message" => "Faltan datos del perfil"
            ];
        }
        break;

    case 'GET':
        if (isset($_GET['action']) && $_GET['action'] === 'get_user') {
            $user->id = $_SESSION['id'];

            if ($user->getUserById()) {
                $response = [
                    "success" => true,
                    "user" => [
                        "id" => $user->id,
                        "userName" => $user->userName,
                        "email" => $user->email,
                        "userImage" => $user->userImage ?? '../uploads/profile_images/default.png'
                    ]
                ];
            } else {
                $response["message"] = "No se pudo obtener la información del usuario";
            }
        }
        break;
}

echo json_encode($response);
