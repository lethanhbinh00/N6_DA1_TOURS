<?php
class Operation {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. Lấy danh sách PAX của Booking
    public function getPaxByBooking($bookingId) {
        $stmt = $this->conn->prepare("SELECT * FROM booking_pax WHERE booking_id = :bid ORDER BY id ASC");
        $stmt->execute([':bid' => $bookingId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Thêm PAX (Khách) - Xử lý chuyển đổi Gender sang ENUM DB
    public function addPax($data) {
        $genderMap = [
            'Nam'    => 'male',
            'Nữ'     => 'female',
            'Khác'   => 'other',
            'male'   => 'male',
            'female' => 'female'
        ];

        $formGender = $data[':gen'] ?? 'Nam';
        $data[':gen'] = $genderMap[$formGender] ?? 'male';

        $query = "INSERT INTO booking_pax (booking_id, full_name, gender, dob, note) 
                  VALUES (:bid, :name, :gen, :dob, :note)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($data);
    }

    // 3. Xóa PAX
    public function deletePax($id) {
        $stmt = $this->conn->prepare("DELETE FROM booking_pax WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // 4. Lấy danh sách Dịch vụ của một Booking
    // 4. Lấy danh sách Dịch vụ - Sửa lỗi Unknown column 's.supplier_name'
public function getServicesByBooking($bookingId) {
    // Nếu bảng suppliers của bạn dùng cột 'name' thay vì 'supplier_name'
    $sql = "SELECT bs.*, s.name as supplier_name 
            FROM booking_services bs
            LEFT JOIN suppliers s ON bs.supplier_id = s.id
            WHERE bs.booking_id = :bid 
            ORDER BY bs.service_type, bs.id";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([':bid' => $bookingId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 11. Lấy dịch vụ theo ID - Cũng cần sửa tương tự
public function getServiceById($sid) {
    $sql = "SELECT bs.*, s.name as supplier_name 
            FROM booking_services bs
            LEFT JOIN suppliers s ON bs.supplier_id = s.id
            WHERE bs.id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([':id' => $sid]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// 5. Cập nhật luôn hàm getSuppliers để đồng bộ tên cột
public function getSuppliers() {
    // Sửa 'supplier_name' thành 'name' nếu bảng suppliers dùng cột name
    $sql = "SELECT id, name as supplier_name FROM suppliers ORDER BY name ASC";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

   

    // 6. Thêm Dịch vụ (Costing) - Khớp với enum('unpaid', 'paid')
    public function addService($data) {
        $sql = "INSERT INTO booking_services (booking_id, supplier_id, service_type, description, cost, supplier_payment_status) 
                VALUES (:bid, :sup, :type, :desc, :cost, 'unpaid')"; 
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    // 7. Xóa Dịch vụ (Costing)
    public function deleteService($id) {
        $stmt = $this->conn->prepare("DELETE FROM booking_services WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
    
    // 8. Cập nhật trạng thái điểm danh (Ajax)
    public function updatePaxCheckinStatus($paxId, $status) {
        $stmt = $this->conn->prepare("UPDATE booking_pax SET checkin_status = :status WHERE id = :id");
        return $stmt->execute([':status' => $status, ':id' => $paxId]);
    }

    // 9. Cập nhật ghi chú đặc biệt
    public function updatePaxSpecialRequest($paxId, $requestNote) {
        $stmt = $this->conn->prepare("UPDATE booking_pax SET special_requests = :note WHERE id = :id");
        return $stmt->execute([':note' => $requestNote, ':id' => $paxId]);
    }

    // 10. Ghi nhật ký Tour
    // server/models/Operation.php
public function logTourIncident($data) {
    $query = "INSERT INTO tour_logs (booking_id, guide_id, log_date, log_time, incident_type, details, photo_url)
              VALUES (:bid, :gid, :date, :time, :type, :details, :photo)";
    $stmt = $this->conn->prepare($query);
    return $stmt->execute($data);
}

    // 11. Cập nhật trạng thái thanh toán NCC
    public function updateSupplierPaymentStatus($serviceId, $status) {
        $paymentDate = ($status === 'paid') ? date('Y-m-d H:i:s') : null;
        $query = "UPDATE booking_services 
                  SET supplier_payment_status = :status, 
                      supplier_payment_date = :pdate 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':status' => $status,
            ':pdate'  => $paymentDate,
            ':id'     => $serviceId
        ]);
    }

    // 12. Xóa sạch danh sách khách của booking
    public function deleteAllPaxByBooking($bookingId) {
        $stmt = $this->conn->prepare("DELETE FROM booking_pax WHERE booking_id = :bid");
        return $stmt->execute([':bid' => $bookingId]);
    }

    // 13. Lấy thông tin Booking kèm tên HDV phụ trách
    public function getBookingById($id) {
        $sql = "SELECT b.*, u.full_name as guide_name 
                FROM bookings b 
                LEFT JOIN users u ON b.guide_id = u.id 
                WHERE b.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 14. Lấy thông tin Tour
    public function getTourById($id) {
        $sql = "SELECT * FROM tours WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // server/models/Operation.php
    // server/models/Operation.php
public function getTourLogs($bookingId) {
    $sql = "SELECT tl.*, u.full_name as guide_name 
            FROM tour_logs tl
            LEFT JOIN users u ON tl.guide_id = u.id
            WHERE tl.booking_id = :bid 
            ORDER BY tl.log_date DESC, tl.log_time DESC";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([':bid' => $bookingId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// server/models/Operation.php

// Hàm xóa nhật ký
public function deleteTourLog($logId) {
    $stmt = $this->conn->prepare("DELETE FROM tour_logs WHERE id = :id");
    return $stmt->execute([':id' => $logId]);
}

// Hàm cập nhật nhật ký
public function updateTourLog($logId, $details, $type) {
    $sql = "UPDATE tour_logs SET details = :details, incident_type = :type WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute([
        ':details' => $details,
        ':type'    => $type,
        ':id'      => $logId
    ]);
}
}