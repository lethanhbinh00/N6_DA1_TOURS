<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid p-4">
    <div class="d-flex align-items-center mb-4">
        <h3 class="fw-bold text-secondary mb-0">📊 Bảng điều khiển trung tâm</h3>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 p-3 border-start border-4 border-success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small fw-bold">DOANH THU</p>
                        <h4 class="fw-bold text-success mb-0"><?= number_format($revenue ?? 0) ?> ₫</h4>
                    </div>
                    <i class="fas fa-dollar-sign fa-2x text-success opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 p-3 border-start border-4 border-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small fw-bold">TỔNG ĐƠN HÀNG</p>
                        <h4 class="fw-bold text-primary mb-0"><?= $totalBookings ?? 0 ?></h4>
                    </div>
                    <i class="fas fa-shopping-cart fa-2x text-primary opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 p-3 border-start border-4 border-warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small fw-bold">TOUR ĐANG CHẠY</p>
                        <h4 class="fw-bold text-warning mb-0"><?= $totalTours ?? 0 ?></h4>
                    </div>
                    <i class="fas fa-map-marked-alt fa-2x text-warning opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 p-3 border-start border-4 border-info">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small fw-bold">KHÁCH HÀNG</p>
                        <h4 class="fw-bold text-info mb-0"><?= $totalCustomers ?? 0 ?></h4>
                    </div>
                    <i class="fas fa-users fa-2x text-info opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="fas fa-chart-line me-2 text-primary"></i>Xu hướng doanh thu (7 ngày qua)
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="fas fa-chart-pie me-2 text-warning"></i>Tỉ lệ đơn hàng
                </div>
                <div class="card-body">
                    <canvas id="statusChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between">
                    <h6 class="fw-bold mb-0">🛒 Đơn hàng mới nhất</h6>
                    <a href="index.php?action=booking-list" class="small text-decoration-none">Xem tất cả</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light small">
                            <tr><th>Mã</th><th>Khách hàng</th><th>Tour</th><th>Trạng thái</th></tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($recentBookings)): foreach($recentBookings as $bk): ?>
                            <tr>
                                <td class="fw-bold text-primary"><?= htmlspecialchars($bk['booking_code'] ?? '---') ?></td>
                                <td><?= htmlspecialchars($bk['customer_name'] ?? '---') ?></td>
                                <td class="text-truncate" style="max-width: 120px;"><?= htmlspecialchars($bk['tour_name'] ?? 'Tour bị xóa') ?></td>
                                <td>
                                    <?php 
                                        $s = $bk['status'];
                                        $badge = 'secondary'; $label = 'Mới';
                                        if($s=='confirmed') { $badge='primary'; $label = 'Xác nhận'; }
                                        if($s=='deposited') { $badge='warning text-dark'; $label = 'Đã cọc'; }
                                        if($s=='completed') { $badge='success'; $label = 'Hoàn tất'; }
                                        if($s=='cancelled') { $badge='danger'; $label = 'Hủy'; }
                                    ?>
                                    <span class="badge bg-<?= $badge ?>"><?= $label ?></span>
                                </td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0 text-danger"><i class="fas fa-fire me-2"></i>Sắp khởi hành (7 ngày tới)</h6>
                </div>
                <div class="card-body">
                    <?php if(!empty($upcomingTours)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach($upcomingTours as $up): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <div class="fw-bold"><?= htmlspecialchars($up['tour_name'] ?? '---') ?></div>
                                    <small class="text-muted">
                                        <i class="far fa-calendar-alt me-1"></i> <?= date('d/m', strtotime($up['travel_date'])) ?> 
                                        • <i class="fas fa-users me-1"></i> <?= $up['total_pax'] ?? 0 ?> khách
                                    </small>
                                </div>
                                <a href="index.php?action=booking-ops&id=<?= $up['id'] ?? 0 ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-arrow-right"></i></a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-calendar-check fa-2x mb-2 opacity-50"></i><br>Không có đoàn nào sắp đi.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Biểu đồ Doanh thu
    const ctxRev = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctxRev, {
        type: 'line',
        data: {
            labels: <?= json_encode($chartDates ?? []) ?>,
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: <?= json_encode($chartRevenue ?? []) ?>,
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

    // 2. Biểu đồ Trạng thái
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: ['Mới', 'Đã xác nhận', 'Đã cọc', 'Hoàn tất', 'Hủy'],
            datasets: [{
                data: [
                    <?= $statusCounts['new'] ?? 0 ?>, 
                    <?= $statusCounts['confirmed'] ?? 0 ?>, 
                    <?= $statusCounts['deposited'] ?? 0 ?>,
                    <?= $statusCounts['completed'] ?? 0 ?>,
                    <?= $statusCounts['cancelled'] ?? 0 ?>
                ],
                backgroundColor: ['#6c757d', '#0d6efd', '#ffc107', '#198754', '#dc3545']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>