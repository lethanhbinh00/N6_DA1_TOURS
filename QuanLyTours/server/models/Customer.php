<?php
class Customer {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM customers ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        try {
            $query = "INSERT INTO customers (full_name, phone, email, address, source, notes) 
                      VALUES (:name, :phone, :email, :addr, :src, :note)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':name'  => $data['full_name'],
                ':phone' => $data['phone'],
                ':email' => $data['email'],
                ':addr'  => $data['address'],
                ':src'   => $data['source'],
                ':note'  => $data['notes']
            ]);
            return "success";
        } catch (Exception $e) {
            if ($e->errorInfo[1] == 1062) return "duplicate"; // Trùng SĐT
            return false;
        }
    }
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM customers WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function update($id, $data) {
        $query = "UPDATE customers SET full_name=:name, phone=:phone, email=:email, 
                  address=:addr, source=:src, notes=:note WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':name'  => $data['full_name'],
            ':phone' => $data['phone'],
            ':email' => $data['email'],
            ':addr'  => $data['address'],
            ':src'   => $data['source'],
            ':note'  => $data['notes'],
            ':id'    => $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM customers WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
    public function getBookingHistory($phone) {
        $query = "SELECT b.*, t.name as tour_name 
                  FROM bookings b 
                  JOIN tours t ON b.tour_id = t.id 
                  WHERE b.customer_phone = :phone 
                  ORDER BY b.travel_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':phone' => $phone]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>