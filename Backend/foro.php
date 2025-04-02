<?php
session_start(); // Asegúrate de iniciar la sesión antes de acceder a las variables de sesión.

require_once 'config.php';

class Foro {
    private $conn;
    
    public function __construct() {
        $dbConfig = new Database();
        $this->conn = $dbConfig->getConnection();
    }
    
    // Función para crear un nuevo tema con su primer post
    public function crear_tema($titulo, $contenido, $categoria = 'general') {
        try {
            $this->conn->beginTransaction();
            
            // Insertar el tema
            $sql = "INSERT INTO temas (usuario_id, titulo, categoria) VALUES (:usuario_id, :titulo, :categoria)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':usuario_id', $_SESSION['user_id']);
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':categoria', $categoria);
            
            if ($stmt->execute()) {
                $tema_id = $this->conn->lastInsertId();
                
                // Insertar el primer post del tema
                $sql = "INSERT INTO posts (tema_id, usuario_id, contenido) VALUES (:tema_id, :usuario_id, :contenido)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':tema_id', $tema_id);
                $stmt->bindParam(':usuario_id', $_SESSION['user_id']);
                $stmt->bindParam(':contenido', $contenido);
                
                if ($stmt->execute()) {
                    $this->conn->commit();
                    return $tema_id;
                }
            }
            
            $this->conn->rollBack();
            return false;
            
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return $e->getMessage();
        }
    }
    
    // Función para responder a un tema existente
    public function responder_tema($tema_id, $contenido) {
        try {
            $sql = "INSERT INTO posts (tema_id, usuario_id, contenido) VALUES (:tema_id, :usuario_id, :contenido)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':tema_id', $tema_id);
            $stmt->bindParam(':usuario_id', $_SESSION['user_id']);
            $stmt->bindParam(':contenido', $contenido);
            
            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            
            return false;
            
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
    
    // Obtener un tema con todas sus respuestas
    public function obtener_tema($tema_id) {
        try {
            // Obtener información del tema
            $sql = "SELECT t.*, u.nombre as autor 
                    FROM temas t 
                    JOIN usuarios u ON t.usuario_id = u.id 
                    WHERE t.id = :tema_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':tema_id', $tema_id);
            $stmt->execute();
            $tema = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$tema) {
                return false;
            }
            
            // Obtener todos los posts del tema
            $sql = "SELECT p.*, u.nombre as autor 
                    FROM posts p 
                    JOIN usuarios u ON p.usuario_id = u.id 
                    WHERE p.tema_id = :tema_id 
                    ORDER BY p.fecha_creacion ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':tema_id', $tema_id);
            $stmt->execute();
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $tema['posts'] = $posts;
            return $tema;
            
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
    
    //Listar todos los temas
    public function listar_temas($categoria = null, $pagina = 1, $por_pagina = 20) {
        try {
            $offset = ($pagina - 1) * $por_pagina;
            
            $where = "";
            $params = [];
            
            if ($categoria) {
                $where = "WHERE t.categoria = :categoria";
                $params[':categoria'] = $categoria;
            }
            
            $sql = "SELECT t.*, u.nombre as autor, COUNT(p.id) as num_respuestas, 
                    MAX(p.fecha_creacion) as ultima_actividad
                    FROM temas t 
                    JOIN usuarios u ON t.usuario_id = u.id 
                    LEFT JOIN posts p ON t.id = p.tema_id
                    $where
                    GROUP BY t.id
                    ORDER BY ultima_actividad DESC
                    LIMIT :offset, :limit";
                    
            $stmt = $this->conn->prepare($sql);
            
            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value);
            }
            
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
}

?>