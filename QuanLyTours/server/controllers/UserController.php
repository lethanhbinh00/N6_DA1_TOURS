<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';

class UserController {
    
    public function index() {
        $db = (new Database())->getConnection();
        $users = (new User($db))->getAll();
        require_once __DIR__ . '/../../views/user/index.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = (new Database())->getConnection();
            
            $avatar = "";
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
                $target_dir = __DIR__ . "/../../public/uploads/imguser/";
                if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                $avatar = time() . "_" . basename($_FILES["avatar"]["name"]);
                move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_dir . $avatar);
            }

            $data = [
                'full_name' => $_POST['full_name'],
                'email'     => $_POST['email'],
                'password'  => $_POST['password'],
                'phone'     => $_POST['phone'],
                'role'      => $_POST['role'],
                'status'    => $_POST['status'],
                'avatar'    => $avatar
            ];

            $result = (new User($db))->create($data);

            if ($result === "success") header("Location: index.php?action=user-list&msg=success");
            elseif ($result === "duplicate") echo "<script>alert('Email này đã tồn tại!'); window.history.back();</script>";
            else echo "Lỗi: " . $result;
        }
    }

    // [MỚI] Xem chi tiết
    public function detail() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $db = (new Database())->getConnection();
            $user = (new User($db))->getById($id);
            if(!$user) { echo "Không tìm thấy!"; die(); }
            require_once __DIR__ . '/../../views/user/detail.php';
        }
    }

    // [MỚI] Hiển thị form sửa
    public function edit() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $db = (new Database())->getConnection();
            $user = (new User($db))->getById($id);
            if(!$user) { echo "Không tìm thấy!"; die(); }
            require_once __DIR__ . '/../../views/user/edit.php';
        }
    }

    // Xử lý cập nhật
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = (new Database())->getConnection();
            $id = $_POST['id'];
            
            $avatar = "";
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
                $target_dir = __DIR__ . "/../../public/uploads/imguser/";
                $avatar = time() . "_" . basename($_FILES["avatar"]["name"]);
                move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_dir . $avatar);
            }

            $data = [
                'full_name' => $_POST['full_name'],
                'password'  => $_POST['password'],
                'phone'     => $_POST['phone'],
                'role'      => $_POST['role'],
                'status'    => $_POST['status'],
                'avatar'    => $avatar
            ];

            $result = (new User($db))->update($id, $data);
            
            if ($result === "success") header("Location: index.php?action=user-list&msg=updated");
            else echo "Lỗi: " . $result;
        }
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $db = (new Database())->getConnection();
            (new User($db))->delete($id);
            header("Location: index.php?action=user-list&msg=deleted");
        }
    }
}
?>