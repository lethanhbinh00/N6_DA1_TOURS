<?php
class Booking {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($keyword = null, $status = null, $dateFrom = null, $dateTo = null) {
        // Kỹ thuật 1=1 để dễ nối chuỗi AND
        $sql = "SELECT b.*, t.name as tour_name, t.code as tour_code 
                FROM bookings b 
                LEFT JOIN tours t ON b.tour_id = t.id 
                WHERE 1=1"; 
        
        $params = [];

        // 1. Lọc từ khóa (Tìm trong cả Mã, Tên, SĐT, CCCD, Tên Tour)
        if (!empty($keyword)) {
            $keyword = trim($keyword);
            $sql .= " AND (b.booking_code LIKE ? OR b.customer_name LIKE ? OR b.customer_phone LIKE ? OR b.customer_id_card LIKE ? OR t.name LIKE ?)";
            $searchTerm = "%$keyword%";
            // Đẩy 5 tham số vào mảng
            array_push($params, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
        }

        // 2. Lọc trạng thái
        if (!empty($status)) {
            $sql .= " AND b.status = ?";
            $params[] = $status;
        }

        // 3. Lọc ngày đi (Từ ngày)
        if (!empty($dateFrom)) {
            $sql .= " AND b.travel_date >= ?";
            $params[] = $dateFrom;
        }

        // 4. Lọc ngày đi (Đến ngày)
        if (!empty($dateTo)) {
            $sql .= " AND b.travel_date <= ?";
            $params[] = $dateTo;
        }

        $sql .= " ORDER BY b.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // --- CÁC HÀM KHÁC GIỮ NGUYÊN ---

    public function create($data) {
        try {
            $booking_code = "BK-" . time();
            $query = "INSERT INTO bookings (booking_code, tour_id, customer_name, customer_id_card, customer_phone, customer_email, travel_date, return_date, adults, children, total_price, note) VALUES (:code, :tid, :name, :card, :phone, :email, :start, :end, :adults, :child, :total, :note)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':code'=>$booking_code, ':tid'=>$data['tour_id'], ':name'=>$data['customer_name'], ':card'=>$data['customer_id_card'], ':phone'=>$data['customer_phone'], ':email'=>$data['customer_email'], ':start'=>$data['travel_date'], ':end'=>$data['return_date'], ':adults'=>$data['adults'], ':child'=>$data['children'], ':total'=>$data['total_price'], ':note'=>$data['note']]);
            return "success";
        } catch (Exception $e) { return $e->getMessage(); }
    }

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

    public function updateDeposit($id, $amount, $method, $note, $status) {
        try {
            $query = "UPDATE bookings SET deposit_amount = :amount, payment_method = :method, payment_note = :note, status = :status WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':amount' => $amount, ':method' => $method, ':note' => $note, ':status' => $status, ':id' => $id]);
            return "success";
        } catch (Exception $e) { return $e->getMessage(); }
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM bookings WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
?>