<?php
// 1. CẤU HÌNH & SESSION
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Khởi động session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. REQUIRE CÁC FILE CẦN THIẾT
require_once __DIR__ . '/server/commons/env.php'; 
require_once __DIR__ . '/server/commons/function.php'; 

// Require Controllers
require_once __DIR__ . '/server/controllers/AuthController.php';
require_once __DIR__ . '/server/controllers/DashboardController.php';
require_once __DIR__ . '/server/controllers/TourController.php';
require_once __DIR__ . '/server/controllers/BookingController.php';
require_once __DIR__ . '/server/controllers/CustomerController.php';
require_once __DIR__ . '/server/controllers/GuideController.php';
require_once __DIR__ . '/server/controllers/SupplierController.php';
require_once __DIR__ . '/server/controllers/UserController.php'; 

// 3. KHỞI TẠO CONTROLLERS
$authController = new AuthController();
$dashboardController = new DashboardController();
$tourController = new TourController();
$bookingController = new BookingController();
$cusController = new CustomerController();
$guideController = new GuideController();
$supController = new SupplierController();
$userController = new UserController(); // [ĐÃ SỬA LỖI UNDEFINED]

// 4. LẤY ACTION (PHẢI LÀM BƯỚC NÀY TRƯỚC KHI CHECK LOGIN)
$action = $_GET['action'] ?? 'dashboard';

// 5. KIỂM TRA ĐĂNG NHẬP (BẢO VỆ HỆ THỐNG)
// Nếu chưa đăng nhập VÀ không phải đang ở trang login/check-login
if (!isset($_SESSION['user_id'])) {
    if ($action !== 'login' && $action !== 'check-login') {
        header("Location: index.php?action=login");
        exit();
    }
}

// 6. ĐIỀU HƯỚNG (ROUTER)
switch ($action) {
    // --- AUTH ---
    case 'login':       $authController->loginPage(); break;
    case 'check-login': $authController->handleLogin(); break;
    case 'logout':      $authController->logout(); break;

    // --- DASHBOARD ---
    case 'dashboard':   $dashboardController->index(); break;

    // --- USER (TÀI KHOẢN) ---
    case 'user-list':   $userController->index(); break;
    case 'user-store':  $userController->store(); break;
    case 'user-detail': $userController->detail(); break;
    case 'user-edit':   $userController->edit(); break;
    case 'user-update': $userController->update(); break;
    case 'user-delete': $userController->delete(); break;

    // --- TOUR ---
    case 'index':       $tourController->index(); break;
    case 'store':       $tourController->store(); break;
    case 'delete':      $tourController->delete(); break;
    case 'edit':        $tourController->edit(); break;
    case 'update':      $tourController->update(); break;
    case 'tour-detail': $tourController->show(); break;
    case 'tour-prices':       $tourController->prices(); break;
    case 'tour-price-store':  $tourController->priceStore(); break;
    case 'tour-price-delete': $tourController->priceDelete(); break;

    // --- BOOKING ---
    case 'booking-list':    $bookingController->index(); break;
    case 'booking-create':  $bookingController->create(); break;
    case 'booking-store':   $bookingController->store(); break;
    case 'booking-edit':    $bookingController->edit(); break;
    case 'booking-update':  $bookingController->update(); break;
    case 'booking-status':  $bookingController->status(); break;
    case 'booking-deposit': $bookingController->deposit(); break;
    case 'booking-delete':  $bookingController->delete(); break;
    case 'booking-detail':  $bookingController->detail(); break;
    // ... Trong phần Booking ...
    case 'booking-pax':      $bookingController->pax(); break;       // Xem giao diện Pax
    case 'booking-pax-store': $bookingController->paxStore(); break; // Lưu khách
    case 'booking-pax-del':   $bookingController->paxDelete(); break; // Xóa khách
    // ... Trong phần Booking ...
    case 'booking-pax-import': $bookingController->paxImport(); break; // [MỚI]
    
    // ...
    case 'booking-invoice': 
        $bookingController->invoice(); 
        break;
    // ...
    
    // Booking Operations
    case 'booking-ops':     $bookingController->operations(); break;
    case 'booking-pax-add': $bookingController->paxStore(); break;
    case 'booking-pax-del': $bookingController->paxDelete(); break;
    case 'booking-srv-add': $bookingController->serviceStore(); break;
    case 'booking-srv-del': $bookingController->serviceDelete(); break;

    // --- CUSTOMER ---
    case 'customer-list':   $cusController->index(); break;
    case 'customer-store':  $cusController->store(); break;
    case 'customer-edit':   $cusController->edit(); break;
    case 'customer-update': $cusController->update(); break;
    case 'customer-detail': $cusController->detail(); break;
    case 'customer-delete': $cusController->delete(); break;

    // --- GUIDE ---
    case 'guide-list':   $guideController->index(); break;
    case 'guide-store':  $guideController->store(); break;
    case 'guide-delete': $guideController->delete(); break;

    // --- SUPPLIER ---
    case 'supplier-list':   $supController->index(); break;
    case 'supplier-store':  $supController->store(); break;
    case 'supplier-delete': $supController->delete(); break;

    case 'receipt':
        // Xử lý In Phiếu thu lẻ
        $bookingController->receipt();
        break;

    case 'payment-delete':
        // Xử lý Xóa giao dịch thanh toán (Cần RBAC)
        // Nếu bạn đã có hàm checkRole(), hãy sử dụng nó ở đây:
        // checkRole(['admin']); 
        $bookingController->paymentDelete();
        break;

    // --- DEFAULT ---
    default:
        $dashboardController->index();
        break;
}
?>