<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Customer.php';

class CustomerController {

    // 1. Danh sách
    public function index() {
        $db = (new Database())->getConnection();
        $customers = (new Customer($db))->getAll();
        require_once __DIR__ . '/../../views/customer/index.php';
    }

    // 2. Lưu Tạo mới
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processForm('create');
        }
    }

    // 3. Form Sửa
    public function edit() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $db = (new Database())->getConnection();
            $customer = (new Customer($db))->getById($id);
            if(!$customer) { echo "Không tìm thấy khách hàng"; die(); }
            require_once __DIR__ . '/../../views/customer/edit.php';
        }
    }

    // 4. Lưu Cập nhật
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processForm('update');
        }
    }

    // 5. Xem chi tiết
    public function detail() {
        $id = $_GET['id'] ?? null;
        if($id) {
            $db = (new Database())->getConnection();
            $model = new Customer($db);
            
            $customer = $model->getById($id);
            $history = $model->getBookingHistory($customer['phone']);
            $summary = $model->getSummary($customer['phone']);
            
            require_once __DIR__ . '/../../views/customer/detail.php';
        }
    }

    // 6. Xóa
    public function delete() {
        $id = $_GET['id'] ?? null;
        if($id) {
            $db = (new Database())->getConnection();
            (new Customer($db))->delete($id);
            header("Location: index.php?action=customer-list&msg=deleted");
        }
    }

    // --- HÀM XỬ LÝ CHUNG (VALIDATE) ---
    private function processForm($mode) {
        $db = (new Database())->getConnection();
        $model = new Customer($db);

        $name    = trim($_POST['full_name']);
        $id_card = trim($_POST['id_card']);
        $phone   = trim($_POST['phone']);
        $email   = trim($_POST['email']);
        $addr    = trim($_POST['address']);
        $src     = $_POST['source'];
        $notes   = $_POST['notes'];

        // Validate
        $errors = [];
        if (empty($name)) $errors[] = "Họ tên là bắt buộc.";
        if (empty($phone)) $errors[] = "SĐT là bắt buộc.";

        // Check định dạng
        if (!empty($id_card) && !preg_match('/^[0-9]{9,12}$/', $id_card)) {
            $errors[] = "CCCD không hợp lệ (phải là 9-12 số).";
        }
        if (!preg_match('/^[0-9]{9,11}$/', $phone)) {
            $errors[] = "SĐT không hợp lệ (phải là 9-11 số).";
        }
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email không đúng định dạng.";
        }

        // Check trùng lặp (Chỉ check khi tạo mới)
        if ($mode == 'create' && $model->checkExists($phone, $id_card)) {
            $errors[] = "Số điện thoại hoặc CCCD đã tồn tại trong hệ thống.";
        }

        if (!empty($errors)) {
            $msg = implode("\\n- ", $errors);
            echo "<script>alert('LỖI DỮ LIỆU:\\n- $msg'); window.history.back();</script>";
            return;
        }

        $data = [
            ':name'  => $name,
            ':card'  => $id_card,
            ':phone' => $phone,
            ':email' => $email,
            ':addr'  => $addr,
            ':src'   => $src,
            ':note'  => $notes
        ];

        if ($mode == 'create') {
            $result = $model->create($data);
        } else {
            $id = $_POST['id'];
            $result = $model->update($id, $data);
        }

        if ($result === "success") {
            $msg = ($mode == 'create') ? 'success' : 'updated';
            header("Location: index.php?action=customer-list&msg=$msg");
        } else {
            echo "<script>alert('Lỗi hệ thống: $result'); window.history.back();</script>";
        }
    }
}
?>