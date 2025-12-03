<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid p-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-secondary"><i class="fas fa-cart-plus me-2"></i>Tạo Booking Mới</h4>
        <a href="index.php?action=booking-list" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-2"></i>Quay lại</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-success text-white fw-bold py-3"><i class="fas fa-edit me-2"></i>Thông tin đặt tour</div>
        
        <div class="card-body p-4">
            <form action="index.php?action=booking-store" method="POST">
                
                <h6 class="text-success border-bottom pb-2 mb-3 fw-bold">1. Thông tin dịch vụ</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Chọn Tour <span class="text-danger">*</span></label>
                        <select name="tour_id" class="form-select" required onchange="updatePrice(this)">
                            <option value="">-- Chọn Tour --</option>
                            <?php foreach($tours as $t): ?>
                                <option value="<?= $t['id'] ?>" 
                                        data-price-adult="<?= $t['price_adult'] ?>" 
                                        data-price-child="<?= $t['price_child'] ?>"
                                        <?= (isset($oldData['tour_id']) && $oldData['tour_id'] == $t['id']) ? 'selected' : '' ?>>
                                    [<?= $t['code'] ?>] <?= $t['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Ngày khởi hành <span class="text-danger">*</span></label>
                        <input type="date" name="travel_date" class="form-control" required value="<?= $oldData['travel_date'] ?? '' ?>">
                    </div>
                </div>

                <h6 class="text-success border-bottom pb-2 mb-3 fw-bold">2. Thông tin khách hàng</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="customer_name" 
                               class="form-control <?= isset($errors['customer_name']) ? 'is-invalid' : '' ?>" 
                               required placeholder="Nguyễn Văn A"
                               value="<?= $oldData['customer_name'] ?? '' ?>">
                        <?php if(isset($errors['customer_name'])): ?>
                            <div class="invalid-feedback"><?= $errors['customer_name'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label fw-bold">CCCD/CMND</label>
                        <input type="text" name="customer_id_card" 
                               class="form-control <?= isset($errors['customer_id_card']) ? 'is-invalid' : '' ?>" 
                               placeholder="12 số..."
                               value="<?= $oldData['customer_id_card'] ?? '' ?>">
                        
                        <?php if(isset($errors['customer_id_card'])): ?>
                            <div class="invalid-feedback fw-bold">
                                <i class="fas fa-exclamation-circle me-1"></i><?= $errors['customer_id_card'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="text" name="customer_phone" 
                               class="form-control <?= isset($errors['customer_phone']) ? 'is-invalid' : '' ?>" 
                               required placeholder="09xxxx"
                               value="<?= $oldData['customer_phone'] ?? '' ?>">
                        <?php if(isset($errors['customer_phone'])): ?>
                            <div class="invalid-feedback"><?= $errors['customer_phone'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="customer_email" class="form-control" placeholder="abc@gmail.com" value="<?= $oldData['customer_email'] ?? '' ?>">
                    </div>
                </div>

                <h6 class="text-success border-bottom pb-2 mb-3 fw-bold">3. Số lượng & Thanh toán</h6>
                <div class="row g-3 mb-4 bg-light p-3 rounded mx-0">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Người lớn</label>
                        <input type="number" id="adults" name="adults" class="form-control" min="1" onchange="calcTotal()" value="<?= $oldData['adults'] ?? 1 ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Trẻ em</label>
                        <input type="number" id="children" name="children" class="form-control" min="0" onchange="calcTotal()" value="<?= $oldData['children'] ?? 0 ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-success">TỔNG TIỀN TẠM TÍNH</label>
                        <input type="text" id="display_total" class="form-control fw-bold text-success fs-5" readonly value="0 ₫">
                        <input type="hidden" id="total_price" name="total_price" value="<?= $oldData['total_price'] ?? 0 ?>">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Ghi chú</label>
                    <textarea name="note" class="form-control" rows="2"><?= $oldData['note'] ?? '' ?></textarea>
                </div>

                <div class="text-end">
                    <a href="index.php?action=booking-list" class="btn btn-secondary me-2">Hủy bỏ</a>
                    <button type="submit" class="btn btn-success px-4 fw-bold"><i class="fas fa-paper-plane me-2"></i> Xác nhận Đặt Tour</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let priceAdult = 0;
    let priceChild = 0;

    // Tự động tính lại giá khi load trang (nếu có dữ liệu cũ)
    document.addEventListener("DOMContentLoaded", function() {
        const select = document.querySelector('select[name="tour_id"]');
        if(select.value) {
            updatePrice(select);
        }
    });

    function updatePrice(select) {
        const option = select.options[select.selectedIndex];
        priceAdult = parseInt(option.getAttribute('data-price-adult')) || 0;
        priceChild = parseInt(option.getAttribute('data-price-child')) || 0;
        calcTotal();
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