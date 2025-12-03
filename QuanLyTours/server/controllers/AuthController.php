<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    
    public function loginPage() {
        // Nếu đã đăng nhập thì vào thẳng dashboard
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php?action=dashboard");
            exit();
        }
        require_once __DIR__ . '/../../views/auth/login.php';
    }

    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            $db = (new Database())->getConnection();
            $userModel = new User($db);
            
            $user = $userModel->login($email, $password);

            if ($user) {
                // Lưu session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_avatar'] = $user['avatar'];
                
                header("Location: index.php?action=dashboard");
                exit();
            } else {
                $error = "Email hoặc mật khẩu không đúng!";
                require_once __DIR__ . '/../../views/auth/login.php';
            }
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        header("Location: index.php?action=login");
        exit();
    }
}
?>