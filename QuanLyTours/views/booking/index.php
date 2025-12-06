<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
    .modal { z-index: 9999 !important; }
    .modal-backdrop { z-index: 9998 !important; }
    .table td { vertical-align: middle; }
    .progress { background-color: #e9ecef; border-radius: 0.25rem; box-shadow: inset 0 1px 2px rgba(0,0,0,.075); }
    .filter-bar { background: #fff; border-bottom: 1px solid #eee; padding: 15px 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.02); margin-bottom: 20px; }
    .btn-icon { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; margin-right: 2px; }
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
                elseif($_GET['msg']=='locked') echo 'LỖI: Không thể thay đổi danh sách khi Đã chốt cọc!';
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-secondary"><i class="fas fa-file-invoice-dollar me-2"></i>Quản lý Booking</h4>
        <a href="index.php?action=booking-create" class="btn btn-primary shadow-sm"><i class="fas fa-plus me-2"></i>Tạo Booking Mới</a>
    </div>

    <div class="filter-bar">
        <form action="index.php" method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="action" value="booking-list">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Từ khóa</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="keyword" class="form-control border-start-0" placeholder="Mã BK, Tên, SĐT..." value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">-- Tất cả --</option>
                    <option value="new" <?= (isset($_GET['status']) && $_GET['status']=='new')?'selected':'' ?>>Mới</option>
                    <option value="confirmed" <?= (isset($_GET['status']) && $_GET['status']=='confirmed')?'selected':'' ?>>Đã xác nhận</option>
                    <option value="deposited" <?= (isset($_GET['status']) && $_GET['status']=='deposited')?'selected':'' ?>>Đã cọc</option>
                    <option value="completed" <?= (isset($_GET['status']) && $_GET['status']=='completed')?'selected':'' ?>>Hoàn tất</option>
                    <option value="cancelled" <?= (isset($_GET['status']) && $_GET['status']=='cancelled')?'selected':'' ?>>Đã hủy</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted">Từ ngày</label>
                <input type="date" name="date_from" class="form-control" value="<?= $_GET['date_from'] ?? '' ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted">Đến ngày</label>
                <input type="date" name="date_to" class="form-control" value="<?= $_GET['date_to'] ?? '' ?>">
            </div>
            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Lọc</button>
                    <a href="index.php?action=booking-list" class="btn btn-outline-secondary w-50" title="Xóa lọc"><i class="fas fa-sync-alt"></i></a>
                </div>
            </div>
        </form>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0" id="bookingTable">
                <thead class="bg-light text-secondary text-center">
                    <tr>
                        <th class="ps-4 text-start">Mã BK</th>
                        <th class="text-start">Khách hàng</th>
                        <th class="text-start">Tour & Thời gian</th>
                        <th>Số lượng</th>
                        <th class="text-start" style="min-width: 180px;">Tài chính</th>
                        <th style="width: 120px;">Trạng thái</th>
                        <th class="text-end pe-4" style="min-width: 250px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($bookings)): ?>
                        <?php foreach ($bookings as $bk): ?>
                        
                        <?php 
                            $isZeroPrice = ($bk['total_price'] <= 0);
                            $total = $bk['total_price'] ?? 0;
                            $deposit = $bk['deposit_amount'] ?? 0;
                            $remain = $total - $deposit;
                            
                            $percent = ($total > 0) ? round(($deposit / $total) * 100) : 0;
                            if($percent > 100) $percent = 100;
                            $progressColor = ($percent >= 100) ? 'bg-success' : (($percent > 0) ? 'bg-warning' : 'bg-danger');

                            $st = $bk['status'] ?? 'new';
                            $minDepPercent = $bk['min_deposit'] ?? 30; // Lấy % cọc tối thiểu
                        ?>

                        <tr class="<?= $isZeroPrice ? 'bg-danger bg-opacity-10' : '' ?>">
                            
                            <td class="ps-4 fw-bold text-primary text-start"><?= htmlspecialchars($bk['booking_code'] ?? '') ?></td>
                            
                            <td class="text-start">
                                <div class="fw-bold text-dark"><?= htmlspecialchars($bk['customer_name'] ?? 'Khách lẻ') ?></div>
                                <?php if(!empty($bk['customer_id_card'])): ?>
                                    <span class="badge bg-light text-dark border my-1">
                                        <i class="fas fa-id-card me-1 text-secondary"></i> <?= htmlspecialchars($bk['customer_id_card']) ?>
                                    </span>
                                <?php endif; ?>
                                <div class="small text-muted">
                                    <i class="fas fa-phone-alt me-1" style="font-size: 0.8rem;"></i> <?= htmlspecialchars($bk['customer_phone'] ?? '') ?>
                                </div>
                            </td>

                            <td class="text-start">
                                <span class="badge bg-secondary bg-opacity-10 text-dark border"><?= htmlspecialchars($bk['tour_code'] ?? 'N/A') ?></span>
                                <div class="fw-bold text-truncate my-1" style="max-width: 150px;" title="<?= htmlspecialchars($bk['tour_name'] ?? '') ?>"><?= htmlspecialchars($bk['tour_name'] ?? 'Tour đã xóa') ?></div>
                                <div class="small text-muted">
                                    <i class="far fa-calendar-alt me-1"></i> <?= !empty($bk['travel_date']) ? date('d/m/Y', strtotime($bk['travel_date'])) : '--' ?>
                                    <?php if(!empty($bk['return_date'])): ?> - <?= date('d/m/Y', strtotime($bk['return_date'])) ?> <?php endif; ?>
                                </div>
                            </td>

                            <td class="text-center">
                                <div><span class="fw-bold"><?= $bk['adults'] ?? 0 ?></span> Lớn</div>
                                <?php if(!empty($bk['children']) && $bk['children'] > 0): ?><div class="small text-muted"><span class="fw-bold"><?= $bk['children'] ?></span> Trẻ</div><?php endif; ?>
                            </td>

                            <td class="text-start">
                                <?php if($total > 0): ?>
                                    <div class="d-flex justify-content-between small fw-bold mb-1"><span>Tổng:</span><span><?= number_format($total) ?></span></div>
                                    <div class="progress mb-2" style="height: 6px;"><div class="progress-bar <?= $progressColor ?>" role="progressbar" style="width: <?= $percent ?>%"></div></div>
                                    <?php if($remain <= 0): ?>
                                        <div class="text-success small fw-bold text-end"><i class="fas fa-check-circle me-1"></i> Đã thanh toán đủ</div>
                                    <?php else: ?>
                                        <div class="d-flex justify-content-between small text-muted"><span>Đã trả:</span><span class="<?= ($deposit > 0) ? 'text-dark fw-bold' : '' ?>"><?= number_format($deposit) ?></span></div>
                                        <div class="d-flex justify-content-between small fw-bold text-danger mt-1 pt-1 border-top border-light"><span>Còn nợ:</span><span><?= number_format($remain) ?></span></div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge bg-danger bg-opacity-75"><i class="fas fa-exclamation-triangle me-1"></i>Chưa có giá</span>
                                <?php endif; ?>
                            </td>

                            <td class="text-center">
                                <?php 
                                    $badge = 'secondary'; $label = 'Mới';
                                    if($st == 'confirmed') { $badge = 'primary'; $label = 'Đã xác nhận'; }
                                    elseif($st == 'deposited') { $badge = 'warning text-dark'; $label = 'Đã cọc'; }
                                    elseif($st == 'completed') { $badge = 'success'; $label = 'Hoàn tất'; }
                                    elseif($st == 'cancelled') { $badge = 'danger'; $label = 'Đã hủy'; }
                                ?>
                                <span class="badge bg-<?= $badge ?> p-2"><?= $label ?></span>
                            </td>

                            <td class="text-end pe-4 text-nowrap">
                                <div class="d-flex justify-content-end gap-1">
                                    
                                    <?php if($st == 'new'): ?>
                                        <a href="index.php?action=booking-status&id=<?= $bk['id'] ?>&status=confirmed" class="btn btn-primary btn-sm" title="Duyệt đơn"><i class="fas fa-check"></i> Xác nhận</a>
                                    <?php elseif(($st == 'confirmed' || $st == 'deposited') && $total > 0 && $remain > 0): ?>
                                        <button class="btn btn-warning btn-sm text-dark fw-bold" 
                                                onclick="openDepositModal(<?= $bk['id'] ?>, '<?= $bk['booking_code'] ?? '' ?>', <?= $total ?>, <?= $deposit ?>, <?= $minDepPercent ?>)"
                                                title="Thu tiền thanh toán">
                                            <i class="fas fa-hand-holding-usd"></i> Thu
                                        </button>
                                    <?php elseif($remain <= 0 && $st != 'completed' && $st != 'cancelled' && $st != 'new'): ?>
                                        <a href="index.php?action=booking-status&id=<?= $bk['id'] ?>&status=completed" class="btn btn-success btn-sm" title="Kết thúc tour"><i class="fas fa-flag-checkered"></i> Xong</a>
                                    <?php endif; ?>

                                    <div class="btn-group">
                                        <button type="button" class="btn btn-light btn-sm border dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"><i class="fas fa-cog"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow">
                                            <li><a class="dropdown-item" href="index.php?action=booking-invoice&id=<?= $bk['id'] ?>" target="_blank"><i class="fas fa-print me-2 text-secondary"></i>In Hóa đơn</a></li>
                                            <li><a class="dropdown-item" href="index.php?action=booking-contract&id=<?= $bk['id'] ?>" target="_blank"><i class="fas fa-file-contract me-2 text-success"></i>In Hợp đồng</a></li>
                                            
                                            <li><hr class="dropdown-divider"></li>
                                            
                                            <li><a class="dropdown-item" href="index.php?action=booking-detail&id=<?= $bk['id'] ?>"><i class="fas fa-eye me-2 text-info"></i>Xem chi tiết</a></li>
                                            <li><a class="dropdown-item" href="index.php?action=booking-ops&id=<?= $bk['id'] ?>"><i class="fas fa-list-ul me-2 text-dark"></i>Điều hành & Pax</a></li>
                                            
                                            <?php if($st != 'cancelled' && $st != 'completed'): ?>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item" href="index.php?action=booking-edit&id=<?= $bk['id'] ?>"><i class="fas fa-edit me-2 text-primary"></i>Sửa thông tin</a></li>
                                                <li><a class="dropdown-item text-danger" href="index.php?action=booking-status&id=<?= $bk['id'] ?>&status=cancelled" onclick="return confirm('Hủy đơn này?')"><i class="fas fa-times me-2"></i>Hủy đơn hàng</a></li>
                                            <?php endif; ?>
                                            
                                            <?php if($st == 'cancelled'): ?>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="index.php?action=booking-delete&id=<?= $bk['id'] ?>" onclick="return confirm('CẢNH BÁO: Hành động này sẽ xóa vĩnh viễn đơn hàng. Bạn có chắc chắn?')"><i class="fas fa-trash me-2"></i>Xóa vĩnh viễn</a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
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

<div class="modal fade" id="depositModal" tabindex="-1" aria-hidden="true" style="z-index: 99999 !important;">
    <style>.modal-backdrop { z-index: 99998 !important; }</style>
    <div class="modal-dialog modal-dialog-centered">
        <form action="index.php?action=booking-deposit" method="POST">
            <input type="hidden" name="booking_id" id="deposit_booking_id">
            <input type="hidden" name="total_price_hidden" id="deposit_total_hidden">
            <input type="hidden" name="min_deposit_hidden" id="deposit_min_percent">
            <input type="hidden" id="deposit_current_paid">
            
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-warning border-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-money-bill-wave me-2"></i>Thu Tiền Cọc</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body bg-light p-4">
                    <div class="card p-3 border-0 shadow-sm mb-3">
                        <div class="d-flex justify-content-between mb-1"><span class="text-muted">Mã Booking:</span><strong id="deposit_code_display" class="text-primary">---</strong></div>
                        <div class="d-flex justify-content-between mb-1"><span class="text-muted">Tổng giá trị:</span><strong id="deposit_total_display" class="text-dark">0 ₫</strong></div>
                        <div class="d-flex justify-content-between mb-1"><span class="text-muted">Đã thanh toán:</span><strong id="deposit_paid_display" class="text-success">0 ₫</strong></div>
                        <div class="border-top my-2"></div>
                        <div class="d-flex justify-content-between"><span class="text-danger fw-bold">CÒN PHẢI THU:</span><strong id="deposit_remain_display" class="text-danger fs-5">0 ₫</strong></div>
                        <div class="d-flex justify-content-between text-muted small mt-2">
                            <span>Yêu cầu cọc tối thiểu:</span>
                            <strong class="text-danger"><span id="deposit_min_display">0 ₫</span> (<span id="deposit_percent_display">0</span>%)</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-success">Số tiền khách thanh toán (VNĐ)</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white text-success border-end-0"><i class="fas fa-wallet"></i></span>
                            <input type="number" name="deposit_amount" id="deposit_amount_input" class="form-control border-start-0 fw-bold fs-4 text-dark" placeholder="Ví dụ: 2000000" required>
                        </div>
                        <div class="mt-2 d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="fillAmount(30)">30%</button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="fillAmount(50)">50%</button>
                            <button type="button" class="btn btn-sm btn-outline-success fw-bold" onclick="fillAmount('full')">Thanh toán hết</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Hình thức thanh toán</label>
                        <select name="payment_method" class="form-select">
                            <option value="Chuyển khoản">🏦 Chuyển khoản</option>
                            <option value="Tiền mặt">💵 Tiền mặt</option>
                            <option value="Cổng thanh toán">💳 Thẻ / VNPAY</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Ghi chú giao dịch</label>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // 1. Chuyển Modal ra khỏi Main Content để tránh lỗi Z-Index
    document.addEventListener("DOMContentLoaded", function() {
        const modal = document.getElementById('depositModal');
        if(modal) document.body.appendChild(modal); 
    });

    // 2. Hàm mở Modal và TỰ ĐỘNG ĐIỀN TIỀN CỌC
    function openDepositModal(id, code, total, deposit, minPercent) {
        document.getElementById('deposit_booking_id').value = id;
        document.getElementById('deposit_total_hidden').value = total;
        document.getElementById('deposit_current_paid').value = deposit;
        document.getElementById('deposit_min_percent').value = minPercent;

        document.getElementById('deposit_code_display').innerText = code;
        document.getElementById('deposit_total_display').innerText = new Intl.NumberFormat('vi-VN').format(total) + ' ₫';
        document.getElementById('deposit_paid_display').innerText = new Intl.NumberFormat('vi-VN').format(deposit) + ' ₫';
        
        let remain = total - deposit;
        document.getElementById('deposit_remain_display').innerText = new Intl.NumberFormat('vi-VN').format(remain) + ' ₫';

        // Tính toán và hiển thị min deposit
        let minAmount = (total * minPercent) / 100;
        document.getElementById('deposit_min_display').innerText = new Intl.NumberFormat('vi-VN').format(minAmount) + ' ₫';
        document.getElementById('deposit_percent_display').innerText = minPercent;
        
        // Tự động điền số tiền tối thiểu vào ô nhập
        document.getElementById('deposit_amount_input').value = Math.round(minAmount);
        
        var myModal = new bootstrap.Modal(document.getElementById('depositModal'));
        myModal.show();
    }

    // 3. Hàm điền tiền nhanh
    function fillAmount(type) {
        let total = parseFloat(document.getElementById('deposit_total_hidden').value);
        let paid = parseFloat(document.getElementById('deposit_current_paid').value);
        let minPercent = parseFloat(document.getElementById('deposit_min_percent').value);
        let amount = 0;

        if (type === 'full') {
            amount = total - paid; // Thanh toán nốt phần còn thiếu
        } else {
            amount = (total * type) / 100; // Tính theo %
        }

        document.getElementById('deposit_amount_input').value = Math.round(amount);
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>