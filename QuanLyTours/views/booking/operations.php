<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
    /* Fix lỗi Modal bị chìm */
    .modal { z-index: 9999 !important; }
    .modal-backdrop { z-index: 9998 !important; }
    
    /* Bảng có thanh cuộn nếu dài quá */
    .table-responsive-limit {
        max-height: 500px; 
        overflow-y: auto;
        border-bottom: 1px solid #dee2e6;
    }
    /* Cố định tiêu đề bảng khi cuộn */
    .table-responsive-limit thead th {
        position: sticky; 
        top: 0;
        background: #f8f9fa; 
        z-index: 1;
    }
    
    .table td { vertical-align: middle; }
</style>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="index.php?action=booking-list" class="text-decoration-none text-secondary"><i class="fas fa-arrow-left"></i> Quay lại danh sách</a>
            <h4 class="fw-bold text-primary mt-2"><i class="fas fa-cogs me-2"></i>Điều hành Booking: <?= $booking['booking_code'] ?? 'N/A' ?></h4>
            <small class="text-muted">Tour: <b><?= htmlspecialchars($tour['name'] ?? '---') ?></b> | Khách: <b><?= htmlspecialchars($booking['customer_name'] ?? '---') ?></b></small>
            
            <?php if(in_array($booking['status'] ?? 'new', ['deposited', 'completed', 'cancelled'])): ?>
                <span class="badge bg-danger ms-2"><i class="fas fa-lock"></i> Đã khóa danh sách</span>
            <?php endif; ?>
        </div>
        <div class="text-end">
            <div class="badge bg-success p-3 fs-6 shadow-sm">
                Doanh thu: <?= number_format($booking['total_price'] ?? 0) ?> ₫
            </div>
        </div>
    </div>

    <div class="row g-4">
        
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-bottom">
                    <h6 class="fw-bold m-0 text-success"><i class="fas fa-users me-2"></i>Danh sách đoàn (<?= count($paxList ?? []) ?>)</h6>
                    
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-success me-1" data-bs-toggle="modal" data-bs-target="#modalImportPax">
                            <i class="fas fa-file-excel"></i> Import
                        </button>
                        <?php if(!in_array($booking['status'] ?? 'new', ['deposited', 'completed', 'cancelled'])): ?>
                            <button class="btn btn-sm btn-success shadow-sm" onclick="openPaxModal()">
                                <i class="fas fa-user-plus me-1"></i> Thêm khách
                            </button>
                        <?php else: ?>
                             <button class="btn btn-sm btn-secondary disabled" title="Đã khóa"><i class="fas fa-lock"></i></button>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card-body p-0 table-responsive-limit">
                    <table class="table table-hover align-middle mb-0" id="paxTable">
                        <thead class="bg-light small text-secondary">
                            <tr>
                                <th class="ps-4">STT</th>
                                <th>Họ tên</th>
                                <th>Thông tin</th>
                                <th>Ghi chú</th>
                                <th class="text-end pe-3">Xóa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($paxList)): $i=1; foreach($paxList as $p): ?>
                            <tr>
                                <td class="ps-4 text-muted"><?= $i++ ?></td>
                                <td class="fw-bold text-dark"><?= htmlspecialchars($p['full_name'] ?? '') ?></td>
                                <td>
                                    <div class="small">
                                        <?= ($p['gender']=='male') ? '<span class="text-primary"><i class="fas fa-mars"></i> Nam</span>' : (($p['gender']=='female')?'<span class="text-danger"><i class="fas fa-venus"></i> Nữ</span>':'Khác') ?>
                                        <span class="mx-1 text-muted">|</span>
                                        <?= !empty($p['dob']) ? date('d/m/Y', strtotime($p['dob'])) : '--/--/----' ?>
                                    </div>
                                </td>
                                <td class="small text-muted fst-italic"><?= htmlspecialchars($p['note'] ?? '') ?></td>
                                <td class="text-end pe-3">
                                    <?php if(!in_array($booking['status'] ?? 'new', ['deposited', 'completed', 'cancelled'])): ?>
                                        <a href="index.php?action=booking-pax-del&id=<?= $p['id'] ?>&bid=<?= $booking['id'] ?>" 
                                           class="btn btn-sm btn-light text-danger border" onclick="return confirm('Xóa khách này?')">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr><td colspan="5" class="text-center py-5 text-muted"><i class="fas fa-user-slash fa-2x mb-2 opacity-25"></i><br>Chưa nhập danh sách đoàn.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-bottom">
                    <h6 class="fw-bold m-0 text-warning text-dark"><i class="fas fa-concierge-bell me-2"></i>Dịch vụ / Chi phí</h6>
                    <button class="btn btn-sm btn-warning text-dark fw-bold shadow-sm" onclick="openServiceModal()">
                        <i class="fas fa-plus-circle"></i> Đặt dịch vụ
                    </button>
                </div>
                <div class="card-body p-0 d-flex flex-column">
                    <div class="table-responsive flex-grow-1" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light small text-secondary sticky-top" style="z-index: 1;">
                                <tr>
                                    <th class="ps-4">Dịch vụ / NCC</th>
                                    <th>Chi tiết</th>
                                    <th class="text-end">Chi phí</th>
                                    <th class="text-end pe-3">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $totalCost=0; if(!empty($services)): foreach($services as $s): $totalCost+=$s['cost']; ?>
                                <tr>
                                    <td class="ps-4">
                                        <span class="badge bg-secondary bg-opacity-10 text-dark border mb-1">
                                            <?= htmlspecialchars(ucfirst($s['service_type'] ?? '')) ?>
                                        </span>
                                        <div class="fw-bold small"><?= htmlspecialchars($s['supplier_name'] ?? '---') ?></div>
                                    </td>
                                    <td class="small text-muted"><?= htmlspecialchars($s['description'] ?? '') ?></td>
                                    <td class="text-end fw-bold text-danger"><?= number_format($s['cost'] ?? 0) ?></td>
                                    <td class="text-end pe-3">
                                        <a href="index.php?action=booking-srv-del&id=<?= $s['id'] ?>&bid=<?= $booking['id'] ?>" 
                                           class="btn btn-sm btn-light text-danger border" onclick="return confirm('Hủy dịch vụ này?')">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr><td colspan="4" class="text-center py-5 text-muted"><i class="fas fa-receipt fa-2x mb-2 opacity-25"></i><br>Chưa đặt dịch vụ nào.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="border-top bg-light p-3 mt-auto">
                        <div class="row mb-1">
                            <div class="col-6 text-end small text-muted">Tổng Chi phí:</div>
                            <div class="col-6 text-end fw-bold text-danger"><?= number_format($totalCost) ?> ₫</div>
                        </div>
                        <div class="row pt-2 border-top border-secondary border-opacity-10">
                            <div class="col-6 text-end fw-bold text-uppercase text-primary">Lợi nhuận:</div>
                            <div class="col-6 text-end fw-bold fs-5 text-success">
                                <?= number_format($booking['total_price'] - $totalCost) ?> ₫
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<div class="modal fade" id="modalAddPax" tabindex="-1" style="z-index: 99999 !important;">
    <style>.modal-backdrop { z-index: 99998 !important; }</style>
    <div class="modal-dialog modal-dialog-centered">
        <form action="index.php?action=booking-pax-add" method="POST" onsubmit="return validatePaxForm()">
            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-success text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="fas fa-user-plus me-2"></i>Thêm thành viên đoàn</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="mb-3">
                        <label class="fw-bold small text-success">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control" required placeholder="Ví dụ: Nguyễn Văn A">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="fw-bold small">Giới tính</label>
                            <select name="gender" class="form-select">
                                <option value="male">Nam</option>
                                <option value="female">Nữ</option>
                                <option value="other">Khác</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="fw-bold small">Ngày sinh</label>
                            <input type="date" name="dob" id="pax_dob" class="form-control" max="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold small">Ghi chú</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Ăn chay, dị ứng, phòng tầng thấp..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-white border-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-success btn-sm fw-bold px-4">Lưu vào danh sách</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalImportPax" tabindex="-1" style="z-index: 99999 !important;">
    <div class="modal-dialog modal-lg">
        <form action="index.php?action=booking-pax-import" method="POST">
            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">Import Danh Sách Nhanh</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light p-4">
                    <p class="alert alert-info small mb-3">
                        <i class="fas fa-info-circle me-1"></i> 
                        <strong>Hướng dẫn:</strong> Dán dữ liệu từ Excel (Ctrl+C, Ctrl+V).
                    </p>
                    <div class="form-floating">
                        <textarea name="excel_data" class="form-control" style="height: 250px; font-family: monospace; font-size: 0.9rem;" placeholder="Họ tên [TAB] Giới tính [TAB] Ngày sinh..."></textarea>
                        <label>Dán dữ liệu vào đây (Ctrl+V)</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-success fw-bold">Xử lý & Nhập liệu</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalAddService" tabindex="-1" style="z-index: 99999 !important;">
    <div class="modal-dialog">
        <form action="index.php?action=booking-srv-add" method="POST">
            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark"><h5 class="modal-title fw-bold">Đặt dịch vụ</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body p-4"><div class="mb-3"><label class="fw-bold small">Nhà cung cấp</label><select name="supplier_id" class="form-select" required><option value="">-- Chọn NCC --</option><?php foreach($suppliers as $sup): ?><option value="<?= $sup['id'] ?>"><?= $sup['name'] ?></option><?php endforeach; ?></select></div><div class="row g-3"><div class="col-6"><label class="fw-bold small">Loại dịch vụ</label><select name="service_type" class="form-select"><option value="hotel">Khách sạn</option><option value="transport">Xe</option><option value="restaurant">Nhà hàng</option></select></div><div class="col-6"><label class="fw-bold small">Chi phí (Net)</label><input type="number" name="cost" class="form-control" value="0"></div></div><div class="mb-3 mt-3"><label class="fw-bold small">Chi tiết</label><textarea name="description" class="form-control" rows="2" placeholder="VD: 2 phòng đôi..."></textarea></div></div><div class="modal-footer"><button type="submit" class="btn btn-warning fw-bold">Lưu</button></div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Đưa các modal ra body để tránh lỗi Z-index
        const m1 = document.getElementById('modalAddPax');
        const m2 = document.getElementById('modalAddService');
        const m3 = document.getElementById('modalImportPax');
        if(m1) document.body.appendChild(m1);
        if(m2) document.body.appendChild(m2);
        if(m3) document.body.appendChild(m3);
    });

    // Hàm gọi modal thủ công
    function openPaxModal() {
        new bootstrap.Modal(document.getElementById('modalAddPax')).show();
    }
    function openServiceModal() {
        new bootstrap.Modal(document.getElementById('modalAddService')).show();
    }
    
    // Validate Form Pax (Chặn năm sinh sai)
    function validatePaxForm() {
        const dobInput = document.getElementById('pax_dob');
        if (dobInput.value) {
            const year = new Date(dobInput.value).getFullYear();
            const currentYear = new Date().getFullYear();
            if (year < 1900 || year > currentYear) {
                alert("Năm sinh không hợp lệ! Vui lòng kiểm tra lại.");
                return false; // Chặn submit
            }
        }
        return true; // Cho phép submit
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>