<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid p-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="index.php?action=booking-list" class="text-decoration-none text-secondary mb-1 d-block">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
            <h4 class="fw-bold text-primary m-0">
                Thông tin đơn hàng: <span class="text-dark">#<?= $booking['booking_code'] ?></span>
            </h4>
            <span class="text-muted small">Ngày tạo: <?= date('d/m/Y H:i', strtotime($booking['created_at'])) ?></span>
        </div>

        <div class="d-flex gap-2">
            <a href="index.php?action=booking-invoice&id=<?= $booking['id'] ?>" target="_blank" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-print me-2"></i>In Phiếu
            </a>
            <a href="index.php?action=booking-ops&id=<?= $booking['id'] ?>" class="btn btn-dark shadow-sm">
                <i class="fas fa-list-ul me-2"></i>Điều hành
            </a>
            <?php if($booking['status'] != 'cancelled'): ?>
            <a href="index.php?action=booking-edit&id=<?= $booking['id'] ?>" class="btn btn-primary shadow-sm">
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
                        $st = $booking['status'];
                        $class = ($st=='confirmed')?'primary':(($st=='deposited')?'warning':(($st=='completed')?'success':'secondary'));
                    ?>
                    <div class="mb-3">
                        Trạng thái: <span class="badge bg-<?= $class ?> fs-6"><?= ucfirst($st) ?></span>
                    </div>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Tổng giá trị:</span>
                            <span class="fw-bold fs-5 text-primary"><?= number_format($booking['total_price']) ?> ₫</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Đã thanh toán:</span>
                            <span class="fw-bold text-success"><?= number_format($booking['deposit_amount']) ?> ₫</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Còn nợ:</span>
                            <span class="fw-bold text-danger"><?= number_format($booking['total_price'] - $booking['deposit_amount']) ?> ₫</span>
                        </li>
                    </ul>
                    
                    <div class="mt-3 bg-light p-2 rounded border small">
                        <strong>Lịch sử giao dịch:</strong><br>
                        <span class="text-muted" style="font-family: monospace;">
                            <?= nl2br(htmlspecialchars($booking['payment_note'] ?? '-- Trống --')) ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-bold text-uppercase small text-secondary py-3">Khách hàng đại diện</div>
                <div class="card-body">
                    <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($booking['customer_name']) ?></h5>
                    <p class="text-muted small mb-3"><i class="fas fa-id-card me-2"></i> <?= htmlspecialchars($booking['customer_id_card']) ?></p>
                    
                    <hr class="my-2">
                    <div class="my-2"><i class="fas fa-phone me-2 text-secondary"></i> <?= htmlspecialchars($booking['customer_phone']) ?></div>
                    <div class="my-2"><i class="fas fa-envelope me-2 text-secondary"></i> <?= htmlspecialchars($booking['customer_email']) ?></div>
                    
                    <div class="alert alert-warning mt-3 mb-0 small">
                        <i class="fas fa-sticky-note me-1"></i> <b>Ghi chú:</b> <?= htmlspecialchars($booking['note'] ?? 'Không có') ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-light p-3 rounded me-3 text-center" style="min-width: 80px;">
                        <i class="fas fa-suitcase fa-2x text-primary opacity-50"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1"><?= htmlspecialchars($tour['name']) ?></h5>
                        <div class="text-muted small">
                            Mã: <span class="badge bg-secondary"><?= $tour['code'] ?></span> | 
                            Khởi hành: <b class="text-dark"><?= date('d/m/Y', strtotime($booking['travel_date'])) ?></b>
                            <?php if(!empty($booking['return_date'])): ?> - <?= date('d/m/Y', strtotime($booking['return_date'])) ?><?php endif; ?>
                        </div>
                        <div class="mt-1">
                            <span class="me-3"><i class="fas fa-user me-1"></i> <b><?= $booking['adults'] ?></b> Người lớn</span>
                            <span><i class="fas fa-child me-1"></i> <b><?= $booking['children'] ?></b> Trẻ em</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-bold text-uppercase small text-success py-3">
                    <i class="fas fa-users me-2"></i>Danh sách thành viên đoàn (Pax List)
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr class="small text-muted">
                                <th class="ps-4">#</th>
                                <th>Họ và tên</th>
                                <th>Giới tính</th>
                                <th>Ngày sinh</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($paxList)): $i=1; foreach($paxList as $p): ?>
                            <tr>
                                <td class="ps-4 text-muted"><?= $i++ ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($p['full_name']) ?></td>
                                <td><?= ($p['gender']=='male') ? 'Nam' : 'Nữ' ?></td>
                                <td><?= !empty($p['dob']) ? date('d/m/Y', strtotime($p['dob'])) : '' ?></td>
                                <td class="text-danger small"><?= htmlspecialchars($p['note']) ?></td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr><td colspan="5" class="text-center py-3 text-muted small">Chưa cập nhật danh sách đoàn.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold text-uppercase small text-warning text-dark py-3">
                    <i class="fas fa-concierge-bell me-2"></i>Dịch vụ / Chi phí vận hành
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr class="small text-muted">
                                <th class="ps-4">Loại</th>
                                <th>Nhà cung cấp</th>
                                <th>Chi tiết</th>
                                <th class="text-end pe-4">Chi phí (Net)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $totalCost=0; if(!empty($services)): foreach($services as $s): $totalCost += $s['cost']; ?>
                            <tr>
                                <td class="ps-4"><span class="badge bg-light text-dark border"><?= ucfirst($s['service_type']) ?></span></td>
                                <td class="fw-bold small"><?= htmlspecialchars($s['supplier_name'] ?? 'NCC lẻ') ?></td>
                                <td class="small text-muted"><?= htmlspecialchars($s['description']) ?></td>
                                <td class="text-end pe-4 text-danger fw-bold"><?= number_format($s['cost']) ?> ₫</td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr><td colspan="4" class="text-center py-3 text-muted small">Chưa đặt dịch vụ nào.</td></tr>
                            <?php endif; ?>
                        </tbody>
                        <?php if($totalCost > 0): ?>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Tổng chi phí thực tế:</td>
                                <td class="text-end pe-4 fw-bold text-danger fs-6"><?= number_format($totalCost) ?> ₫</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end fw-bold text-success">Lợi nhuận ước tính:</td>
                                <td class="text-end pe-4 fw-bold text-success fs-5"><?= number_format($booking['total_price'] - $totalCost) ?> ₫</td>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>