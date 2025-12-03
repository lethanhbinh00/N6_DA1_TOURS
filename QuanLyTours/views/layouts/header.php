<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel ERP System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root { --sidebar-bg: #2c3e50; --sidebar-color: #ecf0f1; --primary-color: #3498db; --active-bg: #34495e; }
        body { font-family: 'Inter', sans-serif; background-color: #f4f6f9; font-size: 0.9rem; }
        
        /* Sidebar Styling - Z-Index 2000 để luôn nổi trên cùng */
        .sidebar { 
            width: 260px; 
            height: 100vh; 
            position: fixed; 
            top: 0; left: 0;
            background: var(--sidebar-bg); 
            color: var(--sidebar-color); 
            overflow-y: auto; 
            z-index: 2000; 
            transition: all 0.3s;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        
        .sidebar-header { padding: 20px; font-size: 1.2rem; font-weight: bold; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .nav-item { border-bottom: 1px solid rgba(255,255,255,0.05); }
        
        /* Link Styling */
        .nav-link { 
            color: #bdc3c7; padding: 12px 20px; display: flex; 
            justify-content: space-between; align-items: center; 
            text-decoration: none; cursor: pointer; width: 100%;
        }
        .nav-link:hover, .nav-link.active { color: #fff; background: var(--active-bg); }
        .nav-link i { width: 25px; text-align: center; }
        
        /* Submenu */
        .submenu { background: #233342; display: none; }
        .submenu.show { display: block; }
        .submenu .nav-link { padding-left: 50px; font-size: 0.85rem; }
        
        /* Main Content - Đẩy sang phải */
        .main-content { margin-left: 260px; padding: 20px; position: relative; z-index: 1; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header"><i class="fas fa-globe-americas me-2"></i>FOURCHICKENS</div>
    
    <?php $act = $_GET['action'] ?? ''; ?>

    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="index.php?action=dashboard" class="nav-link <?= ($act=='dashboard'||$act=='')?'active':'' ?>">
                <div class="d-flex align-items-center"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</div>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link" href="#" onclick="toggleMenu('menu-tour'); return false;">
                <div class="d-flex align-items-center"><i class="fas fa-suitcase me-2"></i> Quản lý Tour</div>
                <i class="fas fa-chevron-down small"></i>
            </a>
            <div id="menu-tour" class="submenu <?= (strpos($act, 'tour')!==false || strpos($act, 'index')!==false)?'show':'' ?>">
                <a href="index.php?action=index" class="nav-link text-white">Danh sách Tour</a>
                <a href="#" class="nav-link">Tour Theo Yêu Cầu</a>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="#" onclick="toggleMenu('menu-booking'); return false;">
                <div class="d-flex align-items-center"><i class="fas fa-shopping-cart me-2"></i> Booking & Sales</div>
                <i class="fas fa-chevron-down small"></i>
            </a>
            <div id="menu-booking" class="submenu <?= (strpos($act, 'booking')!==false || strpos($act, 'customer')!==false)?'show':'' ?>">
                <a href="index.php?action=booking-create" class="nav-link">Tạo Booking Mới</a>
                <a href="index.php?action=booking-list" class="nav-link">Danh sách Đơn hàng</a>
                <a href="index.php?action=customer-list" class="nav-link">Quản lý Khách hàng</a>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="#" onclick="toggleMenu('menu-ops'); return false;">
                <div class="d-flex align-items-center"><i class="fas fa-cogs me-2"></i> Điều hành</div>
                <i class="fas fa-chevron-down small"></i>
            </a>
            <div id="menu-ops" class="submenu <?= (strpos($act, 'guide')!==false || strpos($act, 'departure')!==false || strpos($act, 'carbooking')!==false)?'show':'' ?>">
                <a href="index.php?action=guide-list" class="nav-link <?= (strpos($act,'guide')!==false)?'active':'' ?>">Quản lý HDV</a>
                <a href="index.php?action=departure-list" class="nav-link <?= (strpos($act,'departure')!==false)?'active':'' ?>">Lịch khởi hành</a>
                <a href="index.php?action=carbooking-list" class="nav-link <?= (strpos($act,'carbooking')!==false)?'active':'' ?>">Đặt dịch vụ (Xe/KS)</a>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="#" onclick="toggleMenu('menu-fin'); return false;">
                <div class="d-flex align-items-center"><i class="fas fa-file-invoice-dollar me-2"></i> Tài chính & NCC</div>
                <i class="fas fa-chevron-down small"></i>
            </a>
            <div id="menu-fin" class="submenu <?= (strpos($act, 'supplier')!==false)?'show':'' ?>">
                <a href="index.php?action=supplier-list" class="nav-link">Nhà cung cấp</a>
                <a href="#" class="nav-link">Công nợ</a>
            </div>
        </li>

        <li class="nav-item"><a href="#" class="nav-link"><div class="d-flex align-items-center"><i class="fas fa-chart-line me-2"></i> Báo cáo</div></a></li>
    </ul>
</div>

<div class="main-content">