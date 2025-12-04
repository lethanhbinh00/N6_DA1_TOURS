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
                if($_GET['msg']=='success') echo 'Thao tác thành công!';
                elseif($_GET['msg']=='deleted') echo 'Đã xóa dữ liệu!';
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-secondary">🏨 Nhà cung cấp dịch vụ</h4>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addSupModal">
            <i class="fas fa-plus me-2"></i>Thêm Nhà Cung Cấp
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4">Tên đơn vị</th>
                        <th>Loại hình</th>
                        <th>Người liên hệ</th>
                        <th>Thông tin liên lạc</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($suppliers)): ?>
                        <?php foreach($suppliers as $s): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-primary"><?= htmlspecialchars($s['name']) ?></td>
                            <td>
                                <?php 
                                    $types = ['hotel'=>'Khách sạn', 'transport'=>'Vận chuyển', 'restaurant'=>'Nhà hàng', 'other'=>'Khác'];
                                    $badges = ['hotel'=>'bg-primary', 'transport'=>'bg-warning', 'restaurant'=>'bg-success', 'other'=>'bg-secondary'];
                                    $sType = $s['type'] ?? 'other';
                                ?>
                                <span class="badge <?= $badges[$sType] ?? 'bg-secondary' ?> bg-opacity-75 border border-<?= str_replace('bg-', '', $badges[$sType]) ?>">
                                    <?= $types[$sType] ?? 'Khác' ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($s['contact_person']) ?></td>
                            <td>
                                <div><i class="fas fa-phone-alt text-muted me-1" style="font-size: 0.8rem;"></i> <?= htmlspecialchars($s['phone']) ?></div>
                                <div class="small text-muted"><i class="fas fa-envelope text-muted me-1"></i> <?= htmlspecialchars($s['email']) ?></div>
                            </td>
                            <td class="text-end pe-4 text-nowrap">
                                <a href="index.php?action=supplier-delete&id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa nhà cung cấp này?')" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">Chưa có dữ liệu.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<div class="modal fade" id="addSupModal" tabindex="-1" style="z-index: 99999 !important;">
    <style>.modal-backdrop { z-index: 99998 !important; }</style>
    <div class="modal-dialog modal-lg">
        <form action="index.php?action=supplier-store" method="POST">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-white border-bottom py-3">
                    <h5 class="modal-title fw-bold text-primary"><i class="fas fa-building me-2"></i>Thêm Nhà Cung Cấp</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light p-4">
                    <div class="card p-4 border-0 shadow-sm">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="fw-bold small">Tên đơn vị <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required placeholder="VD: Khách sạn Mường Thanh...">
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold small">Loại hình</label>
                                <select name="type" class="form-select">
                                    <option value="hotel">Khách sạn</option>
                                    <option value="transport">Vận chuyển (Xe/Tàu/Bay)</option>
                                    <option value="restaurant">Nhà hàng</option>
                                    <option value="other">Khác</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold small">Người liên hệ</label>
                                <input type="text" name="contact_person" class="form-control" placeholder="VD: Anh Nam (Sale)">
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold small">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" required pattern="[0-9]{9,11}" placeholder="09xxxx">
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold small">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="contact@example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold small">Địa chỉ</label>
                                <input type="text" name="address" class="form-control">
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