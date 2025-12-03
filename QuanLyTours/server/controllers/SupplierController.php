<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Supplier.php';

class SupplierController {
    
    // Hiển thị danh sách
    public function index() {
        $db = (new Database())->getConnection();
        $model = new Supplier($db);
        $suppliers = $model->getAll();
        require_once __DIR__ . '/../../views/supplier/index.php';
    }

    // Xử lý lưu (Có Validate)
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = (new Database())->getConnection();
            $model = new Supplier($db);

            // 1. LẤY DỮ LIỆU & LÀM SẠCH (TRIM)
            $name    = trim($_POST['name']);
            $type    = $_POST['type'];
            $contact = trim($_POST['contact_person']);
            $phone   = trim($_POST['phone']);
            $email   = trim($_POST['email']);
            $addr    = trim($_POST['address']);

            // 2. VALIDATE DỮ LIỆU (Backend)
            $errors = [];

            // Kiểm tra tên rỗng
            if (empty($name)) {
                $errors[] = "Tên nhà cung cấp không được để trống.";
            }

            // Kiểm tra SĐT (Phải là số, từ 9-11 ký tự)
            if (!empty($phone)) {
                if (!preg_match('/^[0-9]{9,11}$/', $phone)) {
                    $errors[] = "Số điện thoại không hợp lệ (Phải là số, 9-11 ký tự).";
                }
            }

            // Kiểm tra Email (Đúng định dạng)
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email không đúng định dạng.";
            }

            // Kiểm tra Trùng lặp (Gọi Model)
            if (!empty($phone) && $model->checkExists($phone, $email)) {
                $errors[] = "Số điện thoại hoặc Email này đã tồn tại trong hệ thống.";
            }

            // 3. NẾU CÓ LỖI -> BÁO LỖI & QUAY LẠI
            if (!empty($errors)) {
                $errorMsg = implode("\\n- ", $errors); // Nối các lỗi thành chuỗi
                echo "<script>alert('CÓ LỖI XẢY RA:\\n- $errorMsg'); window.history.back();</script>";
                return; // Dừng lại, không lưu
            }

            // 4. NẾU ỔN -> GOM DỮ LIỆU ĐỂ LƯU
            $data = [
                ':name'    => $name,
                ':type'    => $type,
                ':contact' => $contact,
                ':phone'   => $phone,
                ':email'   => $email,
                ':addr'    => $addr
            ];

            // Gọi Model để lưu
            $result = $model->create($data);

            if ($result === "success") {
                header("Location: index.php?action=supplier-list&msg=success");
            } else {
                echo "<script>alert('Lỗi hệ thống: $result'); window.history.back();</script>";
            }
        }
    }

    // Xóa
    public function delete() {
        $id = $_GET['id'] ?? null;
        if($id) {
            $db = (new Database())->getConnection();
            (new Supplier($db))->delete($id);
            header("Location: index.php?action=supplier-list&msg=deleted");
        }
    }
}
?>