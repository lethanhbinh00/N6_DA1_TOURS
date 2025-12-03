<?php 
require_once __DIR__ . '/../layouts/header.php'; 

$revenue = $revenue ?? 0;
$totalBookings = $totalBookings ?? 0;
$totalTours = $totalTours ?? 0;
$totalCustomers = $totalCustomers ?? 0;
$recentBookings = $recentBookings ?? [];
?>

<div class="container-fluid p-4">
    <div class="d-flex align-items-center mb-4">
        <h3 class="fw-bold text-secondary mb-0">Dashboard</h3>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small fw-bold">DOANH THU</p>
                        <h4 class="fw-bold text-success mb-0"><?= number_format($revenue) ?> ₫</h4>
                    </div>
                    <i class="fas fa-dollar-sign fa-2x text-success opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small fw-bold">ĐƠN HÀNG</p>
                        <h4 class="fw-bold text-primary mb-0"><?= $totalBookings ?></h4>
                    </div>
                    <i class="fas fa-shopping-cart fa-2x text-primary opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small fw-bold">TOUR CHẠY</p>
                        <h4 class="fw-bold text-warning mb-0"><?= $totalTours ?></h4>
                    </div>
                    <i class="fas fa-suitcase fa-2x text-warning opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small fw-bold">KHÁCH HÀNG</p>
                        <h4 class="fw-bold text-info mb-0"><?= $totalCustomers ?></h4>
                    </div>
                    <i class="fas fa-users fa-2x text-info opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0">🛒 Đơn hàng mới nhất</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light small">
                            <tr>
                                <th>Mã</th>
                                <th>Khách hàng</th>
                                <th>Tour</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($recentBookings)): ?>
                            <?php foreach($recentBookings as $bk): ?>
                            <tr>
                                <td class="fw-bold"><?= $bk['booking_code'] ?></td>
                                <td><?= htmlspecialchars($bk['customer_name']) ?></td>
                                <td><?= htmlspecialchars($bk['tour_name']) ?></td>
                                <td>
                                    <?php if($bk['status']=='new'): ?>
                                    <span class="badge bg-secondary">Mới</span>
                                    <?php elseif($bk['status']=='confirmed'): ?>
                                    <span class="badge bg-primary">Đã xác nhận</span>
                                    <?php else: ?>
                                    <span class="badge bg-danger"><?= $bk['status'] ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0">⚡ Thao tác nhanh</h6>
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="index.php?action=booking-create" class="btn btn-success p-3 text-start"><i
                            class="fas fa-plus-circle me-2"></i> Tạo Booking Mới</a>
                    <a href="index.php?action=index" class="btn btn-primary p-3 text-start"><i
                            class="fas fa-layer-group me-2"></i> Thêm Tour Mới</a>
                    <a href="index.php?action=customer-list" class="btn btn-info text-white p-3 text-start"><i
                            class="fas fa-user-plus me-2"></i> Thêm Khách Hàng</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>