<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Tour.php'; 

class BookingController {

    public function create() {
        $database = new Database();
        $db = $database->getConnection();
        $tourModel = new Tour($db);
        $tours = $tourModel->getAll();

        require_once __DIR__ . '/../../views/booking/create.php';
    }
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $database = new Database();
            $db = $database->getConnection();
            $bookingModel = new Booking($db);
            $total = 0; 

            $data = [
                'tour_id'        => $_POST['tour_id'],
                'travel_date'    => $_POST['travel_date'],
                'customer_name'  => $_POST['customer_name'],
                'customer_phone' => $_POST['customer_phone'],
                'customer_email' => $_POST['customer_email'],
                'adults'         => $_POST['adults'],
                'children'       => $_POST['children'],
                'total_price'    => $_POST['total_price'] ?? 0,
                'note'           => $_POST['note']
            ];

            $result = $bookingModel->create($data);

            if ($result === "success") {
                header("Location: index.php?msg=booking_success");
            } else {
                echo "Lỗi: " . $result;
            }
        }
    }
    public function index() {
        $database = new Database();
        $db = $database->getConnection();
        $bookingModel = new Booking($db);
        $bookings = $bookingModel->getAll();
        require_once __DIR__ . '/../../views/booking/index.php';
    }

    public function status() {
        $id = $_GET['id'] ?? null;
        $status = $_GET['status'] ?? null;

        if ($id && $status) {
            $database = new Database();
            $db = $database->getConnection();
            $bookingModel = new Booking($db);

            $bookingModel->updateStatus($id, $status);
            header("Location: index.php?action=booking-list&msg=status_updated");
        }
    }
    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $database = new Database();
            $db = $database->getConnection();
            $bookingModel = new Booking($db);

            $bookingModel->delete($id);
            header("Location: index.php?action=booking-list&msg=deleted");
        }
    }
}
?>