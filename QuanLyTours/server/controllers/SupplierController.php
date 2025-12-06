<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Supplier.php';

class SupplierController {

    // 1. Danh sách
    public function index() {
        $db = (new Database())->getConnection();
        $suppliers = (new Supplier($db))->getAll();
        require_once __DIR__ . '/../../views/supplier/index.php';
    }

    // 2. Form Tạo
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processForm('create');
        }
    }

    // 3. [MỚI] Form Sửa
    public function edit() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $db = (new Database())->getConnection();
            $supplier = (new Supplier($db))->getById($id);
            if (!$supplier) { echo "Không tìm thấy NCC!"; die(); }
            require_once __DIR__ . '/../../views/supplier/edit.php';
        }
    }

    // 4. [MỚI] Lưu Cập nhật
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processForm('update');
        }
    }

    // 5. Xóa
    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $db = (new Database())->getConnection();
            (new Supplier($db))->delete($id);
            header("Location: index.php?action=supplier-list&msg=deleted");
        }
    }

    // --- HÀM XỬ LÝ CHUNG (VALIDATE & SAVE) ---
    private function processForm($mode) {
        $db = (new Database())->getConnection();
        $model = new Supplier($db);
        $id = $_POST['id'] ?? null;

        // Xử lý file (Tạm thời chỉ lấy tên file/link)
        $contractFile = trim($_POST['contract_file'] ?? '');

        // Lấy dữ liệu
        $data = [
            ':name'    => trim($_POST['name']),
            ':type'    => $_POST['type'],
            ':contact' => trim($_POST['contact_person']),
            ':phone'   => trim($_POST['phone']),
            ':email'   => trim($_POST['email']),
            ':addr'    => trim($_POST['address']),
            
            // [MỚI] Dữ liệu chi tiết
            ':desc'    => trim($_POST['service_description']),
            ':capa'    => trim($_POST['service_capacity']),
            ':file'    => $contractFile,
            ':expiry'  => empty($_POST['contract_expiry']) ? null : $_POST['contract_expiry'],
        ];

        // Validate cơ bản
        $errors = [];
        if (empty($data[':name'])) $errors[] = "Tên đơn vị không được trống.";
        if (empty($data[':phone'])) $errors[] = "SĐT không được trống.";

        // Validate Trùng lặp (trừ chính nó khi update)
        if ($model->checkExists($data[':phone'], $data[':email'], $id)) {
             $errors[] = "SĐT hoặc Email này đã được sử dụng.";
        }

        if (!empty($errors)) {
            $msg = implode("\\n- ", $errors);
            echo "<script>alert('LỖI DỮ LIỆU:\\n- $msg'); window.history.back();</script>";
            return;
        }

        if ($mode == 'create') {
            $result = $model->create($data);
        } else {
            $result = $model->update($id, $data);
        }

        if ($result === "success") {
            $msg = ($mode == 'create') ? 'success' : 'updated';
            header("Location: index.php?action=supplier-list&msg=$msg");
        } else {
            echo "<script>alert('Lỗi hệ thống: $result'); window.history.back();</script>";
        }
    }
}
?>