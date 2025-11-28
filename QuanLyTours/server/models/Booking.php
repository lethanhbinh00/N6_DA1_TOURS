<?php
class Booking {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        try {
            $booking_code = "BK-" . time(); 
            $query = "INSERT INTO bookings 
                     (booking_code, tour_id, customer_name, customer_phone, customer_email, 
                      travel_date, adults, children, total_price, note) 
                     VALUES 
                     (:code, :tid, :name, :phone, :email, :date, :adults, :child, :total, :note)";
            
            $stmt = $this->conn->prepare($query);

            $stmt->execute([
                ':code'   => $booking_code,
                ':tid'    => $data['tour_id'],
                ':name'   => $data['customer_name'],
                ':phone'  => $data['customer_phone'],
                ':email'  => $data['customer_email'],
                ':date'   => $data['travel_date'],
                ':adults' => $data['adults'],
                ':child'  => $data['children'],
                ':total'  => $data['total_price'],
                ':note'   => $data['note']
            ]);

            return "success";
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function getAll() {
        $query = "SELECT b.*, t.name as tour_name, t.code as tour_code 
                  FROM bookings b 
                  LEFT JOIN tours t ON b.tour_id = t.id 
                  ORDER BY b.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function updateStatus($id, $status) {
        $query = "UPDATE bookings SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public function delete($id) {
        $query = "DELETE FROM bookings WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }
}

?>