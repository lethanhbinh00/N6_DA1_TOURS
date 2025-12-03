<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Guide.php';

class GuideController {

    public function index() {
        $database = new Database();
        $db = $database->getConnection();
        $guideModel = new Guide($db);
        $guides = $guideModel->getAll();
        
        require_once __DIR__ . '/../../views/guide/index.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $database = new Database();
            $db = $database->getConnection();
            $name = trim($_POST['full_name']);
            $phone = trim($_POST['phone']);
            
            if(empty($name) || empty($phone)) {
                echo "<script>alert('Tên và SĐT Hướng dẫn viên là bắt buộc!'); window.history.back();</script>";
                return;
            }
            if (!preg_match('/^[0-9]{9,11}$/', $phone)) {
                echo "<script>alert('SĐT Hướng dẫn viên không hợp lệ!'); window.history.back();</script>";
                return;
            }
            $guideModel = new Guide($db);
            $imageName = "";
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $target_dir = __DIR__ . "/../../public/uploads/";
                $imageName = time() . "_guide_" . basename($_FILES["image"]["name"]);
                move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $imageName);
            }

            $data = [
                'full_name'        => $_POST['full_name'],
                'gender'           => $_POST['gender'],
                'dob'              => $_POST['dob'],
                'phone'            => $_POST['phone'],
                'email'            => $_POST['email'],
                'address'          => $_POST['address'],
                'license_number'   => $_POST['license_number'],
                'languages'        => $_POST['languages'],
                'experience_years' => $_POST['experience_years'],
                'image'            => $imageName
            ];

            $guideModel->create($data);
            header("Location: index.php?action=guide-list&msg=success");
        }
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if($id) {
            $database = new Database();
            $db = $database->getConnection();
            $guideModel = new Guide($db);
            $guideModel->delete($id);
            header("Location: index.php?action=guide-list&msg=deleted");
        }
    }
}
?>