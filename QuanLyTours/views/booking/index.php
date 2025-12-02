<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
    .modal { z-index: 9999 !important; }
    .modal-backdrop { z-index: 9998 !important; }
    .table td { vertical-align: middle; }
</style>

<div class="container-fluid p-4">

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="fas fa-check-circle me-2"></i>
            <?php 
                if($_GET['msg']=='booking_success') echo 'Tạo booking thành công!';
                elseif($_GET['msg']=='updated') echo 'Cập nhật thông tin thành công!';
                elseif($_GET['msg']=='deposit_success') echo 'Đã thu tiền cọc thành công!';
                elseif($_GET['msg']=='status_updated') echo 'Trạng thái đã được cập nhật!';
                elseif($_GET['msg']=='deleted') echo 'Đã xóa đơn hàng!';
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-secondary">
            <i class="fas fa-file-invoice-dollar me-2"></i>Quản lý Booking
        </h4>
        <div>
            <a href="index.php?action=index" class="btn btn-outline-secondary me-2 shadow-sm"><i class="fas fa-suitcase me-1"></i> DS Tour</a>
            <a href="index.php?action=booking-create" class="btn btn-primary shadow-sm"><i class="fas fa-plus me-2"></i>Tạo Booking Mới</a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4">Mã BK</th>
                        <th>Khách hàng</th>
                        <th>Tour & Thời gian</th>
                        <th>Số lượng</th>
                        <th>Tài chính</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($bookings)): ?>
                        <?php foreach ($bookings as $bk): ?>
                        
                        <?php $isZeroPrice = ($bk['total_price'] <= 0); ?>

                        <tr class="<?= $isZeroPrice ? 'bg-danger bg-opacity-10' : '' ?>"> <td class="ps-4 fw-bold text-primary"><?= htmlspecialchars($bk['booking_code'] ?? '') ?></td>
                            
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($bk['customer_name'] ?? 'Khách lẻ') ?></div>
                                <?php if(!empty($bk['customer_id_card'])): ?>
                                    <span class="badge bg-light text-dark border my-1">
                                        <i class="fas fa-id-card me-1 text-secondary"></i> <?= htmlspecialchars($bk['customer_id_card']) ?>
                                    </span>
                                <?php endif; ?>
                                <div class="small text-muted"><i class="fas fa-phone-alt me-1"></i> <?= htmlspecialchars($bk['customer_phone'] ?? '') ?></div>
                            </td>

                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-dark border"><?= htmlspecialchars($bk['tour_code'] ?? 'N/A') ?></span>
                                <div class="small text-truncate fw-bold mt-1" style="max-width: 150px;"><?= htmlspecialchars($bk['tour_name'] ?? '') ?></div>
                                <div class="small text-muted">
                                    <i class="far fa-calendar-alt me-1"></i> <?= !empty($bk['travel_date']) ? date('d/m/Y', strtotime($bk['travel_date'])) : '' ?>
                                </div>
                            </td>

                            <td>
                                <div><span class="fw-bold"><?= $bk['adults'] ?? 0 ?></span> Lớn</div>
                                <?php if(!empty($bk['children']) && $bk['children'] > 0): ?>
                                    <div class="small text-muted"><span class="fw-bold"><?= $bk['children'] ?></span> Trẻ</div>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php 
                                    $total = $bk['total_price'] ?? 0;
                                    $deposit = $bk['deposit_amount'] ?? 0;
                                    $remain = $total - $deposit;
                                ?>
                                
                                <?php if($total > 0): ?>
                                    <div class="d-flex justify-content-between small" style="min-width: 140px;">
                                        <span>Tổng:</span> <span class="fw-bold"><?= number_format($total) ?></span>
                                    </div>
                                    <?php if($deposit > 0): ?>
                                        <div class="d-flex justify-content-between small text-success">
                                            <span>Đã cọc:</span> <span><?= number_format($deposit) ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between small text-danger border-top mt-1 pt-1">
                                            <span>Còn nợ:</span> <span class="fw-bold"><?= number_format($remain) ?></span>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge bg-danger">⚠ Chưa có giá</span>
                                    <div class="small text-danger fst-italic mt-1">Vui lòng cập nhật!</div>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php 
                                    $st = $bk['status'] ?? 'new';
                                    if($st == 'new') echo '<span class="badge bg-secondary">Mới</span>';
                                    elseif($st == 'confirmed') echo '<span class="badge bg-primary">Đã xác nhận</span>';
                                    elseif($st == 'deposited') echo '<span class="badge bg-warning text-dark">Đã cọc</span>';
                                    elseif($st == 'completed') echo '<span class="badge bg-success">Hoàn tất</span>';
                                    elseif($st == 'cancelled') echo '<span class="badge bg-danger">Đã hủy</span>';
                                ?>
                            </td>

                            <td class="text-end pe-4 text-nowrap">
                                
                                <?php if($st == 'new'): ?>
                                    <a href="index.php?action=booking-status&id=<?= $bk['id'] ?>&status=confirmed" 
                                       class="btn btn-sm btn-primary me-1 shadow-sm"><i class="fas fa-check"></i> Xác nhận</a>
                                <?php endif; ?>

                                <?php if($st == 'confirmed'): ?>
                                    <?php if($total > 0): ?>
                                        <button type="button" class="btn btn-sm btn-warning text-dark fw-bold me-1 shadow-sm" 
                                                onclick="openDepositModal(<?= $bk['id'] ?>, '<?= $bk['booking_code'] ?? '' ?>', <?= $total ?>)">
                                            <i class="fas fa-hand-holding-usd"></i> Thu Cọc
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-sm btn-secondary me-1 shadow-sm disabled" title="Cần cập nhật giá trước!">
                                            <i class="fas fa-ban"></i> Thu Cọc
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if($st == 'deposited'): ?>
                                    <a href="index.php?action=booking-status&id=<?= $bk['id'] ?>&status=completed" 
                                       class="btn btn-sm btn-success me-1 shadow-sm"><i class="fas fa-flag-checkered"></i> Hoàn tất</a>
                                <?php endif; ?>

                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-light border dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-cog"></i></button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow">
                                        <?php if($st != 'cancelled'): ?>
                                            <li><a class="dropdown-item fw-bold" href="index.php?action=booking-edit&id=<?= $bk['id'] ?>"><i class="fas fa-edit me-2 text-primary"></i>Sửa (Cập nhật giá)</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="index.php?action=booking-status&id=<?= $bk['id'] ?>&status=cancelled" onclick="return confirm('Hủy đơn này?')"><i class="fas fa-times me-2"></i>Hủy đơn hàng</a></li>
                                        <?php endif; ?>
                                        <?php if($st == 'cancelled'): ?>
                                            <li><a class="dropdown-item text-danger" href="index.php?action=booking-delete&id=<?= $bk['id'] ?>" onclick="return confirm('Xóa vĩnh viễn?')"><i class="fas fa-trash me-2"></i>Xóa vĩnh viễn</a></li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center py-5 text-muted">Chưa có dữ liệu.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="depositModal" tabindex="-1" style="z-index: 99999 !important;">
    <style>.modal-backdrop { z-index: 99998 !important; }</style>
    <div class="modal-dialog modal-dialog-centered">
        <form action="index.php?action=booking-deposit" method="POST">
            <input type="hidden" name="booking_id" id="deposit_booking_id">
            <input type="hidden" name="total_price_hidden" id="deposit_total_hidden">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-warning border-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-money-bill-wave me-2"></i>Thu Tiền Cọc</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light p-4">
                    <div class="card p-3 border-0 shadow-sm mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Mã Booking:</span>
                            <strong id="deposit_code_display" class="text-primary">---</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Tổng giá trị Tour:</span>
                            <strong id="deposit_total_display" class="text-success">0 ₫</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Số tiền thanh toán</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white text-warning border-end-0"><i class="fas fa-wallet"></i></span>
                            <input type="number" name="deposit_amount" class="form-control border-start-0 fw-bold fs-4" placeholder="Ví dụ: 5000000" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Hình thức</label>
                        <select name="payment_method" class="form-select">
                            <option value="Chuyển khoản">🏦 Chuyển khoản</option>
                            <option value="Tiền mặt">💵 Tiền mặt</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Ghi chú</label>
                        <textarea name="payment_note" class="form-control" rows="2" placeholder="Mã giao dịch..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-white border-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-warning px-4 fw-bold shadow-sm">Xác nhận</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modal = document.getElementById('depositModal');
        if(modal) document.body.appendChild(modal); 
    });

    function openDepositModal(id, code, total) {
        document.getElementById('deposit_booking_id').value = id;
        document.getElementById('deposit_total_hidden').value = total;
        document.getElementById('deposit_code_display').innerText = code;
        document.getElementById('deposit_total_display').innerText = new Intl.NumberFormat('vi-VN').format(total) + ' ₫';
        var myModal = new bootstrap.Modal(document.getElementById('depositModal'));
        myModal.show();
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>