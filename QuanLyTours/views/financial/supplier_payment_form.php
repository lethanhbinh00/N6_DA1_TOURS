<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid p-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php?action=booking-ops&id=<?= $booking['id'] ?>">Điều hành Tour</a></li>
            <li class="breadcrumb-item active">Lập phiếu chi trả NCC</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="fas fa-file-invoice-dollar me-2"></i>PHIẾU CHI TIỀN DỊCH VỤ</h5>
                </div>
                <div class="card-body p-4">
                    <form action="index.php?action=store-supplier-payment" method="POST">
                        <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                        <input type="hidden" name="service_id" value="<?= $service['id'] ?>">

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small uppercase">Thông tin dịch vụ</label>
                            <div class="p-3 bg-light rounded shadow-sm border-start border-primary border-4">
                                <div class="d-flex justify-content-between">
                                    <span>Loại dịch vụ:</span>
                                    <strong class="text-uppercase"><?= htmlspecialchars($service['service_type']) ?></strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Nhà cung cấp:</span>
                                    <strong><?= htmlspecialchars($service['supplier_name']) ?></strong>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <span>Số tiền cần thanh toán:</span>
                                    <strong class="text-danger fs-5"><?= number_format($service['cost']) ?> ₫</strong>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Số tiền chi trả thực tế</label>
                            <div class="input-group">
                                <input type="number" name="amount" class="form-control form-control-lg fw-bold text-primary" 
                                       value="<?= $service['cost'] ?>" required>
                                <span class="input-group-text">₫</span>
                            </div>
                            <div class="form-text">Mặc định lấy theo tổng chi phí dịch vụ.</div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ngày chi</label>
                                <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Phương thức</label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="Chuyển khoản">Chuyển khoản</option>
                                    <option value="Tiền mặt">Tiền mặt</option>
                                    <option value="Công nợ">Ghi nợ NCC</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Ghi chú phiếu chi</label>
                            <textarea name="note" class="form-control" rows="2" placeholder="Ví dụ: Thanh toán đợt 1 tiền xe..."></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="index.php?action=booking-ops&id=<?= $booking['id'] ?>" class="btn btn-outline-secondary px-4">Hủy</a>
                            <button type="submit" class="btn btn-primary px-5 fw-bold">Xác nhận Chi tiền</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>