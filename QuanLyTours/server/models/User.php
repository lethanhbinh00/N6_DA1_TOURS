<?php
class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. Đăng nhập (Giữ nguyên)
    public function login($email, $password) {
        $query = "SELECT * FROM users WHERE email = :email AND status = 'active' LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    // 2. Lấy danh sách nhân viên
    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM users ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Lấy 1 nhân viên
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 4. Tạo mới (Có mã hóa mật khẩu)
    public function create($data) {
        try {
            $query = "INSERT INTO users (full_name, email, password, phone, role, avatar, status) 
                      VALUES (:name, :email, :pass, :phone, :role, :avatar, :status)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':name'   => $data['full_name'],
                ':email'  => $data['email'],
                ':pass'   => password_hash($data['password'], PASSWORD_DEFAULT), // Mã hóa
                ':phone'  => $data['phone'],
                ':role'   => $data['role'],
                ':avatar' => $data['avatar'],
                ':status' => $data['status']
            ]);
            return "success";
        } catch (Exception $e) {
            if ($e->errorInfo[1] == 1062) return "duplicate"; // Trùng email
            return $e->getMessage();
        }
    }

    // 5. Cập nhật (Nếu không nhập pass thì giữ nguyên pass cũ)
    public function update($id, $data) {
        try {
            $passQuery = "";
            $params = [
                ':name'   => $data['full_name'],
                ':phone'  => $data['phone'],
                ':role'   => $data['role'],
                ':status' => $data['status'],
                ':id'     => $id
            ];

            // Nếu có đổi mật khẩu
            if (!empty($data['password'])) {
                $passQuery = ", password = :pass";
                $params[':pass'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            // Nếu có đổi avatar
            $avatarQuery = "";
            if (!empty($data['avatar'])) {
                $avatarQuery = ", avatar = :avatar";
                $params[':avatar'] = $data['avatar'];
            }

            $query = "UPDATE users SET 
                      full_name = :name, 
                      phone = :phone, 
                      role = :role, 
                      status = :status 
                      $passQuery 
                      $avatarQuery 
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return "success";
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    // 6. Xóa
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
?>