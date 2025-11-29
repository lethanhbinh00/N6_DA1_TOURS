<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Tour.php';

class BookingController {

    public function index() {
        $db = (new Database())->getConnection();
        $bookings = (new Booking($db))->getAll();
        
        // Xử lý dữ liệu null
        if (!empty($bookings)) {
            foreach ($bookings as $key => $value) {
                $bookings[$key]['customer_id_card'] = $value['customer_id_card'] ?? ''; 
                $bookings[$key]['customer_phone']   = $value['customer_phone'] ?? '';   
                $bookings[$key]['tour_code']        = $value['tour_code'] ?? 'N/A';     
                $bookings[$key]['tour_name']        = $value['tour_name'] ?? 'Tour đã xóa';
                $bookings[$key]['customer_name']    = $value['customer_name'] ?? 'Khách lẻ';
            }
        }
        require_once __DIR__ . '/../../views/booking/index.php';
    }

    public function create() {
        $db = (new Database())->getConnection();
        $tours = (new Tour($db))->getAll();
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

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $db = (new Database())->getConnection();
            (new Booking($db))->delete($id);
            header("Location: index.php?action=booking-list&msg=deleted");
        }
    }

    // --- HÀM XỬ LÝ DỮ LIỆU & VALIDATE (ĐÃ SỬA ĐỂ HIỆN LỖI DƯỚI INPUT) ---
    private function processForm($mode) {
        $database = new Database();
        $db = $database->getConnection();
        $bookingModel = new Booking($db);

        // 1. Lấy dữ liệu
        $data = [
            'tour_id'          => $_POST['tour_id'],
            'travel_date'      => $_POST['travel_date'],
            'customer_name'    => trim($_POST['customer_name']),
            'customer_id_card' => trim($_POST['customer_id_card'] ?? ''),
            'customer_phone'   => trim($_POST['customer_phone']),
            'customer_email'   => trim($_POST['customer_email']),
            'adults'           => $_POST['adults'],
            'children'         => $_POST['children'],
            'total_price'      => $_POST['total_price'],
            'note'             => $_POST['note']
        ];

        // 2. VALIDATE (Tạo mảng chứa lỗi)
        $errors = [];

        // Validate Tên
        if (empty($data['customer_name'])) {
            $errors['customer_name'] = "Vui lòng nhập họ tên khách hàng.";
        }

        // Validate SĐT
        if (empty($data['customer_phone'])) {
            $errors['customer_phone'] = "Vui lòng nhập số điện thoại.";
        } elseif (!preg_match('/^[0-9]{9,11}$/', $data['customer_phone'])) {
            $errors['customer_phone'] = "Số điện thoại không hợp lệ (9-11 số).";
        }

        // Validate CCCD (Nếu có nhập thì phải đúng)
        if (!empty($data['customer_id_card'])) {
            if (!preg_match('/^[0-9]{9,12}$/', $data['customer_id_card'])) {
                // Gán lỗi vào key 'customer_id_card'
                $errors['customer_id_card'] = "CCCD không hợp lệ (Chỉ nhập số, 9-12 ký tự).";
            }
        }

        // 3. NẾU CÓ LỖI -> TRẢ VỀ FORM CŨ KÈM THÔNG BÁO LỖI
        if (!empty($errors)) {
            // Lấy lại danh sách tour để đổ vào dropdown
            $tours = (new Tour($db))->getAll();
            
            // Biến $oldData giữ lại những gì người dùng vừa nhập
            $oldData = $data; 
            
            // Gọi lại file View tương ứng
            if ($mode == 'create') {
                require_once __DIR__ . '/../../views/booking/create.php';
            } else {
                // Nếu là edit thì cần thêm ID
                $booking = $data; // Gán data vừa nhập vào biến booking để view edit hiển thị lại
                $booking['id'] = $_POST['id']; 
                require_once __DIR__ . '/../../views/booking/edit.php';
            }
            return; // Dừng chạy code, không lưu vào DB
        }

        // 4. NẾU KHÔNG CÓ LỖI -> LƯU VÀO DB
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