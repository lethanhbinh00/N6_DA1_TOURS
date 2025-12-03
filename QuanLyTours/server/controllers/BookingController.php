<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Tour.php';
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../models/Operation.php';
require_once __DIR__ . '/../models/Supplier.php';

class BookingController {

    // 1. Xem danh sách
    public function index() {
        $db = (new Database())->getConnection();
        $bookingModel = new Booking($db);
        
        $keyword  = $_GET['keyword'] ?? '';
        $status   = $_GET['status'] ?? '';
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo   = $_GET['date_to'] ?? '';

        if ((!empty($dateFrom) && empty($dateTo)) || (empty($dateFrom) && !empty($dateTo))) {
            echo "<script>alert('Vui lòng chọn đủ Từ ngày và Đến ngày!');</script>";
            $dateFrom = ''; $dateTo = '';
        }

        $bookings = $bookingModel->getAll($keyword, $status, $dateFrom, $dateTo);
        
        // Xử lý null
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

    // 2. Form Tạo
    public function create() {
        $db = (new Database())->getConnection();
        $tours = (new Tour($db))->getAll();
        $customers = (new Customer($db))->getAll();
        require_once __DIR__ . '/../../views/booking/create.php';
    }

    // 3. Lưu Tạo
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processForm('create');
        }
    }

    // 4. Form Sửa
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

    // 5. Lưu Sửa
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processForm('update');
        }
    }

    // 6. Chi tiết
    public function detail() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $db = (new Database())->getConnection();
            $booking = (new Booking($db))->getById($id);
            $tour = (new Tour($db))->getById($booking['tour_id']);
            if (!$booking) { echo "Không tìm thấy!"; die(); }
            require_once __DIR__ . '/../../views/booking/detail.php';
        }
    }

    // 7. In Hóa đơn
    public function invoice() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $db = (new Database())->getConnection();
            $booking = (new Booking($db))->getById($id);
            $tour = (new Tour($db))->getById($booking['tour_id']);
            if (!$booking) { echo "Không tìm thấy!"; die(); }
            require_once __DIR__ . '/../../views/booking/invoice.php';
        }
    }
    
    // 8. In Hợp đồng
    public function contract() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $db = (new Database())->getConnection();
            $booking = (new Booking($db))->getById($id);
            $tour = (new Tour($db))->getById($booking['tour_id']);
            // Lấy khách để điền thông tin
            $cusModel = new Customer($db);
            // (Tạm thời dùng data booking, nâng cao sẽ lấy full info khách)
            
            if (!$booking) { echo "Không tìm thấy!"; die(); }
            require_once __DIR__ . '/../../views/booking/contract.php';
        }
    }

    // 9. Trạng thái
    public function status() {
        $id = $_GET['id'] ?? null; $status = $_GET['status'] ?? null;
        if ($id && $status) {
            $db = (new Database())->getConnection();
            (new Booking($db))->updateStatus($id, $status);
            header("Location: index.php?action=booking-list&msg=status_updated");
        }
    }

    // 10. Thu cọc
    public function deposit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['booking_id'];
            $amount = (float)$_POST['deposit_amount'];
            $total = (float)$_POST['total_price_hidden'];
            $method = $_POST['payment_method'];
            $note = trim($_POST['payment_note']);

            $db = (new Database())->getConnection();
            $bookingModel = new Booking($db);
            $currentBooking = $bookingModel->getById($id);
            $newTotalDeposit = (float)$currentBooking['deposit_amount'] + $amount;

            if ($newTotalDeposit > $total) {
                echo "<script>alert('Tiền đóng vượt quá giá tour!'); window.history.back();</script>";
                return;
            }

            $newStatus = ($newTotalDeposit >= $total) ? 'completed' : 'deposited';
            $historyNote = $currentBooking['payment_note'] . " | " . date('d/m') . ": +" . number_format($amount) . " ($method) $note";
            
            $bookingModel->updateDeposit($id, $newTotalDeposit, $method, $historyNote, $newStatus);
            header("Location: index.php?action=booking-list&msg=deposit_success");
        }
    }

    // 11. Xóa
    public function delete() {
        $id = $_GET['id']; $db = (new Database())->getConnection();
        (new Booking($db))->delete($id);
        header("Location: index.php?action=booking-list&msg=deleted");
    }

    // 12. Trang Điều hành
    public function operations() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $db = (new Database())->getConnection();
            $booking = (new Booking($db))->getById($id);
            $tour = (new Tour($db))->getById($booking['tour_id']);
            
            $opModel = new Operation($db);
            $paxList = $opModel->getPaxByBooking($id);
            $services = $opModel->getServicesByBooking($id);
            $suppliers = (new Supplier($db))->getAll();

            require_once __DIR__ . '/../../views/booking/operations.php';
        }
    }

    // 13. Thêm Pax
    // --- [ĐÃ SỬA] THÊM PAX (CÓ KIỂM TRA NGÀY SINH) ---
    public function paxStore() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $booking_id = $_POST['booking_id'];
            $dob = $_POST['dob'];

            // 1. Kiểm tra ngày sinh hợp lệ
            if (!empty($dob)) {
                $year = date('Y', strtotime($dob));
                // Nếu năm > 2100 hoặc < 1900 thì báo lỗi ngay
                if ($year > 2100 || $year < 1900) {
                    echo "<script>alert('Lỗi: Năm sinh không hợp lệ ($year)! Vui lòng kiểm tra lại.'); window.history.back();</script>";
                    return; // Dừng lại, không lưu
                }
            }

            // 2. Nếu ổn thì lưu
            $db = (new Database())->getConnection();
            (new Operation($db))->addPax([
                ':bid'  => $booking_id,
                ':name' => $_POST['full_name'],
                ':gen'  => $_POST['gender'],
                ':dob'  => !empty($dob) ? $dob : null,
                ':note' => $_POST['note']
            ]);
            
            header("Location: index.php?action=booking-ops&id=" . $booking_id);
        }
    }

    // 14. Xóa Pax
    public function paxDelete() {
        $id = $_GET['id']; $bid = $_GET['bid'];
        $db = (new Database())->getConnection();
        (new Operation($db))->deletePax($id);
        header("Location: index.php?action=booking-ops&id=".$bid);
    }

    // 15. Thêm Dịch vụ
    public function serviceStore() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = (new Database())->getConnection();
            (new Operation($db))->addService([
                ':bid' => $_POST['booking_id'],
                ':sup' => $_POST['supplier_id'],
                ':type' => $_POST['service_type'],
                ':desc' => $_POST['description'],
                ':cost' => $_POST['cost']
            ]);
            header("Location: index.php?action=booking-ops&id=".$_POST['booking_id']);
        }
    }

    // 16. Xóa Dịch vụ
    public function serviceDelete() {
        $id = $_GET['id']; $bid = $_GET['bid'];
        $db = (new Database())->getConnection();
        (new Operation($db))->deleteService($id);
        header("Location: index.php?action=booking-ops&id=".$bid);
    }

    // --- HÀM PHỤ TRỢ ---
    private function processForm($mode) {
        $db = (new Database())->getConnection();
        $bookingModel = new Booking($db);
        
        $data = [
            'tour_id' => $_POST['tour_id'], 
            'travel_date' => $_POST['travel_date'],
            'return_date' => $_POST['return_date'] ?? null,
            'customer_name' => trim($_POST['customer_name']),
            'customer_id_card' => substr(trim($_POST['customer_id_card'] ?? ''), 0, 50),
            'customer_phone' => trim($_POST['customer_phone']),
            'customer_email' => trim($_POST['customer_email']),
            'adults' => (int)$_POST['adults'], 
            'children' => (int)$_POST['children'],
            'total_price' => $_POST['total_price'], 
            'note' => $_POST['note']
        ];

        if(empty($data['customer_name']) || empty($data['customer_phone'])) {
            echo "<script>alert('Vui lòng nhập thông tin!'); window.history.back();</script>";
            return;
        }

        if ($mode == 'create') $result = $bookingModel->create($data);
        else { $id = $_POST['id']; $result = $bookingModel->update($id, $data); }

        if ($result === "success") header("Location: index.php?action=booking-list&msg=".($mode=='create'?'booking_success':'updated'));
        else echo "Lỗi: " . $result;
    }
}
?>