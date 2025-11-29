<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Tour.php';

class TourController {
        public function index() {
        $database = new Database();
        $db = $database->getConnection();
        $tourModel = new Tour($db);
        $tours = $tourModel->getAll(); 
        require_once __DIR__ . '/../../views/tour/index.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $database = new Database();
            $db = $database->getConnection();
            $tourModel = new Tour($db);
            $imageName = ""; 
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $target_dir = __DIR__ . "/../../public/uploads/";
                $imageName = time() . "_" . basename($_FILES["image"]["name"]);
                $target_file = $target_dir . $imageName;
                move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
            }

            $tourData = [
                'code' => $_POST['code'],
                'name' => $_POST['name'],
                'type' => $_POST['type'],
                'highlight' => $_POST['highlight'],
                'price_adult' => $_POST['price_adult'],
                'price_child' => $_POST['price_child'],
                'image' => $imageName 
            ];

            $itineraryData = [
                'titles' => $_POST['itinerary_title'] ?? [],
                'descs'  => $_POST['itinerary_desc'] ?? [],
                'meals'  => $_POST['itinerary_meals'] ?? [],
                'spots'  => $_POST['itinerary_spot'] ?? [],
                'hotels' => $_POST['itinerary_hotel'] ?? []
            ];

            $result = $tourModel->create($tourData, $itineraryData);

            if ($result === "success") {
                header("Location: index.php?msg=success");
            } elseif ($result === "duplicate") {
                echo "<script>alert('Lỗi: Mã tour đã tồn tại!'); window.history.back();</script>";
            } else {
                echo "<script>alert('Lỗi hệ thống!'); window.history.back();</script>";
            }
        }
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $database = new Database();
            $db = $database->getConnection();
            $tourModel = new Tour($db);
            
            if ($tourModel->delete($id)) {
                header("Location: index.php?msg=deleted");
            } else {
                echo "<script>alert('Lỗi xóa tour'); window.location.href='index.php';</script>";
            }
        }
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $database = new Database();
            $db = $database->getConnection();
            $tourModel = new Tour($db);
            $tour = $tourModel->getById($id);
            $itineraries = $tourModel->getItinerary($id);
            require_once __DIR__ . '/../../views/tour/edit.php';
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $database = new Database();
            $db = $database->getConnection();
            $tourModel = new Tour($db);
            $imageName = "";
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $target_dir = __DIR__ . "/../../public/uploads/";
                $imageName = time() . "_" . basename($_FILES["image"]["name"]);
                move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $imageName);
            }

            $tourData = [
                'code' => $_POST['code'],
                'name' => $_POST['name'],
                'type' => $_POST['type'],
                'highlight' => $_POST['highlight'],
                'price_adult' => $_POST['price_adult'],
                'price_child' => $_POST['price_child'],
                'image' => $imageName 
            ];
            
            $itineraryData = [
                'titles' => $_POST['itinerary_title'] ?? [],
                'descs'  => $_POST['itinerary_desc'] ?? [],
                'meals'  => $_POST['itinerary_meals'] ?? [],
                'spots'  => $_POST['itinerary_spot'] ?? [],
                'hotels' => $_POST['itinerary_hotel'] ?? []
            ];

            $result = $tourModel->update($id, $tourData, $itineraryData);

            if ($result === "success") {
                header("Location: index.php?msg=updated");
            } elseif ($result === "duplicate") {
                echo "<script>alert('Lỗi: Mã tour trùng lặp!'); window.history.back();</script>";
            } else {
                echo "<script>alert('Lỗi update!'); window.history.back();</script>";
            }
        }
    }
}
?>