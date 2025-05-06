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

// User class
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

    // Update profile information
    public function updateProfile()
    {
        // Sanitize input
        $this->userName = htmlspecialchars(strip_tags($this->userName));
        $this->email = htmlspecialchars(strip_tags($this->email));

        // Prepare the query to update the user's profile
        $query = "UPDATE {$this->tableName} 
                  SET userName = :userName, email = :email";

        // If a password is provided, update it
        if ($this->password) {
            $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
            $query .= ", password = :password";
        }

        // If a new image is provided, update it
        if ($this->userImage) {
            $query .= ", userImage = :userImage";
        }

        $query .= " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Bind the parameters
        $stmt->bindParam(':userName', $this->userName);
        $stmt->bindParam(':email', $this->email);
        if ($this->password) {
            $stmt->bindParam(':password', $hashedPassword);
        }
        if ($this->userImage) {
            $stmt->bindParam(':userImage', $this->userImage);
        }
        $stmt->bindParam(':id', $this->id);

        // Execute the query
        return $stmt->execute();
    }

    // Get user by ID
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

// Instantiate database and model
$database = new Database();
$db = $database->getConnection();
$user = new User($db);

// Default response
$response = [
    "success" => false,
    "message" => "Acción no reconocida"
];

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        // Verificar si el usuario está autenticado
        if (!isset($_SESSION['id'])) {
            $response = [
                "success" => false,
                "message" => "Usuario no autenticado"
            ];
            break;
        }

        // Set the user ID from session
        $user->id = $_SESSION['id'];

        // Verificar si los datos del perfil fueron enviados
        if (isset($_POST['name']) && isset($_POST['email'])) {
            $user->userName = $_POST['name'];
            $user->email = $_POST['email'];

            // Si se envía una nueva contraseña, asignarla
            if (isset($_POST['password']) && !empty($_POST['password'])) {
                $user->password = $_POST['password'];
            }

            // Si se envía una imagen de usuario, manejarla
            if (isset($_FILES['userImage']) && $_FILES['userImage']['error'] == 0) {
                // Aquí puedes manejar la subida de la imagen (reemplaza esto por tu lógica)
                $targetDir = "uploads/";
                $targetFile = $targetDir . basename($_FILES['userImage']['name']);
                if (move_uploaded_file($_FILES['userImage']['tmp_name'], $targetFile)) {
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

            // Intentar actualizar el perfil
            if ($user->updateProfile()) {
                $response = [
                    "success" => true,
                    "message" => "Perfil actualizado correctamente"
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
            // Obtener los datos del usuario por ID
            $user->id = $_SESSION['id'];

            if ($user->getUserById()) {
                $response = [
                    "success" => true,
                    "user" => [
                        "id" => $user->id,
                        "userName" => $user->userName,
                        "email" => $user->email,
                        "userImage" => $user->userImage ?? '../../uploads/profile_images/default.jpg'
                    ]
                ];
            } else {
                $response["message"] = "No se pudo obtener la información del usuario";
            }
        }
        break;
}

// Send JSON response
echo json_encode($response);
?>
