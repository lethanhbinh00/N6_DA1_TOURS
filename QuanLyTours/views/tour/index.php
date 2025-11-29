<?php
// BẮT ĐẦU PHP: Kiểm tra biến $tours từ Controller truyền sang
// Nếu chưa có, gán mảng rỗng để không bị lỗi giao diện khi chạy thử
if (!isset($tours)) $tours = [];
?>
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
        :root {
            --sidebar-bg: #2c3e50;
            --sidebar-color: #ecf0f1;
            --primary-color: #3498db;
            --active-bg: #34495e;
        }
        body { font-family: 'Inter', sans-serif; background-color: #f4f6f9; font-size: 0.9rem; }
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            background: var(--sidebar-bg);
            color: var(--sidebar-color);
            overflow-y: auto;
            z-index: 1000;
        }
        .sidebar-header { padding: 20px; font-size: 1.2rem; font-weight: bold; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .nav-item { border-bottom: 1px solid rgba(255,255,255,0.05); }
        .nav-link { color: #bdc3c7; padding: 12px 20px; display: flex; justify-content: space-between; align-items: center; }
        .nav-link:hover, .nav-link.active { color: #fff; background: var(--active-bg); text-decoration: none; }
        .nav-link i { width: 25px; }
        .submenu { background: #233342; display: none; }
        .submenu .nav-link { padding-left: 50px; font-size: 0.85rem; }
        .main-content { margin-left: 260px; padding: 20px; }
        .nav-tabs .nav-link { color: #495057; font-weight: 500; }
        .nav-tabs .nav-link.active { color: var(--primary-color); border-top: 3px solid var(--primary-color); }
        .form-label { font-weight: 600; font-size: 0.85rem; color: #34495e; }
        .accordion-button:not(.collapsed) { color: var(--primary-color); background-color: #e7f1ff; }
        .timeline-step { border-left: 2px solid #e9ecef; padding-left: 20px; margin-left: 10px; padding-bottom: 20px; position: relative; }
        .timeline-step::before { content: ''; width: 12px; height: 12px; background: var(--primary-color); border-radius: 50%; position: absolute; left: -7px; top: 5px; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header"><i class="fas fa-globe-americas me-2"></i>FOURCHICKENS</div>
    <ul class="nav flex-column">
        <li class="nav-item"><a href="index.php?action=dashboard" class="nav-link active"><div class="d-flex align-items-center"><i class="fas fa-tachometer-alt"></i> Dashboard</div></a></li>
        
        <li class="nav-item">
            <a href="#" class="nav-link" onclick="toggleMenu('menu-tour')">
                <div class="d-flex align-items-center"><i class="fas fa-suitcase"></i> Quản lý Tour</div>
                <i class="fas fa-chevron-down small"></i>
            </a>
            <div id="menu-tour" class="submenu" style="display: block;"> <a href="index.php?action=index" class="nav-link text-white">Danh sách Tour</a>
                <a href="#" class="nav-link">Tour Theo Yêu Cầu</a>
                <a href="#" class="nav-link">Quản lý Phiên bản (Mùa/KM)</a>
            </div>
        </li>

        <li class="nav-item">
            <a href="#" class="nav-link" onclick="toggleMenu('menu-booking')">
                <div class="d-flex align-items-center"><i class="fas fa-shopping-cart"></i> Booking & Sales</div>
                <i class="fas fa-chevron-down small"></i>
            </a>
            <div id="menu-booking" class="submenu">
                <a href="index.php?action=booking-create" class="nav-link">Tạo Booking Mới</a>
                
                <a href="index.php?action=booking-list" class="nav-link">Danh sách Đơn hàng</a>
                <a href="index.php?action=customer-list" class="nav-link">Quản lý Khách hàng</a>
            </div>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link" onclick="toggleMenu('menu-ops')">
                <div class="d-flex align-items-center">
                    <i class="fas fa-cogs me-2"></i> Điều hành
                </div>
                <i class="fas fa-chevron-down small"></i>
            </a>

            <div id="menu-ops" class="submenu">
                <a href="index.php?action=guide-list" class="nav-link">
                    <i class="fas fa-user-tie me-2"></i> Quản lý HDV
                </a>

                <a href="#" class="nav-link">
                    <i class="fas fa-calendar-alt me-2"></i> Lịch khởi hành
                </a>
                <a href="#" class="nav-link">
                    <i class="fas fa-bus me-2"></i> Đặt dịch vụ (Xe/KS)
                </a>
            </div>
        </li>        
        <li class="nav-item"><a href="#" class="nav-link"><div class="d-flex align-items-center"><i class="fas fa-file-invoice-dollar"></i> Tài chính & NCC</div></a></li>
        <li class="nav-item"><a href="#" class="nav-link"><div class="d-flex align-items-center"><i class="fas fa-chart-line"></i> Báo cáo</div></a></li>
    </ul>
</div>

<div class="main-content">
    
    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>Thao tác thành công!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-secondary">Danh sách Tour</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tourModalFull">
            <i class="fas fa-plus me-2"></i>Thêm Tour Mới
        </button>
    </div>

    <div class="card shadow-sm border-0">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th style="width: 100px;">Hình ảnh</th> 
                    <th>Mã</th>
                    <th>Tên Tour</th>
                    <th>Loại</th>
                    <th>Thời lượng</th>
                    <th>Giá người lớn</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($tours)): ?>
                    <?php foreach ($tours as $tour): ?>
                    <tr>
                        <td>
                            <?php 
                                $imgData = !empty($tour['image']) ? $tour['image'] : null;
                            ?>
                            
                            <?php if($imgData && file_exists('public/uploads/' . $imgData)): ?>
                                <img src="public/uploads/<?= $imgData ?>" 
                                     alt="Img" 
                                     style="width: 70px; height: 50px; object-fit: cover; border-radius: 5px; border: 1px solid #eee;">
                            <?php else: ?>
                                <div class="bg-light d-flex justify-content-center align-items-center text-muted" 
                                     style="width: 70px; height: 50px; border-radius: 5px; border: 1px solid #eee; font-size: 10px;">
                                    <i class="fas fa-image fa-2x"></i>
                                </div>
                            <?php endif; ?>
                        </td>

                        <td class="fw-bold text-primary"><?= htmlspecialchars($tour['code']) ?></td>
                        
                        <td>
                            <div class="fw-bold"><?= htmlspecialchars($tour['name']) ?></div>
                            <div class="small text-muted text-truncate" style="max-width: 200px;">
                                <?= htmlspecialchars($tour['highlight'] ?? '') ?>
                            </div>
                        </td>

                        <td>
                            <?php 
                                $typeLabel = ['domestic'=>'Trong nước', 'international'=>'Quốc tế', 'custom'=>'Theo yêu cầu'];
                                $typeClass = ['domestic'=>'bg-info', 'international'=>'bg-warning', 'custom'=>'bg-secondary'];
                                $tType = $tour['type'] ?? 'domestic';
                            ?>
                            <span class="badge <?= $typeClass[$tType] ?> rounded-pill">
                                <?= $typeLabel[$tType] ?>
                            </span>
                        </td>

                        <td>3N2Đ</td> <td class="fw-bold text-success"><?= number_format($tour['price_adult']) ?> ₫</td>
                        
                        <td class="text-nowrap"> 
                            <div class="d-flex gap-2"> <a href="index.php?action=edit&id=<?= $tour['id'] ?>" 
                            class="btn btn-sm btn-outline-primary" 
                            title="Sửa tour">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <a href="index.php?action=delete&id=<?= $tour['id'] ?>" 
                            class="btn btn-sm btn-outline-danger" 
                            title="Xóa tour"
                            onclick="return confirm('Bạn có chắc chắn muốn xóa tour này không?');">
                                <i class="fas fa-trash"></i>
                            </a>

                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-box-open fa-3x mb-3 opacity-50"></i><br>
                            Chưa có dữ liệu tour nào.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<div class="modal fade" id="tourModalFull" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable"> 
        
        <form action="index.php?action=store" method="POST" enctype="multipart/form-data">
            
            <div class="modal-content">
                <div class="modal-header bg-white">
                    <h5 class="modal-title text-primary fw-bold"><i class="fas fa-layer-group me-2"></i>Thiết lập Tour Chi tiết</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    
                    <ul class="nav nav-tabs px-3 pt-3" id="tourTabs" role="tablist">
                        <li class="nav-item"><button class="nav-link active" type="button" data-bs-target="#tab-info" data-bs-toggle="tab">Thông tin chung</button></li>
                        <li class="nav-item"><button class="nav-link" type="button" data-bs-target="#tab-itinerary" data-bs-toggle="tab">Lịch trình & Dịch vụ</button></li>
                        <li class="nav-item"><button class="nav-link" type="button" data-bs-target="#tab-price" data-bs-toggle="tab">Bảng giá & Phiên bản</button></li>
                        <li class="nav-item"><button class="nav-link" type="button" data-bs-target="#tab-seo" data-bs-toggle="tab">Hình ảnh & QR</button></li>
                    </ul>

                    <div class="tab-content p-4">
                        
                        <div class="tab-pane fade show active" id="tab-info">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Mã Tour <span class="text-danger">*</span></label>
                                    <input type="text" name="code" class="form-control" placeholder="VD: T-HG-001" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tên Tour <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="Tên hiển thị trên báo giá" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Phân loại</label>
                                    <select name="type" class="form-select">
                                        <option value="domestic">Tour Trong nước</option>
                                        <option value="international">Tour Quốc tế</option>
                                        <option value="custom">Tour Theo yêu cầu</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Mô tả ngắn (Highlight)</label>
                                    <textarea name="highlight" class="form-control" rows="3" placeholder="Các điểm nổi bật của tour..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-itinerary">
                            <div class="d-flex justify-content-between mb-3">
                                <h6>Chi tiết hành trình</h6>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="addDay()"><i class="fas fa-plus"></i> Thêm Ngày</button>
                            </div>
                            <div id="itinerary-container">
                                <div class="card mb-3 border-start border-3 border-primary">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="fw-bold text-primary">Ngày 1</h6>
                                            <button type="button" class="btn btn-sm text-danger" disabled><i class="fas fa-trash"></i></button>
                                        </div>
                                        
                                        <div class="mb-2">
                                            <input type="text" name="itinerary_title[]" class="form-control fw-bold" placeholder="Tiêu đề (VD: Đón khách - Khởi hành)">
                                        </div>

                                        <textarea name="itinerary_desc[]" class="form-control mb-2" rows="2" placeholder="Mô tả hoạt động trong ngày..."></textarea>
                                        
                                        <div class="row g-2">
                                            <div class="col-md-4">
                                                <input type="text" name="itinerary_spot[]" class="form-control form-control-sm" placeholder="Điểm tham quan">
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" name="itinerary_hotel[]" class="form-control form-control-sm" placeholder="Khách sạn/Nghỉ đêm">
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">Ăn uống</span>
                                                    <input type="text" name="itinerary_meals[]" class="form-control" placeholder="Sáng, Trưa, Tối">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-price">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Đối tượng</th>
                                            <th>Giá Gốc (Net)</th>
                                            <th>Giá Bán (Public)</th>
                                            <th>Lợi nhuận (Tạm tính)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Người lớn (>12t)</td>
                                            <td><input type="number" name="price_net_adult" class="form-control form-control-sm" value="0"></td>
                                            <td><input type="number" name="price_adult" class="form-control form-control-sm" value="0"></td>
                                            <td class="text-success fw-bold">--</td>
                                        </tr>
                                        <tr>
                                            <td>Trẻ em (5-11t)</td>
                                            <td><input type="number" name="price_net_child" class="form-control form-control-sm" value="0"></td>
                                            <td><input type="number" name="price_child" class="form-control form-control-sm" value="0"></td>
                                            <td class="text-success fw-bold">--</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                <label class="form-label">Chính sách hoàn hủy</label>
                                <textarea name="policy" class="form-control" rows="3" placeholder="Nhập điều kiện hủy tour..."></textarea>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-seo">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="border border-dashed p-4 text-center rounded bg-light">
                                        <i class="fas fa-cloud-upload-alt fa-2x text-secondary mb-2"></i>
                                        <p>Chọn ảnh đại diện cho Tour</p>
                                        <input type="file" name="image" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <label class="form-label">Mã QR Đặt Tour</label>
                                    <div class="bg-white border p-3 d-inline-block">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=DemoTravelERP" alt="QR Code">
                                    </div>
                                    <div class="mt-2 small text-muted">Hệ thống tự sinh sau khi lưu</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i> Lưu & Tạo báo giá</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleMenu(id) {
        const el = document.getElementById(id);
        el.style.display = (el.style.display === 'block') ? 'none' : 'block';
    }
    function addDay() {
        const container = document.getElementById('itinerary-container');
        const dayCount = container.children.length + 1;
        const html = `
            <div class="card mb-3 border-start border-3 border-secondary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h6 class="fw-bold text-secondary">Ngày ${dayCount}</h6>
                        <button type="button" class="btn btn-sm text-danger" onclick="this.closest('.card').remove()"><i class="fas fa-trash"></i></button>
                    </div>
                    
                    <div class="mb-2">
                        <input type="text" name="itinerary_title[]" class="form-control fw-bold" placeholder="Tiêu đề ngày ${dayCount}">
                    </div>

                    <textarea name="itinerary_desc[]" class="form-control mb-2" rows="2" placeholder="Mô tả hoạt động..."></textarea>
                    
                    <div class="row g-2">
                        <div class="col-md-4"><input type="text" name="itinerary_spot[]" class="form-control form-control-sm" placeholder="Điểm tham quan"></div>
                        <div class="col-md-4"><input type="text" name="itinerary_hotel[]" class="form-control form-control-sm" placeholder="Khách sạn"></div>
                        <div class="col-md-4">
                             <div class="input-group input-group-sm">
                                <span class="input-group-text">Ăn uống</span>
                                <input type="text" name="itinerary_meals[]" class="form-control" placeholder="Sáng, Trưa, Tối">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }
</script>
</body>
</html>