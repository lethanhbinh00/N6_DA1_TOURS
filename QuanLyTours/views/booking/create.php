<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
    .select2-basic { width: 100%; }
</style>

<div class="container-fluid p-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-success">
            <i class="fas fa-cart-plus me-2"></i>Tạo Booking Mới
        </h4>
        <a href="index.php?action=booking-list" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-11">
            <div class="card shadow-lg border-0">
                
                <div class="card-body p-4">
                    <form action="index.php?action=booking-store" method="POST">
                        
                        <h5 class="text-success border-bottom pb-2 mb-3 fw-bold"><i class="fas fa-suitcase me-2"></i>1. Thông tin Tour & Số lượng</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Chọn Tour <span class="text-danger">*</span></label>
                                <select name="tour_id" id="tour_select" class="form-select" required onchange="updatePrice(this)">
                                    <option value="">-- Gõ tên hoặc mã Tour để tìm --</option>
                                    <?php foreach($tours as $t): ?>
                                        <option value="<?= $t['id'] ?>" 
                                                data-price-adult="<?= $t['price_adult'] ?>" 
                                                data-price-child="<?= $t['price_child'] ?>">
                                            [<?= $t['code'] ?>] <?= htmlspecialchars($t['name']) ?>
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

                        <h5 class="text-success border-bottom pb-2 mb-3 fw-bold d-flex justify-content-between align-items-center">
                            <span>2. Khách hàng / Trưởng đoàn</span>
                            <a href="index.php?action=customer-list" target="_blank" class="btn btn-sm btn-outline-success">
                                <i class="fas fa-user-plus me-1"></i> Tạo khách mới
                            </a>
                        </h5>

                        <div class="row g-3 mb-4 bg-light p-3 rounded mx-0 border">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold text-primary small">
                                    <i class="fas fa-search me-1"></i> Tìm khách hàng (Gõ Tên hoặc SĐT) <span class="text-danger">*</span>
                                </label>
                                <select name="customer_id" id="customer_select" class="form-select" required>
                                    <option value="">-- Gõ để tìm kiếm --</option>
                                    <?php foreach($customers as $cus): ?>
                                        <option value="<?= $cus['id'] ?>" 
                                                data-fullname="<?= htmlspecialchars($cus['full_name']) ?>"
                                                data-phone="<?= htmlspecialchars($cus['phone']) ?>" 
                                                data-email="<?= htmlspecialchars($cus['email'] ?? '') ?>" 
                                                data-card="<?= htmlspecialchars($cus['id_card'] ?? '') ?>">
                                            <?= htmlspecialchars($cus['full_name']) ?> - <?= htmlspecialchars($cus['phone']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted fst-italic ps-1">Hệ thống sẽ tự điền thông tin bên dưới sau khi bạn chọn.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Họ tên</label>
                                <input type="text" name="customer_name" id="customer_name" class="form-control bg-white" readonly required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Số điện thoại</label>
                                <input type="text" name="customer_phone" id="customer_phone" class="form-control bg-white" readonly required>
                            </div>
                            <input type="hidden" name="customer_id_card" id="customer_id_card">
                            <input type="hidden" name="customer_email" id="customer_email">
                        </div>
                        
                        <h5 class="text-success border-bottom pb-2 mb-3 fw-bold">3. Dịch vụ chính & Điểm đón</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Xe / Vận chuyển</label>
                                <select name="transport_supplier_id" class="form-select select2-basic">
                                    <option value="">-- Chưa chọn NCC --</option>
                                    <?php if(isset($suppliers)) foreach($suppliers as $s): if($s['type']=='transport'): ?>
                                        <option value="<?= $s['id'] ?>"><?= $s['name'] ?></option>
                                    <?php endif; endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Khách sạn / Lưu trú</label>
                                <select name="hotel_supplier_id" class="form-select select2-basic">
                                    <option value="">-- Chưa chọn NCC --</option>
                                    <?php if(isset($suppliers)) foreach($suppliers as $s): if($s['type']=='hotel'): ?>
                                        <option value="<?= $s['id'] ?>"><?= $s['name'] ?></option>
                                    <?php endif; endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Điểm đón khách</label>
                                <input type="text" name="pickup_location" class="form-control" placeholder="VD: 123 Nguyễn Trãi...">
                            </div>
                        </div>

                        <h5 class="text-success border-bottom pb-2 mb-3 fw-bold">4. Số lượng & Thanh toán</h5>
                        <div class="row g-3 mb-4 bg-light p-3 rounded mx-0">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Người lớn</label>
                                <input type="number" id="adults" name="adults" class="form-control" value="" min="1" placeholder="0" required autocomplete="off" onchange="calcTotal()">
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
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // [ULTIMATE FIX]: Ép giá trị rỗng sau khi trình duyệt load
        setTimeout(function() {
            $('#adults').val(''); 
        }, 10);
        
        // Kích hoạt Select2
        $('#customer_select').select2({
            placeholder: "-- Tìm kiếm Khách hàng --",
            allowClear: true,
            width: '100%'
        });
        $('#tour_select').select2({
            placeholder: "-- Tìm kiếm Tour --",
            allowClear: true,
            width: '100%'
        });
        $('.select2-basic').select2({ width: '100%' });

        // Sự kiện khi chọn khách hàng -> Điền thông tin vào input ẩn
        $('#customer_select').on('select2:select', function (e) {
            var option = e.params.data.element;
            
            $('#customer_name').val($(option).data('fullname'));
            $('#customer_phone').val($(option).data('phone'));
            $('#customer_email').val($(option).data('email'));
            $('#customer_id_card').val($(option).data('card'));
        });

        // Sự kiện khi xóa chọn khách
        $('#customer_select').on('select2:clear', function (e) {
            $('#customer_name').val('');
            $('#customer_phone').val('');
            $('#customer_email').val('');
            $('#customer_id_card').val('');
        });

        // Sự kiện chọn Tour -> Tính giá
        $('#tour_select').on('select2:select', function (e) {
            var option = $(this).find(':selected')[0];
            updatePriceFromOption(option);
        });
    });

    // --- CÁC HÀM TÍNH TIỀN ---
    let priceAdult = 0;
    let priceChild = 0;

    function updatePrice(selectElement) {
        const option = selectElement.options[selectElement.selectedIndex];
        updatePriceFromOption(option);
    }

    function updatePriceFromOption(option) {
        priceAdult = parseInt(option.getAttribute('data-price-adult')) || 0;
        priceChild = parseInt(option.getAttribute('data-price-child')) || 0;
        calcTotal();
    }

    function calcTotal() {
        // Lấy giá trị sau khi đã xóa cache
        const adults = parseInt(document.getElementById('adults').value) || 0;
        const children = parseInt(document.getElementById('children').value) || 0;
        const total = (adults * priceAdult) + (children * priceChild);
        
        document.getElementById('display_total').value = new Intl.NumberFormat('vi-VN').format(total) + ' ₫';
        document.getElementById('total_price').value = total;
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>