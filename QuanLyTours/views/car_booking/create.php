<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5 d-flex justify-content-center">
    <div class="card shadow-lg" style="width: 100%; max-width: 600px; border-radius: 1rem;">
        <div class="card-header bg-success text-white text-center py-3" style="border-radius: 1rem 1rem 0 0;">
            <h4 class="mb-0"><i class="fas fa-car me-2"></i>Tạo Đặt Dịch Vụ (Xe/KS)</h4>
        </div>
        <div class="card-body p-4">
            <form action="index.php?action=carbooking-store" method="POST">

                <div class="mb-4">
                    <label class="form-label fw-bold">Dịch vụ <span class="text-danger">*</span></label>
                    <select name="service_id" class="form-select form-select-lg" required>
                        <option value="">-- Chọn dịch vụ --</option>
                        <?php foreach ($services as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?> - <?= number_format($s['price'] ?? 0) ?>₫</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Tên khách <span class="text-danger">*</span></label>
                    <input type="text" name="customer_name" class="form-control form-control-lg" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Số điện thoại</label>
                    <input type="text" name="phone" class="form-control form-control-lg">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Ngày <span class="text-danger">*</span></label>
                    <input type="date" name="date" class="form-control form-control-lg" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Số lượng</label>
                    <input type="number" name="quantity" class="form-control form-control-lg" value="1" min="1">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Ghi chú</label>
                    <textarea name="note" class="form-control" rows="2"></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="index.php?action=carbooking-list" class="btn btn-secondary btn-lg px-4">
                        <i class="fas fa-arrow-left me-2"></i> Hủy
                    </a>
                    <button type="submit" class="btn btn-success btn-lg px-4">
                        <i class="fas fa-save me-2"></i> Lưu
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>