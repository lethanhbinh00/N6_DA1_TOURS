<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container p-4">
    <h4>Tạo đặt dịch vụ (Xe/KS)</h4>

    <form action="index.php?action=carbooking-store" method="POST">
        <div class="mb-3">
            <label class="form-label">Dịch vụ</label>
            <select name="service_id" class="form-select" required>
                <option value="">-- Chọn dịch vụ --</option>
                <?php foreach ($services as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?> - <?= number_format($s['price'] ?? 0) ?>₫</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Tên khách</label>
            <input type="text" name="customer_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Số điện thoại</label>
            <input type="text" name="phone" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Ngày</label>
            <input type="date" name="date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Số lượng</label>
            <input type="number" name="quantity" class="form-control" value="1">
        </div>

        <div class="mb-3">
            <label class="form-label">Ghi chú</label>
            <textarea name="note" class="form-control"></textarea>
        </div>

        <button class="btn btn-success">Lưu</button>
        <a href="index.php?action=carbooking-list" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>