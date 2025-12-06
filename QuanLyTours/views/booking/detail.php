<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid p-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="index.php?action=booking-list" class="text-decoration-none text-secondary mb-1 d-block">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
            <h4 class="fw-bold text-primary m-0">
                Hồ sơ Booking: <span class="text-dark">#<?= htmlspecialchars($booking['booking_code'] ?? '---') ?></span>
            </h4>
            <span class="text-muted small">Ngày tạo: <?= date('d/m/Y H:i', strtotime($booking['created_at'] ?? 'now')) ?></span>
        </div>

        <div class="d-flex gap-2">
            <div class="btn-group">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle shadow-sm" data-bs-toggle="dropdown">
                    <i class="fas fa-print me-2"></i>In ấn
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="index.php?action=booking-invoice&id=<?= $booking['id'] ?? 0 ?>" target="_blank">In Hóa đơn</a></li>
                    <li><a class="dropdown-item" href="index.php?action=booking-contract&id=<?= $booking['id'] ?? 0 ?>" target="_blank">In Hợp đồng</a></li>
                </ul>
            </div>
            <a href="index.php?action=booking-ops&id=<?= $booking['id'] ?? 0 ?>" class="btn btn-dark shadow-sm">
                <i class="fas fa-list-ul me-2"></i>Điều hành
            </a>
            <?php if(($booking['status'] ?? 'cancelled') != 'cancelled'): ?>
            <a href="index.php?action=booking-edit&id=<?= $booking['id'] ?? 0 ?>" class="btn btn-primary shadow-sm">
                <i class="fas fa-edit me-2"></i>Sửa thông tin
            </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row g-4">
        
        <div class="col-lg-4">
            
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h6 class="fw-bold text-secondary text-uppercase small mb-3">Tình hình tài chính</h6>
                    
                    <?php 
                        $st = $booking['status'] ?? 'new';
                        $class = ($st=='confirmed')?'primary':(($st=='deposited')?'warning':(($st=='completed')?'success':'secondary'));
                    ?>
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <span>Trạng thái:</span>
                        <span class="badge bg-<?= $class ?> fs-6"><?= ucfirst($st) ?></span>
                    </div>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Tổng giá trị:</span>
                            <span class="fw-bold fs-5 text-primary"><?= number_format($booking['total_price'] ?? 0) ?> ₫</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Đã thanh toán:</span>
                            <span class="fw-bold text-success"><?= number_format($booking['deposit_amount'] ?? 0) ?> ₫</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Còn nợ:</span>
                            <span class="fw-bold text-danger"><?= number_format(($booking['total_price'] ?? 0) - ($booking['deposit_amount'] ?? 0)) ?> ₫</span>
                        </li>
                    </ul>
                    
                    <div class="mt-4">
                        <h6 class="small fw-bold text-muted border-bottom pb-2">LỊCH SỬ GIAO DỊCH CHI TIẾT</h6>
                        <?php if(!empty($payments)): ?>
                            <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                                <table class="table table-sm small mb-0 align-middle">
                                    <thead class="table-light"><tr><th>Ngày</th><th>Số tiền</th><th>HT</th><th>NV Thu</th><th class="text-center" width="80">Xóa</th></tr></thead>
                                    <tbody>
                                        <?php foreach($payments as $pay): ?>
                                        <tr>
                                            <td><?= date('d/m H:i', strtotime($pay['created_at'])) ?></td>
                                            <td class="text-end fw-bold text-success">+<?= number_format($pay['amount']) ?></td>
                                            <td><?= htmlspecialchars($pay['payment_method'] ?? '---') ?></td>
                                            <td><?= htmlspecialchars($pay['collector_name'] ?? 'Hệ thống') ?></td>
                                            <td class="text-center">
                                                <a href="index.php?action=payment-receipt&id=<?= $pay['id'] ?>&bid=<?= $booking['id'] ?>" class="text-secondary me-2" title="In phiếu thu"><i class="fas fa-print"></i></a>
                                                <a href="index.php?action=payment-delete&id=<?= $pay['id'] ?>&bid=<?= $booking['id'] ?>" class="text-danger" onclick="return confirm('Xóa khoản thu này?')"><i class="fas fa-times"></i></a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted small fst-italic py-2">Chưa có giao dịch nào.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-bold text-uppercase small text-secondary py-3">Khách hàng đại diện</div>
                <div class="card-body">
                    <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($booking['customer_name'] ?? '---') ?></h5>
                    <p class="text-muted small mb-3"><i class="fas fa-id-card me-2"></i> <?= htmlspecialchars($booking['customer_id_card'] ?? '---') ?></p>
                    
                    <hr class="my-2">
                    <div class="my-2"><i class="fas fa-phone me-2 text-secondary"></i> <?= htmlspecialchars($booking['customer_phone'] ?? '---') ?></div>
                    <div class="my-2"><i class="fas fa-envelope me-2 text-secondary"></i> <?= htmlspecialchars($booking['customer_email'] ?? '---') ?></div>
                    
                    <?php if(!empty($booking['note'])): ?>
                    <div class="alert alert-warning mt-3 mb-0 small">
                        <i class="fas fa-sticky-note me-1"></i> <b>Ghi chú:</b> <?= htmlspecialchars($booking['note'] ?? '') ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="bg-light p-3 rounded me-3 text-center" style="min-width: 80px;">
                            <i class="fas fa-suitcase fa-2x text-primary opacity-50"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="fw-bold mb-1"><?= htmlspecialchars($tour['name'] ?? '---') ?></h4>
                            </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold m-0 text-primary"><i class="fas fa-map-marked-alt me-2"></i>Lịch trình chi tiết</h6>
                </div>
                <div class="card-body">
                    <?php if(!empty($itineraries)): ?>
                        <div class="timeline">
                            <?php foreach($itineraries as $day): ?>
                                <div class="d-flex mb-4">
                                    <div class="flex-shrink-0 text-center" style="width: 60px;">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold mx-auto" style="width: 40px; height: 40px;">N<?= $day['day_number'] ?></div>
                                        <small class="text-muted d-block mt-1 fw-bold">Ngày</small>
                                    </div>
                                    <div class="flex-grow-1 ms-3 pb-3 border-bottom border-light">
                                        <h6 class="fw-bold text-uppercase text-dark mb-1"><?= htmlspecialchars($day['title'] ?? '') ?></h6>
                                        <div class="bg-light p-3 rounded text-secondary mb-2" style="font-size: 0.95rem; line-height: 1.6;">
                                            <?= nl2br(htmlspecialchars($day['description'] ?? '')) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-map fa-3x mb-3 opacity-25"></i><br>
                            Chưa có dữ liệu lịch trình cho tour này.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold m-0 text-dark"><i class="fas fa-concierge-bell me-2"></i>Dịch vụ đã đặt (Costing)</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <tbody><tr><td colspan="4" class="text-center py-3 text-muted small">Thông tin chi phí vận hành cần xem ở trang Điều hành.</td></tr></tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>