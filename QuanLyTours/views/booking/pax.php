<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="index.php?action=booking-list" class="text-decoration-none text-secondary mb-1 d-block">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
            <h4 class="fw-bold text-primary m-0">
                <i class="fas fa-users me-2"></i>Danh sách đoàn: <?= $booking['booking_code'] ?>
            </h4>
            <small class="text-muted">Tour: <?= $tour['name'] ?> | Ngày đi: <?= date('d/m/Y', strtotime($booking['travel_date'])) ?></small>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold text-success">
                    <i class="fas fa-user-plus me-1"></i> Thêm thành viên
                </div>
                <div class="card-body">
                    <form action="index.php?action=booking-pax-store" method="POST">
                        <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Họ và tên *</label>
                            <input type="text" name="full_name" class="form-control" required placeholder="Nguyễn Văn A">
                        </div>
                        
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold">Loại khách</label>
                                <select name="customer_type" class="form-select">
                                    <option value="adult">Người lớn</option>
                                    <option value="child">Trẻ em</option>
                                    <option value="infant">Em bé</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">Giới tính</label>
                                <select name="gender" class="form-select">
                                    <option value="male">Nam</option>
                                    <option value="female">Nữ</option>
                                    <option value="other">Khác</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Ngày sinh</label>
                            <input type="date" name="dob" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Ghi chú (Ăn chay/Dị ứng...)</label>
                            <input type="text" name="note" class="form-control" placeholder="...">
                        </div>

                        <button type="submit" class="btn btn-success w-100">Thêm vào danh sách</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>STT</th>
                                <th>Họ tên</th>
                                <th>Thông tin</th>
                                <th>Ghi chú</th>
                                <th class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($paxList)): ?>
                                <?php foreach ($paxList as $index => $p): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td class="fw-bold"><?= htmlspecialchars($p['full_name']) ?></td>
                                    <td>
                                        <span class="badge bg-light text-dark border me-1">
                                            <?= ($p['gender']=='male') ? 'Nam' : (($p['gender']=='female')?'Nữ':'Khác') ?>
                                        </span>
                                        <span class="badge bg-info bg-opacity-10 text-dark border border-info">
                                            <?= ($p['customer_type']=='adult') ? 'Người lớn' : 'Trẻ em' ?>
                                        </span>
                                        <?php if($p['dob']): ?>
                                            <div class="small text-muted mt-1"><i class="fas fa-birthday-cake me-1"></i> <?= date('d/m/Y', strtotime($p['dob'])) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-danger small"><?= htmlspecialchars($p['note']) ?></td>
                                    <td class="text-end">
                                        <a href="index.php?action=booking-pax-del&id=<?= $p['id'] ?>&booking_id=<?= $booking['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Xóa khách này?')">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center py-5 text-muted">Chưa có thành viên nào trong đoàn.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>