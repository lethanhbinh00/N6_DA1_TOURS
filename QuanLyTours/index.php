<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) session_start();

// 2. REQUIRE BASE
require_once __DIR__ . '/server/commons/env.php';
require_once __DIR__ . '/server/commons/function.php';

// 3. REQUIRE CONTROLLERS
require_once __DIR__ . '/server/controllers/AuthController.php';
require_once __DIR__ . '/server/controllers/DashboardController.php';
require_once __DIR__ . '/server/controllers/TourController.php';
require_once __DIR__ . '/server/controllers/BookingController.php';
require_once __DIR__ . '/server/controllers/CustomerController.php';
require_once __DIR__ . '/server/controllers/GuideController.php';
require_once __DIR__ . '/server/controllers/SupplierController.php';
require_once __DIR__ . '/server/controllers/UserController.php';

// (THÊM 2 CONTROLLER MỚI)
require_once __DIR__ . '/server/controllers/CarBookingController.php';
require_once __DIR__ . '/server/controllers/DepartureController.php';

// 4. KHỞI TẠO CONTROLLER
$authController = new AuthController();
$dashboardController = new DashboardController();
$tourController = new TourController();
$bookingController = new BookingController();
$cusController = new CustomerController();
$guideController = new GuideController();
$supController = new SupplierController();
$userController = new UserController();

$carBookingController = new CarBookingController();
$departureController  = new DepartureController();

// 5. LẤY ACTION
$action = $_GET['action'] ?? 'dashboard';

// 6. CHECK LOGIN
if (!isset($_SESSION['user_id']) && !in_array($action, ['login', 'check-login'])) {
    header("Location: index.php?action=login");
    exit();
}

// 7. ROUTER CHÍNH
switch ($action) {

    // AUTH
    case 'login':       $authController->loginPage(); break;
    case 'check-login': $authController->handleLogin(); break;
    case 'logout':      $authController->logout(); break;

    // DASHBOARD
    case 'dashboard':   $dashboardController->index(); break;

    // USER
    case 'user-list':   $userController->index(); break;
    case 'user-store':  $userController->store(); break;
    case 'user-detail': $userController->detail(); break;
    case 'user-edit':   $userController->edit(); break;
    case 'user-update': $userController->update(); break;
    case 'user-delete': $userController->delete(); break;

    // TOUR
    case 'index':       $tourController->index(); break;
    case 'store':       $tourController->store(); break;
    case 'delete':      $tourController->delete(); break;
    case 'edit':        $tourController->edit(); break;
    case 'update':
        $tourController->update();
        break;

    case 'tour-detail':
        $tourController->show();
        break;
    case 'tour-prices':
        $tourController->prices();
        break;
    case 'tour-price-store':
        $tourController->priceStore();
        break;
    case 'tour-price-delete':
        $tourController->priceDelete();
        break;

    // BOOKING
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
    case 'booking-pax-del':
        $bookingController->paxDelete();
        break; // Xóa khách

    // ...
    case 'booking-invoice': 
        $bookingController->invoice();
        break;

    case 'booking-ops':
        $bookingController->operations();
        break;
    case 'booking-srv-add':
        $bookingController->serviceStore();
        break;
    case 'booking-srv-del':
        $bookingController->serviceDelete();
        break;

    // CUSTOMER
    case 'customer-list':   $cusController->index(); break;
    case 'customer-store':  $cusController->store(); break;
    case 'customer-edit':   $cusController->edit(); break;
    case 'customer-update': $cusController->update(); break;
    case 'customer-detail': $cusController->detail(); break;
    case 'customer-delete': $cusController->delete(); break;
    case 'booking-pax-import': 
    $bookingController->paxImport(); 
    break;
    case 'booking-pax-delete-all': 
    $bookingController->paxDeleteAll(); 
    break;

    // GUIDE
    case 'guide-list':   $guideController->index(); break;
    case 'guide-store':  $guideController->store(); break;
    case 'guide-delete': $guideController->delete(); break;

    // SUPPLIER
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

    case 'reports-profitability':
        // Xử lý Báo cáo Lãi/Lỗ
        $bookingController->profitabilityReport();
        break;
        
    case 'payables':
        // Xử lý Trang Công nợ (Phải trả)
        // LƯU Ý: Bạn cần tạo hàm payablesIndex() trong BookingController.php
        $bookingController->payablesIndex(); 
        break;
        case 'assign-guide':
        // Xử lý Phân công HDV
        $bookingController->assignGuide();
        break;
        // index.php
case 'update-supplier-payment':
    // Gọi hàm xử lý trong BookingController
    $bookingController->updateSupplierPayment();
    break;
    // index.php
case 'create-supplier-payment':
    $bookingController->showSupplierPaymentForm(); // Hiển thị form
    break;
case 'store-supplier-payment':
    $bookingController->storeSupplierPayment(); // Lưu vào DB
    break;
    // --- DEFAULT ---
    case 'update-supplier-payment':
    $bookingController->updateSupplierPayment();
    break;
    case 'update-pax-status-ajax':
    $bookingController->updatePaxStatusAjax();
    break;
    case 'store-tour-log':
        $bookingController->storeTourLog();
        break;
        case 'delete-tour-log':
        $bookingController->deleteTourLog();
        break;
    // ...

    // CAR BOOKING & DEPARTURES
    case 'car-booking':
        $carBookingController->index();
        break;
    case 'car-booking-create':
        $carBookingController->create();
        break;
    case 'car-booking-store':
        $carBookingController->store();
        break;
    case 'car-booking-edit':
        $carBookingController->edit();
        break;
    case 'car-booking-update':
        $carBookingController->update();
        break;
    case 'car-booking-delete':
        $carBookingController->delete();
        break;

    case 'departure-list':
        $departureController->index();
        break;
    case 'departure-create':
        $departureController->create();
        break;
    case 'departure-store':
        $departureController->store();
        break;
    case 'departure-edit':
        $departureController->edit();
        break;
    case 'departure-update':
        $departureController->update();
        break;
    case 'departure-delete':
        $departureController->delete();
        break;
    case 'departure-detail':
        $departureController->detail();
        break;
    case 'departure-report-store':
        $departureController->storeReport();
        break;


    // DEFAULT
    default:
        $dashboardController->index();
        break;
}
