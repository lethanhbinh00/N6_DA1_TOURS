<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Customer.php';

class CustomerController {

    // 1. Xem danh sách
    public function index() {
        $db = (new Database())->getConnection();
        $customers = (new Customer($db))->getAll();
        
        // Xử lý null để tránh lỗi view
        if(!empty($customers)) {
            foreach($customers as $k => $v) {
                $customers[$k]['id_card'] = $v['id_card'] ?? '';
                $customers[$k]['email'] = $v['email'] ?? '';
                $customers[$k]['address'] = $v['address'] ?? '';
                $customers[$k]['notes'] = $v['notes'] ?? '';
            }
        }

        require_once __DIR__ . '/../../views/customer/index.php';
    }

    // 2. Thêm mới (Có Validate)
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = (new Database())->getConnection();
            $model = new Customer($db);

            // Lấy dữ liệu & Làm sạch
            $name    = trim($_POST['full_name']);
            $id_card = trim($_POST['id_card']);
            $phone   = trim($_POST['phone']);
            $email   = trim($_POST['email']);
            $addr    = trim($_POST['address']);
            $src     = $_POST['source'];
            $notes   = $_POST['notes'];

            // Validate
            $errors = [];
            if (empty($name)) $errors[] = "Họ tên không được để trống.";
            if (empty($phone)) $errors[] = "Số điện thoại không được để trống.";

            // Check CCCD
            if (!empty($id_card) && !preg_match('/^[0-9]{9,12}$/', $id_card)) {
                $errors[] = "CCCD/CMND phải là số (9 hoặc 12 chữ số).";
            }

            // Check SĐT
            if (!preg_match('/^[0-9]{9,11}$/', $phone)) {
                $errors[] = "Số điện thoại không hợp lệ (9-11 số).";
            }

            // Check Email
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email không đúng định dạng.";
            }

            // Check Trùng lặp
            if ($model->checkExists($phone, $id_card)) {
                $errors[] = "Số điện thoại hoặc CCCD này đã tồn tại.";
            }

            // Xử lý lỗi
            if (!empty($errors)) {
                $msg = implode("\\n- ", $errors);
                echo "<script>alert('LỖI DỮ LIỆU:\\n- $msg'); window.history.back();</script>";
                return;
            }

            // Lưu
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

    // 3. Xem chi tiết
    public function detail() {
        $id = $_GET['id'] ?? null;
        if($id) {
            $db = (new Database())->getConnection();
            $model = new Customer($db);
            
            $customer = $model->getById($id);
            $history = $model->getBookingHistory($customer['phone']);
            
            require_once __DIR__ . '/../../views/customer/detail.php';
        }
    }

    // 4. [BỔ SUNG] Xóa Khách Hàng
    public function delete() {
        $id = $_GET['id'] ?? null;
        if($id) {
            $db = (new Database())->getConnection();
            $model = new Customer($db);
            $model->delete($id);
            header("Location: index.php?action=customer-list&msg=deleted");
        }
    }
}
?>