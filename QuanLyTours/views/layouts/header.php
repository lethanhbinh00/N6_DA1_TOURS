<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel ERP System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        :root {
            --sidebar-bg: #2c3e50;
            --sidebar-color: #ecf0f1;
            --primary-color: #3498db;
            --active-bg: #34495e;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
            font-size: 0.9rem;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: var(--sidebar-bg);
            color: var(--sidebar-color);
            overflow-y: auto;
            z-index: 2000;
            transition: all 0.3s;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .brand-text {
            font-size: 1.2rem;
            font-weight: bold;
            display: block;
        }

        .nav-link {
            color: #bdc3c7;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-decoration: none;
            cursor: pointer;
            transition: 0.2s;
        }

        .nav-link:hover,
        .nav-link.active {
            color: #fff;
            background: var(--active-bg);
        }

        /* Submenu Styling */
        .submenu {
            background: #233342;
            display: none;
        }

        .submenu.show {
            display: block;
        }

        .submenu .nav-link {
            padding-left: 50px;
            font-size: 0.85rem;
        }

        /* Main Content Styling */
        .main-content {
            margin-left: 260px;
            padding: 20px;
            min-height: 100vh;
        }

        /* Select2 Bootstrap Patch */
        .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1px solid #dee2e6 !important;
            display: flex;
            align-items: center;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <span class="brand-text text-uppercase"><i class="fas fa-globe-americas me-2"></i>FourChickens</span>

            <?php if (isset($_SESSION['user_name'])): ?>
                <div class="mt-2 pt-2 border-top border-secondary small text-white-50">
                    <div class="d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-user-circle me-1"></i> <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                        <a href="index.php?action=logout" class="btn btn-sm btn-danger py-0 px-2" style="font-size: 10px;">Thoát</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php $act = $_GET['action'] ?? ''; ?>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="index.php?action=dashboard" class="nav-link <?= ($act == 'dashboard' || $act == '') ? 'active' : '' ?>">
                    <div><i class="fas fa-tachometer-alt me-2"></i> Dashboard</div>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0)" onclick="toggleMenu('menu-tour')">
                    <div><i class="fas fa-suitcase me-2"></i> Quản lý Tour</div>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div id="menu-tour" class="submenu <?= (strpos($act, 'tour') !== false || $act == 'index') ? 'show' : '' ?>">
                    <a href="index.php?action=index" class="nav-link <?= ($act == 'index') ? 'active' : '' ?>">Danh sách Tour</a>
                    <a href="#" class="nav-link">Tour Theo Yêu Cầu</a>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0)" onclick="toggleMenu('menu-booking')">
                    <div><i class="fas fa-shopping-cart me-2"></i> Booking & Sales</div>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div id="menu-booking" class="submenu <?= (strpos($act, 'booking') !== false || strpos($act, 'customer') !== false) ? 'show' : '' ?>">
                    <a href="index.php?action=booking-create" class="nav-link <?= ($act == 'booking-create') ? 'active' : '' ?>">Tạo Booking Mới</a>
                    <a href="index.php?action=booking-list" class="nav-link <?= ($act == 'booking-list') ? 'active' : '' ?>">Quản lý Booking</a>
                    <a href="index.php?action=customer-list" class="nav-link <?= ($act == 'customer-list') ? 'active' : '' ?>">Quản lý Khách hàng</a>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0)" onclick="toggleMenu('menu-ops')">
                    <div><i class="fas fa-cogs me-2"></i> Điều hành</div>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div id="menu-ops" class="submenu <?= (strpos($act, 'guide') !== false || $act == 'departure-list' || $act == 'car-booking') ? 'show' : '' ?>">
                    <a href="index.php?action=guide-list" class="nav-link <?= ($act == 'guide-list') ? 'active' : '' ?>">Quản lý HDV</a>
                    <a href="index.php?action=departure-list" class="nav-link <?= ($act == 'departure-list') ? 'active' : '' ?>">Lịch khởi hành</a>
                    <a href="index.php?action=car-booking" class="nav-link <?= ($act == 'car-booking') ? 'active' : '' ?>">Đặt dịch vụ (Xe/KS)</a>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0)" onclick="toggleMenu('menu-fin')">
                    <div><i class="fas fa-file-invoice-dollar me-2"></i> Tài chính & NCC</div>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div id="menu-fin" class="submenu <?= (strpos($act, 'supplier') !== false || $act == 'payables') ? 'show' : '' ?>">
                    <a href="index.php?action=supplier-list" class="nav-link <?= ($act == 'supplier-list') ? 'active' : '' ?>">Nhà cung cấp</a>
                    <a href="index.php?action=payables" class="nav-link <?= ($act == 'payables') ? 'active' : '' ?>">Công nợ</a>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0)" onclick="toggleMenu('menu-report')">
                    <div><i class="fas fa-chart-line me-2"></i> Báo cáo</div>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div id="menu-report" class="submenu <?= (strpos($act, 'reports-') !== false) ? 'show' : '' ?>">
                    <a href="index.php?action=reports-profitability" class="nav-link <?= ($act == 'reports-profitability') ? 'active' : '' ?>">Báo cáo Lãi/Lỗ</a>
                </div>
            </li>

            <li class="nav-item">
                <a href="index.php?action=user-list" class="nav-link <?= ($act == 'user-list') ? 'active' : '' ?>">
                    <div><i class="fas fa-users-cog me-2"></i> Quản lý Tài khoản</div>
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">

    <script>
        function toggleMenu(id) {
            const menu = document.getElementById(id);
            const isShown = menu.classList.contains('show');
            
            // Đóng tất cả các menu đang mở khác để tạo hiệu ứng gọn gàng
            document.querySelectorAll('.submenu').forEach(sub => sub.classList.remove('show'));
            
            // Nếu menu click chưa mở thì mới mở
            if (!isShown) {
                menu.classList.add('show');
            }
        }
    </script>