<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
    .modal { z-index: 9999 !important; }
    .modal-backdrop { z-index: 9998 !important; }
    .table td { vertical-align: middle; }
    .table-detail-info { font-size: 0.9rem; }
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
        <h4 class="fw-bold text-secondary">🏨 Nhà cung cấp dịch vụ</h4>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addEditSupModal" onclick="clearModal()">
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
                        <th>Liên hệ</th>
                        <th>Năng lực cung cấp</th>
                        <th>Hợp đồng</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($suppliers)): ?>
                        <?php foreach($suppliers as $s): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-primary">
                                <?= htmlspecialchars($s['name']) ?>
                            </td>
                            <td>
                                <?php 
                                    $types = ['hotel'=>'Khách sạn', 'transport'=>'Vận chuyển', 'restaurant'=>'Nhà hàng', 'other'=>'Khác'];
                                    $badges = ['hotel'=>'bg-primary', 'transport'=>'bg-warning', 'restaurant'=>'bg-success', 'other'=>'bg-secondary'];
                                    $sType = $s['type'] ?? 'other';
                                ?>
                                <span class="badge <?= $badges[$sType] ?? 'bg-secondary' ?> bg-opacity-75">
                                    <?= $types[$sType] ?? 'Khác' ?>
                                </span>
                            </td>
                            <td class="table-detail-info">
                                <div><i class="fas fa-phone-alt text-muted me-1" style="font-size: 0.8rem;"></i> <?= htmlspecialchars($s['phone']) ?></div>
                                <div class="small text-muted"><i class="fas fa-user me-1"></i> <?= htmlspecialchars($s['contact_person']) ?></div>
                            </td>
                            <td class="text-muted table-detail-info">
                                <?= htmlspecialchars($s['service_capacity'] ?? '---') ?>
                            </td>
                            <td>
                                <?php if(!empty($s['contract_expiry'])): ?>
                                    <span class="badge bg-success">Hết hạn: <?= date('d/m/Y', strtotime($s['contract_expiry'])) ?></span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Chưa có HĐ</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4 text-nowrap">
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="editSupplier(<?= htmlspecialchars(json_encode($s)) ?>)" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="index.php?action=supplier-delete&id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa NCC này?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">Chưa có NCC nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<div class="modal fade" id="addEditSupModal" tabindex="-1" style="z-index: 99999 !important;">
    <style>.modal-backdrop { z-index: 99998 !important; }</style>
    <div class="modal-dialog modal-xl">
        <form action="index.php?action=supplier-store" method="POST">
            <input type="hidden" name="id" id="sup_id">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold" id="modalTitle"><i class="fas fa-building me-2"></i>Thêm Nhà Cung Cấp</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card p-4 border-0 shadow-sm bg-white h-100">
                                <h6 class="text-uppercase text-primary fw-bold mb-3 border-bottom pb-2 small">1. Thông tin cơ bản</h6>
                                <div class="mb-3">
                                    <label class="fw-bold small">Tên đơn vị <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="sup_name" class="form-control" required placeholder="VD: Khách sạn Mường Thanh...">
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="fw-bold small">Loại hình</label>
                                        <select name="type" id="sup_type" class="form-select">
                                            <option value="hotel">Khách sạn</option>
                                            <option value="transport">Vận chuyển</option>
                                            <option value="restaurant">Nhà hàng</option>
                                            <option value="other">Khác</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-bold small">Người liên hệ</label>
                                        <input type="text" name="contact_person" id="sup_contact_person" class="form-control" placeholder="VD: Anh Nam (Sale)">
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="fw-bold small">Số điện thoại <span class="text-danger">*</span></label>
                                        <input type="text" name="phone" id="sup_phone" class="form-control" required placeholder="09xxxx">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-bold small">Email</label>
                                        <input type="email" name="email" id="sup_email" class="form-control" placeholder="contact@example.com">
                                    </div>
                                </div>
                                <div class="mb-3 mt-3">
                                    <label class="fw-bold small">Địa chỉ</label>
                                    <input type="text" name="address" id="sup_address" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card p-4 border-0 shadow-sm bg-white h-100">
                                <h6 class="text-uppercase text-primary fw-bold mb-3 border-bottom pb-2 small">2. Chi tiết dịch vụ & Hợp đồng</h6>
                                <div class="mb-3">
                                    <label class="fw-bold small">Mô tả dịch vụ chi tiết</label>
                                    <textarea name="service_description" id="sup_service_description" class="form-control" rows="3" placeholder="VD: Khách sạn 4 sao, 100 phòng, gần biển..."></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="fw-bold small">Năng lực cung cấp</label>
                                    <input type="text" name="service_capacity" id="sup_service_capacity" class="form-control" placeholder="VD: 50 phòng đôi, 3 xe 45 chỗ...">
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="fw-bold small">Ngày hết hạn HĐ</label>
                                        <input type="date" name="contract_expiry" id="sup_contract_expiry" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-bold small">File Hợp đồng (Link/Tên file)</label>
                                        <input type="text" name="contract_file" id="sup_contract_file" class="form-control" placeholder="Link file drive...">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top py-3">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" id="submitButton" class="btn btn-primary px-4 fw-bold shadow-sm">Lưu Hồ Sơ</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Hàm làm sạch modal (cho chế độ Thêm mới)
    function clearModal() {
        $('#modalTitle').text('Thêm Nhà Cung Cấp Mới');
        $('#submitButton').text('Lưu Hồ Sơ').removeClass('btn-warning').addClass('btn-primary');
        $('#sup_id').val('');
        $('#addEditSupModal form').attr('action', 'index.php?action=supplier-store');
        $('#addEditSupModal form')[0].reset();
        
        // Reset tất cả các ô nhập liệu thủ công
        $('#sup_contract_expiry').val(''); 
        $('#sup_type').val('hotel').trigger('change');
    }

    // Hàm đổ dữ liệu vào modal (cho chế độ Sửa)
    function editSupplier(supplier) {
        $('#modalTitle').text('Cập nhật NCC: ' + supplier.name);
        $('#submitButton').text('Lưu Cập Nhật').removeClass('btn-primary').addClass('btn-warning');
        $('#addEditSupModal form').attr('action', 'index.php?action=supplier-update');

        $('#sup_id').val(supplier.id);
        $('#sup_name').val(supplier.name);
        $('#sup_type').val(supplier.type).trigger('change'); 
        $('#sup_contact_person').val(supplier.contact_person);
        $('#sup_phone').val(supplier.phone);
        $('#sup_email').val(supplier.email);
        $('#sup_address').val(supplier.address);
        
        // Dữ liệu mới
        $('#sup_service_description').val(supplier.service_description);
        $('#sup_service_capacity').val(supplier.service_capacity);
        $('#sup_contract_file').val(supplier.contract_file);
        $('#sup_contract_expiry').val(supplier.contract_expiry);

        new bootstrap.Modal(document.getElementById('addEditSupModal')).show();
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>