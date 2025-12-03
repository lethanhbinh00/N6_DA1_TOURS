<?php
class Supplier {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy danh sách
    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM suppliers ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm mới
    public function create($data) {
        try {
            $query = "INSERT INTO suppliers (name, type, contact_person, phone, email, address) 
                      VALUES (:name, :type, :contact, :phone, :email, :addr)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute($data);
            return "success";
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    // Xóa
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM suppliers WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // Kiểm tra trùng lặp SĐT hoặc Email
    public function checkExists($phone, $email) {
        // Logic: Tìm xem có ai trùng SĐT không? HOẶC trùng Email (nếu email không rỗng)
        $query = "SELECT COUNT(*) as count FROM suppliers 
                  WHERE phone = :phone 
                  OR (email != '' AND email = :email)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':phone' => $phone,
            ':email' => $email
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0; // Trả về True nếu đã tồn tại
    }
}
?>