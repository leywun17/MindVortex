<?php
session_start();
header('Content-Type: application/json');

require_once "./config.php"; // Archivo de conexión

class User {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Método para actualizar la información del usuario
    public function updateUserInfo($user_id, $name, $email) {
        try {
            // Validar email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ["status" => "error", "message" => "Formato de correo electrónico inválido"];
            }

            // Verificar si el email ya existe para otro usuario
            $checkQuery = "SELECT id FROM users WHERE email = :email AND id != :user_id";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindParam(":email", $email);
            $checkStmt->bindParam(":user_id", $user_id);
            $checkStmt->execute();

            if ($checkStmt->rowCount() > 0) {
                return ["status" => "error", "message" => "El correo electrónico ya está en uso"];
            }

            $query = "UPDATE users SET name = :name, email = :email WHERE id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":user_id", $user_id);

            if ($stmt->execute()) {
                return ["status" => "success", "message" => "Información actualizada correctamente"];
            } else {
                return ["status" => "error", "message" => "No se pudo actualizar la información."];
            }
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Error en la base de datos: " . $e->getMessage()];
        }
    }

    // Método para cambiar la contraseña
    public function changePassword($user_id, $current_password, $new_password) {
        try {
            // Verificar contraseña actual
            $checkQuery = "SELECT password FROM users WHERE id = :user_id";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindParam(":user_id", $user_id);
            $checkStmt->execute();
            $user = $checkStmt->fetch(PDO::FETCH_ASSOC);

            // Verificar contraseña actual
            if (!password_verify($current_password, $user['password'])) {
                return ["status" => "error", "message" => "Contraseña actual incorrecta"];
            }

            // Validaciones de la nueva contraseña
            if (strlen($new_password) < 8) {
                return ["status" => "error", "message" => "La contraseña debe tener al menos 8 caracteres"];
            }

            // Hashear nueva contraseña
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Actualizar contraseña
            $query = "UPDATE users SET password = :password WHERE id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":password", $hashed_password);
            $stmt->bindParam(":user_id", $user_id);

            if ($stmt->execute()) {
                return ["status" => "success", "message" => "Contraseña cambiada correctamente"];
            } else {
                return ["status" => "error", "message" => "No se pudo cambiar la contraseña"];
            }
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Error en la base de datos: " . $e->getMessage()];
        }
    }

    // Método para subir imagen de perfil
    public function uploadProfileImage($user_id, $image) {
        try {

            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $max_size = 5 * 1024 * 1024; // 5MB

            if (!in_array($image['type'], $allowed_types)) {
                return ["status" => "error", "message" => "Tipo de archivo no permitido. Solo se aceptan JPEG, PNG, GIF y WebP"];
            }

            if ($image['size'] > $max_size) {
                return ["status" => "error", "message" => "El tamaño de la imagen supera el límite de 5MB"];
            }

            // Generar nombre único para la imagen
            $file_extension = pathinfo($image['name'], PATHINFO_EXTENSION);
            $new_filename = "profile_" . $user_id . "_" . uniqid() . "." . $file_extension;
            $upload_dir = "../uploads/profile_images/";
            
            // Crear directorio si no existe
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $upload_path = $upload_dir . $new_filename;

            // Mover imagen
            if (move_uploaded_file($image['tmp_name'], $upload_path)) {
                // Actualizar ruta de imagen en base de datos
                $query = "UPDATE users SET profile_image = :profile_image WHERE id = :user_id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":profile_image", $new_filename);
                $stmt->bindParam(":user_id", $user_id);
                $stmt->execute();

                return [
                    "status" => "success", 
                    "message" => "Imagen de perfil actualizada correctamente",
                    "filename" => $new_filename
                ];
            } else {
                return ["status" => "error", "message" => "No se pudo subir la imagen"];
            }
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Error en la base de datos: " . $e->getMessage()];
        }
    }
}

// Conectar a la base de datos
$database = new Database();
$db = $database->getConnection();
$user = new User($db);

// Verificar si el usuario está autenticado
$user_id = $_SESSION["id"] ?? null;

// Procesar solicitud
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])) {
    $action = $_POST["action"];

    // Sin usuario autenticado, no se permiten acciones
    if (!$user_id) {
        echo json_encode(["status" => "error", "message" => "No autorizado"]);
        exit;
    }

    switch ($action) {
        case "update_info":
            $name = trim($_POST["name"] ?? '');
            $email = trim($_POST["email"] ?? '');

            $response = $user->updateUserInfo($user_id, $name, $email);
            echo json_encode($response);
            break;

        case "change_password":
            $current_password = $_POST["current_password"] ?? '';
            $new_password = $_POST["new_password"] ?? '';

            $response = $user->changePassword($user_id, $current_password, $new_password);
            echo json_encode($response);
            break;

            case "upload_image":
                if (isset($_FILES["profile_image"])) {
                    $profile_image = $_FILES["profile_image"];
    
                    // Verifica si no hay errores en la subida
                    if ($profile_image['error'] === UPLOAD_ERR_OK) {
                        $response = $user->uploadProfileImage($user_id, $profile_image);
                        echo json_encode($response);
                    } else {
                        // Manejo específico para errores de subida
                        echo json_encode(["status" => "error", "message" => "Error al subir la imagen: " . $profile_image['error']]);
                    }
                } else {
                    echo json_encode(["status" => "error", "message" => "No se recibió ninguna imagen"]);
                }
                break;

        default:
            echo json_encode(["status" => "error", "message" => "Acción no válida"]);
            break;
    }

    exit;
}

// Si no se reconoce la acción
echo json_encode(["status" => "error", "message" => "Acción no válida"]);
exit;
?>