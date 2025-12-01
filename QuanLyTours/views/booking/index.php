<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid p-4">

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="fas fa-check-circle me-2"></i>
            <?php 
                if($_GET['msg']=='booking_success') echo 'Tạo booking thành công!';
                elseif($_GET['msg']=='updated') echo 'Cập nhật thành công!';
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
            <a href="index.php?action=index" class="btn btn-outline-secondary me-2 shadow-sm">
                <i class="fas fa-suitcase me-1"></i> DS Tour
            </a>
            <a href="index.php?action=booking-create" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus me-2"></i>Tạo Booking Mới
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4">Mã BK</th>
                        <th>Khách hàng</th>
                        <th>Tour</th>
                        <th>Ngày đi</th>
                        <th>Tài chính</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($bookings)): ?>
                        <?php foreach ($bookings as $bk): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-primary"><?= htmlspecialchars($bk['booking_code'] ?? '') ?></td>
                            
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($bk['customer_name'] ?? 'Khách lẻ') ?></div>
                                <div class="small text-muted">
                                    <i class="fas fa-phone-alt me-1"></i> <?= htmlspecialchars($bk['customer_phone'] ?? '') ?>
                                </div>
                            </td>

                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-dark border"><?= htmlspecialchars($bk['tour_code'] ?? 'N/A') ?></span>
                                <div class="small text-truncate fw-bold mt-1" style="max-width: 150px;"><?= htmlspecialchars($bk['tour_name'] ?? '') ?></div>
                            </td>

                            <td><?= !empty($bk['travel_date']) ? date('d/m/Y', strtotime($bk['travel_date'])) : '' ?></td>

                            <td>
                                <?php 
                                    $total = $bk['total_price'] ?? 0;
                                    $deposit = $bk['deposit_amount'] ?? 0;
                                    $remain = $total - $deposit;
                                ?>
                                <div class="d-flex justify-content-between small" style="min-width: 120px;">
                                    <span>Tổng:</span> <span class="fw-bold"><?= number_format($total) ?></span>
                                </div>
                                <?php if($deposit > 0): ?>
                                    <div class="d-flex justify-content-between small text-success">
                                        <span>Đã cọc:</span> <span><?= number_format($deposit) ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between small text-danger border-top mt-1 pt-1">
                                        <span>Còn nợ:</span> <span><?= number_format($remain) ?></span>
                                    </div>
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
                                       class="btn btn-sm btn-primary me-1 shadow-sm" title="Duyệt đơn">
                                       <i class="fas fa-check"></i> Xác nhận
                                    </a>
                                <?php endif; ?>

                                <?php if($st == 'confirmed'): ?>
                                    <button type="button" class="btn btn-sm btn-warning text-dark fw-bold me-1 shadow-sm" 
                                            onclick="openDepositModal(<?= $bk['id'] ?>, '<?= $bk['booking_code'] ?>', <?= $bk['total_price'] ?>)">
                                        <i class="fas fa-hand-holding-usd"></i> Thu Cọc
                                    </button>
                                <?php endif; ?>

                                <?php if($st == 'deposited'): ?>
                                    <a href="index.php?action=booking-status&id=<?= $bk['id'] ?>&status=completed" 
                                       class="btn btn-sm btn-success me-1 shadow-sm" title="Hoàn tất tour">
                                       <i class="fas fa-flag-checkered"></i> Hoàn tất
                                    </a>
                                <?php endif; ?>

                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-light border dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow">
                                        <?php if($st != 'cancelled'): ?>
                                            <li><a class="dropdown-item" href="index.php?action=booking-edit&id=<?= $bk['id'] ?>"><i class="fas fa-edit me-2 text-primary"></i>Sửa thông tin</a></li>
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

<div class="modal fade" id="depositModal" tabindex="-1" style="z-index: 1060;">
    <style>
        .modal-backdrop.show {
            z-index: 1050; /* Thấp hơn z-index của modal */
        }
    </style>

    <div class="modal-dialog modal-dialog-centered">
        <form action="index.php?action=booking-deposit" method="POST">
            <input type="hidden" name="booking_id" id="deposit_booking_id">
            <input type="hidden" name="total_price_hidden" id="deposit_total_hidden">
            
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-warning bg-gradient border-bottom-0 py-3">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-money-bill-wave me-2"></i>Thu Tiền Cọc</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body bg-light p-4">
                    <div class="card border-0 shadow-sm p-3 mb-3 bg-white rounded">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fw-medium">Mã Booking:</span>
                            <strong id="deposit_code_display" class="text-primary fs-5">---</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border-top pt-2">
                            <span class="text-muted fw-medium">Tổng giá trị Tour:</span>
                            <strong id="deposit_total_display" class="text-success fs-4">0 ₫</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Số tiền khách thanh toán (VNĐ)</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white border-end-0 text-warning"><i class="fas fa-wallet"></i></span>
                            <input type="number" name="deposit_amount" class="form-control border-start-0 fw-bold text-dark fs-4" placeholder="Ví dụ: 5000000" required>
                        </div>
                        <small class="text-muted mt-2 d-block fst-italic"><i class="fas fa-info-circle me-1"></i> Hệ thống sẽ tự động tính số tiền còn nợ sau khi lưu.</small>
                    </div>
                </div>

                <div class="modal-footer bg-white border-top-0 py-3 justify-content-between">
                    <button type="button" class="btn btn-outline-secondary px-4 fw-medium" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Đóng
                    </button>
                    <button type="submit" class="btn btn-warning px-4 fw-bold shadow-sm">
                        <i class="fas fa-save me-2"></i>Xác nhận Thu Tiền
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    var depositModal;
    document.addEventListener('DOMContentLoaded', function() {
        depositModal = new bootstrap.Modal(document.getElementById('depositModal'), {
            keyboard: false
        });
    });

    function openDepositModal(id, code, total) {
        // Gán dữ liệu vào input ẩn
        document.getElementById('deposit_booking_id').value = id;
        document.getElementById('deposit_total_hidden').value = total;
        
        // Hiển thị thông tin lên giao diện
        document.getElementById('deposit_code_display').innerText = code;
        document.getElementById('deposit_total_display').innerText = new Intl.NumberFormat('vi-VN').format(total) + ' ₫';
        
        // Mở Modal
        depositModal.show();
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>