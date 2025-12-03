<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Tour.php';

class TourController {
    
    // --- DANH SÁCH TOUR ---
    public function index() {
        $database = new Database();
        $db = $database->getConnection();
        $tourModel = new Tour($db);
        $tours = $tourModel->getAll(); 
        require_once __DIR__ . '/../../views/tour/index.php';
    }

    // --- THÊM TOUR MỚI (STORE) ---
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $database = new Database();
            $db = $database->getConnection();
            $tourModel = new Tour($db);
            $code = trim($_POST['code']);
            $name = trim($_POST['name']);
            $priceA = $_POST['price_adult'];
            
            if(empty($code) || empty($name)) {
                echo "<script>alert('Mã Tour và Tên Tour không được để trống!'); window.history.back();</script>";
                return;
            }
            if($priceA < 0) {
                echo "<script>alert('Giá tiền không được âm!'); window.history.back();</script>";
                return;
            }

            // 1. Xử lý ảnh đại diện (Avatar)
            $imageName = ""; 
            $target_dir = __DIR__ . "/../../public/uploads/";
            
            // Tạo thư mục nếu chưa có
            if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $imageName = time() . "_" . basename($_FILES["image"]["name"]);
                move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $imageName);
            }

            // 2. Gom dữ liệu Tour
            $tourData = [
                'code' => $_POST['code'],
                'name' => $_POST['name'],
                'type' => $_POST['type'],
                'highlight' => $_POST['highlight'] ?? '', // Thêm ?? '' để tránh lỗi
                'price_adult' => $_POST['price_adult'],
                'price_child' => $_POST['price_child'],
                'image' => $imageName 
            ];

            // 3. Gom dữ liệu Lịch trình
            $itineraryData = [
                'titles' => $_POST['itinerary_title'] ?? [],
                'descs'  => $_POST['itinerary_desc'] ?? [],
                'meals'  => $_POST['itinerary_meals'] ?? [],
                'spots'  => $_POST['itinerary_spot'] ?? [],
                'hotels' => $_POST['itinerary_hotel'] ?? []
            ];

            // 4. GỌI MODEL ĐỂ LƯU
            $result = $tourModel->create($tourData, $itineraryData);

            if ($result === "success") {
                // --- XỬ LÝ LƯU GALLERY ẢNH (NẾU CÓ) ---
                // Chỉ làm khi tạo tour thành công và có ID mới
                $newTourId = $db->lastInsertId();
                $this->uploadGallery($db, $newTourId, $_FILES['gallery'] ?? null);
                // ----------------------------------------

                header("Location: index.php?msg=success");
            } elseif ($result === "duplicate") {
                echo "<script>alert('Lỗi: Mã tour đã tồn tại!'); window.history.back();</script>";
            } else {
                echo "<script>alert('Lỗi hệ thống: $result'); window.history.back();</script>";
            }
        }
    }

    // --- XÓA TOUR ---
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

    // --- HIỆN FORM SỬA ---
    public function edit() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $database = new Database();
            $db = $database->getConnection();
            $tourModel = new Tour($db);
            $tour = $tourModel->getById($id);
            $itineraries = $tourModel->getItinerary($id);
            
            if(!$tour) { echo "Không tìm thấy tour!"; die(); }

            require_once __DIR__ . '/../../views/tour/edit.php';
        }
    }

    // --- CẬP NHẬT TOUR (UPDATE) ---
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $database = new Database();
            $db = $database->getConnection();
            $tourModel = new Tour($db);

            // Xử lý ảnh đại diện mới
            $imageName = "";
            $target_dir = __DIR__ . "/../../public/uploads/";
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $imageName = time() . "_" . basename($_FILES["image"]["name"]);
                move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $imageName);
            }

            $tourData = [
                'code' => $_POST['code'],
                'name' => $_POST['name'],
                'type' => $_POST['type'],
                'highlight' => $_POST['highlight'] ?? '',
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
                // Cập nhật thêm ảnh Gallery nếu có
                $this->uploadGallery($db, $id, $_FILES['gallery'] ?? null);
                header("Location: index.php?msg=updated");
            } elseif ($result === "duplicate") {
                echo "<script>alert('Lỗi: Mã tour trùng lặp!'); window.history.back();</script>";
            } else {
                echo "<script>alert('Lỗi update!'); window.history.back();</script>";
            }
        }
    }

    // --- XEM CHI TIẾT TOUR ---
    public function show() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $database = new Database();
            $db = $database->getConnection();
            $tourModel = new Tour($db);
            
            // 1. Lấy thông tin cơ bản
            $tour = $tourModel->getById($id);
            
            // 2. Lấy lịch trình chi tiết
            $itineraries = $tourModel->getItinerary($id);
            
            $gallery = $tourModel->getGallery($id);

            if (!$tour) {
                echo "Không tìm thấy tour!"; die();
            }

            // Gọi giao diện chi tiết
            require_once __DIR__ . '/../../views/tour/detail.php';
        }
    }

    // --- HÀM PHỤ: UPLOAD GALLERY (Dùng chung cho Store và Update) ---
    private function uploadGallery($db, $tourId, $files) {
        if (!empty($files['name'][0])) {
            $target_dir = __DIR__ . "/../../public/uploads/";
            $total = count($files['name']);
            
            // Chuẩn bị câu lệnh SQL
            $stmtGal = $db->prepare("INSERT INTO tour_gallery (tour_id, image_path) VALUES (?, ?)");
            
            for($i=0; $i<$total; $i++) {
                if($files['error'][$i] == 0) {
                    $galName = time() . "_" . $i . "_gal_" . basename($files['name'][$i]);
                    if(move_uploaded_file($files['tmp_name'][$i], $target_dir . $galName)) {
                        // Chỉ insert nếu upload thành công
                        try {
                            $stmtGal->execute([$tourId, $galName]);
                        } catch(Exception $e) {
                            // Bỏ qua lỗi nếu insert thất bại (ví dụ chưa có bảng gallery)
                        }
                    }
                }
            }
        }
    }
    // --- QUẢN LÝ GIÁ THEO MÙA ---
    public function prices() {
        require_once __DIR__ . '/../models/TourPrice.php';
        $tour_id = $_GET['id'] ?? null;
        
        if($tour_id) {
            $db = (new Database())->getConnection();
            $tour = (new Tour($db))->getById($tour_id); // Lấy thông tin tour để hiện tên
            $prices = (new TourPrice($db))->getByTourId($tour_id); // Lấy danh sách giá
            
            require_once __DIR__ . '/../../views/tour/prices.php';
        }
    }

    public function priceStore() {
        require_once __DIR__ . '/../models/TourPrice.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = (new Database())->getConnection();
            $model = new TourPrice($db);
            $data = [
                ':tid' => $_POST['tour_id'],
                ':name' => $_POST['name'],
                ':start' => $_POST['start_date'],
                ':end' => $_POST['end_date'],
                ':padult' => $_POST['price_adult'],
                ':pchild' => $_POST['price_child']
            ];
            $model->create($data);
            header("Location: index.php?action=tour-prices&id=".$_POST['tour_id']);
        }
    }

    public function priceDelete() {
        require_once __DIR__ . '/../models/TourPrice.php';
        $id = $_GET['id'];
        $tour_id = $_GET['tour_id']; // Để quay lại đúng trang
        
        $db = (new Database())->getConnection();
        (new TourPrice($db))->delete($id);
        header("Location: index.php?action=tour-prices&id=".$tour_id);
    }

} // Kết thúc class TourController
?>