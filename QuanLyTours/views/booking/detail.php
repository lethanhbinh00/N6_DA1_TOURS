<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
    .card-title-lg { font-size: 1.1rem; font-weight: bold; }
    .detail-label { font-weight: bold; color: #6c757d; font-size: 0.85rem; }
    .detail-value { font-weight: 600; color: #343a40; font-size: 1rem; }
    .transaction-table th { font-size: 0.8rem; }
    .transaction-table td { font-size: 0.9rem; }
    .service-table th { font-size: 0.8rem; }
    .btn-icon { width: 30px; height: 30px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }
</style>

<div class="container-fluid p-4">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-primary mb-0">
            <i class="fas fa-file-invoice me-2"></i>Hồ sơ Booking: <?= htmlspecialchars($booking['booking_code'] ?? 'N/A') ?>
        </h3>
        <div class="d-flex gap-2">
            <a href="index.php?action=booking-list" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Quay lại danh sách</a>
            <a href="index.php?action=booking-ops&id=<?= $booking['id'] ?? 0 ?>" class="btn btn-info btn-sm text-white"><i class="fas fa-cogs me-1"></i> Điều hành</a>
            <a href="index.php?action=booking-edit&id=<?= $booking['id'] ?? 0 ?>" class="btn btn-warning btn-sm text-dark"><i class="fas fa-edit me-1"></i> Sửa thông tin</a>
            <a href="index.php?action=booking-invoice&id=<?= $booking['id'] ?? 0 ?>" target="_blank" class="btn btn-success btn-sm"><i class="fas fa-print me-1"></i> In Hóa đơn</a>
        </div>
    </div>
    
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <h5 class="card-title-lg text-secondary mb-3"><i class="fas fa-chart-line me-2"></i>Tình hình Tài chính</h5>
                    
                    <div class="mb-3">
                        <span class="detail-label">Trạng thái:</span>
                        <?php 
                            $st = $booking['status'] ?? 'new';
                            $badge = 'secondary'; $label = 'Mới';
                            if($st=='confirmed') { $badge='primary'; $label = 'Đã xác nhận'; }
                            if($st=='deposited') { $badge='warning text-dark'; $label = 'Đã cọc'; }
                            if($st=='completed') { $badge='success'; $label = 'Hoàn tất'; }
                            if($st=='cancelled') { $badge='danger'; $label = 'Đã hủy'; }
                        ?>
                        <span class="badge bg-<?= $badge ?> fs-6 p-2"><?= $label ?></span>
                    </div>

                    <div class="border-top pt-3 mt-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="detail-label">Tổng giá trị:</span>
                            <span class="detail-value text-primary"><?= number_format($booking['total_price'] ?? 0) ?> ₫</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="detail-label">Đã thanh toán:</span>
                            <span class="detail-value text-success"><?= number_format($booking['deposit_amount'] ?? 0) ?> ₫</span>
                        </div>
                        <div class="d-flex justify-content-between border-top mt-2 pt-2">
                            <span class="detail-label fs-5 text-danger">CÒN PHẢI THU:</span>
                            <span class="detail-value fs-5 text-danger"><?= number_format(($booking['total_price'] ?? 0) - ($booking['deposit_amount'] ?? 0)) ?> ₫</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <h5 class="card-title-lg text-secondary mb-3"><i class="fas fa-user-circle me-2"></i>Khách hàng Đại diện</h5>
                    
                    <p class="detail-label mb-1">Họ tên:</p>
                    <p class="detail-value"><?= htmlspecialchars($booking['customer_name'] ?? 'Khách lẻ') ?></p>
                    
                    <p class="detail-label mb-1">Điện thoại:</p>
                    <p class="detail-value"><?= htmlspecialchars($booking['customer_phone'] ?? '---') ?></p>
                    
                    <p class="detail-label mb-1">CCCD/Email:</p>
                    <p class="detail-value"><?= htmlspecialchars($booking['customer_id_card'] ?? '---') ?> / <?= htmlspecialchars($booking['customer_email'] ?? '---') ?></p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <h5 class="card-title-lg text-secondary mb-3"><i class="fas fa-info-circle me-2"></i>Chi tiết Tour & Vận hành</h5>
                    
                    <p class="detail-label mb-1">Tour:</p>
                    <p class="detail-value text-dark"><?= htmlspecialchars($tour['code'] ?? '') ?> - <?= htmlspecialchars($tour['name'] ?? 'Tour đã bị xóa') ?></p>
                    
                    <p class="detail-label mb-1">Ngày khởi hành:</p>
                    <?php 
                        $travelDate = $booking['travel_date'] ?? null;
                        $returnDate = $booking['return_date'] ?? null;
                        $displayTravel = $travelDate ? date('d/m/Y', strtotime($travelDate)) : '---';
                        $displayReturn = $returnDate ? date('d/m/Y', strtotime($returnDate)) : '';
                    ?>
                    <p class="detail-value text-dark">
                        <?= $displayTravel ?>
                        <?php if(!empty($displayReturn)): ?>
                            (Đến <?= $displayReturn ?>)
                        <?php endif; ?>
                    </p>
                    
                    <p class="detail-label mb-1">Vận chuyển/Điểm đón:</p>
                    <p class="detail-value text-dark">
                        <?= htmlspecialchars($booking['flight_number'] ?? $booking['transport_supplier_id'] ?? '---') ?>
                        <div class="small text-muted">Đón tại: <?= htmlspecialchars($booking['pickup_location'] ?? '---') ?></div>
                    </p>

                    <p class="detail-label mb-1">Ghi chú Booking:</p>
                    <p class="text-muted small border p-2 bg-light rounded"><?= nl2br(htmlspecialchars($booking['note'] ?? 'Không có ghi chú')) ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-md-5">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body p-0">
                    <h5 class="card-title-lg text-secondary p-3 border-bottom mb-0"><i class="fas fa-history me-2"></i>Lịch sử giao dịch</h5>
                    <table class="table table-striped table-hover transaction-table mb-0">
                        <thead class="bg-light">
                            <tr><th>Ngày</th><th>Số tiền</th><th>HT</th><th>NV Thu</th><th>#</th></tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($payments)): foreach($payments as $p): ?>
                            <tr>
                                <?php 
                                    $pDate = $p['payment_date'] ?? null; 
                                    // Kiểm tra pDate có phải là NULL/rỗng hoặc ngày không hợp lệ không
                                    if (empty($pDate) || $pDate === '0000-00-00 00:00:00') {
                                        $displayDate = '---';
                                    } else {
                                        // Sử dụng strtotime an toàn
                                        $displayDate = date('d/m/y H:i', strtotime($pDate)); 
                                    }
                                    
                                    $paymentId = $p['id'] ?? 0;
                                    $amount = number_format($p['amount'] ?? 0);
                                ?>
                                <td><?= $displayDate ?></td>
                                <td class="fw-bold text-success">+<?= $amount ?>₫</td>
                                <td><?= htmlspecialchars($p['payment_method'] ?? '---') ?></td>
                                <td><?= htmlspecialchars($p['collector_name'] ?? 'Admin') ?></td>
                                <td class="text-nowrap">
                                    <a href="index.php?action=receipt&id=<?= $paymentId ?>" target="_blank" class="btn btn-sm btn-outline-info btn-icon" title="In phiếu thu"><i class="fas fa-receipt"></i></a>
                                    
                                    <?php if(($_SESSION['user_role'] ?? 'guest') === 'admin'): ?>
                                    <a href="index.php?action=payment-delete&id=<?= $paymentId ?>&bid=<?= $booking['id'] ?? 0 ?>" 
                                       onclick="return confirm('CẢNH BÁO: Bạn chắc chắn muốn xóa giao dịch này (<?= $amount ?>₫)? Số tiền cọc sẽ bị trừ đi.')" 
                                       class="btn btn-sm btn-outline-danger btn-icon" title="Xóa giao dịch"><i class="fas fa-trash-alt"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr><td colspan="5" class="text-center text-muted py-3">Chưa có giao dịch nào.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-7">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body p-0">
                    <h5 class="card-title-lg text-secondary p-3 border-bottom mb-0 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-handshake me-2"></i>Dịch vụ đã đặt (Costing)</span>
                        <a href="index.php?action=booking-ops&id=<?= $booking['id'] ?? 0 ?>" class="btn btn-sm btn-primary">Chỉnh sửa Dịch vụ</a>
                    </h5>
                    <table class="table table-sm table-hover service-table mb-0">
                        <thead class="bg-light">
                            <tr><th>Loại</th><th>NCC/Mô tả</th><th>Chi phí (VNĐ)</th></tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($services)): foreach($services as $s): ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars(strtoupper($s['service_type'] ?? '---')) ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($s['supplier_name'] ?? 'N/A') ?></div>
                                    <div class="small text-muted"><?= htmlspecialchars($s['description'] ?? '---') ?></div>
                                </td>
                                <td class="text-danger fw-bold"><?= number_format($s['cost'] ?? 0) ?></td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr><td colspan="3" class="text-center text-muted py-3">Chưa có chi phí dịch vụ nào được ghi nhận.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>