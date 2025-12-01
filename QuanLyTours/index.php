<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/server/commons/env.php'; 
require_once __DIR__ . '/server/commons/function.php'; 


require_once __DIR__ . '/server/controllers/DashboardController.php';
require_once __DIR__ . '/server/controllers/TourController.php';
require_once __DIR__ . '/server/controllers/BookingController.php';
require_once __DIR__ . '/server/controllers/CustomerController.php';
require_once __DIR__ . '/server/controllers/GuideController.php';
require_once __DIR__ . '/server/controllers/SupplierController.php';

$dashboardController = new DashboardController();
$tourController = new TourController();
$bookingController = new BookingController();
$cusController = new CustomerController();
$guideController = new GuideController();
$supController = new SupplierController();

$action = $_GET['action'] ?? 'dashboard';

switch ($action) {
    case 'dashboard': $dashboardController->index(); break;

    // TOUR
    case 'index':       $tourController->index(); break;
    case 'store':       $tourController->store(); break;
    case 'delete':      $tourController->delete(); break;
    case 'edit':        $tourController->edit(); break;
    case 'update':      $tourController->update(); break;
    case 'tour-detail': $tourController->show(); break;
    case 'tour-prices':       $tourController->prices(); break;
    case 'tour-price-store':  $tourController->priceStore(); break;
    case 'tour-price-delete': $tourController->priceDelete(); break;

    // BOOKING
    case 'booking-list':    $bookingController->index(); break;
    case 'booking-create':  $bookingController->create(); break;
    case 'booking-store':   $bookingController->store(); break;
    case 'booking-edit':    $bookingController->edit(); break;
    case 'booking-update':  $bookingController->update(); break;
    case 'booking-status':  $bookingController->status(); break;
    case 'booking-delete':  $bookingController->delete(); break;
    case 'booking-deposit': $bookingController->deposit(); break; // [MỚI]

    // CUSTOMER
    case 'customer-list':   $cusController->index(); break;
    case 'customer-store':  $cusController->store(); break;
    case 'customer-detail': $cusController->detail(); break;
    case 'customer-delete': $cusController->delete(); break;

    // GUIDE
    case 'guide-list':   $guideController->index(); break;
    case 'guide-store':  $guideController->store(); break;
    case 'guide-delete': $guideController->delete(); break;

    // SUPPLIER
    case 'supplier-list':   $supController->index(); break;
    case 'supplier-store':  $supController->store(); break;
    case 'supplier-delete': $supController->delete(); break;

    default: $dashboardController->index(); break;
}
?>