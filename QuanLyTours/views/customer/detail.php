<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="index.php?action=customer-list" class="text-decoration-none text-secondary mb-1 d-block"><i class="fas fa-arrow-left"></i> Quay lại</a>
            <h4 class="fw-bold text-primary m-0"><i class="fas fa-id-badge me-2"></i>Hồ sơ: <?= htmlspecialchars($customer['full_name']) ?></h4>
        </div>
        <a href="index.php?action=customer-edit&id=<?= $customer['id'] ?>" class="btn btn-outline-primary btn-sm"><i class="fas fa-edit me-1"></i> Chỉnh sửa</a>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body text-center pt-4 pb-4">
                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-user fa-3x text-secondary opacity-25"></i>
                    </div>
                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($customer['full_name']) ?></h5>
                    <span class="badge bg-info bg-opacity-10 text-dark border border-info mt-2"><?= htmlspecialchars($customer['source']) ?></span>
                </div>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between px-4 py-3"><span class="text-muted">SĐT:</span><span class="fw-bold"><?= htmlspecialchars($customer['phone']) ?></span></li>
                    <li class="list-group-item d-flex justify-content-between px-4 py-3"><span class="text-muted">Email:</span><span class="fw-bold"><?= htmlspecialchars($customer['email'] ?? '--') ?></span></li>
                    <li class="list-group-item d-flex justify-content-between px-4 py-3"><span class="text-muted">CCCD:</span><span class="fw-bold"><?= htmlspecialchars($customer['id_card'] ?? '--') ?></span></li>
                </ul>
            </div>
            
            <div class="row g-2">
                <div class="col-6"><div class="card border-0 bg-primary text-white text-center p-3 h-100"><div class="display-6 fw-bold"><?= $summary['total_tours'] ?? 0 ?></div><div class="small opacity-75">Chuyến đi</div></div></div>
                <div class="col-6"><div class="card border-0 bg-success text-white text-center p-3 h-100"><div class="fw-bold fs-5"><?= number_format($summary['total_spent'] ?? 0) ?></div><div class="small opacity-75">Tổng chi tiêu</div></div></div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 fw-bold text-uppercase text-secondary small"><i class="fas fa-history me-2"></i>Lịch sử đặt tour</div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-secondary small">
                            <tr><th class="ps-4">Mã BK</th><th>Tour</th><th>Ngày đi</th><th>Tổng tiền</th><th>Trạng thái</th></tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($history)): foreach($history as $h): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-primary"><?= $h['booking_code'] ?></td>
                                    <td><span class="fw-bold"><?= htmlspecialchars($h['tour_name']) ?></span></td>
                                    <td><?= date('d/m/Y', strtotime($h['travel_date'])) ?></td>
                                    <td class="fw-bold text-success"><?= number_format($h['total_price']) ?> ₫</td>
                                    <td><span class="badge bg-secondary"><?= $h['status'] ?></span></td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="5" class="text-center py-5 text-muted">Chưa có lịch sử giao dịch.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>