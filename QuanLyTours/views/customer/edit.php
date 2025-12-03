<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-primary m-0">✏️ Cập nhật thông tin khách hàng</h5>
                    <a href="index.php?action=customer-list" class="btn btn-outline-secondary btn-sm">Hủy bỏ</a>
                </div>
                
                <div class="card-body p-4">
                    <form action="index.php?action=customer-update" method="POST">
                        <input type="hidden" name="id" value="<?= $customer['id'] ?>">
                        
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="fw-bold small">Họ tên <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control" required value="<?= htmlspecialchars($customer['full_name']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold small">CCCD/CMND</label>
                                <input type="text" name="id_card" class="form-control" pattern="[0-9]{9,12}" value="<?= htmlspecialchars($customer['id_card'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold small">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" required pattern="[0-9]{9,11}" value="<?= htmlspecialchars($customer['phone']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold small">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($customer['email'] ?? '') ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="fw-bold small">Địa chỉ</label>
                                <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($customer['address'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold small">Nguồn khách</label>
                                <select name="source" class="form-select">
                                    <option value="Direct" <?= ($customer['source']=='Direct')?'selected':'' ?>>Trực tiếp</option>
                                    <option value="Facebook" <?= ($customer['source']=='Facebook')?'selected':'' ?>>Facebook</option>
                                    <option value="Website" <?= ($customer['source']=='Website')?'selected':'' ?>>Website</option>
                                    <option value="Referral" <?= ($customer['source']=='Referral')?'selected':'' ?>>Giới thiệu</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="fw-bold small">Ghi chú</label>
                                <textarea name="notes" class="form-control" rows="3"><?= htmlspecialchars($customer['notes'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary px-4 fw-bold">Lưu Cập Nhật</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>