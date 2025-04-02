<?php
require_once "config.php";

class Usuarios {
    private $conex;

    public function __construct($db) {
        $this->conex = $db;
    }

    public function registrar($name, $email, $password, $confirmPassword) {
        // Validar correo
        $email = filter_var(trim($email), FILTER_VALIDATE_EMAIL);
        if (!$email) {
            return ["status" => "error", "message" => "Correo electrónico inválido."];
        }

        // Verificar si las contraseñas coinciden
        if ($password !== $confirmPassword) {
            return ["status" => "error", "message" => "Las contraseñas no coinciden."];
        }

        $contraseña_encriptada = password_hash($password, PASSWORD_ARGON2ID);

        try {
            $sql = "INSERT INTO users (name, email, password, estado) VALUES (:name, :email, :password, 'activa')";
            $stmt = $this->conex->prepare($sql);
            $stmt->bindParam(":name", $name, PDO::PARAM_STR);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $stmt->bindParam(":password", $contraseña_encriptada, PDO::PARAM_STR);
            $stmt->execute();

            return ["status" => "success", "message" => "Usuario registrado con éxito."];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Error en el registro. Intente más tarde."];
        }
    }
}

// Conectar a la base de datos
$database = new Database();
$conexion = $database->getConnection();
$user = new Usuarios($conexion);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];

    $resultado = $user->registrar($name, $email, $password, $confirmPassword);

    // Enviar respuesta JSON
    header("Content-Type: application/json");
    echo json_encode($resultado);
    exit;
}
