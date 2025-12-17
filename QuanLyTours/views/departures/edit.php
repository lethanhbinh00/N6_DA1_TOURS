<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid p-4">
    <div class="page-header">
        <h4 class="fw-bold text-primary"><i class="fas fa-route me-2"></i> Chỉnh sửa Lịch khởi hành</h4>
        <div class="page-actions">
            <a href="index.php?action=departure-list" class="btn btn-outline-secondary btn-sm">Quay lại</a>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="POST" action="index.php?action=departure-update">
                <input type="hidden" name="id" value="<?= htmlspecialchars($departure['id'] ?? '') ?>">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tour</label>
                        <select name="tour_id" class="form-select" required>
                            <option value="">-- Chọn tour --</option>
                            <?php foreach ($tours as $t): ?>
                                <option value="<?= $t['id'] ?>" <?= ($t['id'] == ($departure['tour_id'] ?? '')) ? 'selected' : '' ?>><?= htmlspecialchars($t['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Ngày khởi hành</label>
                        <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($departure['start_date'] ?? '') ?>" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Số chỗ</label>
                        <input type="number" name="seats" class="form-control" value="<?= htmlspecialchars($departure['seats'] ?? '') ?>" min="0">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Hướng dẫn viên</label>
                        <select name="guide_id" class="form-select">
                            <option value="">-- Chọn HDV (nếu có) --</option>
                            <?php foreach ($guides as $g): ?>
                                <option value="<?= $g['id'] ?>" <?= (isset($departure['guide_id']) && $departure['guide_id'] == $g['id']) ? 'selected' : '' ?>><?= htmlspecialchars($g['full_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Ghi chú</label>
                        <textarea name="note" class="form-control" rows="3"><?= htmlspecialchars($departure['note'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="text-end mt-3">
                    <button class="btn btn-primary px-4">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>