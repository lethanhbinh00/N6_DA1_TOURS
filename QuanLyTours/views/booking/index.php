<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
.modal {
    z-index: 9999 !important;
}

.modal-backdrop {
    z-index: 9998 !important;
}

.table td {
    vertical-align: middle;
}

.progress {
    background-color: #e9ecef;
    border-radius: 0.25rem;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, .075);
}

/* Style cho thanh lọc */
.filter-bar {
    background: #fff;
    border-bottom: 1px solid #eee;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
    margin-bottom: 20px;
}
</style>

<div class="container-fluid p-4">

    <?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm">
        <i class="fas fa-check-circle me-2"></i>
        <?php 
                if($_GET['msg']=='booking_success') echo 'Tạo booking thành công!';
                elseif($_GET['msg']=='updated') echo 'Cập nhật thông tin thành công!';
                elseif($_GET['msg']=='deposit_success') echo 'Giao dịch thanh toán thành công!';
                elseif($_GET['msg']=='status_updated') echo 'Trạng thái đã được cập nhật!';
                elseif($_GET['msg']=='deleted') echo 'Đã xóa đơn hàng vĩnh viễn!';
            ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-secondary">
            <i class="fas fa-file-invoice-dollar me-2"></i>Quản lý Booking
        </h4>
        <a href="index.php?action=booking-create" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-2"></i>Tạo Booking Mới
        </a>
    </div>

    <div class="filter-bar">
        <form action="index.php" method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="action" value="booking-list">

            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Từ khóa</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="keyword" class="form-control border-start-0"
                        placeholder="Mã BK, Tên, SĐT, CCCD..." value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                </div>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">-- Tất cả --</option>
                    <option value="new" <?= (isset($_GET['status']) && $_GET['status']=='new')?'selected':'' ?>>Mới
                    </option>
                    <option value="confirmed"
                        <?= (isset($_GET['status']) && $_GET['status']=='confirmed')?'selected':'' ?>>Đã xác nhận
                    </option>
                    <option value="deposited"
                        <?= (isset($_GET['status']) && $_GET['status']=='deposited')?'selected':'' ?>>Đã cọc</option>
                    <option value="completed"
                        <?= (isset($_GET['status']) && $_GET['status']=='completed')?'selected':'' ?>>Hoàn tất</option>
                    <option value="cancelled"
                        <?= (isset($_GET['status']) && $_GET['status']=='cancelled')?'selected':'' ?>>Đã hủy</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted">Từ ngày (Khởi hành)</label>
                <input type="date" name="date_from" class="form-control" value="<?= $_GET['date_from'] ?? '' ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted">Đến ngày</label>
                <input type="date" name="date_to" class="form-control" value="<?= $_GET['date_to'] ?? '' ?>">
            </div>

            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Lọc dữ
                        liệu</button>
                    <a href="index.php?action=booking-list" class="btn btn-outline-secondary w-50" title="Xóa bộ lọc"><i
                            class="fas fa-sync-alt"></i></a>
                </div>
            </div>
        </form>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary text-center">
                    <tr>
                        <th class="ps-4 text-start">Mã BK</th>
                        <th class="text-start">Khách hàng</th>
                        <th class="text-start">Tour & Thời gian</th>
                        <th>Số lượng</th>
                        <th class="text-start" style="min-width: 180px;">Tài chính</th>
                        <th style="width: 120px;">Trạng thái</th>
                        <th style="width: 150px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($bookings)): ?>
                    <?php foreach ($bookings as $bk): ?>

                    <?php $isZeroPrice = ($bk['total_price'] <= 0); ?>

                    <tr class="<?= $isZeroPrice ? 'bg-danger bg-opacity-10' : '' ?>">

                        <td class="ps-4 fw-bold text-primary text-start">
                            <?= htmlspecialchars($bk['booking_code'] ?? '') ?>
                        </td>

                        <td class="text-start">
                            <div class="fw-bold text-dark"><?= htmlspecialchars($bk['customer_name'] ?? 'Khách lẻ') ?>
                            </div>
                            <?php if(!empty($bk['customer_id_card'])): ?>
                            <span class="badge bg-light text-dark border my-1">
                                <i class="fas fa-id-card me-1 text-secondary"></i>
                                <?= htmlspecialchars($bk['customer_id_card']) ?>
                            </span>
                            <?php endif; ?>
                            <div class="small text-muted">
                                <i class="fas fa-phone-alt me-1" style="font-size: 0.8rem;"></i>
                                <?= htmlspecialchars($bk['customer_phone'] ?? '') ?>
                            </div>
                        </td>

                        <td class="text-start">
                            <span class="badge bg-secondary bg-opacity-10 text-dark border">
                                <?= htmlspecialchars($bk['tour_code'] ?? 'N/A') ?>
                            </span>
                            <div class="fw-bold text-truncate my-1" style="max-width: 150px;"
                                title="<?= htmlspecialchars($bk['tour_name'] ?? '') ?>">
                                <?= htmlspecialchars($bk['tour_name'] ?? 'Tour đã xóa') ?>
                            </div>

                            <div class="small text-muted">
                                <i class="far fa-calendar-alt me-1 text-primary"></i>
                                <?= !empty($bk['travel_date']) ? date('d/m/Y', strtotime($bk['travel_date'])) : '--' ?>

                                <?php if(!empty($bk['return_date'])): ?>
                                <span class="text-secondary mx-1">➝</span>
                                <?= date('d/m/Y', strtotime($bk['return_date'])) ?>
                                <?php endif; ?>
                            </div>
                        </td>

                        <td class="text-center">
                            <div><span class="fw-bold"><?= $bk['adults'] ?? 0 ?></span> Lớn</div>
                            <?php if(!empty($bk['children']) && $bk['children'] > 0): ?>
                            <div class="small text-muted"><span class="fw-bold"><?= $bk['children'] ?></span> Trẻ</div>
                            <?php endif; ?>
                        </td>

                        <td class="text-start">
                            <?php 
                                    $total = $bk['total_price'] ?? 0;
                                    $deposit = $bk['deposit_amount'] ?? 0;
                                    $remain = $total - $deposit;
                                    
                                    $percent = ($total > 0) ? round(($deposit / $total) * 100) : 0;
                                    if($percent > 100) $percent = 100;
                                    $progressColor = ($percent >= 100) ? 'bg-success' : (($percent > 0) ? 'bg-warning' : 'bg-danger');
                                ?>

                            <?php if($total > 0): ?>
                            <div class="d-flex justify-content-between small fw-bold mb-1"><span
                                    class="text-secondary">Tổng:</span><span><?= number_format($total) ?></span></div>
                            <div class="progress mb-2" style="height: 6px;">
                                <div class="progress-bar <?= $progressColor ?>" role="progressbar"
                                    style="width: <?= $percent ?>%"></div>
                            </div>
                            <?php if($remain <= 0): ?>
                            <div class="text-success small fw-bold text-end"><i class="fas fa-check-circle me-1"></i> Đã
                                thanh toán đủ</div>
                            <?php else: ?>
                            <div class="d-flex justify-content-between small text-muted"><span>Đã trả:</span><span
                                    class="<?= ($deposit > 0) ? 'text-dark fw-bold' : '' ?>"><?= number_format($deposit) ?></span>
                            </div>
                            <div
                                class="d-flex justify-content-between small fw-bold text-danger mt-1 pt-1 border-top border-light">
                                <span>Còn nợ:</span><span><?= number_format($remain) ?></span></div>
                            <?php endif; ?>
                            <?php else: ?>
                            <span class="badge bg-danger bg-opacity-75"><i
                                    class="fas fa-exclamation-triangle me-1"></i>Chưa có giá</span>
                            <?php endif; ?>
                        </td>

                        <td class="text-center">
                            <?php 
                                    $st = $bk['status'] ?? 'new';
                                    if($st == 'new') echo '<span class="badge bg-secondary">Mới</span>';
                                    elseif($st == 'confirmed') echo '<span class="badge bg-primary">Đã xác nhận</span>';
                                    elseif($st == 'deposited') echo '<span class="badge bg-warning text-dark">Đã cọc</span>';
                                    elseif($st == 'completed') echo '<span class="badge bg-success">Hoàn tất</span>';
                                    elseif($st == 'cancelled') echo '<span class="badge bg-danger">Đã hủy</span>';
                                    else echo '<span class="badge bg-dark">Khác</span>';
                                ?>
                        </td>

                        <td class="text-center">
                            <div class="d-flex justify-content-center align-items-center gap-1">

                                <?php if($st == 'new'): ?>
                                <a href="index.php?action=booking-status&id=<?= $bk['id'] ?>&status=confirmed"
                                    class="btn btn-primary btn-sm" title="Duyệt"><i class="fas fa-check"></i></a>
                                <?php elseif(($st == 'confirmed' || $st == 'deposited') && $total > 0 && $remain > 0): ?>
                                <button class="btn btn-warning btn-sm text-dark fw-bold"
                                    onclick="openDepositModal(<?= $bk['id'] ?>, '<?= $bk['booking_code'] ?? '' ?>', <?= $total ?>)"
                                    title="Thu tiền"><i class="fas fa-hand-holding-usd"></i></button>
                                <?php elseif($remain <= 0 && $st != 'completed' && $st != 'cancelled'): ?>
                                <a href="index.php?action=booking-status&id=<?= $bk['id'] ?>&status=completed"
                                    class="btn btn-success btn-sm" title="Kết thúc"><i
                                        class="fas fa-flag-checkered"></i></a>
                                <?php else: ?>
                                <a href="index.php?action=booking-detail&id=<?= $bk['id'] ?>"
                                    class="btn btn-info btn-sm text-white" title="Xem"><i class="fas fa-eye"></i></a>
                                <?php endif; ?>

                                <div class="btn-group">
                                    <button type="button"
                                        class="btn btn-light btn-sm border dropdown-toggle dropdown-toggle-split"
                                        data-bs-toggle="dropdown"></button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow">
                                        <li><a class="dropdown-item"
                                                href="index.php?action=booking-detail&id=<?= $bk['id'] ?>"><i
                                                    class="fas fa-eye me-2 text-info"></i>Xem chi tiết</a></li>
                                        <li><a class="dropdown-item"
                                                href="index.php?action=booking-ops&id=<?= $bk['id'] ?>"><i
                                                    class="fas fa-list-ul me-2 text-dark"></i>Điều hành & Pax</a></li>
                                        <?php if($st != 'cancelled' && $st != 'completed'): ?>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item"
                                                href="index.php?action=booking-edit&id=<?= $bk['id'] ?>"><i
                                                    class="fas fa-edit me-2 text-primary"></i>Sửa thông tin</a></li>
                                        <li><a class="dropdown-item text-danger"
                                                href="index.php?action=booking-status&id=<?= $bk['id'] ?>&status=cancelled"
                                                onclick="return confirm('Hủy đơn này?')"><i
                                                    class="fas fa-times me-2"></i>Hủy đơn</a></li>
                                        <?php endif; ?>
                                        <?php if($st == 'cancelled'): ?>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item text-danger"
                                                href="index.php?action=booking-delete&id=<?= $bk['id'] ?>"
                                                onclick="return confirm('Xóa vĩnh viễn?')"><i
                                                    class="fas fa-trash me-2"></i>Xóa vĩnh viễn</a></li>
                                        <?php endif; ?>
                                        
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">Chưa có booking nào phù hợp.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="depositModal" tabindex="-1" style="z-index: 99999 !important;">
    <style>
    .modal-backdrop {
        z-index: 99998 !important;
    }
    </style>
    <div class="modal-dialog modal-dialog-centered">
        <form action="index.php?action=booking-deposit" method="POST">
            <input type="hidden" name="booking_id" id="deposit_booking_id">
            <input type="hidden" name="total_price_hidden" id="deposit_total_hidden">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-warning border-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-money-bill-wave me-2"></i>Thanh Toán /
                        Thu Cọc</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light p-4">
                    <div class="card p-3 border-0 shadow-sm mb-3">
                        <div class="d-flex justify-content-between mb-1"><span class="text-muted">Mã
                                Booking:</span><strong id="deposit_code_display" class="text-primary">---</strong></div>
                        <div class="d-flex justify-content-between"><span class="text-muted">Tổng giá trị
                                Tour:</span><strong id="deposit_total_display" class="text-success">0 ₫</strong></div>
                    </div>
                    <div class="mb-3"><label class="form-label fw-bold small text-uppercase text-success">SỐ TIỀN KHÁCH
                            THANH TOÁN (VNĐ)</label>
                        <div class="input-group input-group-lg"><span
                                class="input-group-text bg-white text-success border-end-0"><i
                                    class="fas fa-wallet"></i></span><input type="number" name="deposit_amount"
                                class="form-control border-start-0 fw-bold fs-4 text-dark" placeholder="Ví dụ: 2000000"
                                required></div>
                    </div>
                    <div class="mb-3"><label class="form-label fw-bold small text-uppercase text-secondary">Hình thức
                            thanh toán</label><select name="payment_method" class="form-select">
                            <option value="Chuyển khoản">🏦 Chuyển khoản</option>
                            <option value="Tiền mặt">💵 Tiền mặt</option>
                            <option value="Cổng thanh toán">💳 Thẻ / VNPAY</option>
                        </select></div>
                    <div class="mb-3"><label class="form-label fw-bold small text-uppercase text-secondary">Ghi chú giao
                            dịch</label><textarea name="payment_note" class="form-control" rows="2"
                            placeholder="Mã giao dịch..."></textarea></div>
                </div>
                <div class="modal-footer bg-white border-0"><button type="button" class="btn btn-secondary px-4"
                        data-bs-dismiss="modal">Đóng</button><button type="submit"
                        class="btn btn-warning px-4 fw-bold shadow-sm">Xác nhận</button></div>
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById('depositModal');
    if (modal) document.body.appendChild(modal);
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