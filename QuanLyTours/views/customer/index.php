<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
    .modal { z-index: 9999 !important; }
    .modal-backdrop { z-index: 9998 !important; }
    .table td { vertical-align: middle; }
</style>

<div class="container-fluid p-4">
    
    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="fas fa-check-circle me-2"></i>
            <?php 
                if($_GET['msg']=='success') echo 'Thêm mới thành công!';
                elseif($_GET['msg']=='updated') echo 'Cập nhật thành công!';
                elseif($_GET['msg']=='deleted') echo 'Đã xóa dữ liệu!';
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-secondary">👥 Danh sách Khách hàng</h4>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
            <i class="fas fa-user-plus me-2"></i>Thêm Khách Hàng
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4">Họ tên</th>
                        <th>CCCD/CMND</th>
                        <th>Liên hệ</th>
                        <th>Nguồn</th>
                        <th>Ghi chú</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($customers)): ?>
                        <?php foreach ($customers as $cus): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-primary">
                                <?= htmlspecialchars($cus['full_name']) ?>
                            </td>
                            <td>
                                <?php if(!empty($cus['id_card'])): ?>
                                    <span class="badge bg-light text-dark border"><?= htmlspecialchars($cus['id_card']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted small">--</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div><i class="fas fa-phone-alt text-muted me-1" style="font-size:0.8rem"></i> <?= htmlspecialchars($cus['phone']) ?></div>
                                <?php if(!empty($cus['email'])): ?>
                                    <div class="small text-muted"><i class="fas fa-envelope text-muted me-1"></i> <?= htmlspecialchars($cus['email']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-info bg-opacity-10 text-dark border border-info">
                                    <?= htmlspecialchars($cus['source']) ?>
                                </span>
                            </td>
                            <td class="text-muted small text-truncate" style="max-width: 150px;">
                                <?= htmlspecialchars($cus['notes']) ?>
                            </td>
                            <td class="text-end pe-4 text-nowrap">
                                <a href="index.php?action=customer-detail&id=<?= $cus['id'] ?>" class="btn btn-sm btn-outline-info me-1" title="Xem">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="index.php?action=customer-edit&id=<?= $cus['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="index.php?action=customer-delete&id=<?= $cus['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa khách hàng này?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">Chưa có dữ liệu khách hàng.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<div class="modal fade" id="addCustomerModal" tabindex="-1" style="z-index: 99999 !important;">
    <style>.modal-backdrop { z-index: 99998 !important; }</style>
    <div class="modal-dialog modal-lg">
        <form action="index.php?action=customer-store" method="POST">
            <div class="modal-content">
                <div class="modal-header bg-white border-bottom py-3">
                    <h5 class="modal-title fw-bold text-primary"><i class="fas fa-user-plus me-2"></i>Thêm Khách Hàng Mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light p-4">
                    <div class="card p-4 border-0 shadow-sm">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="fw-bold small">Họ tên <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold small">CCCD/CMND</label>
                                <input type="text" name="id_card" class="form-control" pattern="[0-9]{9,12}" title="9 hoặc 12 số">
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold small">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" required pattern="[0-9]{9,11}">
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold small">Email</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label class="fw-bold small">Địa chỉ</label>
                                <input type="text" name="address" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold small">Nguồn khách</label>
                                <select name="source" class="form-select">
                                    <option value="Direct">Trực tiếp / Hotline</option>
                                    <option value="Facebook">Facebook</option>
                                    <option value="Website">Website</option>
                                    <option value="Referral">Giới thiệu</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="fw-bold small">Ghi chú</label>
                                <textarea name="notes" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top py-3">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">Lưu Thông Tin</button>
                </div>
            </div>
        </form>
    </div>
</div>