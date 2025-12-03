<?php
require_once __DIR__ . '/../config/Database.php';

class DashboardController {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function index() {
        // 1. Thống kê Tổng Tour
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM tours");
        $stmt->execute();
        $totalTours = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // 2. Thống kê Tổng Khách hàng
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM customers");
        $stmt->execute();
        $totalCustomers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // 3. Thống kê Tổng Booking (Đơn hàng)
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM bookings");
        $stmt->execute();
        $totalBookings = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // 4. Tính Tổng Doanh Thu (Chỉ tính đơn đã xác nhận/hoàn tất)
        // Dùng COALESCE để nếu không có đơn nào thì trả về 0 thay vì null
        $stmt = $this->conn->prepare("SELECT COALESCE(SUM(total_price), 0) as total FROM bookings WHERE status IN ('confirmed', 'completed', 'paid')");
        $stmt->execute();
        $revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

     
        $queryRecent = "SELECT b.*, t.name as tour_name 
                        FROM bookings b 
                        JOIN tours t ON b.tour_id = t.id 
                        ORDER BY b.created_at DESC LIMIT 5";
        $stmt = $this->conn->prepare($queryRecent);
        $stmt->execute();
        $recentBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Load giao diện
        require_once __DIR__ . '/../../views/dashboard/index.php';
    }
}
?>