<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
    .stat-card { transition: transform 0.2s; cursor: pointer; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    .icon-box { width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-size: 24px; }
</style>

<div class="container-fluid p-4">
    <div class="d-flex align-items-center mb-4">
        <h3 class="fw-bold text-secondary mb-0">👋 Xin chào, Admin!</h3>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Doanh thu tạm tính</p>
                            <h4 class="fw-bold text-success mb-0"><?= number_format($revenue) ?> ₫</h4>
                        </div>
                        <div class="icon-box bg-success bg-opacity-10 text-success">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card h-100" onclick="window.location.href='index.php?action=booking-list'">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Tổng đơn hàng</p>
                            <h4 class="fw-bold text-primary mb-0"><?= $totalBookings ?></h4>
                        </div>
                        <div class="icon-box bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card h-100" onclick="window.location.href='index.php?action=index'">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Tour đang chạy</p>
                            <h4 class="fw-bold text-warning mb-0"><?= $totalTours ?></h4>
                        </div>
                        <div class="icon-box bg-warning bg-opacity-10 text-warning">
                            <i class="fas fa-suitcase"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card h-100" onclick="window.location.href='index.php?action=customer-list'">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Khách hàng</p>
                            <h4 class="fw-bold text-info mb-0"><?= $totalCustomers ?></h4>
                        </div>
                        <div class="icon-box bg-info bg-opacity-10 text-info">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="fas fa-receipt me-2 text-secondary"></i> Đơn hàng mới nhất</h6>
                    <a href="index.php?action=booking-list" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-secondary small">
                            <tr>
                                <th>Mã</th>
                                <th>Khách hàng</th>
                                <th>Tour</th>
                                <th>Ngày tạo</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($recentBookings)): ?>
                                <?php foreach($recentBookings as $bk): ?>
                                <tr>
                                    <td class="fw-bold text-primary"><?= $bk['booking_code'] ?></td>
                                    <td><?= htmlspecialchars($bk['customer_name']) ?></td>
                                    <td class="text-truncate" style="max-width: 150px;"><?= htmlspecialchars($bk['tour_name']) ?></td>
                                    <td class="small text-muted"><?= date('d/m H:i', strtotime($bk['created_at'])) ?></td>
                                    <td>
                                        <?php 
                                            $st = $bk['status'];
                                            if($st=='new') echo '<span class="badge bg-secondary">Mới</span>';
                                            elseif($st=='confirmed') echo '<span class="badge bg-primary">Đã xác nhận</span>';
                                            elseif($st=='cancelled') echo '<span class="badge bg-danger">Hủy</span>';
                                            else echo '<span class="badge bg-success">Hoàn tất</span>';
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted">Chưa có đơn hàng nào.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0">⚡ Thao tác nhanh</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="index.php?action=booking-create" class="btn btn-success p-3 text-start shadow-sm border-0">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-plus-circle fa-2x me-3"></i>
                                <div>
                                    <div class="fw-bold">Tạo Booking Mới</div>
                                    <small class="opacity-75">Lên đơn cho khách lẻ/đoàn</small>
                                </div>
                            </div>
                        </a>
                        
                        <a href="index.php?action=store" class="btn btn-primary p-3 text-start shadow-sm border-0" data-bs-toggle="modal" data-bs-target="#tourModalFull">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-layer-group fa-2x me-3"></i>
                                <div>
                                    <div class="fw-bold">Thêm Tour Mới</div>
                                    <small class="opacity-75">Tạo sản phẩm du lịch mới</small>
                                </div>
                            </div>
                        </a>
                        
                        <a href="index.php?action=customer-list" class="btn btn-info text-white p-3 text-start shadow-sm border-0">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user-plus fa-2x me-3"></i>
                                <div>
                                    <div class="fw-bold">Thêm Khách Hàng</div>
                                    <small class="opacity-75">Lưu hồ sơ khách hàng tiềm năng</small>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>