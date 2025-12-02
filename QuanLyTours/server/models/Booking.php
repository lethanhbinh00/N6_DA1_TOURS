<?php
class Booking {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
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

    // [ĐÃ SỬA] Thêm return_date
    public function create($data) {
        try {
            $booking_code = "BK-" . time();
            $query = "INSERT INTO bookings 
                     (booking_code, tour_id, customer_name, customer_id_card, customer_phone, customer_email, 
                      travel_date, return_date, adults, children, total_price, note) 
                     VALUES 
                     (:code, :tid, :name, :card, :phone, :email, :start, :end, :adults, :child, :total, :note)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':code'   => $booking_code,
                ':tid'    => $data['tour_id'],
                ':name'   => $data['customer_name'],
                ':card'   => $data['customer_id_card'],
                ':phone'  => $data['customer_phone'],
                ':email'  => $data['customer_email'],
                ':start'  => $data['travel_date'],
                ':end'    => $data['return_date'], // Mới
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

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM bookings WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // [ĐÃ SỬA] Thêm return_date
    public function update($id, $data) {
        try {
            $query = "UPDATE bookings SET 
                      tour_id = :tid, 
                      customer_name = :name, 
                      customer_id_card = :card,
                      customer_phone = :phone, 
                      customer_email = :email, 
                      travel_date = :start, 
                      return_date = :end,
                      adults = :adults, 
                      children = :child, 
                      total_price = :total, 
                      note = :note 
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':tid'    => $data['tour_id'],
                ':name'   => $data['customer_name'],
                ':card'   => $data['customer_id_card'],
                ':phone'  => $data['customer_phone'],
                ':email'  => $data['customer_email'],
                ':start'  => $data['travel_date'],
                ':end'    => $data['return_date'], // Mới
                ':adults' => $data['adults'],
                ':child'  => $data['children'],
                ':total'  => $data['total_price'],
                ':note'   => $data['note'],
                ':id'     => $id
            ]);
            return "success";
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function updateStatus($id, $status) {
        $stmt = $this->conn->prepare("UPDATE bookings SET status = :status WHERE id = :id");
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public function updateDeposit($id, $amount, $method, $note) {
        try {
            $query = "UPDATE bookings SET 
                      deposit_amount = :amount, 
                      payment_method = :method, 
                      payment_note = :note,
                      status = 'deposited' 
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':amount' => $amount, ':method' => $method, ':note' => $note, ':id' => $id]);
            return "success";
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM bookings WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
?>