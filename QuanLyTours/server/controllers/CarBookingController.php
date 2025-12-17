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
        $bookings = $model->getAll();

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

        $database = new Database();
        $db = $database->getConnection();
        $model = new CarBooking($db);
        $model->create($data);

        header("Location: index.php?action=car-booking&msg=created");
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

        $database = new Database();
        $db = $database->getConnection();
        $model = new CarBooking($db);
        $model->updateBooking($data);

        header("Location: index.php?action=car-booking&msg=updated");
    }

    public function delete()
    {
        $id = $_GET['id'];
        $database = new Database();
        $db = $database->getConnection();
        $model = new CarBooking($db);
        $model->deleteBooking($id);

        header("Location: index.php?action=car-booking&msg=deleted");
    }
}
