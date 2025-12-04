<?php
class Operation {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // --- 1. PAX (KHÁCH ĐI CÙNG) ---
    public function getPaxByBooking($booking_id) {
        $stmt = $this->conn->prepare("SELECT * FROM booking_pax WHERE booking_id = :bid ORDER BY id ASC");
        $stmt->execute([':bid' => $booking_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addPax($data) {
        $stmt = $this->conn->prepare("INSERT INTO booking_pax (booking_id, full_name, gender, dob, note) VALUES (:bid, :name, :gen, :dob, :note)");
        return $stmt->execute($data);
    }

    public function deletePax($id) {
        $stmt = $this->conn->prepare("DELETE FROM booking_pax WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // --- 2. SERVICES (DỊCH VỤ) ---
    public function getServicesByBooking($booking_id) {
        $sql = "SELECT s.*, sup.name as supplier_name 
                FROM booking_services s
                LEFT JOIN suppliers sup ON s.supplier_id = sup.id
                WHERE s.booking_id = :bid ORDER BY s.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':bid' => $booking_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addService($data) {
        $stmt = $this->conn->prepare("INSERT INTO booking_services (booking_id, supplier_id, service_type, description, cost) VALUES (:bid, :sup, :type, :desc, :cost)");
        return $stmt->execute($data);
    }

    public function deleteService($id) {
        $stmt = $this->conn->prepare("DELETE FROM booking_services WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
?>