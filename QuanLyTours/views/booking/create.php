<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tạo Booking Mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Tạo Booking Mới (Khách lẻ / Đoàn)</h5>
            <a href="index.php?action=booking-list" class="btn btn-sm btn-light text-success fw-bold">
                <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
            </a>
        </div>
        <div class="card-body">
            <form action="index.php?action=booking-store" method="POST">
                
                <h6 class="text-secondary border-bottom pb-2">1. Thông tin dịch vụ</h6>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Chọn Tour <span class="text-danger">*</span></label>
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
                    <div class="col-md-6">
                        <label class="form-label">Ngày khởi hành <span class="text-danger">*</span></label>
                        <input type="date" name="travel_date" class="form-control" required>
                    </div>
                </div>

                <h6 class="text-secondary border-bottom pb-2 mt-4">2. Thông tin khách hàng / Trưởng đoàn</h6>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Họ và tên</label>
                        <input type="text" name="customer_name" class="form-control" required placeholder="Nguyễn Văn A">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="customer_phone" class="form-control" required placeholder="09xxxx">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input type="email" name="customer_email" class="form-control" placeholder="abc@gmail.com">
                    </div>
                </div>

                <h6 class="text-secondary border-bottom pb-2 mt-4">3. Số lượng & Thanh toán</h6>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Người lớn</label>
                        <input type="number" id="adults" name="adults" class="form-control" value="1" min="1" onchange="calcTotal()">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Trẻ em</label>
                        <input type="number" id="children" name="children" class="form-control" value="0" min="0" onchange="calcTotal()">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tổng tiền tạm tính (VNĐ)</label>
                        <input type="text" id="display_total" class="form-control fw-bold text-success" readonly value="0">
                        <input type="hidden" id="total_price" name="total_price" value="0">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ghi chú / Yêu cầu đặc biệt</label>
                    <textarea name="note" class="form-control" rows="2" placeholder="VD: Khách ăn chay, Đoàn công ty ABC..."></textarea>
                </div>

                <div class="text-end">
                    <div class="text-end">
                        <a href="index.php?action=booking-list" class="btn btn-secondary me-2">
                            <i class="fas fa-times me-1"></i> Hủy bỏ </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Xác nhận Đặt Tour </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
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
</script>

</body>
</html>