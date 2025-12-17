<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary">✏️ Cập nhật Booking: <?= $booking['booking_code'] ?? '(Đang cập nhật)' ?></h4>
        <a href="index.php?action=booking-list" class="btn btn-outline-secondary btn-sm">Quay lại</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="index.php?action=booking-update" method="POST">
                <input type="hidden" name="id" value="<?= $booking['id'] ?? $_POST['id'] ?>">

                <h6 class="text-primary border-bottom pb-2 mb-3 fw-bold">1. Thông tin dịch vụ</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Chọn Tour</label>
                        <select name="tour_id" class="form-select" required onchange="updatePrice(this)">
                            <?php foreach ($tours as $t): ?>
                                <option value="<?= $t['id'] ?>"
                                    data-price-adult="<?= $t['price_adult'] ?>"
                                    data-price-child="<?= $t['price_child'] ?>"
                                    <?= ($t['id'] == $booking['tour_id']) ? 'selected' : '' ?>>
                                    [<?= $t['code'] ?>] <?= $t['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Ngày khởi hành</label>
                        <input type="date" name="travel_date" class="form-control" value="<?= $booking['travel_date'] ?>" required>
                    </div>
                </div>

                <h6 class="text-primary border-bottom pb-2 mb-3 fw-bold">2. Thông tin khách hàng</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Họ tên</label>
                        <input type="text" name="customer_name"
                            class="form-control <?= isset($errors['customer_name']) ? 'is-invalid' : '' ?>"
                            value="<?= htmlspecialchars($booking['customer_name']) ?>" required>
                        <?php if (isset($errors['customer_name'])): ?>
                            <div class="invalid-feedback"><?= $errors['customer_name'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">CCCD/CMND</label>
                        <input type="text" name="customer_id_card"
                            class="form-control <?= isset($errors['customer_id_card']) ? 'is-invalid' : '' ?>"
                            value="<?= htmlspecialchars($booking['customer_id_card'] ?? '') ?>"
                            placeholder="Chỉ nhập số...">

                        <?php if (isset($errors['customer_id_card'])): ?>
                            <div class="invalid-feedback fw-bold">
                                <i class="fas fa-exclamation-circle me-1"></i> <?= $errors['customer_id_card'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Số điện thoại</label>
                        <input type="text" name="customer_phone"
                            class="form-control <?= isset($errors['customer_phone']) ? 'is-invalid' : '' ?>"
                            value="<?= htmlspecialchars($booking['customer_phone']) ?>" required>
                        <?php if (isset($errors['customer_phone'])): ?>
                            <div class="invalid-feedback"><?= $errors['customer_phone'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="customer_email" class="form-control" value="<?= htmlspecialchars($booking['customer_email']) ?>">
                    </div>
                </div>

                <h6 class="text-primary border-bottom pb-2 mb-3 fw-bold">3. Chi phí (Tự động tính lại khi sửa)</h6>
                <div class="row g-3 mb-4 bg-light p-3 rounded mx-0">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Người lớn</label>
                        <input type="number" id="adults" name="adults" class="form-control" value="<?= $booking['adults'] ?>" min="1" onchange="calcTotal()">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Trẻ em</label>
                        <input type="number" id="children" name="children" class="form-control" value="<?= $booking['children'] ?>" min="0" onchange="calcTotal()">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-success">TỔNG TIỀN MỚI</label>
                        <input type="text" id="display_total" class="form-control fw-bold text-success fs-5" readonly value="<?= number_format($booking['total_price']) ?> ₫">
                        <input type="hidden" id="total_price" name="total_price" value="<?= $booking['total_price'] ?>">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Ghi chú</label>
                    <textarea name="note" class="form-control" rows="2"><?= htmlspecialchars($booking['note']) ?></textarea>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        <i class="fas fa-save me-2"></i> Lưu Cập Nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let priceAdult = 0;
    let priceChild = 0;

    document.addEventListener("DOMContentLoaded", function() {
        const select = document.querySelector('select[name="tour_id"]');
        if (select) updatePrice(select, false);
    });

    function updatePrice(select, recalc = true) {
        const option = select.options[select.selectedIndex];
        priceAdult = parseInt(option.getAttribute('data-price-adult')) || 0;
        priceChild = parseInt(option.getAttribute('data-price-child')) || 0;
        if (recalc) calcTotal();
    }

    function calcTotal() {
        const adults = parseInt(document.getElementById('adults').value) || 0;
        const children = parseInt(document.getElementById('children').value) || 0;
        const total = (adults * priceAdult) + (children * priceChild);

        document.getElementById('display_total').value = new Intl.NumberFormat('vi-VN').format(total) + ' ₫';
        document.getElementById('total_price').value = total;
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>