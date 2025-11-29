<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Customer.php';

class CustomerController {
    // ... (Hàm index, detail, delete giữ nguyên) ...
    public function index() {
        $db = (new Database())->getConnection();
        $customers = (new Customer($db))->getAll();
        require_once __DIR__ . '/../../views/customer/index.php';
    }
    
    public function detail() { /* ...giữ nguyên... */ }
    public function delete() { /* ...giữ nguyên... */ }

    // --- HÀM STORE CÓ VALIDATE ---
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = (new Database())->getConnection();
            $model = new Customer($db);

            // 1. Lấy dữ liệu & Làm sạch
            $name    = trim($_POST['full_name']);
            $id_card = trim($_POST['id_card']); // Mới
            $phone   = trim($_POST['phone']);
            $email   = trim($_POST['email']);
            $addr    = trim($_POST['address']);
            $src     = $_POST['source'];
            $notes   = $_POST['notes'];

            // 2. VALIDATE
            $errors = [];

            if (empty($name)) $errors[] = "Họ tên không được để trống.";
            if (empty($phone)) $errors[] = "Số điện thoại không được để trống.";

            // Validate CCCD (9 hoặc 12 số)
            if (!empty($id_card)) {
                if (!preg_match('/^[0-9]{9,12}$/', $id_card)) {
                    $errors[] = "CCCD/CMND phải là số (9 hoặc 12 chữ số).";
                }
            }

            // Validate SĐT
            if (!preg_match('/^[0-9]{9,11}$/', $phone)) {
                $errors[] = "Số điện thoại không hợp lệ.";
            }

            // Validate Email
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email không đúng định dạng.";
            }

            // Kiểm tra trùng trong DB
            if ($model->checkExists($phone, $id_card)) {
                $errors[] = "Số điện thoại hoặc CCCD này đã tồn tại.";
            }

            // 3. XỬ LÝ LỖI
            if (!empty($errors)) {
                $msg = implode("\\n- ", $errors);
                echo "<script>alert('LỖI DỮ LIỆU:\\n- $msg'); window.history.back();</script>";
                return;
            }

            // 4. LƯU
            $data = [
                ':name'  => $name,
                ':card'  => $id_card,
                ':phone' => $phone,
                ':email' => $email,
                ':addr'  => $addr,
                ':src'   => $src,
                ':note'  => $notes
            ];

            if ($model->create($data) === "success") {
                header("Location: index.php?action=customer-list&msg=success");
            } else {
                echo "<script>alert('Lỗi hệ thống! Có thể dữ liệu bị trùng.'); window.history.back();</script>";
            }
        }
    }
}
?>