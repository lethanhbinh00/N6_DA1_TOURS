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
            // [CẦN THIẾT] Fetch danh sách Suppliers
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
    // server/controllers/BookingController.php

public function operations() {
    // 1. Lấy ID booking từ URL
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    if (!$id) {
        header("Location: index.php?action=booking-list");
        exit();
    }

    $db = (new Database())->getConnection();
    
    // 2. Khởi tạo các Model cần thiết
    $opModel = new Operation($db);
    $userModel = new User($db); // Khởi tạo model User để lấy danh sách nhân sự

    // 3. Truy vấn dữ liệu từ Database
    // Nếu bạn đã có BookingModel
$bookingModel = new Booking($db);
$booking = $bookingModel->getBookingById($id);
    $tour = $opModel->getTourById($booking['tour_id']);
    $paxList = $opModel->getPaxByBooking($id);
    $services = $opModel->getServicesByBooking($id);
    $suppliers = $opModel->getSuppliers();
    $tourLogs = $opModel->getTourLogs($id);
    
    // ĐÂY LÀ DÒNG QUAN TRỌNG NHẤT:
    // Biến $users này sẽ được file operations.php sử dụng để chạy vòng lặp foreach trong Modal
    $users = $userModel->getAllUsers(); 

    // 4. Load giao diện
// Sử dụng đường dẫn chuẩn để tránh lỗi trên Windows/Laragon
$viewPath = __DIR__ . '/../../views/booking/operations.php';

if (file_exists($viewPath)) {
    require_once $viewPath;
} else {
    die("Lỗi: Không tìm thấy file giao diện tại: " . $viewPath);
}}
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
    // server/controllers/BookingController.php
public function serviceStore() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $db = (new Database())->getConnection();
        $opModel = new Operation($db);
        
        $data = [
            ':bid'  => $_POST['booking_id'],
            ':sup'  => $_POST['supplier_id'],
            ':type' => $_POST['service_type'],
            ':desc' => $_POST['description'],
            ':cost' => $_POST['cost']
        ];
        
        $opModel->addService($data);
        
        // Quay lại trang điều hành
        header("Location: index.php?action=booking-ops&id=" . $_POST['booking_id']);
        exit();
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
        
        $paymentInfo = (new Booking($db))->getPaymentById($id); 
        $result = (new Booking($db))->deletePayment($id);
        
        if ($result === "success") {
            $logModel = new SystemLog($db);
            $description = "Đã hủy giao dịch thu tiền: -".number_format($paymentInfo['amount'] ?? 0). " VNĐ. Mã BK: {$paymentInfo['booking_code']}";
            $logModel->logAction('DELETE', 'Payment', $paymentInfo['id'] ?? 0, $description);

            // [REDIRECT VỀ TRANG CHI TIẾT]
            header("Location: index.php?action=booking-detail&id=$bid&msg=payment_deleted");
        } else {
            echo "<script>alert('Lỗi: $result'); window.history.back();</script>";
        }
    }
    
    // 18. In Phiếu thu lẻ
    public function receipt() {
        $id = $_GET['id']; $db = (new Database())->getConnection();
        // Cần đảm bảo hàm getPaymentById trong Model Booking lấy đủ thông tin
        $payment = (new Booking($db))->getPaymentById($id);
        if (!$payment) { echo "Không tìm thấy phiếu!"; die(); }
        require_once __DIR__ . '/../../views/booking/receipt.php';
    }



    // 19. Hàm phụ trợ xử lý Create/Update (QUAN TRỌNG NHẤT)
    private function processForm($mode) {
    $db = (new Database())->getConnection();
    $bookingModel = new Booking($db);
    
    // 1. Thu thập và chuẩn hóa dữ liệu từ $_POST
    // Chuyển chuỗi rỗng thành NULL cho các ID để tránh lỗi ràng buộc Database (Foreign Key)
    $data = [
        'tour_id'           => $_POST['tour_id'], 
        'customer_id'       => $_POST['customer_id'],
        'customer_name'     => $_POST['customer_name'] ?? '',
        'customer_id_card'  => $_POST['customer_id_card'] ?? '',
        'customer_phone'    => $_POST['customer_phone'] ?? '',
        'customer_email'    => $_POST['customer_email'] ?? '',
        
        // Khớp tên cột với logic xử lý trong Model Booking.php
        'transport_id'      => empty($_POST['transport_supplier_id']) ? null : $_POST['transport_supplier_id'],  
        'hotel_id'          => empty($_POST['hotel_supplier_id']) ? null : $_POST['hotel_supplier_id'],
        
        'pickup_location'   => trim($_POST['pickup_location'] ?? ''),
        'flight_number'     => $_POST['flight_number'] ?? '',
        'room_details'      => $_POST['room_details'] ?? '',
        'guide_id'          => empty($_POST['guide_id']) ? null : $_POST['guide_id'],
        
        'travel_date'       => $_POST['travel_date'], 
        'return_date'       => $_POST['return_date'] ?? null, 
        'adults'            => (int)$_POST['adults'], 
        'children'          => (int)$_POST['children'], 
        'total_price'       => $_POST['total_price'], 
        'note'              => $_POST['note']
    ];

    // 2. Kiểm tra dữ liệu bắt buộc
    if (empty($data['customer_id'])) { 
        echo "<script>alert('Vui lòng chọn khách hàng!'); window.history.back();</script>"; 
        return; 
    }

    // 3. Thực thi lưu dữ liệu (Đây là phần code bạn bị thiếu trước đó)
    if ($mode == 'create') {
        // Gọi lệnh tạo mới và kiểm tra kết quả trả về
        $result = $bookingModel->create($data);
        if ($result) {
            // Chuyển hướng ngay lập tức để tránh reload gây duplicate dữ liệu hoặc màn hình trắng
            header("Location: index.php?action=booking-list&msg=success");
            exit(); 
        } else {
            die("Lỗi: Hệ thống không thể tạo Booking. Vui lòng kiểm tra lại thông tin tour hoặc khách hàng.");
        }
    } else {
        // Xử lý cập nhật cho trường hợp Edit
        $id = $_POST['id'];
        $result = $bookingModel->update($id, $data);
        header("Location: index.php?action=booking-list&msg=updated");
        exit();
    }
}
    
    // 23. [MỚI] Phân công HDV (Assign Guide)
    public function assignGuide() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = (new Database())->getConnection();
            $bookingModel = new Booking($db);
            
            $bid = $_POST['booking_id'];
            // [FIX CORE]: Đảm bảo nhận NULL nếu người dùng chọn '-- Bỏ phân công --'
            $gid = empty($_POST['guide_id']) ? null : $_POST['guide_id']; 

            $result = $bookingModel->assignGuide($bid, $gid);
            
            // Ghi Log và Redirect
            $logModel = new SystemLog($db);
            $logModel->logAction('UPDATE', 'Booking', $bid, "Phân công HDV (ID: $gid) cho Booking #$bid.");

            header("Location: index.php?action=booking-ops&id=$bid&msg=guide_assigned");
        }
    }

   public function updateSupplierPayment() {
    // Sử dụng $_REQUEST để nhận cả GET (từ link) hoặc POST (từ form)
    if (isset($_REQUEST['service_id']) && isset($_REQUEST['booking_id'])) {
        $db = (new Database())->getConnection();
        $opModel = new Operation($db);

        $bid = $_REQUEST['booking_id'];
        $sid = $_REQUEST['service_id'];
        $status = $_REQUEST['status'] ?? 'paid';

        // Thực hiện cập nhật vào Database
        $opModel->updateSupplierPaymentStatus($sid, $status);

        // Lưu nhật ký hệ thống nếu bạn có class SystemLog
        if (class_exists('SystemLog')) {
            $logModel = new SystemLog($db);
            $logModel->logAction('PAY', 'Supplier', $sid, "Xác nhận thanh toán dịch vụ ID: $sid cho Booking #$bid");
        }

        // Chuyển hướng về trang điều hành kèm thông báo thành công
        header("Location: index.php?action=booking-ops&id=$bid&msg=supplier_paid");
        exit();
    }
}

    public function profitabilityReport() {
        $db = (new Database())->getConnection();
        $bookingModel = new Booking($db);
        
        $dateFrom = $_GET['date_from'] ?? null;
        $dateTo = $_GET['date_to'] ?? null;

        $reports = $bookingModel->getFinancialSummary($dateFrom, $dateTo);

        // Tính lợi nhuận (Profit) trong Controller
        if (!empty($reports)) {
            foreach ($reports as $key => $report) {
                $reports[$key]['profit'] = $report['total_revenue'] - $report['total_cost'];
            }
        }
        
        // Cần có view mới cho báo cáo
        require_once __DIR__ . '/../../views/reports/profitability.php';
    }
    // Thêm vào cuối file server/controllers/BookingController.php

// 25. [MỚI] Trang Công nợ (Payables Index)
public function payablesIndex() {
    $db = (new Database())->getConnection();
    $bookingModel = new Booking($db);
    
    // TẠM THỜI: Tái sử dụng hàm fetch báo cáo tài chính để có dữ liệu booking
    $reports = $bookingModel->getFinancialSummary(); 

    // Yêu cầu view mới để hiển thị công nợ
    require_once __DIR__ . '/../../views/reports/payables.php';
}

public function updatePaxStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_REQUEST['pax_id'])) {
            $bid = $_REQUEST['booking_id'];
            $pid = $_REQUEST['pax_id'];
            $status = $_REQUEST['status'];

            $db = (new Database())->getConnection();
            $opModel = new Operation($db);
            
            $opModel->updatePaxCheckinStatus($pid, $status);
            header("Location: index.php?action=booking-ops&id=$bid&msg=pax_status_updated");
        }
    }

    // 21. [MỚI] Cập nhật Ghi chú Yêu cầu đặc biệt của PAX
    public function updatePaxNote() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bid = $_POST['booking_id'];
            $pid = $_POST['pax_id'];
            $note = $_POST['special_requests'];

            $db = (new Database())->getConnection();
            $opModel = new Operation($db);
            
            $opModel->updatePaxSpecialRequest($pid, $note);
            header("Location: index.php?action=booking-ops&id=$bid&msg=pax_note_updated");
        }
    }

    // 22. [MỚI] Lưu Nhật ký Tour
    // BookingController.php

// 1. Hiển thị Form
public function showSupplierPaymentForm() {
    $sid = $_GET['service_id'];
    $bid = $_GET['booking_id'];
    
    $db = (new Database())->getConnection();
    $bookingModel = new Booking($db);
    $opModel = new Operation($db);

    $booking = $bookingModel->getById($bid);
    $service = $opModel->getServiceById($sid); // Bạn cần viết hàm này trong Model Operation

    require_once __DIR__ . '/../../views/financial/supplier_payment_form.php';
}

// 2. Lưu dữ liệu giao dịch
public function storeSupplierPayment() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $db = (new Database())->getConnection();
        $opModel = new Operation($db);

        $sid = $_POST['service_id'];
        $bid = $_POST['booking_id'];
        $amount = $_POST['amount'];
        $status = 'paid';

        // Cập nhật trạng thái 'paid' cho dịch vụ
        $opModel->updateSupplierPaymentStatus($sid, $status);

        // (Tùy chọn) Lưu vào bảng lịch sử giao dịch (Payment Ledger)
        // $opModel->saveSupplierTransaction($sid, $bid, $amount, $_POST['payment_method'], $_POST['note']);

        header("Location: index.php?action=booking-ops&id=$bid&msg=supplier_paid");
        exit();
    }
}

public function paxImport() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pax_file'])) {
        $bookingId = $_POST['booking_id'];
        $file = $_FILES['pax_file']['tmp_name'];

        try {
            $db = (new Database())->getConnection();
            $opModel = new Operation($db);

            // Mở file với chế độ đọc nhị phân để xử lý BOM
            $handle = fopen($file, "r");
            if ($handle !== FALSE) {
                // Kiểm tra và bỏ qua ký tự BOM của UTF-8 (đặc thù của Google Sheets/Excel UTF-8)
                $bom = fread($handle, 3);
                if ($bom != "\xEF\xBB\xBF") {
                    rewind($handle); // Quay lại đầu file nếu không có BOM
                }

                // Bỏ qua dòng tiêu đề
                fgetcsv($handle, 1000, ",");

                $importCount = 0;
                while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if (empty($row[0])) continue;

                    // KHÔNG dùng mb_convert_encoding nữa vì file từ Google Sheets đã là UTF-8 chuẩn
                    // Chỉ làm sạch khoảng trắng
                    $row = array_map('trim', $row);

                    // Cắt chuỗi để đảm bảo không vượt quá 100 ký tự của Database
                    $fullName = mb_substr($row[0], 0, 100, 'UTF-8'); 

                    $paxData = [
                        ':bid'  => $bookingId,
                        ':name' => $fullName,
                        // Chuyển đổi ngày tháng d/m/Y sang Y-m-d để khớp với kiểu date của DB
                        ':dob'  => !empty($row[1]) ? date('Y-m-d', strtotime(str_replace('/', '-', $row[1]))) : null,
                        ':gen'  => trim($row[2] ?? 'Nam'),
                        ':note' => mb_substr(trim($row[3] ?? 'Import'), 0, 255, 'UTF-8')
                    ];

                    // Hàm addPax trong Operation.php sẽ tự chuyển Nam/Nữ sang male/female
                    if ($opModel->addPax($paxData)) {
                        $importCount++;
                    }
                }
                fclose($handle);
            }

            header("Location: index.php?action=booking-ops&id=$bookingId&msg=import_success&count=$importCount");
            exit();

        } catch (Exception $e) {
            die("Lỗi xử lý file: " . $e->getMessage());
        }
    }
}
public function paxDeleteAll() {
    if (isset($_GET['booking_id'])) {
        $bookingId = $_GET['booking_id'];
        $db = (new Database())->getConnection();
        $opModel = new Operation($db);
        
        if ($opModel->deleteAllPaxByBooking($bookingId)) {
            header("Location: index.php?action=booking-ops&id=$bookingId&msg=delete_success");
        }
        exit();
    }
}
public function updatePaxStatusAjax() {
    $paxId = $_GET['pax_id'];
    $status = $_GET['status'];
    
    $db = (new Database())->getConnection();
    $opModel = new Operation($db);
    
    if ($opModel->updatePaxCheckinStatus($paxId, $status)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit(); // Kết thúc để không load giao diện
}
public function storeTourLog() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $db = (new Database())->getConnection();
        $opModel = new Operation($db);

        $data = [
            ':bid'     => $_POST['booking_id'],
            ':gid'     => $_POST['guide_id'],
            ':date'    => $_POST['log_date'],
            ':time'    => $_POST['log_time'] ?? date('H:i:s'),
            ':type'    => $_POST['incident_type'],
            ':details' => $_POST['details'],
            ':photo'   => null // Nếu chưa làm phần upload ảnh
        ];

        if ($opModel->logTourIncident($data)) {
            header("Location: index.php?action=booking-ops&id=" . $_POST['booking_id'] . "&msg=log_added");
        } else {
            echo "Lỗi khi lưu nhật ký!";
        }
    }
}
public function deleteTourLog() {
    // 1. Lấy ID cần xóa và ID booking để quay lại trang cũ
    $logId = $_GET['log_id'] ?? null;
    $bookingId = $_GET['booking_id'] ?? null;

    if ($logId && $bookingId) {
        $db = (new Database())->getConnection();
        $opModel = new Operation($db);

        // 2. Gọi hàm xóa trong Model Operation
        if ($opModel->deleteTourLog($logId)) {
            // 3. Chuyển hướng về lại trang điều hành tour kèm thông báo
            header("Location: index.php?action=booking-ops&id=" . $bookingId . "&msg=log_deleted");
            exit();
        } else {
            die("Lỗi: Không thể xóa nhật ký.");
        }
    } else {
        header("Location: index.php?action=booking-list");
        exit();
    }
}
}
?>