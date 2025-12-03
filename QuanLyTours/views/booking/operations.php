<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="index.php?action=booking-list" class="text-decoration-none text-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
            <h4 class="fw-bold text-primary mt-2"><i class="fas fa-cogs me-2"></i>Điều hành Booking: <?= $booking['booking_code'] ?></h4>
        </div>
        <div class="text-end">
            <div class="badge bg-success p-2 fs-6">Doanh thu: <?= number_format($booking['total_price']) ?> ₫</div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h6 class="fw-bold m-0 text-success"><i class="fas fa-users me-2"></i>Danh sách thành viên (<?= count($paxList) ?>)</h6>
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalAddPax"><i class="fas fa-plus"></i> Thêm khách</button>
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
                            <tr><td colspan="6" class="text-center py-4 text-muted">Chưa có danh sách đoàn.</td></tr>
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

<div class="modal fade" id="modalAddPax" tabindex="-1" style="z-index: 9999;">
    <div class="modal-dialog"><form action="index.php?action=booking-pax-add" method="POST"><input type="hidden" name="booking_id" value="<?= $booking['id'] ?>"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Thêm khách</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><input type="text" name="full_name" class="form-control mb-2" placeholder="Họ tên" required><div class="row g-2"><div class="col-6"><select name="gender" class="form-select"><option value="male">Nam</option><option value="female">Nữ</option></select></div><div class="col-6"><input type="date" name="dob" class="form-control"></div></div><input type="text" name="note" class="form-control mt-2" placeholder="Ghi chú"></div><div class="modal-footer"><button type="submit" class="btn btn-success">Lưu</button></div></div></form></div>
</div>

<div class="modal fade" id="modalAddService" tabindex="-1" style="z-index: 9999;">
    <div class="modal-dialog"><form action="index.php?action=booking-srv-add" method="POST"><input type="hidden" name="booking_id" value="<?= $booking['id'] ?>"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Đặt dịch vụ</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><select name="supplier_id" class="form-select mb-2" required><option value="">-- Chọn NCC --</option><?php foreach($suppliers as $sup): ?><option value="<?= $sup['id'] ?>"><?= $sup['name'] ?></option><?php endforeach; ?></select><div class="row g-2"><div class="col-6"><select name="service_type" class="form-select"><option value="hotel">Khách sạn</option><option value="transport">Xe</option><option value="restaurant">Nhà hàng</option><option value="other">Khác</option></select></div><div class="col-6"><input type="number" name="cost" class="form-control" placeholder="Chi phí" required></div></div><input type="text" name="description" class="form-control mt-2" placeholder="Chi tiết (2 phòng...)"></div><div class="modal-footer"><button type="submit" class="btn btn-warning">Lưu</button></div></div></form></div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>