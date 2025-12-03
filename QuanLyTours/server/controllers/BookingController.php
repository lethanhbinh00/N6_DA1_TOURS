<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Tour.php';
require_once __DIR__ . '/../models/Customer.php';

class BookingController {

    // 1. Danh sách
    public function index() {
        $db = (new Database())->getConnection();
        $bookingModel = new Booking($db);
        $bookings = $bookingModel->getAll();
        
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
        $db = (new Database())->getConnection();
        $tours = (new Tour($db))->getAll();
        $customers = (new Customer($db))->getAll();
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
            $db = (new Database())->getConnection();
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

    // 7. [ĐÃ SỬA] Xử lý Thu Tiền Cọc (Logic Cộng Dồn)
    public function deposit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['booking_id'];
            $addedAmount = (float)$_POST['deposit_amount']; // Tiền nạp thêm đợt này
            $totalPrice = (float)$_POST['total_price_hidden'];
            $method = $_POST['payment_method'];
            $note = trim($_POST['payment_note']);

            $db = (new Database())->getConnection();
            $bookingModel = new Booking($db);

            // A. Lấy thông tin cũ để biết đã cọc bao nhiêu rồi
            $currentBooking = $bookingModel->getById($id);
            $currentDeposit = (float)$currentBooking['deposit_amount'];

            // B. Cộng dồn tiền
            $newTotalDeposit = $currentDeposit + $addedAmount;

            // C. Validate dư nợ
            if ($newTotalDeposit > $totalPrice) {
                echo "<script>alert('Lỗi: Tổng tiền đóng ($newTotalDeposit) lớn hơn giá tour ($totalPrice)!'); window.history.back();</script>";
                return;
            }

            // D. Tự động xác định trạng thái
            // Nếu đã đóng đủ (hoặc chênh lệch cực nhỏ) -> Hoàn tất
            $newStatus = ($newTotalDeposit >= $totalPrice) ? 'completed' : 'deposited';

            // E. Nối chuỗi ghi chú lịch sử
            $historyNote = $currentBooking['payment_note'] . " | " . date('d/m') . ": +" . number_format($addedAmount) . " ($method)";
            if(!empty($note)) $historyNote .= " - $note";

            // F. Gọi Model cập nhật
            $result = $bookingModel->updateDeposit($id, $newTotalDeposit, $method, $historyNote, $newStatus);

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

    // Hàm phụ trợ xử lý form
    private function processForm($mode) {
        $database = new Database();
        $db = $database->getConnection();
        $bookingModel = new Booking($db);

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

        if (empty($data['customer_name']) || empty($data['customer_phone'])) {
            echo "<script>alert('Vui lòng chọn khách hàng!'); window.history.back();</script>";
            return;
        }

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