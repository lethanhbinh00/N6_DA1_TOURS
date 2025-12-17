<?php require_once __DIR__ . '/../layouts/header.php'; 
/**
 * YÊU CẦU: Controller phải truyền đủ các biến sau:
 * $booking, $tour, $paxList, $services, $suppliers, $users
 */// operations.php
$isLocked = !empty($booking['guide_id']); // Nếu đã có guide_id thì coi như đã xác nhận và khóa
?>

<style>
    .pax-table td { vertical-align: middle; }
    .status-badge { width: 90px; }
    .guide-info { font-size: 1.1rem; font-weight: bold; color: #007bff; }
    .service-table td { font-size: 0.9rem; }
    .btn-icon { width: 30px; height: 30px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }
    
    /* FIX LỖI MODAL BỊ MỜ/ẨN */
    .modal { background: rgba(0, 0, 0, 0.5); }
    .modal-backdrop { display: none !important; }
    body.modal-open { overflow: hidden; padding-right: 0 !important; }

    /* Style cho nút điểm danh nhanh */
    .btn-checkin { font-size: 1.2rem; transition: transform 0.2s; cursor: pointer; }
    .btn-checkin:hover { transform: scale(1.2); }
</style>

<div class="container-fluid p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-info">
            <i class="fas fa-cogs me-2"></i>Điều hành Tour: <?= htmlspecialchars($booking['booking_code'] ?? 'N/A') ?>
        </h4>
        <div class="d-flex gap-2">
            <a href="index.php?action=booking-detail&id=<?= $booking['id'] ?? 0 ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Hồ sơ Booking
            </a>
            <button class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="fas fa-print me-1"></i> In danh sách đoàn
            </button>
        </div>
    </div>
    
    <div id="ajax-alert-container"></div> <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="fas fa-check-circle me-2"></i>
            <?php 
                switch($_GET['msg']) {
                    case 'pax_status_updated': echo 'Cập nhật điểm danh thành công!'; break;
                    case 'pax_note_updated': echo 'Lưu ghi chú khách hàng thành công!'; break;
                    case 'guide_assigned': echo 'Đã phân công HDV phụ trách!'; break;
                    case 'supplier_paid': echo 'Xác nhận thanh toán thành công!'; break;
                    case 'service_added': echo 'Đã thêm chi phí dịch vụ mới!'; break;
                    case 'pax_added': echo 'Đã thêm khách mới thành công!'; break;
                    case 'import_success': echo 'Đã nhập danh sách thành công!'; break;
                    case 'delete_success': echo 'Đã xóa toàn bộ danh sách khách đoàn!'; break;
                    default: echo 'Thao tác thực hiện thành công!';
                }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-3">
            <div class="row small align-items-center">
                <div class="col-md-3 border-end">
                    <span class="text-muted">Tour:</span> <br>
                    <strong><?= htmlspecialchars($tour['code'] ?? '') ?> - <?= htmlspecialchars($tour['name'] ?? 'N/A') ?></strong>
                </div>
                <div class="col-md-2 border-end">
                    <span class="text-muted">Ngày đi:</span> <br>
                    <strong><?= date('d/m/Y', strtotime($booking['travel_date'] ?? 'now')) ?></strong>
                </div>
                <div class="col-md-3 border-end">
                    <span class="text-muted">Số lượng khách:</span> <br>
                    <strong><?= ($booking['adults'] ?? 0) + ($booking['children'] ?? 0) ?> (<?= count($paxList ?? []) ?> Pax thực tế)</strong>
                </div>
                <div class="col-md-4 text-end">
                    <span class="text-muted">HDV Phụ trách:</span> 
                    <strong class="guide-info me-2 text-primary"><?= htmlspecialchars($booking['guide_name'] ?? 'Chưa phân công') ?></strong>
                    <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#assignGuideModal">
                        <i class="fas fa-user-tag"></i> Phân công
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-users me-2"></i>Danh sách Khách đoàn & Điểm danh</span>
                        
                        <div class="action-buttons">
                            <?php if (!$isLocked): ?>
                                <button class="btn btn-light btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#addPaxModal">
                                    <i class="fas fa-plus-circle me-1"></i>Thêm khách
                                </button>
                                <button class="btn btn-success btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#importPaxModal">
                                    <i class="fas fa-file-import me-1"></i>Import
                                </button>
                                <button class="btn btn-danger btn-sm fw-bold" onclick="confirmDeleteAll()">
                                    <i class="fas fa-trash-alt me-1"></i>Xóa tất cả
                                </button>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-lock me-1"></i>Danh sách đã chốt
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <div class="card-body p-0">
                    <table class="table table-hover pax-table mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 50px;">STT</th>
                                <th>Họ tên / Liên hệ</th>
                                <th style="width: 120px;">Trạng thái</th>
                                <th class="text-center" style="width: 150px;">Nhiệm vụ HDV</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($paxList)): $stt = 1; foreach($paxList as $pax): ?>
                            <tr>
                                <td class="text-muted"><?= $stt++ ?></td>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($pax['full_name'] ?? '---') ?></div>
                                    <div class="small text-muted">
                                        NS: <?= date('d/m/Y', strtotime($pax['dob'] ?? 'now')) ?> | 
                                        <strong><?= ($pax['gender'] == 'male' || $pax['gender'] == 'Nam') ? 'Nam' : (($pax['gender'] == 'female' || $pax['gender'] == 'Nữ') ? 'Nữ' : 'Khác') ?></strong>
                                    </div>
                                </td>
                                <td>
                                    <?php 
                                        $pStatus = $pax['checkin_status'] ?? 'pending';
                                        $pBadge = ($pStatus == 'checked_in') ? 'success' : (($pStatus == 'absent') ? 'danger' : 'secondary');
                                        $statusText = ($pStatus == 'checked_in') ? 'CÓ MẶT' : (($pStatus == 'absent') ? 'VẮNG' : 'CHỜ');
                                    ?>
                                    <div id="badge-container-<?= $pax['id'] ?>">
                                        <span class="badge bg-<?= $pBadge ?> status-badge"><?= $statusText ?></span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-3 mb-1">
                                        <a href="javascript:void(0)" 
                                           class="btn-checkin text-success ajax-checkin" 
                                           data-id="<?= $pax['id'] ?>" 
                                           data-status="checked_in" 
                                           title="Xác nhận có mặt">
                                           <i class="<?= $pStatus == 'checked_in' ? 'fas' : 'far' ?> fa-check-circle" id="icon-check-<?= $pax['id'] ?>"></i>
                                        </a>
                                        <a href="javascript:void(0)" 
                                           class="btn-checkin text-danger ajax-checkin" 
                                           data-id="<?= $pax['id'] ?>" 
                                           data-status="absent" 
                                           title="Xác nhận vắng mặt">
                                           <i class="<?= $pStatus == 'absent' ? 'fas' : 'far' ?> fa-times-circle" id="icon-absent-<?= $pax['id'] ?>"></i>
                                        </a>
                                    </div>
                                    <div class="small">
                                        <a href="javascript:void(0)" class="text-decoration-none text-info" data-bs-toggle="modal" data-bs-target="#noteModal<?= $pax['id'] ?>">
                                            <i class="far fa-edit me-1"></i>Ghi chú yêu cầu
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr><td colspan="4" class="text-center py-5 text-muted">Chưa có danh sách khách đoàn.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-warning text-dark fw-bold d-flex justify-content-between align-items-center py-2">
                    <span><i class="fas fa-cash-register me-2"></i>Dịch vụ & Costing</span>
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                        <i class="fas fa-plus-circle"></i> Thêm dịch vụ
                    </button>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover service-table mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Ngày GD</th>
                                <th>Loại/NCC</th>
                                <th>Chi phí</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $totalCost = 0; $unpaidCost = 0;
                                if(!empty($services)): foreach($services as $s): 
                                    $totalCost += $s['cost'];
                                    $isPaid = ($s['supplier_payment_status'] == 'paid');
                                    if (!$isPaid) $unpaidCost += $s['cost'];
                            ?>
                            <tr>
                                <td class="text-muted small"><?= date('d/m/Y', strtotime($s['created_at'] ?? 'now')) ?></td>
                                <td>
                                    <div class="fw-bold text-primary text-uppercase"><?= htmlspecialchars($s['service_type'] ?? '---') ?></div>
                                    <div class="small text-muted"><?= htmlspecialchars($s['supplier_name'] ?? 'N/A') ?></div>
                                </td>
                                <td class="fw-bold">
                                    <?= number_format($s['cost'] ?? 0) ?>₫
                                    <div class="small">
                                        <?= $isPaid ? '<span class="text-success small">Đã chi</span>' : '<span class="text-danger small">Chờ chi</span>' ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php if(!$isPaid): ?>
                                        <a href="index.php?action=create-supplier-payment&booking_id=<?= $booking['id'] ?>&service_id=<?= $s['id'] ?>" 
                                           class="btn btn-xs btn-primary px-2 py-0" style="font-size: 0.75rem;">Chi</a>
                                    <?php else: ?>
                                        <i class="fas fa-check-circle text-success"></i>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr><td colspan="4" class="text-center text-muted py-3">Chưa có chi phí dịch vụ.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-light py-2">
                    <div class="d-flex justify-content-between mb-1 small">
                        <span>Tổng chi tính toán:</span>
                        <span class="fw-bold"><?= number_format($totalCost) ?>₫</span>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-1">
                        <span class="fw-bold text-primary">CÒN PHẢI CHI:</span>
                        <span class="fw-bold text-danger fs-5"><?= number_format($unpaidCost) ?>₫</span>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm border-0">
                <div class="card-header bg-secondary text-white fw-bold">
                    <i class="fas fa-pencil-alt me-2"></i>Nhật ký Tour & Sự cố
                </div>
                <div class="card-body">
                    <form action="index.php?action=store-tour-log" method="POST">
                        <input type="hidden" name="booking_id" value="<?= $booking['id'] ?? 0 ?>">
                        <input type="hidden" name="guide_id" value="<?= $booking['guide_id'] ?? 0 ?>">
                        
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <input type="date" name="log_date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <input type="hidden" name="log_time" value="<?= date('H:i:s') ?>">
                            
                            <div class="col-6">
                                <select name="incident_type" class="form-select form-select-sm" required>
                                    <option value="Điểm nhấn">Điểm nhấn</option>
                                    <option value="Sự cố">Sự cố</option>
                                    <option value="Phản hồi khách">Phản hồi khách</option>
                                </select>
                            </div>
                        </div>
                        <textarea name="details" class="form-control mb-2" rows="3" required placeholder="Ghi nhận diễn biến tour..."></textarea>
                        <button type="submit" class="btn btn-dark btn-sm w-100 fw-bold">Gửi báo cáo nhanh</button>
                    </form>
                </div>
                <div class="card shadow-sm border-0 mt-3">
    <div class="card-header bg-light fw-bold small">
        <i class="fas fa-history me-2"></i>Lịch sử nhật ký
    </div>
    <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
    <?php if(!empty($tourLogs)): foreach($tourLogs as $log): ?>
        <div class="p-2 border-bottom">
            <div class="d-flex justify-content-between mb-1">
                <span class="badge bg-info" style="font-size: 0.7rem;">
                    <?= htmlspecialchars($log['incident_type']) ?>
                </span>
                <small class="text-muted">
                    <?= date('d/m H:i', strtotime($log['log_date'] . ' ' . $log['log_time'])) ?>
                </small>
            </div>

            <div class="small fw-bold text-primary mb-1 d-flex justify-content-between align-items-center">
                <span><i class="fas fa-user-edit me-1"></i><?= htmlspecialchars($log['guide_name'] ?? 'Hệ thống') ?></span>
                <div>
                    <a href="index.php?action=delete-tour-log&log_id=<?= $log['id'] ?>&booking_id=<?= $booking['id'] ?>" 
                       class="text-danger ms-2" 
                       onclick="return confirm('Bạn có chắc chắn muốn xóa bản ghi nhật ký này không?')">
                        <i class="fas fa-trash-alt" style="font-size: 0.8rem;"></i>
                    </a>
                </div>
            </div>
            <div class="small text-dark italic">"<?= htmlspecialchars($log['details']) ?>"</div>
        </div>
    <?php endforeach; else: ?>
        <div class="text-center py-3 text-muted small">Chưa có nhật ký nào được ghi nhận.</div>
    <?php endif; ?>
</div>
</div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addPaxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="index.php?action=booking-pax-store" method="POST">
            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?? 0 ?>">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white py-2">
                    <h5 class="modal-title fw-bold">Thêm khách đoàn mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Họ và tên</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold">Ngày sinh</label>
                            <input type="date" name="dob" class="form-control">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold">Giới tính</label>
                            <select name="gender" class="form-select">
                                <option value="Nam">Nam</option>
                                <option value="Nữ">Nữ</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold">Lưu khách</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="importPaxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="index.php?action=booking-pax-import" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?? 0 ?>">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white py-2">
                    <h5 class="modal-title fw-bold">Import từ CSV (UTF-8)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-file-csv fa-3x text-success mb-3"></i>
                    <p class="small text-muted mb-3">Vui lòng sử dụng file CSV xuất từ Google Sheets để tránh lỗi phông chữ.</p>
                    <input type="file" name="pax_file" class="form-control" accept=".csv" required>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="submit" class="btn btn-success btn-sm fw-bold">Bắt đầu Import</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="assignGuideModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="index.php?action=assign-guide" method="POST">
            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?? 0 ?>">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-info text-white py-2">
                    <h5 class="modal-title fw-bold">Phân công HDV Phụ trách</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <select name="guide_id" class="form-select">
                        <option value="">-- Bỏ phân công --</option>
                        <?php if (isset($users) && !empty($users)): ?>
                            <?php foreach($users as $user): ?>
                                <option value="<?= $user['id'] ?>" <?= ($user['id'] == ($booking['guide_id'] ?? 0)) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($user['full_name']) ?> (<?= strtoupper($user['role']) ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option disabled>Không tìm thấy nhân sự khả dụng</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="modal-footer py-2">
                    <button type="submit" class="btn btn-info btn-sm text-white fw-bold">Xác nhận</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if(!empty($paxList)): foreach($paxList as $pax): ?>
<div class="modal fade" id="noteModal<?= $pax['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="index.php?action=update-pax-note" method="POST">
            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?? 0 ?>">
            <input type="hidden" name="pax_id" value="<?= $pax['id'] ?>">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning py-2">
                    <h5 class="modal-title fw-bold">Yêu cầu: <?= htmlspecialchars($pax['full_name']) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea name="special_requests" class="form-control" rows="3" placeholder="Bệnh lý, ăn kiêng..."><?= htmlspecialchars($pax['special_requests'] ?? '') ?></textarea>
                </div>
                <div class="modal-footer py-2">
                    <button type="submit" class="btn btn-warning btn-sm fw-bold">Lưu ghi chú</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endforeach; endif; ?>

<div class="modal fade" id="addServiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="index.php?action=booking-srv-add" method="POST">
            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?? 0 ?>">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white py-2">
                    <h5 class="modal-title fw-bold">Thêm Dịch vụ / Costing</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Loại dịch vụ</label>
                        <select name="service_type" class="form-select" required>
                            <option value="RESTAURANT">Nhà hàng</option>
                            <option value="HOTEL">Khách sạn</option>
                            <option value="TRANSPORT">Vận chuyển</option>
                            <option value="GUIDE">HDV</option>
                            <option value="TICKET">Vé</option>
                            <option value="OTHER">Khác</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nhà cung cấp</label>
                        <select name="supplier_id" class="form-select" required>
                            <option value="">-- Chọn NCC --</option>
                            <?php if (!empty($suppliers)): foreach($suppliers as $sup): ?>
                                <option value="<?= $sup['id'] ?>"><?= htmlspecialchars($sup['supplier_name'] ?? $sup['name']) ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Chi phí (VNĐ)</label>
                        <input type="number" name="cost" class="form-control text-danger fw-bold" required>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="submit" class="btn btn-success btn-sm fw-bold">Lưu dịch vụ</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.ajax-checkin').on('click', function(e) {
        e.preventDefault();
        const paxId = $(this).data('id');
        const status = $(this).data('status');
        const bookingId = '<?= $booking['id'] ?>';

        $.ajax({
            url: 'index.php?action=update-pax-status-ajax',
            type: 'GET',
            data: { 
                booking_id: bookingId, 
                pax_id: paxId, 
                status: status 
            },
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    // 1. Cập nhật Badge trạng thái
                    let badgeClass = (status === 'checked_in') ? 'bg-success' : 'bg-danger';
                    let badgeText = (status === 'checked_in') ? 'CÓ MẶT' : 'VẮNG';
                    
                    $(`#badge-container-${paxId}`).html(
                        `<span class="badge ${badgeClass} status-badge">${badgeText}</span>`
                    );

                    // 2. Cập nhật Icon (Đổi từ nét mảnh sang nét đậm và ngược lại)
                    if(status === 'checked_in') {
                        $(`#icon-check-${paxId}`).removeClass('far').addClass('fas');
                        $(`#icon-absent-${paxId}`).removeClass('fas').addClass('far');
                    } else {
                        $(`#icon-absent-${paxId}`).removeClass('far').addClass('fas');
                        $(`#icon-check-${paxId}`).removeClass('fas').addClass('far');
                    }
                } else {
                    alert('Không thể cập nhật trạng thái. Vui lòng thử lại.');
                }
            },
            error: function() {
                alert('Lỗi kết nối máy chủ!');
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>