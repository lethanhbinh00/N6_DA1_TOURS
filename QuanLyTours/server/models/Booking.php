<?php
class Booking {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

<<<<<<< HEAD
    // 1. Lấy danh sách
    public function getAll($keyword = null, $status = null, $dateFrom = null, $dateTo = null) {
        $sql = "SELECT b.*, t.name as tour_name, t.code as tour_code, t.min_deposit,
                       c.full_name as customer_name, c.phone as customer_phone, c.id_card as customer_id_card
                FROM bookings b 
                LEFT JOIN tours t ON b.tour_id = t.id 
                LEFT JOIN customers c ON b.customer_id = c.id
                WHERE 1=1"; 
        
=======
    // 1. Lấy danh sách (Có bộ lọc)
    public function getAll($keyword = null, $status = null, $dateFrom = null, $dateTo = null, $tourId = null)
    {
        // Note: some installations may not have `t.min_deposit` column — avoid selecting it to prevent errors
        $sql = "SELECT b.*, t.name as tour_name, t.code as tour_code 
            FROM bookings b 
            LEFT JOIN tours t ON b.tour_id = t.id 
            LEFT JOIN customers c ON b.customer_id = c.id
            WHERE 1=1";

>>>>>>> origin/main
        $params = [];
        if (!empty($keyword)) {
            $sql .= " AND (b.booking_code LIKE ? OR c.full_name LIKE ? OR c.phone LIKE ?)";
            $searchTerm = "%$keyword%";
            array_push($params, $searchTerm, $searchTerm, $searchTerm);
        }
        if (!empty($status)) { $sql .= " AND b.status = ?"; $params[] = $status; }
        if (!empty($tourId)) {
            $sql .= " AND b.tour_id = ?";
            $params[] = $tourId;
        }
        if (!empty($dateFrom)) { $sql .= " AND b.travel_date >= ?"; $params[] = $dateFrom; }
        if (!empty($dateTo)) { $sql .= " AND b.travel_date <= ?"; $params[] = $dateTo; }

        $sql .= " ORDER BY b.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Tạo mới
    public function create($data) {
        try {
<<<<<<< HEAD
=======
            $booking_code = "BK-" . time();
            // [CẬP NHẬT] Đã thêm các cột supplier_id và pickup_location
>>>>>>> origin/main
            $query = "INSERT INTO bookings 
                     (booking_code, tour_id, customer_id, customer_name, customer_phone, customer_id_card, customer_email, 
                      guide_id, transport_supplier_id, hotel_supplier_id, pickup_location, flight_number, room_details, 
                      travel_date, return_date, adults, children, total_price, note, status) 
                     VALUES 
                     (:code, :tid, :cid, :name, :phone, :card, :email, 
                      :gid, :trans, :hotel, :pickup, :flight, :room, :start, :end, :adults, :child, :total, :note, 'new')";
            
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                ':code'   => "BK-" . time(),
                ':tid'    => $data['tour_id'],
                ':cid'    => !empty($data['customer_id']) ? $data['customer_id'] : null,
                ':name'   => $data['customer_name'],
                ':phone'  => $data['customer_phone'],
                ':card'   => $data['customer_id_card'],
                ':email'  => $data['customer_email'],
                ':gid'    => !empty($data['guide_id']) ? $data['guide_id'] : null,
                ':trans'  => !empty($data['transport_id']) ? $data['transport_id'] : null,
                ':hotel'  => !empty($data['hotel_id']) ? $data['hotel_id'] : null,
                ':pickup' => $data['pickup_location'] ?? '',
                ':flight' => $data['flight_number'] ?? null,
                ':room'   => $data['room_details'] ?? null,
                ':start'  => $data['travel_date'],
                ':end'    => $data['return_date'] ?? null,
                ':adults' => $data['adults'],
                ':child'  => $data['children'],
                ':total'  => $data['total_price'],
                ':note'   => $data['note'] ?? ''
            ]);
<<<<<<< HEAD
        } catch (Exception $e) {
            die("Lỗi tại Model Booking: " . $e->getMessage());
        }
=======
            $newId = $this->conn->lastInsertId();

            // Auto-create a departure for this tour/date if not exists
            try {
                $tourId = $data['tour_id'];
                $startDate = $data['travel_date'];
                // compute seats as number of pax booked (adults + children)
                $seats = (int)$data['adults'] + (int)$data['children'];

                $stmtCheck = $this->conn->prepare("SELECT id FROM departures WHERE tour_id = ? AND start_date = ? LIMIT 1");
                $stmtCheck->execute([$tourId, $startDate]);
                $exists = $stmtCheck->fetch(PDO::FETCH_ASSOC);
                if (!$exists) {
                    // Insert minimal departure record. Some installations may have guide_id/ note columns; use column list without guide_id.
                    $stmtIns = $this->conn->prepare("INSERT INTO departures (tour_id, start_date, seats) VALUES (?, ?, ?)");
                    $stmtIns->execute([$tourId, $startDate, $seats]);
                }
            } catch (Exception $e) {
                // non-fatal: ignore so booking creation still succeeds
            }

            return $newId;
        } catch (Exception $e) { return "Error: " . $e->getMessage(); }
>>>>>>> origin/main
    }

    // 3. Cập nhật
    public function update($id, $data) {
        try {
            // [CẬP NHẬT] Đã thêm các cột supplier_id và pickup_location
            $query = "UPDATE bookings SET 
                      tour_id=:tid, customer_id=:cid, customer_name=:name, customer_phone=:phone, customer_id_card=:card, customer_email=:email,
                      guide_id=:gid, transport_supplier_id=:trans, hotel_supplier_id=:hotel, pickup_location=:pickup, flight_number=:flight, room_details=:room,
                      travel_date=:start, return_date=:end, adults=:adults, children=:child, total_price=:total, note=:note 
                      WHERE id=:id";
            
            $stmt = $this->conn->prepare($query);
            $flight = empty($data['flight_number']) ? null : $data['flight_number'];
            $room   = empty($data['room_details']) ? null : $data['room_details'];
            
            return $stmt->execute([
                ':tid'=>$data['tour_id'], 
                ':cid'=>$data['customer_id'], 
                ':name'=>$data['customer_name'],
                ':phone'=>$data['customer_phone'],
                ':card'=>$data['customer_id_card'],
                ':email'=>$data['customer_email'],
                ':gid'=>$data['guide_id'] ?? null,
                ':trans'=>$data['transport_id'], 
                ':hotel'=>$data['hotel_id'], 
                ':pickup'=>$data['pickup_location'], 
                ':flight'=>$flight,
                ':room'=>$room,
                ':start'=>$data['travel_date'], 
                ':end'=>$data['return_date'], 
                ':adults'=>$data['adults'], 
                ':child'=>$data['children'], 
                ':total'=>$data['total_price'], 
                ':note'=>$data['note'], 
                ':id'=>$id
            ]);
        } catch (Exception $e) { return $e->getMessage(); }
    }

    // 4. Lấy 1 Booking
    public function getById($id) {
        $sql = "SELECT b.*, u.full_name as guide_name 
                FROM bookings b
                LEFT JOIN users u ON b.guide_id = u.id
                WHERE b.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // 5. Chỉ định HDV
    public function assignGuide($bookingId, $guideId) {
        $stmt = $this->conn->prepare("UPDATE bookings SET guide_id = :gid WHERE id = :bid");
        $guideId = empty($guideId) ? null : $guideId;
        return $stmt->execute([':gid' => $guideId, ':bid' => $bookingId]);
    }

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
            $this->conn->beginTransaction(); 
            $stmt = $this->conn->prepare("INSERT INTO booking_payments (booking_id, amount, payment_method, note, created_by) VALUES (?, ?, ?, ?, ?)"); 
            $stmt->execute([$bookingId, $amount, $method, $note, $userId]);
            
            $stmtSum = $this->conn->prepare("SELECT SUM(amount) as total_paid FROM booking_payments WHERE booking_id = ?"); 
            $stmtSum->execute([$bookingId]); 
            $totalPaid = $stmtSum->fetch(PDO::FETCH_ASSOC)['total_paid'] ?? 0;
            
            $stmtTotal = $this->conn->prepare("SELECT total_price FROM bookings WHERE id = ?"); 
            $stmtTotal->execute([$bookingId]); 
            $totalPrice = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total_price'];
            
            $newStatus = ($totalPaid >= $totalPrice) ? 'completed' : 'deposited'; 
            $stmtUpdate = $this->conn->prepare("UPDATE bookings SET deposit_amount = ?, status = ? WHERE id = ?"); 
            $stmtUpdate->execute([$totalPaid, $newStatus, $bookingId]);
            
            $this->conn->commit(); 
            return "success";
        } catch (Exception $e) { $this->conn->rollBack(); return $e->getMessage(); }
    }

    public function deletePayment($paymentId) {
        try {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare("SELECT booking_id FROM booking_payments WHERE id = ?"); 
            $stmt->execute([$paymentId]); 
            $row = $stmt->fetch(PDO::FETCH_ASSOC); 
            if (!$row) return "Giao dịch không tồn tại"; 
            $bookingId = $row['booking_id'];
            
            $stmtDel = $this->conn->prepare("DELETE FROM booking_payments WHERE id = ?"); 
            $stmtDel->execute([$paymentId]);
            
            $stmtSum = $this->conn->prepare("SELECT COALESCE(SUM(amount), 0) FROM booking_payments WHERE booking_id = ?"); 
            $stmtSum->execute([$bookingId]); 
            $totalPaid = $stmtSum->fetchColumn();
            
            $stmtTotal = $this->conn->prepare("SELECT total_price FROM bookings WHERE id = ?"); 
            $stmtTotal->execute([$bookingId]); 
            $totalPrice = $stmtTotal->fetchColumn();
            
            if ($totalPaid >= $totalPrice) { $newStatus = 'completed'; } elseif ($totalPaid > 0) { $newStatus = 'deposited'; } else { $newStatus = 'confirmed'; }
            $stmtUpd = $this->conn->prepare("UPDATE bookings SET deposit_amount = ?, status = ? WHERE id = ?"); 
            $stmtUpd->execute([$totalPaid, $newStatus, $bookingId]);
            
            $this->conn->commit(); 
            return "success";
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

    public function delete($id) { 
        $stmt = $this->conn->prepare("DELETE FROM bookings WHERE id = :id"); 
        return $stmt->execute([':id' => $id]); 
    }

    public function getFinancialSummary($dateFrom = null, $dateTo = null) {
        $sql = "
            SELECT
                b.id AS booking_id,
                b.booking_code,
                t.name AS tour_name,
                b.travel_date,
                b.total_price AS total_revenue,
                u.full_name AS guide_name,
                COALESCE(SUM(bs.cost), 0) AS total_cost,
                COALESCE(SUM(CASE WHEN bs.supplier_payment_status = 'paid' THEN bs.cost ELSE 0 END), 0) AS total_paid_supplier
            FROM bookings b
            LEFT JOIN tours t ON b.tour_id = t.id
            LEFT JOIN users u ON b.guide_id = u.id
            LEFT JOIN booking_services bs ON b.id = bs.booking_id
            WHERE 1=1
        ";
        
        $params = [];
        if (!empty($dateFrom)) { $sql .= " AND b.travel_date >= ?"; $params[] = $dateFrom; }
        if (!empty($dateTo)) { $sql .= " AND b.travel_date <= ?"; $params[] = $dateTo; }

        $sql .= "
            GROUP BY b.id, b.booking_code, t.name, b.travel_date, b.total_price, u.full_name
            ORDER BY b.travel_date DESC
        ";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateServicePaymentStatus($serviceId, $status) {
        $paymentDate = ($status == 'paid') ? date('Y-m-d') : null;
        $query = "UPDATE booking_services 
                  SET supplier_payment_status = :status, 
                      supplier_payment_date = :pdate 
                  WHERE id = :id";
                  
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':status' => $status,
            ':pdate' => $paymentDate,
            ':id' => $serviceId
        ]);
    }
    // server/models/Booking.php

public function getBookingById($id) {
    // Truy vấn lấy thông tin booking và tên HDV từ bảng users
    $sql = "SELECT b.*, u.full_name as guide_name 
            FROM bookings b 
            LEFT JOIN users u ON b.guide_id = u.id 
            WHERE b.id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
} // Kết thúc class Booking
?>