<?php
require_once __DIR__ . '/../config/Database.php';

class DashboardController {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function index() {
        // 1. CÁC CON SỐ TỔNG QUAN
        $totalTours = $this->conn->query("SELECT COUNT(*) FROM tours")->fetchColumn();
        $totalCustomers = $this->conn->query("SELECT COUNT(*) FROM customers")->fetchColumn();
        $totalBookings = $this->conn->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
        $revenue = $this->conn->query("SELECT COALESCE(SUM(total_price), 0) FROM bookings WHERE status != 'cancelled'")->fetchColumn();

        // 2. DỮ LIỆU BIỂU ĐỒ DOANH THU (7 NGÀY GẦN NHẤT)
        $chartRevenue = [];
        $chartDates = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $stmt = $this->conn->prepare("SELECT COALESCE(SUM(total_price), 0) FROM bookings WHERE DATE(created_at) = :date AND status != 'cancelled'");
            $stmt->execute([':date' => $date]);
            $chartRevenue[] = $stmt->fetchColumn();
            $chartDates[] = date('d/m', strtotime($date));
        }

        // 3. DỮ LIỆU BIỂU ĐỒ TRẠNG THÁI (PIE CHART)
        $statusCounts = [
            'new' => 0, 'confirmed' => 0, 'deposited' => 0, 'completed' => 0, 'cancelled' => 0
        ];
        $stmt = $this->conn->query("SELECT status, COUNT(*) as count FROM bookings GROUP BY status");
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $statusCounts[$row['status']] = $row['count'];
        }

        // 4. TOUR SẮP KHỞI HÀNH (TRONG 7 NGÀY TỚI)
        $upcomingTours = $this->conn->query("
            SELECT b.booking_code, t.name as tour_name, b.travel_date, b.customer_name, b.adults + b.children as total_pax
            FROM bookings b
            JOIN tours t ON b.tour_id = t.id
            WHERE b.travel_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
            AND b.status != 'cancelled'
            ORDER BY b.travel_date ASC
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);

        // 5. ĐƠN HÀNG MỚI NHẤT
        $recentBookings = $this->conn->query("
            SELECT b.*, t.name as tour_name 
            FROM bookings b 
            JOIN tours t ON b.tour_id = t.id 
            ORDER BY b.created_at DESC LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Gọi View
        require_once __DIR__ . '/../../views/dashboard/index.php';
    }
}
?>