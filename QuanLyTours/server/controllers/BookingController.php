<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Tour.php';
require_once __DIR__ . '/../models/Customer.php'; // [MỚI] Gọi model Customer

class BookingController {

    public function index() {
        $db = (new Database())->getConnection();
        $bookings = (new Booking($db))->getAll();
        
        if (!empty($bookings)) {
            foreach ($bookings as $key => $value) {
                $bookings[$key]['customer_id_card'] = $value['customer_id_card'] ?? ''; 
                $bookings[$key]['customer_phone']   = $value['customer_phone'] ?? '';   
                $bookings[$key]['tour_code']        = $value['tour_code'] ?? 'N/A';     
                $bookings[$key]['tour_name']        = $value['tour_name'] ?? 'Tour đã xóa';
                $bookings[$key]['customer_name']    = $value['customer_name'] ?? 'Khách lẻ';
                $bookings[$key]['deposit_amount']   = $value['deposit_amount'] ?? 0;
            }
        }
        require_once __DIR__ . '/../../views/booking/index.php';
    }

    // [ĐÃ SỬA] Hàm tạo mới: Lấy thêm danh sách Khách hàng
    public function create() {
        $db = (new Database())->getConnection();
        
        // Lấy danh sách Tour
        $tours = (new Tour($db))->getAll();
        
        // Lấy danh sách Khách hàng để chọn
        $customers = (new Customer($db))->getAll();

        require_once __DIR__ . '/../../views/booking/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processForm('create');
        }
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $db = (new Database())->getConnection();
            $booking = (new Booking($db))->getById($id);
            $tours = (new Tour($db))->getAll();
            if (!$booking) { echo "Không tìm thấy đơn hàng!"; die(); }
            require_once __DIR__ . '/../../views/booking/edit.php';
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processForm('update');
        }
    }

    public function status() {
        $id = $_GET['id'] ?? null;
        $status = $_GET['status'] ?? null;
        if ($id && $status) {
            $db = (new Database())->getConnection();
            (new Booking($db))->updateStatus($id, $status);
            header("Location: index.php?action=booking-list&msg=status_updated");
        }
    }

    public function deposit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['booking_id'];
            $amount = $_POST['deposit_amount'];
            $total = $_POST['total_price_hidden'];
            $method = $_POST['payment_method'];
            $note = trim($_POST['payment_note']);

            if ($amount > $total) {
                echo "<script>alert('Lỗi: Tiền cọc không được lớn hơn Tổng tiền tour!'); window.history.back();</script>";
                return;
            }

            $db = (new Database())->getConnection();
            $result = (new Booking($db))->updateDeposit($id, $amount, $method, $note);

            if ($result === "success") {
                header("Location: index.php?action=booking-list&msg=deposit_success");
            } else {
                echo "Lỗi: " . $result;
            }
        }
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $db = (new Database())->getConnection();
            (new Booking($db))->delete($id);
            header("Location: index.php?action=booking-list&msg=deleted");
        }
    }

    // --- HÀM XỬ LÝ CHÍNH ---
    private function processForm($mode) {
        $database = new Database();
        $db = $database->getConnection();
        $bookingModel = new Booking($db);

        // Lấy dữ liệu từ form (Các ô input này giờ đây được điền tự động từ JS)
        $data = [
            'tour_id'          => $_POST['tour_id'],
            'travel_date'      => $_POST['travel_date'],
            'return_date'      => $_POST['return_date'] ?? null,
            'customer_name'    => trim($_POST['customer_name']),
            'customer_id_card' => substr(trim($_POST['customer_id_card'] ?? ''), 0, 50),
            'customer_phone'   => trim($_POST['customer_phone']),
            'customer_email'   => trim($_POST['customer_email']),
            'adults'           => (int)$_POST['adults'],
            'children'         => (int)$_POST['children'],
            'total_price'      => $_POST['total_price'],
            'note'             => $_POST['note']
        ];

        // Validate
        if (empty($data['customer_name']) || empty($data['customer_phone'])) {
            echo "<script>alert('Vui lòng chọn khách hàng!'); window.history.back();</script>";
            return;
        }

        // Validate Ngày
        if (!empty($data['return_date']) && $data['return_date'] < $data['travel_date']) {
            echo "<script>alert('Ngày về không được nhỏ hơn ngày đi!'); window.history.back();</script>";
            return;
        }

        // [ĐÃ BỎ] Đoạn code tự động tạo khách hàng đã bị xóa tại đây để đảm bảo quy trình chặt chẽ.

        if ($mode == 'create') {
            $result = $bookingModel->create($data);
        } else {
            $id = $_POST['id'];
            $result = $bookingModel->update($id, $data);
        }

        if ($result === "success") {
            header("Location: index.php?action=booking-list&msg=" . ($mode == 'create' ? 'booking_success' : 'updated'));
        } else {
            echo "Lỗi hệ thống: " . $result;
        }
    }
}
?>