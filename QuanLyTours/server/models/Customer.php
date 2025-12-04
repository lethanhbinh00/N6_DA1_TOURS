<?php
class Customer {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM customers ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM customers WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Thêm mới (Có id_card)
    public function create($data) {
        try {
            $query = "INSERT INTO customers (full_name, id_card, phone, email, address, source, notes) 
                      VALUES (:name, :card, :phone, :email, :addr, :src, :note)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute($data);
            return "success";
        } catch (Exception $e) {
            if ($e->errorInfo[1] == 1062) return "duplicate";
            return $e->getMessage();
        }
    }

    // [ĐÃ NÂNG CẤP] Cập nhật thông tin khách và đồng bộ sang Booking
    public function update($id, $data) {
        try {
            $this->conn->beginTransaction();

            // 1. Cập nhật bảng Customers (Gốc)
            $query = "UPDATE customers SET 
                      full_name = :name, 
                      id_card = :card, 
                      phone = :phone, 
                      email = :email, 
                      address = :addr, 
                      source = :src, 
                      notes = :note 
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $data[':id'] = $id;
            $stmt->execute($data);

            // 2. [MỚI] Đồng bộ sang bảng Bookings (Tìm theo SĐT cũ hoặc ID nếu có)
            // Ở đây ta cập nhật dựa trên ID khách hàng (nếu bảng booking có lưu customer_id thì tốt nhất)
            // Nhưng do bảng booking hiện tại lưu thông tin độc lập, ta sẽ update dựa trên SĐT cũ trước khi đổi (nếu SĐT là khóa chính logic)
            // TUY NHIÊN: Cách an toàn nhất bây giờ là update theo customer_phone MỚI (nếu khách không đổi SĐT)
            // Hoặc tốt nhất là update theo ID (nhưng bảng booking của bạn chưa có cột customer_id liên kết cứng).
            
            // => GIẢI PHÁP TẠM THỜI:
            // Cập nhật bảng bookings dựa trên phone MỚI (giả sử khách chỉ sửa tên/cccd chứ không đổi số đt).
            // Nếu bạn đổi cả số điện thoại thì hệ thống sẽ không biết đường nào mà lần.
            
            $syncQuery = "UPDATE bookings SET 
                          customer_name = :name,
                          customer_id_card = :card,
                          customer_email = :email
                          WHERE customer_phone = :phone"; // Dựa vào SĐT để tìm đơn hàng
            
            $stmtSync = $this->conn->prepare($syncQuery);
            $stmtSync->execute([
                ':name'  => $data[':name'],
                ':card'  => $data[':card'],
                ':email' => $data[':email'],
                ':phone' => $data[':phone']
            ]);

            $this->conn->commit();
            return "success";
        } catch (Exception $e) {
            $this->conn->rollBack();
            if ($e->errorInfo[1] == 1062) return "duplicate";
            return $e->getMessage();
        }
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM customers WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // Kiểm tra trùng SĐT hoặc CCCD
    public function checkExists($phone, $id_card) {
        $query = "SELECT COUNT(*) as count FROM customers 
                  WHERE phone = :phone 
                  OR (id_card != '' AND id_card = :card)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':phone' => $phone, ':card' => $id_card]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    }

    // Lấy lịch sử booking
    public function getBookingHistory($phone) {
        $query = "SELECT b.*, t.name as tour_name, t.code as tour_code
                  FROM bookings b 
                  JOIN tours t ON b.tour_id = t.id 
                  WHERE b.customer_phone = :phone 
                  ORDER BY b.travel_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':phone' => $phone]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thống kê tổng chi tiêu
    public function getSummary($phone) {
        $query = "SELECT 
                    COUNT(*) as total_tours, 
                    COALESCE(SUM(total_price), 0) as total_spent 
                  FROM bookings 
                  WHERE customer_phone = :phone AND status != 'cancelled'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':phone' => $phone]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>