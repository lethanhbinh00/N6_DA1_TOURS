<?php
// [CẦN CÓ] Khai báo tất cả các Models
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Tour.php';
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../models/Operation.php';
require_once __DIR__ . '/../models/Supplier.php';
require_once __DIR__ . '/../models/SystemLog.php';

class BookingController {

    // 1. Danh sách (Hàm Index)
    public function index() {
        $db = (new Database())->getConnection();
        $bookingModel = new Booking($db);
        
        $keyword  = $_GET['keyword'] ?? ''; $status   = $_GET['status'] ?? '';
        $dateFrom = $_GET['date_from'] ?? ''; $dateTo   = $_GET['date_to'] ?? '';

        if ((!empty($dateFrom) && empty($dateTo)) || (empty($dateFrom) && !empty($dateTo))) {
             echo "<script>alert('Vui lòng chọn đầy đủ TỪ NGÀY và ĐẾN NGÀY để lọc chính xác!');</script>";
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
                $bookings[$key]['min_deposit']      = $value['min_deposit'] ?? 30; // Min deposit cho modal
            }
        }
        require_once __DIR__ . '/../../views/booking/index.php';
    }

    // 2. Form Tạo
    public function create() {
        $db = (new Database())->getConnection();
        $tours = (new Tour($db))->getAll();
        $customers = (new Customer($db))->getAll();
        $suppliers = (new Supplier($db))->getAll();
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
            $customers = (new Customer($db))->getAll();
            $suppliers = (new Supplier($db))->getAll();
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

    // 6. Xem chi tiết
    public function detail() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $db = (new Database())->getConnection();
            $booking = (new Booking($db))->getById($id);
            $tour = (new Tour($db))->getById($booking['tour_id']);
            $opModel = new Operation($db);
            $paxList = $opModel->getPaxByBooking($id);
            $services = $opModel->getServicesByBooking($id);
            $payments = (new Booking($db))->getPaymentsByBooking($id); 
            $itineraries = (new Tour($db))->getItinerary($booking['tour_id']);

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
            if (!$booking) { echo "Không tìm thấy đơn hàng!"; die(); }
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
            if (!$booking) { echo "Không tìm thấy đơn hàng!"; die(); }
            require_once __DIR__ . '/../../views/booking/contract.php';
        }
    }

    // 9. Đổi trạng thái
    public function status() {
        $id = $_GET['id'] ?? null; $status = $_GET['status'] ?? null;
        if ($id && $status) {
            $db = (new Database())->getConnection();
            $bookingModel = new Booking($db);
            $oldBooking = $bookingModel->getById($id);

            $bookingModel->updateStatus($id, $status);

            $logModel = new SystemLog($db);
            $description = "Đã thay đổi trạng thái Booking #{$oldBooking['booking_code']} từ '{$oldBooking['status']}' sang '{$status}'.";
            $logModel->logAction('STATUS_CHANGE', 'Booking', $id, $description);

            header("Location: index.php?action=booking-list&msg=status_updated");
        }
    }

    // 10. Thu Tiền Cọc
    public function deposit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['booking_id'];
            $amount = (float)$_POST['deposit_amount'];
            $total = (float)$_POST['total_price_hidden'];
            $method = $_POST['payment_method'];
            $note = trim($_POST['payment_note']);
            $minPercent = (int)($_POST['min_deposit_hidden'] ?? 0); 
            $userId = $_SESSION['user_id'] ?? 1;

            $db = (new Database())->getConnection();
            $bookingModel = new Booking($db);
            $currentBooking = $bookingModel->getById($id);

            $currentPaid = (float)$currentBooking['deposit_amount'];
            $newTotalDeposit = $currentPaid + $amount;
            $minAmount = ($total * $minPercent) / 100;
            
            if ($currentPaid == 0 && $amount < $minAmount) {
                echo "<script>alert('Lỗi: Lần đầu phải đóng tối thiểu " . number_format($minAmount) . "đ ($minPercent%)!'); window.history.back();</script>";
                return;
            }

            if ($newTotalDeposit > $total) { echo "<script>alert('Lỗi: Tiền đóng vượt quá giá tour!'); window.history.back();</script>"; return; }

            $result = $bookingModel->addPayment($id, $amount, $method, $note, $userId);

            if ($result === "success") {
                $logModel = new SystemLog($db);
                $description = "Thu tiền: +" . number_format($amount) . " VNĐ. PT: {$method}. Tổng tiền đã đóng: " . number_format($newTotalDeposit) . " VNĐ.";
                $logModel->logAction('PAYMENT', 'Booking', $id, $description);

                header("Location: index.php?action=booking-list&msg=deposit_success");
            } else {
                echo "Lỗi: " . $result;
            }
        }
    }

    // 11. Xóa
    public function delete() {
        if (($_SESSION['user_role'] ?? 'guest') !== 'admin') { 
            echo "<script>alert('BẠN KHÔNG CÓ QUYỀN XÓA ĐƠN HÀNG NÀY!'); window.history.back();</script>";
            return; 
        }

        $id = $_GET['id'] ?? null;
        if ($id) {
            $db = (new Database())->getConnection();
            $bookingModel = new Booking($db);
            $oldBooking = $bookingModel->getById($id);
            
            if ($oldBooking) {
                $isDeleted = $bookingModel->delete($id);
                if ($isDeleted) {
                    $logModel = new SystemLog($db);
                    $description = "Đã xóa vĩnh viễn Booking: #{$oldBooking['booking_code']} (Tổng tiền: {$oldBooking['total_price']}).";
                    $logModel->logAction('DELETE', 'Booking', $id, $description);

                    header("Location: index.php?action=booking-list&msg=deleted");
                    return;
                }
            }
        }
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
    public function paxStore() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = (new Database())->getConnection();
            $bookingId = $_POST['booking_id'];
            $booking = (new Booking($db))->getById($bookingId);

            if (in_array($booking['status'], ['deposited', 'completed', 'cancelled'])) {
                header("Location: index.php?action=booking-list&msg=locked");
                return;
            }
            
            (new Operation($db))->addPax([
                ':bid'  => $bookingId, ':name' => $_POST['full_name'],
                ':gen'  => $_POST['gender'], ':dob' => !empty($_POST['dob']) ? $_POST['dob'] : null,
                ':note' => $_POST['note']
            ]);
            
            $logModel = new SystemLog($db);
            $logModel->logAction('CREATE', 'Pax', $bookingId, "Thêm khách: {$_POST['full_name']} vào Booking #{$booking['booking_code']}");

            header("Location: index.php?action=booking-ops&id=" . $bookingId);
        }
    }
    
    // 14. Xóa Pax
    public function paxDelete() {
        $id = $_GET['id']; $bid = $_GET['bid'];
        $db = (new Database())->getConnection();
        $booking = (new Booking($db))->getById($bid);

        if (in_array($booking['status'], ['deposited', 'completed', 'cancelled'])) {
            header("Location: index.php?action=booking-list&msg=locked");
            return;
        }

        (new Operation($db))->deletePax($id);
        
        $logModel = new SystemLog($db);
        $logModel->logAction('DELETE', 'Pax', $bid, "Xóa khách ID #{$id} khỏi Booking #{$booking['booking_code']}");

        header("Location: index.php?action=booking-ops&id=" . $bid);
    }
    
    // 15. Thêm Dịch vụ
    public function serviceStore() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = (new Database())->getConnection();
            (new Operation($db))->addService([
                ':bid'  => $_POST['booking_id'],
                ':sup'  => $_POST['supplier_id'],
                ':type' => $_POST['service_type'],
                ':desc' => $_POST['description'],
                ':cost' => $_POST['cost']
            ]);
            header("Location: index.php?action=booking-ops&id=" . $_POST['booking_id']);
        }
    }

    // 16. Xóa Dịch vụ
    public function serviceDelete() {
        $id = $_GET['id']; $bid = $_GET['bid'];
        $db = (new Database())->getConnection();
        (new Operation($db))->deleteService($id);
        header("Location: index.php?action=booking-ops&id=" . $bid);
    }

    // 17. Xóa giao dịch thanh toán
    public function paymentDelete() {
        if (($_SESSION['user_role'] ?? 'guest') !== 'admin') { 
            echo "<script>alert('BẠN KHÔNG CÓ QUYỀN XÓA GIAO DỊCH NÀY!'); window.history.back();</script>";
            return; 
        }
        $id = $_GET['id']; $bid = $_GET['bid'];
        $db = (new Database())->getConnection();
        $result = (new Booking($db))->deletePayment($id);
        
        if ($result === "success") {
            header("Location: index.php?action=booking-detail&id=$bid&msg=payment_deleted");
        } else {
            echo "<script>alert('Lỗi: $result'); window.history.back();</script>";
        }
    }
    
    // 18. In Phiếu thu lẻ
    public function receipt() {
        $id = $_GET['id']; $db = (new Database())->getConnection();
        $payment = (new Booking($db))->getPaymentById($id);
        if (!$payment) { echo "Không tìm thấy phiếu!"; die(); }
        require_once __DIR__ . '/../../views/booking/receipt.php';
    }


    // 19. Hàm phụ trợ xử lý Create/Update (QUAN TRỌNG NHẤT)
    private function processForm($mode) {
        $db = (new Database())->getConnection(); $bookingModel = new Booking($db);
        
        // [FIX LỖI SQL] Chuyển chuỗi rỗng ('') thành NULL cho các cột INT
        // Nếu giá trị là '' (chuỗi rỗng), ta ép nó thành NULL
        $transportId = ($_POST['transport_supplier_id'] === '') ? null : $_POST['transport_supplier_id'];
        $hotelId     = ($_POST['hotel_supplier_id'] === '') ? null : $_POST['hotel_supplier_id'];
        $customerId  = $_POST['customer_id'];

        $data = [
            'tour_id' => $_POST['tour_id'], 
            'transport_id' => $transportId,  // FIX APPLIED
            'hotel_id' => $hotelId,      // FIX APPLIED
            'pickup_location' => trim($_POST['pickup_location']),
            'travel_date' => $_POST['travel_date'], 
            'return_date' => $_POST['return_date'] ?? null, 
            'customer_id' => $customerId,
            // Thêm các trường Snapshot (tên khách, sđt) vào data dù không dùng trong model create/update
            // để đảm bảo array structure cho logic phức tạp hơn
            'adults' => (int)$_POST['adults'], 
            'children' => (int)$_POST['children'], 
            'total_price' => $_POST['total_price'], 
            'note' => $_POST['note']
        ];

        if (empty($data['customer_id'])) { echo "<script>alert('Vui lòng chọn khách hàng!'); window.history.back();</script>"; return; }
        
        if ($mode == 'create') {
            $newId = $bookingModel->create($data); 
            if (is_numeric($newId)) {
                $logModel = new SystemLog($db);
                $logModel->logAction('CREATE', 'Booking', $newId, "Đã tạo mới Booking #{$newId}.");

                 header("Location: index.php?action=booking-ops&id=$newId&msg=booking_success");
            } else {
                 echo "Lỗi tạo mới: " . $newId;
            }
        } else { 
            $id = $_POST['id']; $result = $bookingModel->update($id, $data);
            if ($result === "success") {
                $logModel = new SystemLog($db);
                $logModel->logAction('UPDATE', 'Booking', $id, "Đã cập nhật thông tin Booking ID #{$id}.");
                header("Location: index.php?action=booking-list&msg=updated");
            }
            else echo "Lỗi cập nhật: " . $result;
        }
    }
}
?>