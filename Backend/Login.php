<?php
session_start(); // Asegúrate de iniciar la sesión antes de acceder a las variables de sesión.

require_once './config.php';

class Auth {
    private $db;
    private $maxIntentos = 5; // Máximo de intentos fallidos
    private $tiempoBloqueo = 120; // Tiempo de bloqueo en segundos (2 minutos)

    public function __construct() {
        $dbConfig = new Database();
        $this->db = $dbConfig->getConnection();
    }

    public function getDb() {
        return $this->db;
    }

    public function authenticate($email, $password) {
        $email = filter_var(trim($email), FILTER_VALIDATE_EMAIL);
        if (!$email) {
            return ["status" => "error", "message" => $email];
        }

        // Verificar si la cuenta está bloqueada
        $bloqueo = $this->verificarBloqueo($email);
        if ($bloqueo !== false) {
            return ["status" => "error", "message" => "Cuenta bloqueada. Intenta en $bloqueo minutos."];
        }

        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $hashedPassword = $row['password'];

            if (password_verify($password, $hashedPassword)) {
                if ($row['estado'] === 'activa') {
                    $this->restablecerIntentos($email);
                    $_SESSION['logged_in'] = true;
                    $_SESSION['id'] = $row['id'];
                    $_SESSION['name'] = $row['userName'];
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['desc'] = $row['descripcion'];
                    $_SESSION['profile_image'] = $row['userImage'];
                    return true; // Autenticación exitosa
                } else {
                    return 'inactive'; // Usuario inactivo
                }
            } else {
                // Registrar intento fallido
                $this->registrarIntentoFallido($email);
                return 'invalid'; // Credenciales inválidas
            }
        } else {
            return 'invalid'; // Usuario no encontrado
        }
    }

    private function verificarBloqueo($email) {
        $sql = "SELECT ultimo_intento FROM users WHERE email = :email AND estado = 'bloqueada'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            $ultimoIntento = strtotime($usuario['ultimo_intento']);
            $tiempoActual = time();
            $tiempoRestante = $this->tiempoBloqueo - ($tiempoActual - $ultimoIntento);

            if ($tiempoRestante > 0) {
                return ceil($tiempoRestante / 60); // Devuelve minutos restantes
            } else {
                // Si el tiempo de bloqueo ha expirado, restablecer el estado
                $this->restablecerEstado($email);
            }
        }
        return false;
    }

    private function restablecerEstado($email) {
        $sql = "UPDATE users SET estado = 'activa', intentos = 0, ultimo_intento = NULL WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
    }

    private function restablecerIntentos($email) {
        $sql = "UPDATE users SET intentos = 0, ultimo_intento = NULL WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
    }

    private function registrarIntentoFallido($email) {
        $sql = "SELECT intentos FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        $intentos = $usuario['intentos'] + 1;

        $sql = "UPDATE users SET intentos = :intentos, ultimo_intento = NOW() WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":intentos", $intentos, PDO::PARAM_INT);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        // Verificar si se debe bloquear la cuenta
        if ($intentos >= $this->maxIntentos) {
            $this->bloquearCuenta($email);
        }
    }

    private function bloquearCuenta($email) {
        $sql = "UPDATE users SET estado = 'bloqueada', ultimo_intento = NOW() WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Leer la entrada JSON
    $input = json_decode(file_get_contents("php://input"), true);

    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!$email || !$password) {
        echo json_encode(['status' => 'error', 'message' => 'Correo o contraseña no proporcionados']);
        exit;
    }

    $auth = new Auth();
    $result = $auth->authenticate($email, $password);

    if ($result === true) {
        $response = array(
            'status' => 'success',
            'id'=> $_SESSION['id'],
            'name' => $_SESSION['name'],
            'email' => $_SESSION['email'],
            'desc' => $_SESSION['desc'],
            'img' => $_SESSION['profile_image']
        );
        echo json_encode($response);
    } elseif ($result === 'inactive') {
        echo json_encode(array('status' => 'error', 'message' => 'El usuario está inactivo'));
    } elseif ($result === 'invalid') {
        echo json_encode(array('status' => 'error', 'message' => 'Verifique la información ingresada'));
    } elseif (is_array($result)) {
        echo json_encode($result);
    }
}
?>
