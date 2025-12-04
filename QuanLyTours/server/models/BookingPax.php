<?php
class BookingPax {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy danh sách khách của 1 booking
    public function getByBookingId($bookingId) {
        $stmt = $this->conn->prepare("SELECT * FROM booking_pax WHERE booking_id = :bid");
        $stmt->execute([':bid' => $bookingId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm khách vào đoàn
    public function create($data) {
        $query = "INSERT INTO booking_pax (booking_id, full_name, gender, dob, customer_type, note) 
                  VALUES (:bid, :name, :gen, :dob, :type, :note)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($data);
    }

    // Xóa khách
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM booking_pax WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
?>