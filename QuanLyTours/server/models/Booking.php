<?php
class Booking {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. Lấy danh sách (Có bộ lọc)
    public function getAll($keyword = null, $status = null, $dateFrom = null, $dateTo = null) {
        $sql = "SELECT b.*, t.name as tour_name, t.code as tour_code, t.min_deposit,
                       c.full_name as customer_name, c.phone as customer_phone, c.id_card as customer_id_card
                FROM bookings b 
                LEFT JOIN tours t ON b.tour_id = t.id 
                LEFT JOIN customers c ON b.customer_id = c.id
                WHERE 1=1"; 
        
        $params = [];
        if (!empty($keyword)) {
            $sql .= " AND (b.booking_code LIKE ? OR c.full_name LIKE ? OR c.phone LIKE ?)";
            $searchTerm = "%$keyword%";
            array_push($params, $searchTerm, $searchTerm, $searchTerm);
        }
        if (!empty($status)) { $sql .= " AND b.status = ?"; $params[] = $status; }
        if (!empty($dateFrom)) { $sql .= " AND b.travel_date >= ?"; $params[] = $dateFrom; }
        if (!empty($dateTo)) { $sql .= " AND b.travel_date <= ?"; $params[] = $dateTo; }

        $sql .= " ORDER BY b.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. [FIX] Tạo mới (ĐÃ THÊM TẤT CẢ CÁC CỘT MỚI VÀO SQL INSERT)
    public function create($data) {
        try {
            $booking_code = "BK-" . time();
            $query = "INSERT INTO bookings 
                     (booking_code, tour_id, customer_id, transport_supplier_id, hotel_supplier_id, pickup_location, 
                      travel_date, return_date, adults, children, total_price, note) 
                     VALUES 
                     (:code, :tid, :cid, :trans, :hotel, :pickup, :start, :end, :adults, :child, :total, :note)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':code'   => $booking_code,
                ':tid'    => $data['tour_id'],
                ':cid'    => $data['customer_id'],
                ':trans'  => $data['transport_id'],
                ':hotel'  => $data['hotel_id'],
                ':pickup' => $data['pickup_location'],
                ':start'  => $data['travel_date'],
                ':end'    => $data['return_date'],
                ':adults' => $data['adults'],
                ':child'  => $data['children'],
                ':total'  => $data['total_price'],
                ':note'   => $data['note']
            ]);
            
            return $this->conn->lastInsertId(); 

        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    // 3. [FIX] Cập nhật (ĐÃ THÊM TẤT CẢ CÁC CỘT MỚI VÀO SQL UPDATE)
    public function update($id, $data) {
        try {
            $query = "UPDATE bookings SET 
                      tour_id=:tid, customer_id=:cid, transport_supplier_id=:trans, hotel_supplier_id=:hotel, pickup_location=:pickup, 
                      travel_date=:start, return_date=:end, adults=:adults, children=:child, total_price=:total, note=:note 
                      WHERE id=:id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':tid'=>$data['tour_id'], 
                ':cid'=>$data['customer_id'], 
                ':trans'=>$data['transport_id'], 
                ':hotel'=>$data['hotel_id'], 
                ':pickup'=>$data['pickup_location'], 
                ':start'=>$data['travel_date'], 
                ':end'=>$data['return_date'], 
                ':adults'=>$data['adults'], 
                ':child'=>$data['children'], 
                ':total'=>$data['total_price'], 
                ':note'=>$data['note'], 
                ':id'=>$id
            ]);
            return "success";
        } catch (Exception $e) { return $e->getMessage(); }
    }

    // 4. Lấy 1 Booking
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM bookings WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // --- CÁC HÀM KHÁC GIỮ NGUYÊN ---
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
    public function addPayment($bookingId, $amount, $method, $note, $userId) {
        try {
            $this->conn->beginTransaction(); $stmt = $this->conn->prepare("INSERT INTO booking_payments (booking_id, amount, payment_method, note, created_by) VALUES (?, ?, ?, ?, ?)"); $stmt->execute([$bookingId, $amount, $method, $note, $userId]);
            $stmtSum = $this->conn->prepare("SELECT SUM(amount) as total_paid FROM booking_payments WHERE booking_id = ?"); $stmtSum->execute([$bookingId]); $totalPaid = $stmtSum->fetch(PDO::FETCH_ASSOC)['total_paid'] ?? 0;
            $stmtTotal = $this->conn->prepare("SELECT total_price FROM bookings WHERE id = ?"); $stmtTotal->execute([$bookingId]); $totalPrice = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total_price'];
            $newStatus = ($totalPaid >= $totalPrice) ? 'completed' : 'deposited'; $stmtUpdate = $this->conn->prepare("UPDATE bookings SET deposit_amount = ?, status = ? WHERE id = ?"); $stmtUpdate->execute([$totalPaid, $newStatus, $bookingId]);
            $this->conn->commit(); return "success";
        } catch (Exception $e) { $this->conn->rollBack(); return $e->getMessage(); }
    }
    public function deletePayment($paymentId) {
        try {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare("SELECT booking_id FROM booking_payments WHERE id = ?"); $stmt->execute([$paymentId]); $row = $stmt->fetch(PDO::FETCH_ASSOC); if (!$row) return "Giao dịch không tồn tại"; $bookingId = $row['booking_id'];
            $stmtDel = $this->conn->prepare("DELETE FROM booking_payments WHERE id = ?"); $stmtDel->execute([$paymentId]);
            $stmtSum = $this->conn->prepare("SELECT COALESCE(SUM(amount), 0) FROM booking_payments WHERE booking_id = ?"); $stmtSum->execute([$bookingId]); $totalPaid = $stmtSum->fetchColumn();
            $stmtTotal = $this->conn->prepare("SELECT total_price FROM bookings WHERE id = ?"); $stmtTotal->execute([$bookingId]); $totalPrice = $stmtTotal->fetchColumn();
            if ($totalPaid >= $totalPrice) { $newStatus = 'completed'; } elseif ($totalPaid > 0) { $newStatus = 'deposited'; } else { $newStatus = 'confirmed'; }
            $stmtUpd = $this->conn->prepare("UPDATE bookings SET deposit_amount = ?, status = ? WHERE id = ?"); $stmtUpd->execute([$totalPaid, $newStatus, $bookingId]);
            $this->conn->commit(); return "success";
        } catch (Exception $e) { $this->conn->rollBack(); return $e->getMessage(); }
    }
    public function getPaymentsByBooking($bookingId) {
        $sql = "SELECT p.*, u.full_name as collector_name FROM booking_payments p LEFT JOIN users u ON p.created_by = u.id WHERE p.booking_id = :bid ORDER BY p.created_at DESC";
        $stmt = $this->conn->prepare($sql); $stmt->execute([':bid' => $bookingId]); return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getPaymentById($id) {
        $sql = "SELECT p.*, b.booking_code, b.customer_name, b.customer_phone FROM booking_payments p JOIN bookings b ON p.booking_id = b.id WHERE p.id = :id";
        $stmt = $this->conn->prepare($sql); $stmt->execute([':id' => $id]); return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function delete($id) { $stmt = $this->conn->prepare("DELETE FROM bookings WHERE id = :id"); return $stmt->execute([':id' => $id]); }
}
?>