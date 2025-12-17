<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/CarBooking.php';

class CarBookingController
{
    public function index()
    {
        $database = new Database();
        $db = $database->getConnection();
        $model = new CarBooking($db);

        $keyword = $_GET['keyword'] ?? '';
        $carBookings = $model->getAll($keyword);

        require_once __DIR__ . '/../../views/car_booking/index.php';
    }

    public function create()
    {
        $database = new Database();
        $db = $database->getConnection();
        $model = new CarBooking($db);
        $services = $model->getServices();

        require_once __DIR__ . '/../../views/car_booking/create.php';
    }

    public function store()
    {
        $data = [
            'service_id' => $_POST['service_id'],
            'customer_name' => $_POST['customer_name'],
            'phone' => $_POST['phone'],
            'date' => $_POST['date'],
            'quantity' => $_POST['quantity'],
            'note' => $_POST['note']
        ];

        // basic server-side validation
        if (empty($data['service_id']) || empty($data['customer_name']) || empty($data['date'])) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin dịch vụ, khách hàng và ngày.';
            header('Location: index.php?action=car-booking-create');
            exit;
        }

        $database = new Database();
        $db = $database->getConnection();
        $model = new CarBooking($db);
        $ok = $model->create($data);
        if ($ok) {
            $_SESSION['success'] = 'Đã thêm đặt xe thành công.';
        } else {
            $_SESSION['error'] = 'Lỗi khi thêm: ' . $model->getLastError();
        }
        header("Location: index.php?action=car-booking");
    }

    public function edit()
    {
        $id = $_GET['id'];

        $database = new Database();
        $db = $database->getConnection();
        $model = new CarBooking($db);
        $booking = $model->find($id);
        $services = $model->getServices();

        require_once __DIR__ . '/../../views/car_booking/edit.php';
    }

    public function update()
    {
        $data = [
            'id' => $_POST['id'],
            'service_id' => $_POST['service_id'],
            'customer_name' => $_POST['customer_name'],
            'phone' => $_POST['phone'],
            'date' => $_POST['date'],
            'quantity' => $_POST['quantity'],
            'note' => $_POST['note']
        ];

        if (empty($data['id'])) {
            $_SESSION['error'] = 'ID đặt xe không hợp lệ.';
            header('Location: index.php?action=car-booking');
            exit;
        }

        $database = new Database();
        $db = $database->getConnection();
        $model = new CarBooking($db);
        $ok = $model->updateBooking($data);
        if ($ok) {
            $_SESSION['success'] = 'Cập nhật đặt xe thành công.';
        } else {
            $_SESSION['error'] = 'Lỗi khi cập nhật: ' . $model->getLastError();
        }
        header("Location: index.php?action=car-booking");
    }

    public function delete()
    {
        $id = $_GET['id'];
        $database = new Database();
        $db = $database->getConnection();
        $model = new CarBooking($db);
        $ok = $model->deleteBooking($id);
        if ($ok) $_SESSION['success'] = 'Xóa đặt xe thành công.';
        else $_SESSION['error'] = 'Lỗi khi xóa: ' . $model->getLastError();
        header("Location: index.php?action=car-booking");
    }
}
