<?php
class Supplier {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM suppliers ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM suppliers WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // [CẬP NHẬT] Thêm các cột mới
    public function create($data) {
        try {
            $query = "INSERT INTO suppliers (name, type, contact_person, phone, email, address, service_description, service_capacity, contract_file, contract_expiry) 
                      VALUES (:name, :type, :contact, :phone, :email, :addr, :desc, :capa, :file, :expiry)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute($data);
            return "success";
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    // [CẬP NHẬT] Thêm các cột mới
    public function update($id, $data) {
        try {
            $query = "UPDATE suppliers SET 
                      name = :name, type = :type, contact_person = :contact, phone = :phone, email = :email, address = :addr, 
                      service_description = :desc, service_capacity = :capa, contract_file = :file, contract_expiry = :expiry 
                      WHERE id = :id";
            
            $data[':id'] = $id;
            $stmt = $this->conn->prepare($query);
            $stmt->execute($data);
            return "success";
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM suppliers WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
    
    // [MỚI] Hàm kiểm tra trùng SĐT hoặc email (tùy chọn)
    public function checkExists($phone, $email, $id = null) {
        $query = "SELECT COUNT(*) FROM suppliers WHERE phone = :phone OR email = :email";
        $params = [':phone' => $phone, ':email' => $email];
        
        if ($id) {
            $query .= " AND id != :id";
            $params[':id'] = $id;
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }
}
?>