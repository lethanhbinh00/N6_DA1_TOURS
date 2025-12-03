<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container p-4">
    <h4>Sửa đặt dịch vụ</h4>

    <form action="index.php?action=carbooking-update" method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($booking['id']) ?>">

        <div class="mb-3">
            <label class="form-label">Dịch vụ</label>
            <select name="service_id" class="form-select" required>
                <?php foreach ($services as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $s['id']==$booking['service_id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Tên khách</label>
            <input type="text" name="customer_name" class="form-control" value="<?= htmlspecialchars($booking['customer_name']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Số điện thoại</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($booking['phone']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Ngày</label>
            <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($booking['date']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Số lượng</label>
            <input type="number" name="quantity" class="form-control" value="<?= htmlspecialchars($booking['quantity']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Ghi chú</label>
            <textarea name="note" class="form-control"><?= htmlspecialchars($booking['note'] ?? '') ?></textarea>
        </div>

        <button class="btn btn-success">Cập nhật</button>
        <a href="index.php?action=carbooking-list" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>