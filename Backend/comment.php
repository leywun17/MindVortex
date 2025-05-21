<?php

$title = "Access-Control-Allow-Origin: *";
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

session_start();

require_once 'config.php';

class Comment
{
    private PDO $db;
    private string $table = 'comments';
    public ?int $parentId = null;
    public int    $id;
    public int    $forumId;
    public int    $userId;
    public string $content;
    public string $createdAt;
    public string $updatedAt;
    public string $authorName;
    public string $authorImage;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function create(): bool
    {
        $this->content = htmlspecialchars(strip_tags($this->content));

        $sql = "INSERT INTO {$this->table} (forum_id, parent_id, user_id, content)
                VALUES (:forum_id, :parent_id, :user_id, :content)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':forum_id', $this->forumId, PDO::PARAM_INT);
        $stmt->bindParam(':parent_id', $this->parentId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $this->userId, PDO::PARAM_INT);
        $stmt->bindParam(':content', $this->content, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $this->id = (int)$this->db->lastInsertId();

            if (!$this->parentId) {
                $forumOwnerId = $this->getForumOwner($this->forumId);
                if ($forumOwnerId && $forumOwnerId !== $this->userId) {
                    $this->createNotification(
                        $forumOwnerId,
                        'comment',
                        "Han comentado en tu foro."
                    );
                }
            } else {
                $parentAuthorId = $this->getParentCommentAuthor();
                if ($parentAuthorId && $parentAuthorId !== $this->userId) {
                    $this->createNotification(
                        $parentAuthorId,
                        'reply',
                        "Han respondido a tu comentario."
                    );
                }
            }

            return true;
        }
        return false;
    }

    private function getParentCommentAuthor(): ?int
    {
        $stmt = $this->db->prepare("SELECT user_id FROM comments WHERE id = :parent_id");
        $stmt->bindParam(':parent_id', $this->parentId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() ?: null;
    }

    private function createNotification(int $userId, string $type, string $message): void
    {
        $notifSql = "INSERT INTO notifications (user_id, type, message)
                      VALUES (:user_id, :type, :message)";
        $notifStmt = $this->db->prepare($notifSql);
        $notifStmt->execute([
            ':user_id' => $userId,
            ':type' => $type,
            ':message' => $message
        ]);
    }

    private function getForumOwner(int $forumId): ?int
    {
        $stmt = $this->db->prepare("SELECT userId FROM forums WHERE id = :forum_id");
        $stmt->bindParam(':forum_id', $forumId, PDO::PARAM_INT);
        $stmt->execute();
        $owner = $stmt->fetchColumn();
        return $owner !== false ? (int)$owner : null;
    }

    public function readByForum(): array
    {
        $sql = "SELECT c.*, 
                   u.userName AS author_name,
                   COALESCE(u.userImage, '../../uploads/profile_images/default.jpg') AS author_image
            FROM {$this->table} c
            JOIN users u ON c.user_id = u.id
            WHERE c.forum_id = :forum_id
            ORDER BY COALESCE(c.parent_id, c.id), c.created_at ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':forum_id', $this->forumId, PDO::PARAM_INT);
        $stmt->execute();

        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->buildCommentTree($comments);
    }

    private function buildCommentTree(array $comments): array
    {
        $tree = [];
        $map = [];

        foreach ($comments as &$comment) {
            $comment['replies'] = [];
            $map[$comment['id']] = &$comment;

            if ($comment['parent_id']) {
                $map[$comment['parent_id']]['replies'][] = &$comment;
            } else {
                $tree[] = &$comment;
            }
        }

        return $tree;
    }


    public function readOne(): bool
    {
        $sql = "SELECT c.id, c.forum_id, c.user_id, c.content, c.created_at, c.updated_at,
                       u.userName AS author_name,
                       COALESCE(u.userImage,'../../uploads/profile_images/default.jpg') AS author_image
                FROM {$this->table} c
                JOIN users u ON c.user_id = u.id
                WHERE c.id = :id
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return false;

        $this->forumId     = (int)$row['forum_id'];
        $this->userId      = (int)$row['user_id'];
        $this->content     = $row['content'];
        $this->createdAt   = $row['created_at'];
        $this->updatedAt   = $row['updated_at'];
        $this->authorName  = $row['author_name'];
        $this->authorImage = $row['author_image'];
        return true;
    }


    public function update(): bool
    {
        $this->content = htmlspecialchars(strip_tags($this->content));

        $check = $this->db->prepare("SELECT user_id FROM {$this->table} WHERE id = :id");
        $check->bindParam(':id', $this->id, PDO::PARAM_INT);
        $check->execute();
        $owner = $check->fetchColumn();
        if (!$owner || (int)$owner !== (int)($_SESSION['id'] ?? 0)) {
            return false;
        }

        $sql = "UPDATE {$this->table} SET content = :content WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':content', $this->content, PDO::PARAM_STR);
        $stmt->bindParam(':id',      $this->id,      PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete(): bool
    {
        $check = $this->db->prepare("SELECT user_id FROM {$this->table} WHERE id = :id");
        $check->bindParam(':id', $this->id, PDO::PARAM_INT);
        $check->execute();
        $owner = $check->fetchColumn();
        if (!$owner || (int)$owner !== (int)($_SESSION['id'] ?? 0)) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}


$db      = (new Database())->getConnection();
$comment = new Comment($db);
$data    = json_decode(file_get_contents('php://input'));
$response = ['success' => false, 'message' => 'No data received'];

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'POST':

        if ($action === 'reply' && !empty($data->parent_id) && !empty($data->content)) {
            $stmt = $db->prepare("SELECT forum_id FROM comments WHERE id = :parent_id");
            $stmt->bindParam(':parent_id', $data->parent_id, PDO::PARAM_INT);
            $stmt->execute();
            $forumId = $stmt->fetchColumn();

            if (!$forumId) {
                $response = ['success' => false, 'message' => 'Comentario padre no válido'];
                break;
            }

            $comment->forumId = (int)$forumId;
            $comment->parentId = (int)$data->parent_id;
            $comment->content = $data->content;
            $comment->userId = (int)($_SESSION['id'] ?? 0);

            if ($comment->create()) {
                $response = [
                    'success' => true,
                    'message' => 'Respuesta creada',
                    'id' => $comment->id,
                    'author_name' => $_SESSION['name'],
                    'author_image' => $_SESSION['user_image'] ?? 'default.jpg'
                ];
            } else {
                $response['message'] = 'Error al responder';
            }
        }

        if ($action === 'create' && !empty($data->forum_id) && !empty($data->content)) {
            $comment->forumId = (int)$data->forum_id;
            $comment->content = $data->content;
            $comment->userId  = (int)($_SESSION['id'] ?? 0);
            if ($comment->create()) {
                $response = ['success' => true, 'message' => 'Comentario creado', 'id' => $comment->id];
            } else {
                $response['message'] = 'Error al crear';
            }
        } elseif ($action === 'mark_as_read' && !empty($data->notification_id)) {
            $userId = (int)($_SESSION['id'] ?? 0);
            $notifId = (int)$data->notification_id;

            $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :user_id");
            $stmt->bindParam(':id', $notifId, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

            if ($stmt->execute() && $stmt->rowCount() > 0) {
                $response = ['success' => true, 'message' => 'Notificación marcada como leída'];
            } else {
                $response = ['success' => false, 'message' => 'No se encontró la notificación o ya estaba leída'];
            }
        }
        break;

    case 'GET':
        if ($action === 'read' && isset($_GET['forum_id'])) {
            $comment->forumId = (int)$_GET['forum_id'];
            $list = $comment->readByForum();
            $response = ['success' => true, 'comments' => $list];
        } elseif ($action === 'read_one' && isset($_GET['id'])) {
            $comment->id = (int)$_GET['id'];
            if ($comment->readOne()) {
                $response = ['success' => true, 'comment' => [
                    'id' => $comment->id,
                    'forum_id' => $comment->forumId,
                    'user_id' => $comment->userId,
                    'content' => $comment->content,
                    'created_at' => $comment->createdAt,
                    'updated_at' => $comment->updatedAt,
                    'author_name' => $comment->authorName,
                    'author_image' => $comment->authorImage
                ]];
            } else {
                $response['message'] = 'Comentario no encontrado';
            }
        } elseif ($action === 'get_notifications') {
            $userId = (int)($_SESSION['id'] ?? 0);
            $stmt = $db->prepare("SELECT * FROM notifications WHERE user_id = :id ORDER BY created_at DESC");
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            $response = ['success' => true, 'notifications' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } elseif ($action === 'get_id') {
            $response = ['success' => true, 'user_id' => $_SESSION['id'] ?? null];
        } else {
            $response['message'] = 'Acción GET no válida';
        }
        break;


    case 'PUT':
        if ($action === 'update' && isset($_GET['id'])) {
            $comment->id = (int)$_GET['id'];
            $comment->content = $data->content ?? '';
            if ($comment->update()) {
                $response = ['success' => true, 'message' => 'Actualizado'];
            } else {
                $response['message'] = 'No autorizado o error';
            }
        }
        break;

    case 'DELETE':
        if ($action === 'delete' && isset($_GET['id'])) {
            $comment->id = (int)$_GET['id'];
            if ($comment->delete()) {
                $response = ['success' => true, 'message' => 'Eliminado'];
            } else {
                $response['message'] = 'No autorizado o error';
            }
        }
        break;
}

echo json_encode($response);
