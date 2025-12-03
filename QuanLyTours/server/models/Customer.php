<?php
class Customer {
    private $conn;
    public function __construct($db) { $this->conn = $db; }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM customers ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM customers WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // --- CẬP NHẬT: THÊM TRƯỜNG id_card ---
    public function create($data) {
        try {
            $query = "INSERT INTO customers (full_name, id_card, phone, email, address, source, notes) 
                      VALUES (:name, :card, :phone, :email, :addr, :src, :note)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute($data);
            return "success";
        } catch (Exception $e) {
            // Lỗi 1062 là lỗi trùng lặp (Duplicate entry)
            if ($e->errorInfo[1] == 1062) return "duplicate";
            return $e->getMessage();
        }
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM customers WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // --- MỚI: KIỂM TRA TRÙNG LẶP (SĐT HOẶC CCCD) ---
    public function checkExists($phone, $id_card) {
        $query = "SELECT COUNT(*) as count FROM customers 
                  WHERE phone = :phone 
                  OR (id_card != '' AND id_card = :card)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':phone' => $phone, ':card' => $id_card]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    }
    
    // (Giữ nguyên hàm getBookingHistory...)
}
?>