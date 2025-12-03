<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    
    // 1. Hiển thị trang đăng nhập
    public function loginPage() {
        // Nếu đã đăng nhập rồi thì đá về dashboard luôn
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php?action=dashboard");
            exit();
        }
        
        // Load giao diện login (Bạn cần tạo file này ở bước 2)
        require_once __DIR__ . '/../../views/auth/login.php';
    }

    // 2. Xử lý khi bấm nút Đăng Nhập
    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            $db = (new Database())->getConnection();
            $userModel = new User($db);
            
            // Gọi hàm login từ Model User
            $user = $userModel->login($email, $password);

            if ($user) {
                // Đăng nhập thành công -> Lưu session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_avatar'] = $user['avatar']; // Lưu avatar để hiện ở header
                
                // Chuyển hướng vào Dashboard
                header("Location: index.php?action=dashboard");
                exit();
            } else {
                // Đăng nhập thất bại
                $error = "Email hoặc mật khẩu không đúng!";
                // Load lại trang login kèm thông báo lỗi
                require_once __DIR__ . '/../../views/auth/login.php';
            }
        }
    }

    // 3. Đăng xuất
    public function logout() {
        // Xóa sạch session
        session_unset();
        session_destroy();
        
        // Quay về trang login
        header("Location: index.php?action=login");
        exit();
    }
}
?>