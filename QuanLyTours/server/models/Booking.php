<?php
class Booking {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. Lấy danh sách (JOIN với bảng Tour để lấy tên tour)
    public function getAll() {
        $query = "SELECT b.*, t.name as tour_name, t.code as tour_code 
                  FROM bookings b 
                  LEFT JOIN tours t ON b.tour_id = t.id 
                  ORDER BY b.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Tạo mới Booking
    public function create($data) {
        try {
            $booking_code = "BK-" . time(); // Sinh mã tự động
            
            $query = "INSERT INTO bookings 
                     (booking_code, tour_id, customer_name, customer_id_card, customer_phone, customer_email, 
                      travel_date, adults, children, total_price, note) 
                     VALUES 
                     (:code, :tid, :name, :card, :phone, :email, :date, :adults, :child, :total, :note)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':code'   => $booking_code,
                ':tid'    => $data['tour_id'],
                ':name'   => $data['customer_name'],
                ':card'   => $data['customer_id_card'],
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

    // 3. Lấy thông tin 1 booking theo ID (Để sửa)
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM bookings WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 4. Cập nhật Booking
    public function update($id, $data) {
        try {
            $query = "UPDATE bookings SET 
                      tour_id = :tid, 
                      customer_name = :name, 
                      customer_id_card = :card,
                      customer_phone = :phone, 
                      customer_email = :email, 
                      travel_date = :date, 
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
                ':date'   => $data['travel_date'],
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

    // 5. Cập nhật trạng thái
    public function updateStatus($id, $status) {
        $stmt = $this->conn->prepare("UPDATE bookings SET status = :status WHERE id = :id");
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    // 6. Xóa
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM bookings WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
?>