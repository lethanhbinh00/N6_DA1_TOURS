<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid p-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-secondary">
            <i class="fas fa-cart-plus me-2"></i>Tạo Booking Mới
        </h4>
        <a href="index.php?action=booking-list" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-success text-white fw-bold py-3">
            <i class="fas fa-edit me-2"></i>Thông tin đặt tour
        </div>
        
        <div class="card-body p-4">
            <form action="index.php?action=booking-store" method="POST">
                
                <h6 class="text-success border-bottom pb-2 mb-3 fw-bold">1. Thông tin dịch vụ</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Chọn Tour <span class="text-danger">*</span></label>
                        <select name="tour_id" class="form-select" required onchange="updatePrice(this)">
                            <option value="">-- Chọn Tour --</option>
                            <?php foreach($tours as $t): ?>
                                <option value="<?= $t['id'] ?>" 
                                        data-price-adult="<?= $t['price_adult'] ?>" 
                                        data-price-child="<?= $t['price_child'] ?>">
                                    [<?= $t['code'] ?>] <?= $t['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Ngày đi <span class="text-danger">*</span></label>
                        <input type="date" name="travel_date" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Ngày về (Dự kiến)</label>
                        <input type="date" name="return_date" class="form-control">
                    </div>
                </div>

                <h6 class="text-success border-bottom pb-2 mb-3 fw-bold d-flex justify-content-between align-items-center">
                    <span>2. Thông tin khách hàng / Trưởng đoàn</span>
                    <a href="index.php?action=customer-list" target="_blank" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-user-plus me-1"></i> Tạo khách mới
                    </a>
                </h6>

                <div class="row g-3 mb-4 bg-light p-3 rounded mx-0 border">
                    <div class="col-md-12">
                        <label class="form-label fw-bold text-primary">
                            <i class="fas fa-search me-1"></i> Tìm khách hàng (Tên hoặc SĐT) <span class="text-danger">*</span>
                        </label>
                        <select id="customer_select" class="form-select" onchange="fillCustomerInfo(this)" required>
                            <option value="">-- Chọn khách hàng đã có --</option>
                            <?php foreach($customers as $cus): ?>
                                <option value="<?= $cus['full_name'] ?>" 
                                        data-phone="<?= $cus['phone'] ?>" 
                                        data-email="<?= $cus['email'] ?>" 
                                        data-card="<?= $cus['id_card'] ?>">
                                    <?= $cus['full_name'] ?> - <?= $cus['phone'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted fst-italic">Nếu không tìm thấy, vui lòng bấm nút "Tạo khách mới" ở trên.</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Họ tên</label>
                        <input type="text" name="customer_name" id="customer_name" class="form-control bg-white" readonly required>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">CCCD/CMND</label>
                        <input type="text" name="customer_id_card" id="customer_id_card" class="form-control bg-white" readonly>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Số điện thoại</label>
                        <input type="text" name="customer_phone" id="customer_phone" class="form-control bg-white" readonly required>
                    </div>
                    
                    <div class="col-md-12">
                        <label class="form-label fw-bold small">Email</label>
                        <input type="text" name="customer_email" id="customer_email" class="form-control bg-white" readonly>
                    </div>
                </div>

                <h6 class="text-success border-bottom pb-2 mb-3 fw-bold">3. Số lượng & Thanh toán</h6>
                <div class="row g-3 mb-4 bg-light p-3 rounded mx-0">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Người lớn</label>
                        <input type="number" id="adults" name="adults" class="form-control" value="1" min="1" onchange="calcTotal()">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Trẻ em</label>
                        <input type="number" id="children" name="children" class="form-control" value="0" min="0" onchange="calcTotal()">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-success">TỔNG TIỀN TẠM TÍNH</label>
                        <input type="text" id="display_total" class="form-control fw-bold text-success fs-5" readonly value="0 ₫">
                        <input type="hidden" id="total_price" name="total_price" value="0">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Ghi chú</label>
                    <textarea name="note" class="form-control" rows="2"></textarea>
                </div>

                <div class="text-end">
                    <a href="index.php?action=booking-list" class="btn btn-secondary me-2">Hủy bỏ</a>
                    <button type="submit" class="btn btn-success px-4 fw-bold">
                        <i class="fas fa-paper-plane me-2"></i> Xác nhận Đặt Tour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // 1. Hàm tính tiền Tour
    let priceAdult = 0;
    let priceChild = 0;

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

    // 2. Hàm tự động điền thông tin Khách
    function fillCustomerInfo(select) {
        const option = select.options[select.selectedIndex];
        
        if(select.value === "") {
            // Xóa trắng nếu không chọn
            document.getElementById('customer_name').value = "";
            document.getElementById('customer_phone').value = "";
            document.getElementById('customer_email').value = "";
            document.getElementById('customer_id_card').value = "";
            return;
        }

        // Điền dữ liệu
        document.getElementById('customer_name').value = option.value;
        document.getElementById('customer_phone').value = option.getAttribute('data-phone');
        document.getElementById('customer_email').value = option.getAttribute('data-email');
        document.getElementById('customer_id_card').value = option.getAttribute('data-card');
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>