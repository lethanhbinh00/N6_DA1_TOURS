<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container p-4">
    <h4>Thêm khởi hành mới</h4>

    <form action="index.php?action=departure-store" method="POST">
        <div class="mb-3">
            <label class="form-label">Tour</label>
            <select name="tour_id" class="form-select" required>
                <option value="">-- Chọn tour --</option>
                <?php foreach ($tours as $t): ?>
                    <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Ngày khởi hành</label>
            <input type="date" name="start_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Số chỗ</label>
            <input type="number" name="seats" class="form-control" value="20">
        </div>

        <button class="btn btn-success">Lưu</button>
        <a href="index.php?action=departure-list" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>