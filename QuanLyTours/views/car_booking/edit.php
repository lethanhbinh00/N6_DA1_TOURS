<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary"><i class="fas fa-edit me-2"></i> Sửa đơn đặt xe</h4>
        <a href="index.php?action=car-booking" class="btn btn-outline-secondary btn-sm">Quay lại</a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form method="POST" action="index.php?action=car-booking-update">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($booking['id']) ?>">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Dịch vụ xe</label>
                                <select name="service_id" class="form-select">
                                    <?php foreach ($services as $s): ?>
                                        <option value="<?= $s['id'] ?>" <?= $booking['service_id'] == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ngày đặt</label>
                                <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($booking['date']) ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Khách hàng</label>
                                <input type="text" name="customer_name" class="form-control" value="<?= htmlspecialchars($booking['customer_name']) ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Điện thoại</label>
                                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($booking['phone']) ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Số lượng</label>
                                <input type="number" name="quantity" min="1" value="<?= htmlspecialchars($booking['quantity']) ?>" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ghi chú</label>
                                <textarea name="note" class="form-control"><?= htmlspecialchars($booking['note']) ?></textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="index.php?action=car-booking" class="btn btn-secondary">Quay lại</a>
                            <button class="btn btn-warning px-4">
                                <i class="fas fa-save me-1"></i> Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>