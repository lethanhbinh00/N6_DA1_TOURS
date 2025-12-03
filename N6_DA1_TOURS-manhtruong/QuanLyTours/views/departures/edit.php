<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container p-4">
    <h4>Sửa khởi hành</h4>

    <form action="index.php?action=departure-update" method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($departure['id']) ?>">

        <div class="mb-3">
            <label class="form-label">Tour</label>
            <select name="tour_id" class="form-select" required>
                <?php foreach ($tours as $t): ?>
                    <option value="<?= $t['id'] ?>" <?= $t['id']==$departure['tour_id'] ? 'selected' : '' ?>><?= htmlspecialchars($t['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Ngày khởi hành</label>
            <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($departure['start_date']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Số chỗ</label>
            <input type="number" name="seats" class="form-control" value="<?= htmlspecialchars($departure['seats']) ?>">
        </div>

        <button class="btn btn-success">Cập nhật</button>
        <a href="index.php?action=departure-list" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>