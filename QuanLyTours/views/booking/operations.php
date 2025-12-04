<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
    .modal { z-index: 9999 !important; }
    .modal-backdrop { z-index: 9998 !important; }
    .table td { vertical-align: middle; }
</style>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="index.php?action=booking-list" class="text-decoration-none text-secondary"><i class="fas fa-arrow-left"></i> Quay lại danh sách</a>
            <h4 class="fw-bold text-primary mt-2"><i class="fas fa-cogs me-2"></i>Điều hành Booking: <?= $booking['booking_code'] ?></h4>
            <small class="text-muted">Tour: <b><?= $tour['name'] ?></b> | Khách: <b><?= $booking['customer_name'] ?></b></small>
        </div>
        <div class="text-end">
            <div class="badge bg-success p-2 fs-6 shadow-sm">Doanh thu: <?= number_format($booking['total_price']) ?> ₫</div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h6 class="fw-bold m-0 text-success"><i class="fas fa-users me-2"></i>Danh sách đoàn (<?= count($paxList) ?>)</h6>
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalAddPax"><i class="fas fa-plus me-1"></i> Thêm khách</button>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light small"><tr><th>STT</th><th>Họ tên</th><th>Giới tính</th><th>Ngày sinh</th><th>Ghi chú</th><th></th></tr></thead>
                        <tbody>
                            <?php if(!empty($paxList)): $i=1; foreach($paxList as $p): ?>
                            <tr>
                                <td class="text-center"><?= $i++ ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($p['full_name']) ?></td>
                                <td><?= ($p['gender']=='male') ? 'Nam' : 'Nữ' ?></td>
                                <td><?= !empty($p['dob']) ? date('d/m/Y', strtotime($p['dob'])) : '' ?></td>
                                <td class="small text-muted"><?= htmlspecialchars($p['note']) ?></td>
                                <td class="text-end"><a href="index.php?action=booking-pax-del&id=<?= $p['id'] ?>&bid=<?= $booking['id'] ?>" class="text-danger" onclick="return confirm('Xóa?')"><i class="fas fa-times"></i></a></td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted">Chưa nhập danh sách đoàn.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h6 class="fw-bold m-0 text-warning text-dark"><i class="fas fa-concierge-bell me-2"></i>Dịch vụ / Chi phí</h6>
                    <button class="btn btn-sm btn-warning text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#modalAddService"><i class="fas fa-plus"></i> Đặt dịch vụ</button>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light small"><tr><th>Dịch vụ</th><th>Chi tiết</th><th>Chi phí</th><th></th></tr></thead>
                        <tbody>
                            <?php $totalCost=0; if(!empty($services)): foreach($services as $s): $totalCost+=$s['cost']; ?>
                            <tr>
                                <td>
                                    <span class="badge bg-secondary"><?= $s['service_type'] ?></span><br>
                                    <small class="fw-bold"><?= $s['supplier_name'] ?></small>
                                </td>
                                <td class="small"><?= htmlspecialchars($s['description']) ?></td>
                                <td class="fw-bold text-danger"><?= number_format($s['cost']) ?></td>
                                <td class="text-end"><a href="index.php?action=booking-srv-del&id=<?= $s['id'] ?>&bid=<?= $booking['id'] ?>" class="text-danger" onclick="return confirm('Xóa?')"><i class="fas fa-times"></i></a></td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr><td colspan="4" class="text-center py-4 text-muted">Chưa đặt dịch vụ.</td></tr>
                            <?php endif; ?>
                        </tbody>
                        <?php if($totalCost > 0): ?>
                        <tfoot class="bg-light">
                            <tr><td colspan="2" class="text-end fw-bold">Tổng chi (Net):</td><td class="fw-bold text-danger"><?= number_format($totalCost) ?> ₫</td><td></td></tr>
                            <tr><td colspan="2" class="text-end fw-bold text-primary">Lợi nhuận:</td><td class="fw-bold text-primary fs-5"><?= number_format($booking['total_price'] - $totalCost) ?> ₫</td><td></td></tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<div class="modal fade" id="modalAddPax" tabindex="-1" style="z-index: 99999 !important;">
    <style>.modal-backdrop { z-index: 99998 !important; }</style>
    <div class="modal-dialog">
        <form action="index.php?action=booking-pax-add" method="POST">
            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
            <div class="modal-content">
                <div class="modal-header bg-success text-white"><h5 class="modal-title fw-bold">Thêm thành viên đoàn</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="fw-bold small">Họ và tên *</label><input type="text" name="full_name" class="form-control" required></div>
                    <div class="row mb-3">
                        <div class="col-6"><label class="fw-bold small">Giới tính</label><select name="gender" class="form-select"><option value="male">Nam</option><option value="female">Nữ</option></select></div>
                        <div class="col-6"><label class="fw-bold small">Ngày sinh</label><input type="date" name="dob" class="form-control"></div>
                    </div>
                    <div class="mb-3"><label class="fw-bold small">Ghi chú</label><input type="text" name="note" class="form-control" placeholder="Ăn chay, dị ứng..."></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-success w-100">Lưu lại</button></div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalAddService" tabindex="-1" style="z-index: 99999 !important;">
    <div class="modal-dialog">
        <form action="index.php?action=booking-srv-add" method="POST">
            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark"><h5 class="modal-title fw-bold">Đặt dịch vụ (Điều hành)</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="fw-bold small">Nhà cung cấp *</label>
                        <select name="supplier_id" class="form-select" required>
                            <option value="">-- Chọn NCC --</option>
                            <?php foreach($suppliers as $sup): ?>
                                <option value="<?= $sup['id'] ?>"><?= $sup['name'] ?> (<?= $sup['type'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted"><a href="index.php?action=supplier-list" target="_blank">Thêm NCC mới</a></small>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6"><label class="fw-bold small">Loại dịch vụ</label><select name="service_type" class="form-select"><option value="hotel">Khách sạn</option><option value="transport">Xe/Vận chuyển</option><option value="restaurant">Nhà hàng</option><option value="ticket">Vé tham quan</option></select></div>
                        <div class="col-6"><label class="fw-bold small">Chi phí (Net)</label><input type="number" name="cost" class="form-control" value="0"></div>
                    </div>
                    <div class="mb-3"><label class="fw-bold small">Chi tiết</label><input type="text" name="description" class="form-control" placeholder="VD: 2 phòng đôi, 1 xe 16 chỗ..."></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-warning w-100 fw-bold">Lưu Dịch Vụ</button></div>
            </div>
        </form>
    </div>
</div>