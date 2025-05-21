<?php

$title = "Access-Control-Allow-Origin: *";
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


require_once 'config.php';
session_start();
class Notification
{
    private PDO $db;
    public int $userId;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function createSimple(int $userId, string $type, string $message): bool
    {
        $sql = "INSERT INTO notifications (user_id, type, message, is_read, created_at) VALUES (:user_id, :type, :message, 0, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':message', $message);
        return $stmt->execute();
    }

    public function markAsRead(int $notificationId): bool
    {
        $sql = "UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $notificationId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $this->userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
