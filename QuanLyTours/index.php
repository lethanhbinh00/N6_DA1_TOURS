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

$dashboardController = new DashboardController();
$guideController = new GuideController();
$cusController = new CustomerController();
$tourController = new TourController();
$bookingController = new BookingController();

$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
    case 'dashboard':
        $dashboardController->index();
        break;
    case '/':
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
    case 'guide-list':
        $guideController->index();
        break;
        
    case 'guide-store':
        $guideController->store();
        break;
        
    case 'guide-delete':
        $guideController->delete();
        break;
    default:
        $dashboardController->index();
        break;
}
?>