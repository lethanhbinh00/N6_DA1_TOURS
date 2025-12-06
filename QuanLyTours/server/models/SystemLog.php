<?php
class SystemLog {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Ghi lại hành động của người dùng vào nhật ký hệ thống
     */
    public function logAction(string $actionType, string $entityName, ?int $entityId, string $description, $userId = null): bool {
        // Lấy userId từ Session (cần đảm bảo session_start() đã chạy)
        $userId = $userId ?? ($_SESSION['user_id'] ?? null); 

        $query = "INSERT INTO system_logs (user_id, action_type, entity_name, entity_id, description) 
                  VALUES (:user_id, :act, :ename, :eid, :desc)";
        
        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                ':user_id'    => $userId,
                ':act'        => $actionType,
                ':ename'      => $entityName,
                ':eid'        => $entityId,
                ':desc'       => $description
            ]);
        } catch (PDOException $e) {
            // Tránh gây lỗi Fatal nếu bảng logs bị lỗi (chỉ ghi nhận lỗi vào log PHP)
            return false;
        }
    }
}
?>