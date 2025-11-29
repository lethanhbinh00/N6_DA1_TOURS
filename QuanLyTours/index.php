<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/server/commons/env.php'; 
require_once __DIR__ . '/server/commons/function.php'; 
require_once __DIR__ . '/server/controllers/TourController.php';
require_once __DIR__ . '/server/controllers/BookingController.php';
require_once __DIR__ . '/server/controllers/CustomerController.php';
require_once __DIR__ . '/server/controllers/GuideController.php';
require_once __DIR__ . '/server/controllers/DashboardController.php';
require_once __DIR__ . '/server/controllers/SupplierController.php';
$supController = new SupplierController();
$dashboardController = new DashboardController();
$guideController = new GuideController();
$cusController = new CustomerController();
$tourController = new TourController();
$bookingController = new BookingController();

$action = $_GET['action'] ?? 'index';

switch ($action) {
    // --- TRANG CHỦ (DASHBOARD) ---
    case 'dashboard':
        $dashboardController->index();
        break;

    // --- QUẢN LÝ TOUR ---
    case 'index': // Danh sách Tour
    case '/':     // Mặc định
        $tourController->index();
        break;
        
    case 'store':
        $tourController->store();
        break;
        
    case 'delete':
        $tourController->delete();
        break;
        
    case 'edit':
        $tourController->edit();
        break;
        
    case 'update':
        $tourController->update();
        break;

    // --- QUẢN LÝ BOOKING ---
    case 'booking-create':
        $bookingController->create(); 
        break;

    case 'booking-store':
        $bookingController->store(); 
        break;
        
    case 'booking-list':
        $bookingController->index();
        break;
        
    case 'booking-status':
        $bookingController->status();
        break;

    case 'booking-delete':
        $bookingController->delete();
        break;

    // --- QUẢN LÝ KHÁCH HÀNG ---
    case 'customer-list':
        $cusController->index();
        break;
        
    case 'customer-store':
        $cusController->store();
        break;
        
    case 'customer-detail':
        $cusController->detail();
        break;
        
    case 'customer-delete':
        $cusController->delete();
        break;

    // --- QUẢN LÝ HDV ---
    case 'guide-list':
        $guideController->index();
        break;
        
    case 'guide-store':
        $guideController->store();
        break;
        
    case 'guide-delete':
        $guideController->delete();
        break;
    case 'tour-detail': // Action mới
        $tourController->show();
        break;
    case 'supplier-list': $supController->index(); break;
    case 'supplier-store': $supController->store(); break;
    case 'supplier-delete': $supController->delete(); break;

    // --- GIÁ THEO MÙA ---
    case 'tour-prices': $tourController->prices(); break;
    case 'tour-price-store': $tourController->priceStore(); break;
    case 'tour-price-delete': $tourController->priceDelete(); break;
    case 'booking-edit':    // Hiển thị form sửa
        $bookingController->edit();
        break;

    case 'booking-update':  // Lưu cập nhật
        $bookingController->update();
        break;

    // --- MẶC ĐỊNH ---
    default:
        // Nếu không tìm thấy action, quay về Dashboard
        $dashboardController->index();
        break;
}
?>