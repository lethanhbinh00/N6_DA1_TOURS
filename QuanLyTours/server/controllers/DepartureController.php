<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Departure.php';

class DepartureController {

    public function index() {
        $database = new Database();
        $db = $database->getConnection();
        $model = new Departure($db);
        $departures = $model->getAll();
        $tours = $model->getTours();

        require_once __DIR__ . '/../../views/departures/index.php';
    }

    public function create() {
        $database = new Database();
        $db = $database->getConnection();
        $model = new Departure($db);
        $tours = $model->getTours();

        require_once __DIR__ . '/../../views/departures/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tour_id = $_POST['tour_id'] ?? null;
            $start_date = $_POST['start_date'] ?? null;
            $seats = $_POST['seats'] ?? 0;

            if (empty($tour_id) || empty($start_date)) {
                echo "<script>alert('Vui lòng chọn tour và ngày khởi hành!'); window.history.back();</script>";
                return;
            }

            $database = new Database();
            $db = $database->getConnection();
            $model = new Departure($db);
            $data = [
                'tour_id' => $tour_id,
                'start_date' => $start_date,
                'seats' => $seats
            ];
            $model->create($data);
            header("Location: index.php?action=departure-list&msg=created");
        }
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $database = new Database();
            $db = $database->getConnection();
            $model = new Departure($db);
            $departure = $model->find($id);
            $tours = $model->getTours();

            if (!$departure) { echo "Không tìm thấy khởi hành!"; die(); }
            require_once __DIR__ . '/../../views/departures/edit.php';
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $tour_id = $_POST['tour_id'] ?? null;
            $start_date = $_POST['start_date'] ?? null;
            $seats = $_POST['seats'] ?? 0;

            if (!$id) { echo "ID missing"; return; }

            $database = new Database();
            $db = $database->getConnection();
            $model = new Departure($db);
            $data = ['id' => $id, 'tour_id' => $tour_id, 'start_date' => $start_date, 'seats' => $seats];
            $model->updateDeparture($data);
            header("Location: index.php?action=departure-list&msg=updated");
        }
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $database = new Database();
            $db = $database->getConnection();
            $model = new Departure($db);
            $model->deleteDeparture($id);
            header("Location: index.php?action=departure-list&msg=deleted");
        }
    }
}
