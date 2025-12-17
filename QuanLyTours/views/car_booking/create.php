<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary"><i class="fas fa-car-side me-2"></i> Đặt Dịch Vụ Xe</h4>
        <a href="index.php?action=car-booking" class="btn btn-outline-secondary btn-sm">Quay lại</a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form method="POST" action="index.php?action=car-booking-store">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Dịch vụ xe</label>
                                <select name="service_id" id="service_select" class="form-select" required onchange="updatePrice()">
                                    <option value="">-- Chọn dịch vụ --</option>
                                    <?php foreach ($services as $s): ?>
                                        <option value="<?= $s['id'] ?>" data-price="<?= $s['price'] ?>"><?= htmlspecialchars($s['name']) ?> (<?= number_format($s['price']) ?> đ)</option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ngày đặt</label>
                                <input type="date" name="date" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Khách hàng</label>
                                <input type="text" name="customer_name" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Điện thoại</label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Số lượng</label>
                                <input type="number" id="quantity_input" name="quantity" min="1" value="1" class="form-control" required oninput="updatePrice()">
                            </div>

                            <div class="col-md-8">
                                <label class="form-label fw-bold">Ghi chú</label>
                                <textarea name="note" class="form-control" rows="2"></textarea>
                            </div>

                            <div class="col-12 mt-2">
                                <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded">
                                    <div class="small text-muted">Tạm tính</div>
                                    <div class="fw-bold text-success fs-5" id="display_total">0 đ</div>
                                </div>
                                <input type="hidden" name="total_price" id="total_price_hidden" value="0">
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="index.php?action=car-booking" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Hủy
                            </a>
                            <button class="btn btn-primary px-4">
                                <i class="fas fa-save me-1"></i> Lưu
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updatePrice() {
        const sel = document.getElementById('service_select');
        const qty = parseInt(document.getElementById('quantity_input').value) || 0;
        let price = 0;
        if (sel && sel.selectedIndex > 0) {
            price = parseFloat(sel.options[sel.selectedIndex].getAttribute('data-price')) || 0;
        }
        const total = Math.round(price * qty);
        document.getElementById('display_total').innerText = new Intl.NumberFormat('vi-VN').format(total) + ' đ';
        document.getElementById('total_price_hidden').value = total;
    }
    document.addEventListener('DOMContentLoaded', updatePrice);
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>