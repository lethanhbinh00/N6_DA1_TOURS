<?php
require_once __DIR__ . '/../models/Departure.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Booking.php';

class DepartureController
{
    private $model;
    private $db;

    public function __construct()
    {
        // Ensure we pass a PDO connection into the model (Database class used elsewhere)
        $database = new Database();
        $db = $database->getConnection();
        $this->db = $db;
        $this->model = new Departure($db);
    }

    /* ========= LIST ========= */
    public function index()
    {
        $keyword = $_GET['keyword'] ?? '';
        $status = $_GET['status'] ?? '';
        $departures = $this->model->getAllFiltered($keyword, $status);
        // compute attendance completion status for each departure
        $bookingModel = new Booking($this->db);
        foreach ($departures as &$d) {
            $pid = $d['id'];
            $passengers = $this->model->getPassengers($d['tour_id'], $d['start_date']);
            $attendance = $this->model->getAttendance($pid);
            $total = count($passengers);
            $present = 0;
            $late = 0;
            $absent = 0;
            foreach ($passengers as $p) {
                $bid = $p['id'];
                if (isset($attendance[$bid])) {
                    $s = strtolower(trim($attendance[$bid]['status'] ?? ''));
                    if ($s === 'present') $present++;
                    elseif ($s === 'late') $late++;
                    else $absent++;
                }
            }
            $d['attendance_summary'] = ['total' => $total, 'present' => $present, 'late' => $late, 'absent' => $absent];
            $d['attendance_complete'] = ($total > 0 && ($present + $late) === $total);

            // count bookings for this tour on that date and prepare booking-list link
            $tourId = $d['tour_id'] ?? null;
            $start = $d['start_date'] ?? null;
            $bookings = $bookingModel->getAll(null, null, $start, $start, $tourId);
            $d['booking_count'] = is_array($bookings) ? count($bookings) : 0;
            $d['booking_link'] = "index.php?action=booking-list&tour_id=" . urlencode($tourId) . "&date_from=" . urlencode($start) . "&date_to=" . urlencode($start);
        }
        unset($d);

        include __DIR__ . '/../../views/departures/index.php';
    }

    /* ========= CREATE PAGE ========= */
    public function create()
    {
        $tours = $this->model->getTours();
        $guides = $this->model->getGuides();
        include __DIR__ . '/../../views/departures/create.php';
    }

    /* ========= STORE ========= */
    public function store()
    {
        $this->model->create($_POST);
        $_SESSION['success'] = "Thêm lịch khởi hành thành công";
        header("Location: index.php?action=departure-list");
        exit;
    }

    /* ========= EDIT PAGE ========= */
    public function edit()
    {
        $id = $_GET['id'];
        $departure = $this->model->find($id);
        $tours     = $this->model->getTours();
        $guides    = $this->model->getGuides();

        include __DIR__ . '/../../views/departures/edit.php';
    }

    /* ========= DETAIL PAGE (HDV view) ========= */
    public function detail()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?action=departure-list');
            exit;
        }

        $departure = $this->model->find($id);
        if (!$departure) {
            $_SESSION['error'] = 'Không tìm thấy lịch trình';
            header('Location: index.php?action=departure-list');
            exit;
        }

        $tour = $this->model->getTours(); // lấy danh sách tour để map
        $itinerary = $this->model->getItinerary($departure['tour_id']);
        $passengers = $this->model->getPassengers($departure['tour_id'], $departure['start_date']);

        // load saved attendance records and compute a small summary
        $attendanceRecords = $this->model->getAttendance($id);
        $present = $late = $absent = 0;
        foreach ($passengers as $p) {
            $bid = $p['id'];
            if (isset($attendanceRecords[$bid])) {
                $s = strtolower(trim($attendanceRecords[$bid]['status'] ?? ''));
                if ($s === 'present') $present++;
                elseif ($s === 'late') $late++;
                else $absent++;
            }
        }
        $totalPassengers = count($passengers);
        $attendanceSummary = [
            'total' => $totalPassengers,
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
            'complete' => ($totalPassengers > 0 && ($present + $late) === $totalPassengers)
        ];

        // booking count/link for this departure
        $bookingModel = new Booking($this->db);
        $start = $departure['start_date'] ?? null;
        $tourId = $departure['tour_id'] ?? null;
        $bookings = $bookingModel->getAll(null, null, $start, $start, $tourId);
        $booking_count = is_array($bookings) ? count($bookings) : 0;
        $booking_link = "index.php?action=booking-list&tour_id=" . urlencode($tourId) . "&date_from=" . urlencode($start) . "&date_to=" . urlencode($start);

        include __DIR__ . '/../../views/departures/detail.php';
    }

    /* ========= STORE REPORT/ATTENDANCE ========= */
    public function storeReport()
    {
        $depId = $_POST['departure_id'] ?? null;
        if (!$depId) {
            header('Location: index.php?action=departure-list');
            exit;
        }
        // Basic server-side validation
        $departure = $this->model->find($depId);
        if (!$departure) {
            $_SESSION['error'] = 'Lịch khởi hành không tồn tại';
            header('Location: index.php?action=departure-list');
            exit;
        }

        $content = trim($_POST['content'] ?? '');
        if (strlen($content) == 0) {
            $_SESSION['error'] = 'Vui lòng nhập nội dung báo cáo.';
            header('Location: index.php?action=departure-detail&id=' . $depId);
            exit;
        }

        // validate attendance structure if provided
        $attendance = $_POST['attendance'] ?? [];
        $validStatuses = ['present', 'late', 'absent'];
        if (!is_array($attendance)) {
            $_SESSION['error'] = 'Dữ liệu điểm danh không hợp lệ.';
            header('Location: index.php?action=departure-detail&id=' . $depId);
            exit;
        }

        foreach ($attendance as $booking_id => $row) {
            if (!ctype_digit((string)$booking_id)) {
                $_SESSION['error'] = 'ID khách không hợp lệ trong điểm danh.';
                header('Location: index.php?action=departure-detail&id=' . $depId);
                exit;
            }
            $status = $row['status'] ?? '';
            if (!in_array($status, $validStatuses, true)) {
                $_SESSION['error'] = 'Trạng thái điểm danh không hợp lệ.';
                header('Location: index.php?action=departure-detail&id=' . $depId);
                exit;
            }
        }

        // All validations passed — proceed to save
        $reportSaved = $this->model->saveReport($_POST);
        $attendanceSaved = true;
        if (!empty($attendance)) {
            $attendanceSaved = $this->model->saveAttendance($depId, $attendance);
        }

        if ($reportSaved && $attendanceSaved) {
            $_SESSION['success'] = 'Báo cáo và điểm danh đã được lưu';
        } else {
            $err = $this->model->getLastError();
            if (!empty($err)) {
                $_SESSION['error'] = 'Lưu thất bại: ' . $err;
            } else {
                $_SESSION['error'] = 'Lưu thất bại';
            }
        }

        header('Location: index.php?action=departure-detail&id=' . $depId);
        exit;
    }

    /* ========= UPDATE ========= */
    public function update()
    {
        $this->model->update($_POST);
        $_SESSION['success'] = "Cập nhật lịch khởi hành thành công";
        header("Location: index.php?action=departure-list");
        exit;
    }

    /* ========= DELETE ========= */
    public function delete()
    {
        $this->model->delete($_GET['id']);
        $_SESSION['success'] = "Đã xóa lịch khởi hành";
        header("Location: index.php?action=departure-list");
        exit;
    }
}
