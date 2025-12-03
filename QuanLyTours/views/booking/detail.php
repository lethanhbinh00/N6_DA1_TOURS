<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="index.php?action=booking-list" class="text-decoration-none text-secondary mb-1 d-block"><i class="fas fa-arrow-left"></i> Quay lại</a>
            <h4 class="fw-bold text-primary m-0">Thông tin đơn hàng: #<?= $booking['booking_code'] ?></h4>
        </div>
        <div>
            <a href="index.php?action=booking-ops&id=<?= $booking['id'] ?>" class="btn btn-dark shadow-sm"><i class="fas fa-cogs me-2"></i>Điều hành</a>
            <a href="index.php?action=booking-edit&id=<?= $booking['id'] ?>" class="btn btn-outline-primary shadow-sm"><i class="fas fa-edit me-2"></i>Sửa</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 fw-bold text-uppercase text-secondary small">Khách hàng</div>
                <div class="card-body">
                    <h5 class="fw-bold"><?= htmlspecialchars($booking['customer_name']) ?></h5>
                    <p class="mb-1"><i class="fas fa-phone me-2 text-muted"></i> <?= htmlspecialchars($booking['customer_phone']) ?></p>
                    <p class="mb-1"><i class="fas fa-envelope me-2 text-muted"></i> <?= htmlspecialchars($booking['customer_email']) ?></p>
                    <p class="mb-0"><i class="fas fa-id-card me-2 text-muted"></i> <?= htmlspecialchars($booking['customer_id_card']) ?></p>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 fw-bold text-uppercase text-secondary small">Thông tin Tour</div>
                <div class="card-body">
                    <h6 class="fw-bold text-primary"><?= htmlspecialchars($tour['name']) ?></h6>
                    <span class="badge bg-secondary mb-2"><?= htmlspecialchars($tour['code']) ?></span>
                    <div class="d-flex justify-content-between mt-3 border-top pt-3">
                        <span>Ngày đi:</span>
                        <span class="fw-bold"><?= date('d/m/Y', strtotime($booking['travel_date'])) ?></span>
                    </div>
                    <?php if(!empty($booking['return_date'])): ?>
                    <div class="d-flex justify-content-between mt-2">
                        <span>Ngày về:</span>
                        <span class="fw-bold"><?= date('d/m/Y', strtotime($booking['return_date'])) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="mt-3 p-2 bg-light rounded text-center">
                        <span class="fw-bold"><?= $booking['adults'] ?></span> Lớn, 
                        <span class="fw-bold"><?= $booking['children'] ?></span> Trẻ
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 fw-bold text-uppercase text-secondary small">Tình hình tài chính</div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4 border-end">
                            <small class="text-muted text-uppercase">Tổng giá trị</small>
                            <h3 class="text-primary fw-bold my-2"><?= number_format($booking['total_price']) ?></h3>
                        </div>
                        <div class="col-4 border-end">
                            <small class="text-muted text-uppercase">Đã thanh toán</small>
                            <h3 class="text-success fw-bold my-2"><?= number_format($booking['deposit_amount']) ?></h3>
                        </div>
                        <div class="col-4">
                            <small class="text-muted text-uppercase">Còn lại</small>
                            <h3 class="text-danger fw-bold my-2"><?= number_format($booking['total_price'] - $booking['deposit_amount']) ?></h3>
                        </div>
                    </div>
                    
                    <hr>
                    <h6 class="fw-bold text-secondary small mb-3">LỊCH SỬ THANH TOÁN</h6>
                    <div class="bg-light p-3 rounded border" style="font-family: monospace; font-size: 0.9rem;">
                        <?= nl2br(htmlspecialchars($booking['payment_note'] ?? 'Chưa có ghi chú giao dịch.')) ?>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 fw-bold text-uppercase text-secondary small">Ghi chú Booking</div>
                <div class="card-body">
                    <p class="mb-0 text-muted"><?= nl2br(htmlspecialchars($booking['note'] ?? 'Không có ghi chú.')) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>