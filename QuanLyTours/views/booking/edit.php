<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php 
// Đảm bảo các biến $booking, $tours, $customers, $suppliers đã được truyền vào
if (empty($booking)) {
    echo "<div class='alert alert-danger'>Không tìm thấy dữ liệu Booking.</div>";
    die();
}
?>

<style>
    .select2-basic { width: 100%; }
</style>

<div class="container-fluid p-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary">
            <i class="fas fa-edit me-2"></i>Cập nhật Booking: <?= htmlspecialchars($booking['booking_code']) ?>
        </h4>
        <a href="index.php?action=booking-list" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-11">
            <div class="card shadow-lg border-0">
                
                <div class="card-body p-4">
                    <form action="index.php?action=booking-update" method="POST">
                        <input type="hidden" name="id" value="<?= $booking['id'] ?>">
                        <input type="hidden" id="initial_price_adult" value="<?= $booking['price_adult'] ?? 0 ?>">
                        <input type="hidden" id="initial_price_child" value="<?= $booking['price_child'] ?? 0 ?>">
                        
                        <h5 class="text-success border-bottom pb-2 mb-3 fw-bold"><i class="fas fa-suitcase me-2"></i>1. Thông tin Tour & Ngày đi</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Chọn Tour <span class="text-danger">*</span></label>
                                <select name="tour_id" id="tour_select" class="form-select" required onchange="updatePrice(this)">
                                    <option value="">-- Gõ tên hoặc mã Tour để tìm --</option>
                                    <?php foreach($tours as $t): ?>
                                        <option value="<?= $t['id'] ?>" 
                                                data-price-adult="<?= $t['price_adult'] ?>" 
                                                data-price-child="<?= $t['price_child'] ?>"
                                                <?= ($t['id'] == $booking['tour_id']) ? 'selected' : '' ?>>
                                            [<?= $t['code'] ?>] <?= htmlspecialchars($t['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Ngày đi <span class="text-danger">*</span></label>
                                <input type="date" name="travel_date" class="form-control" value="<?= htmlspecialchars($booking['travel_date']) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Ngày về (Dự kiến)</label>
                                <input type="date" name="return_date" class="form-control" value="<?= htmlspecialchars($booking['return_date']) ?>">
                            </div>
                        </div>

                        <h5 class="text-success border-bottom pb-2 mb-3 fw-bold"><i class="fas fa-user me-2"></i>2. Khách hàng / Trưởng đoàn</h5>
                        <div class="row g-3 mb-4 bg-light p-3 rounded mx-0 border">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold text-primary small">Tìm khách hàng <span class="text-danger">*</span></label>
                                <select name="customer_id" id="customer_select" class="form-select" required>
                                    <option value="">-- Gõ để tìm kiếm --</option>
                                    <?php foreach($customers as $cus): ?>
                                        <option value="<?= $cus['id'] ?>" 
                                                data-fullname="<?= htmlspecialchars($cus['full_name']) ?>"
                                                data-phone="<?= htmlspecialchars($cus['phone']) ?>" 
                                                data-email="<?= htmlspecialchars($cus['email'] ?? '') ?>" 
                                                data-card="<?= htmlspecialchars($cus['id_card'] ?? '') ?>"
                                                <?= ($cus['id'] == $booking['customer_id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cus['full_name']) ?> - <?= htmlspecialchars($cus['phone']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Họ tên</label>
                                <input type="text" name="customer_name" id="customer_name" class="form-control bg-white" readonly required value="<?= htmlspecialchars($booking['customer_name'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Số điện thoại</label>
                                <input type="text" name="customer_phone" id="customer_phone" class="form-control bg-white" readonly required value="<?= htmlspecialchars($booking['customer_phone'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">CCCD/CMND</label>
                                <input type="text" name="customer_id_card" id="customer_id_card" class="form-control bg-white" readonly value="<?= htmlspecialchars($booking['customer_id_card'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Email</label>
                                <input type="text" name="customer_email" id="customer_email" class="form-control bg-white" readonly value="<?= htmlspecialchars($booking['customer_email'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <h5 class="text-success border-bottom pb-2 mb-3 fw-bold"><i class="fas fa-bus-alt me-2"></i>3. Dịch vụ chính & Điểm đón</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-muted">Xe / Vận chuyển</label>
                                <select name="transport_supplier_id" class="form-select select2-basic">
                                    <option value="">-- Chưa chọn NCC --</option>
                                    <?php if(isset($suppliers)) foreach($suppliers as $s): if($s['type']=='transport'): ?>
                                        <option value="<?= $s['id'] ?>" <?= ($s['id'] == $booking['transport_supplier_id']) ? 'selected' : '' ?>>
                                            <?= $s['name'] ?>
                                        </option>
                                    <?php endif; endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-muted">Khách sạn / Lưu trú</label>
                                <select name="hotel_supplier_id" class="form-select select2-basic">
                                    <option value="">-- Chưa chọn NCC --</option>
                                    <?php if(isset($suppliers)) foreach($suppliers as $s): if($s['type']=='hotel'): ?>
                                        <option value="<?= $s['id'] ?>" <?= ($s['id'] == $booking['hotel_supplier_id']) ? 'selected' : '' ?>>
                                            <?= $s['name'] ?>
                                        </option>
                                    <?php endif; endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-muted">Số hiệu (Bay/Xe)</label>
                                <input type="text" name="flight_number" class="form-control" placeholder="VD: VN123 / 29A-12345" value="<?= htmlspecialchars($booking['flight_number'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-muted">Chi tiết phòng</label>
                                <input type="text" name="room_details" class="form-control" placeholder="VD: 2 Phòng đôi" value="<?= htmlspecialchars($booking['room_details'] ?? '') ?>">
                            </div>
                            
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-muted">Điểm đón khách</label>
                                <input type="text" name="pickup_location" class="form-control" placeholder="Ghi rõ địa điểm, thời gian đón khách..." value="<?= htmlspecialchars($booking['pickup_location'] ?? '') ?>">
                            </div>
                        </div>

                        <h5 class="text-success border-bottom pb-2 mb-3 fw-bold"><i class="fas fa-hashtag me-2"></i>4. Số lượng & Thanh toán</h5>
                        <div class="row g-3 mb-4 bg-light p-3 rounded mx-0">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Người lớn</label>
                                <input type="number" id="adults" name="adults" class="form-control" value="<?= $booking['adults'] ?>" min="1" required onchange="calcTotal()">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Trẻ em</label>
                                <input type="number" id="children" name="children" class="form-control" value="<?= $booking['children'] ?>" min="0" onchange="calcTotal()">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-success">TỔNG TIỀN TẠM TÍNH</label>
                                <input type="text" id="display_total" class="form-control fw-bold text-success fs-5" readonly value="<?= number_format($booking['total_price'] ?? 0) ?> ₫">
                                <input type="hidden" id="total_price" name="total_price" value="<?= $booking['total_price'] ?? 0 ?>">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Ghi chú</label>
                            <textarea name="note" class="form-control" rows="2"><?= htmlspecialchars($booking['note'] ?? '') ?></textarea>
                        </div>

                        <div class="text-end">
                            <a href="index.php?action=booking-detail&id=<?= $booking['id'] ?>" class="btn btn-secondary me-2">Hủy bỏ</a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold">
                                <i class="fas fa-save me-2"></i> Lưu Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // --- KHỞI TẠO BIẾN CẦN THIẾT ---
    let priceAdult = 0;
    let priceChild = 0;

    // --- LOGIC FORM ---
    $(document).ready(function() {
        // Khởi tạo Select2 cho tất cả các select box cần thiết
        $('#customer_select').select2({ placeholder: "-- Tìm kiếm Khách hàng --", allowClear: true, width: '100%' });
        $('#tour_select').select2({ placeholder: "-- Tìm kiếm Tour --", allowClear: true, width: '100%' });
        $('.select2-basic').select2({ width: '100%' });

        // KHỞI TẠO GIÁ VÀ TÍNH TỔNG LẦN ĐẦU
        const selectedTour = $('#tour_select').find(':selected')[0];
        if (selectedTour) {
            updatePriceFromOption(selectedTour);
        } else {
            calcTotal();
        }

        // Sự kiện khi chọn khách hàng -> Đồng bộ dữ liệu
        $('#customer_select').on('select2:select', function (e) {
            const option = e.params.data.element;
            const $element = $(option);
            
            // Lấy data snapshot từ option mới
            $('#customer_name').val($element.data('fullname') || '');
            $('#customer_phone').val($element.data('phone') || '');
            $('#customer_email').val($element.data('email') || '');
            $('#customer_id_card').val($element.data('card') || '');
        });

        // Sự kiện khi thay đổi tour -> Cập nhật giá
        $('#tour_select').on('change', function (e) {
            const option = this.options[this.selectedIndex];
            updatePriceFromOption(option);
        });
    });

    // --- CÁC HÀM TÍNH TOÁN ---
    function updatePriceFromOption(option) {
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