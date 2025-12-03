<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Tour.php';

class BookingController {

    // 1. Danh sách
    public function index() {
        $database = new Database();
        $db = $database->getConnection();
        $bookingModel = new Booking($db);
        
        $bookings = $bookingModel->getAll();
        
        // Xử lý dữ liệu null tránh lỗi View
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

    // 2. Form tạo
    public function create() {
        $database = new Database();
        $db = $database->getConnection();
        $tours = (new Tour($db))->getAll();
        require_once __DIR__ . '/../../views/booking/create.php';
    }

    // 3. Lưu tạo
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processForm('create');
        }
    }

    // 4. Form sửa
    public function edit() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $database = new Database();
            $db = $database->getConnection();
            $booking = (new Booking($db))->getById($id);
            $tours = (new Tour($db))->getAll();

            if (!$booking) { echo "Không tìm thấy đơn hàng!"; die(); }
            require_once __DIR__ . '/../../views/booking/edit.php';
        }
    }

    // 5. Lưu sửa
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processForm('update');
        }
    }

    // 6. Đổi trạng thái
    public function status() {
        $id = $_GET['id'] ?? null;
        $status = $_GET['status'] ?? null;
        if ($id && $status) {
            $db = (new Database())->getConnection();
            (new Booking($db))->updateStatus($id, $status);
            header("Location: index.php?action=booking-list&msg=status_updated");
        }
    }

    // 7. [MỚI] Xử lý Tiền cọc
    public function deposit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['booking_id'];
            $amount = $_POST['deposit_amount'];
            $total = $_POST['total_price_hidden'];

            // Validate: Tiền cọc không được lớn hơn tổng tiền
            if ($amount > $total) {
                echo "<script>alert('Lỗi: Tiền cọc không được lớn hơn Tổng tiền tour!'); window.history.back();</script>";
                return;
            }

            $db = (new Database())->getConnection();
            $result = (new Booking($db))->updateDeposit($id, $amount);

            if ($result === "success") {
                header("Location: index.php?action=booking-list&msg=deposit_success");
            } else {
                echo "Lỗi: " . $result;
            }
        }
    }

    // 8. Xóa
    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $db = (new Database())->getConnection();
            (new Booking($db))->delete($id);
            header("Location: index.php?action=booking-list&msg=deleted");
        }
    }

    // --- HÀM PHỤ TRỢ ---
    private function processForm($mode) {
        $database = new Database();
        $db = $database->getConnection();
        $bookingModel = new Booking($db);

        $data = [
            'tour_id'          => $_POST['tour_id'],
            'travel_date'      => $_POST['travel_date'],
            'customer_name'    => trim($_POST['customer_name']),
            'customer_id_card' => substr(trim($_POST['customer_id_card'] ?? ''), 0, 50),
            'customer_phone'   => trim($_POST['customer_phone']),
            'customer_email'   => trim($_POST['customer_email']),
            'adults'           => $_POST['adults'],
            'children'         => $_POST['children'],
            'total_price'      => $_POST['total_price'],
            'note'             => $_POST['note']
        ];

        // Validate cơ bản
        if (empty($data['customer_name']) || empty($data['customer_phone'])) {
            echo "<script>alert('Vui lòng nhập tên và số điện thoại!'); window.history.back();</script>";
            return;
        }

        // Validate SĐT
        if (!preg_match('/^[0-9]{9,11}$/', $data['customer_phone'])) {
            echo "<script>alert('Số điện thoại không hợp lệ!'); window.history.back();</script>";
            return;
        }

        if ($mode == 'create') {
            $result = $bookingModel->create($data);
        } else {
            $id = $_POST['id'];
            $result = $bookingModel->update($id, $data);
        }

        if ($result === "success") {
            $msg = ($mode == 'create') ? 'booking_success' : 'updated';
            header("Location: index.php?action=booking-list&msg=$msg");
        } else {
            echo "Lỗi hệ thống: " . $result;
        }
    }
}
?>