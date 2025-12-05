<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid p-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="index.php?action=booking-list" class="text-decoration-none text-secondary mb-1 d-block">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
            <h4 class="fw-bold text-primary m-0">
                Thông tin đơn hàng: <span class="text-dark">#<?= htmlspecialchars($booking['booking_code']) ?></span>
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
                    <p class="text-muted small mb-3">
                        <i class="fas fa-id-card me-2"></i> <?= htmlspecialchars($booking['customer_id_card'] ?? '---') ?>
                    </p>
                    
                    <hr class="my-2">
                    <div class="my-2"><i class="fas fa-phone me-2 text-secondary"></i> <?= htmlspecialchars($booking['customer_phone']) ?></div>
                    <div class="my-2"><i class="fas fa-envelope me-2 text-secondary"></i> <?= htmlspecialchars($booking['customer_email'] ?? '---') ?></div>
                    
                    <div class="alert alert-warning mt-3 mb-0 small">
                        <i class="fas fa-sticky-note me-1"></i> <b>Ghi chú:</b> <?= htmlspecialchars($booking['note'] ?? 'Không có') ?>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold text-uppercase small text-secondary py-3">Thành viên đoàn (<?= count($paxList) ?>)</div>
                <div class="card-body p-0">
                     <ul class="list-group list-group-flush">
                        <?php if(!empty($paxList)): foreach($paxList as $p): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><?= htmlspecialchars($p['full_name']) ?></span>
                                <small class="text-muted"><?= ($p['gender']=='male')?'Nam':'Nữ' ?></small>
                            </li>
                        <?php endforeach; else: ?>
                            <li class="list-group-item text-center text-muted small">Chưa có danh sách đoàn.</li>
                        <?php endif; ?>
                     </ul>
                     <div class="p-2 text-center">
                         <a href="index.php?action=booking-ops&id=<?= $booking['id'] ?>" class="small text-decoration-none">Quản lý danh sách đoàn &rarr;</a>
                     </div>
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
                            <h4 class="fw-bold mb-1"><?= htmlspecialchars($tour['name']) ?></h4>
                            <div class="mb-2">
                                <span class="badge bg-info text-dark border border-info me-1">
                                    <?= ($tour['type']=='domestic')?'Trong nước':'Quốc tế' ?>
                                </span>
                                <span class="badge bg-secondary"><?= htmlspecialchars($tour['code']) ?></span>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-calendar-alt text-primary me-2 fa-lg"></i>
                                        <div>
                                            <small class="text-muted d-block">Ngày khởi hành</small>
                                            <strong><?= date('d/m/Y', strtotime($booking['travel_date'])) ?></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-undo text-secondary me-2 fa-lg"></i>
                                        <div>
                                            <small class="text-muted d-block">Ngày về (Dự kiến)</small>
                                            <strong><?= !empty($booking['return_date']) ? date('d/m/Y', strtotime($booking['return_date'])) : '---' ?></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-users text-success me-2 fa-lg"></i>
                                        <div>
                                            <small class="text-muted d-block">Số lượng khách</small>
                                            <strong><?= $booking['adults'] ?></strong> Lớn, <strong><?= $booking['children'] ?></strong> Trẻ
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold mx-auto" 
                                             style="width: 40px; height: 40px;">
                                            <?= $day['day_number'] ?>
                                        </div>
                                        <small class="text-muted d-block mt-1">Ngày</small>
                                    </div>
                                    <div class="flex-grow-1 ms-3 pb-4 border-bottom">
                                        <h6 class="fw-bold text-uppercase text-dark mb-2"><?= htmlspecialchars($day['title']) ?></h6>
                                        <p class="text-secondary mb-2" style="white-space: pre-line;"><?= htmlspecialchars($day['description']) ?></p>
                                        
                                        <div class="d-flex flex-wrap gap-2 mt-2">
                                            <?php if(!empty($day['meals'])): ?>
                                                <span class="badge bg-warning text-dark border border-warning"><i class="fas fa-utensils me-1"></i> <?= $day['meals'] ?></span>
                                            <?php endif; ?>
                                            <?php if(!empty($day['accommodation'])): ?>
                                                <span class="badge bg-info text-dark border border-info"><i class="fas fa-bed me-1"></i> <?= $day['accommodation'] ?></span>
                                            <?php endif; ?>
                                            <?php if(!empty($day['spot'])): ?>
                                                <span class="badge bg-light text-dark border"><i class="fas fa-map-marker-alt me-1"></i> <?= $day['spot'] ?></span>
                                            <?php endif; ?>
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
                    <h6 class="fw-bold m-0 text-dark"><i class="fas fa-concierge-bell me-2"></i>Dịch vụ đã đặt (Điều hành)</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light small">
                            <tr>
                                <th class="ps-4">Loại</th>
                                <th>Nhà cung cấp</th>
                                <th>Chi tiết</th>
                                <th class="text-end pe-4">Chi phí</th>
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
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>