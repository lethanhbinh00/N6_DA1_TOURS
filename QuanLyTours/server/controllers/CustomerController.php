<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Customer.php';

class CustomerController {
    public function index() {
        $database = new Database();
        $db = $database->getConnection();
        $customerModel = new Customer($db);
        $customers = $customerModel->getAll();
        
        require_once __DIR__ . '/../../views/customer/index.php';
    }
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $database = new Database();
            $db = $database->getConnection();
            $customerModel = new Customer($db);

            $data = [
                'full_name' => $_POST['full_name'],
                'phone'     => $_POST['phone'],
                'email'     => $_POST['email'],
                'address'   => $_POST['address'],
                'source'    => $_POST['source'],
                'notes'     => $_POST['notes']
            ];

            $result = $customerModel->create($data);

            if ($result === "success") {
                header("Location: index.php?action=customer-list&msg=success");
            } elseif ($result === "duplicate") {
                echo "<script>alert('Số điện thoại này đã tồn tại!'); window.history.back();</script>";
            } else {
                echo "<script>alert('Lỗi hệ thống!'); window.history.back();</script>";
            }
        }
    }

    public function detail() {
        $id = $_GET['id'] ?? null;
        if($id) {
            $database = new Database();
            $db = $database->getConnection();
            $customerModel = new Customer($db);
            
            $customer = $customerModel->getById($id);
            $history = $customerModel->getBookingHistory($customer['phone']);
            
            require_once __DIR__ . '/../../views/customer/detail.php';
        }
    }
        public function delete() {
        $id = $_GET['id'] ?? null;
        if($id) {
            $database = new Database();
            $db = $database->getConnection();
            $customerModel = new Customer($db);
            $customerModel->delete($id);
            header("Location: index.php?action=customer-list&msg=deleted");
        }
    }
}
?>