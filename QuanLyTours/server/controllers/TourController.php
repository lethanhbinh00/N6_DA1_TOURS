<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Tour.php';

class TourController {

    /* ============================
        DANH SÁCH TOUR
    ============================ */
    public function index() {
        $database = new Database();
        $db = $database->getConnection();

        $tourModel = new Tour($db);
        $tours = $tourModel->getAll();

        require_once __DIR__ . '/../../views/tour/index.php';
    }

    /* ============================
        THÊM TOUR (STORE)
    ============================ */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $database = new Database();
            $db = $database->getConnection();
            $tourModel = new Tour($db);

            /* VALIDATE */
            $code = trim($_POST['code']);
            $name = trim($_POST['name']);
            $priceA = $_POST['price_adult'];

            if (empty($code) || empty($name)) {
                echo "<script>alert('Mã Tour và Tên Tour không được để trống!'); window.history.back();</script>";
                return;
            }
            if ($priceA < 0) {
                echo "<script>alert('Giá tiền không được âm!'); window.history.back();</script>";
                return;
            }

            /* UPLOAD ẢNH CHÍNH */
            $imageName = "";
            $target_dir = __DIR__ . "/../../public/uploads/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $imageName = time() . "_" . basename($_FILES["image"]["name"]);
                move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $imageName);
            }

            /* DỮ LIỆU TOUR */
            $tourData = [
                'code' => $_POST['code'],
                'name' => $_POST['name'],
                'type' => $_POST['type'],
                'highlight' => $_POST['highlight'] ?? '',
                'price_adult' => $_POST['price_adult'],
                'price_child' => $_POST['price_child'],
                'image' => $imageName
            ];

            /* DỮ LIỆU LỊCH TRÌNH */
            $itineraryData = [
                'titles' => $_POST['itinerary_title'] ?? [],
                'descs'  => $_POST['itinerary_desc'] ?? [],
                'meals'  => $_POST['itinerary_meals'] ?? [],
                'spots'  => $_POST['itinerary_spot'] ?? [],
                'hotels' => $_POST['itinerary_hotel'] ?? []
            ];

            /* INSERT */
            $result = $tourModel->create($tourData, $itineraryData);

            if ($result === true) {

                // Lấy ID vừa tạo
                $newTourId = $db->lastInsertId();

                // Upload gallery nếu có
                $this->uploadGallery($db, $newTourId, $_FILES['gallery'] ?? null);

                header("Location: index.php?msg=success");
                exit;
            }

            echo "<script>alert('Đã thêm!'); window.history.back();</script>";
        }
    }

    /* ============================
        XÓA TOUR
    ============================ */
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

    /* ============================
        FORM EDIT
    ============================ */
    public function edit() {
        $id = $_GET['id'] ?? null;

        if ($id) {
            $database = new Database();
            $db = $database->getConnection();

            $tourModel = new Tour($db);
            $tour = $tourModel->getById($id);
            $itineraries = $tourModel->getItinerary($id);

            if (!$tour) { echo "Không tìm thấy tour!"; die(); }

            require_once __DIR__ . '/../../views/tour/edit.php';
        }
    }

    /* ============================
        UPDATE TOUR
    ============================ */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $id = $_POST['id'];

            $database = new Database();
            $db = $database->getConnection();
            $tourModel = new Tour($db);

            /* UPLOAD ẢNH MỚI */
            $imageName = "";
            $target_dir = __DIR__ . "/../../public/uploads/";
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $imageName = time() . "_" . basename($_FILES["image"]["name"]);
                move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $imageName);
            }

            /* DỮ LIỆU TOUR */
            $tourData = [
                'code' => $_POST['code'],
                'name' => $_POST['name'],
                'type' => $_POST['type'],
                'highlight' => $_POST['highlight'] ?? '',
                'price_adult' => $_POST['price_adult'],
                'price_child' => $_POST['price_child'],
                'image' => $imageName
            ];

            /* DỮ LIỆU LỊCH TRÌNH */
            $itineraryData = [
                'titles' => $_POST['itinerary_title'] ?? [],
                'descs'  => $_POST['itinerary_desc'] ?? [],
                'meals'  => $_POST['itinerary_meals'] ?? [],
                'spots'  => $_POST['itinerary_spot'] ?? [],
                'hotels' => $_POST['itinerary_hotel'] ?? []
            ];

            $result = $tourModel->update($id, $tourData, $itineraryData);

            if ($result === "success") {
                $this->uploadGallery($db, $id, $_FILES['gallery'] ?? null);
                header("Location: index.php?msg=updated");
                exit;
            }

            echo "<script>alert('Lỗi update!'); window.history.back();</script>";
        }
    }

    /* ============================
        CHI TIẾT TOUR
    ============================ */
    public function show() {
        $id = $_GET['id'] ?? null;
        if ($id) {

            $database = new Database();
            $db = $database->getConnection();

            $tourModel = new Tour($db);

            $tour = $tourModel->getById($id);
            $itineraries = $tourModel->getItinerary($id);
            $gallery = $tourModel->getGallery($id);

            if (!$tour) { echo "Không tìm thấy tour!"; die(); }

            require_once __DIR__ . '/../../views/tour/detail.php';
        }
    }

    /* ============================
        UPLOAD GALLERY (DÙNG CHUNG)
    ============================ */
    private function uploadGallery($db, $tourId, $files) {

        if (!empty($files['name'][0])) {

            $target_dir = __DIR__ . "/../../public/uploads/";
            $total = count($files['name']);

            $stmtGal = $db->prepare("INSERT INTO tour_gallery (tour_id, image_path) VALUES (?, ?)");

            for ($i = 0; $i < $total; $i++) {
                if ($files['error'][$i] == 0) {
                    $galName = time() . "_" . $i . "_gal_" . basename($files['name'][$i]);

                    if (move_uploaded_file($files['tmp_name'][$i], $target_dir . $galName)) {
                        try {
                            $stmtGal->execute([$tourId, $galName]);
                        } catch (Exception $e) {}
                    }
                }
            }
        }
    }
    /* ============================
        GIÁ THEO MÙA
    ============================ */
    public function prices()
    {
        $tour_id = $_GET['id'] ?? null; // lấy id tour từ URL
        if (!$tour_id) {
            echo "Tour không tồn tại!"; 
            exit;
        }

        $database = new Database();
        $db = $database->getConnection();

        require_once __DIR__ . '/../models/TourPrice.php';
        $priceModel = new TourPrice($db);

        require_once __DIR__ . '/../models/Tour.php';
        $tourModel = new Tour($db);

        $tour = $tourModel->getById($tour_id);        // chỉ lấy tour này
        $prices = $priceModel->getByTourId($tour_id); // chỉ lấy giá tour này

        require_once __DIR__ . '/../../views/tour/prices.php';
    }



    public function priceStore()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $database = new Database();
            $db = $database->getConnection();

            require_once __DIR__ . '/../models/TourPrice.php';
            $priceModel = new TourPrice($db);

            $tour_id = $_POST['tour_id'];
            $priceModel->create([
                'tour_id' => $tour_id,
                'name' => $_POST['name'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'price_adult' => $_POST['price_adult'],
                'price_child' => $_POST['price_child']
            ]);

            header("Location: index.php?action=tour-prices&id=" . $tour_id);
            exit;
        }
    }


    public function priceDelete()
    {
        $id = $_GET['id'] ?? null;

        if ($id) {
            $database = new Database();
            $db = $database->getConnection();

            require_once __DIR__ . '/../models/TourPrice.php';
            $priceModel = new TourPrice($db);

            $priceModel->delete($id);
        }

        header("Location: index.php?action=tour-prices");
        exit;
    }


}
?>
