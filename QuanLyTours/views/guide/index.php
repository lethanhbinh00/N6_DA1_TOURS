<!-- 1. GỌI HEADER -->
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- 2. NỘI DUNG CHÍNH (Bảng danh sách) -->
<div class="container-fluid p-4">
    
    <!-- Thông báo -->
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
        <h4 class="fw-bold text-secondary">🚩 Đội ngũ Hướng dẫn viên</h4>
        <!-- Nút Thêm -->
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addGuideModal">
            <i class="fas fa-user-plus me-2"></i>Thêm HDV Mới
        </button>
    </div>

    <!-- Bảng Dữ Liệu -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4">Ảnh</th>
                        <th>Thông tin cá nhân</th>
                        <th>Chuyên môn</th>
                        <th>Kinh nghiệm</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($guides)): ?>
                        <?php foreach ($guides as $g): ?>
                        <tr>
                            <td class="ps-4">
                                <?php if(!empty($g['image']) && file_exists('public/uploads/' . $g['image'])): ?>
                                    <img src="public/uploads/<?= $g['image'] ?>" class="rounded-circle border shadow-sm" width="50" height="50" style="object-fit:cover">
                                <?php else: ?>
                                    <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center text-muted" style="width:50px; height:50px">
                                        <i class="fas fa-user"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-bold text-primary"><?= htmlspecialchars($g['full_name']) ?></div>
                                <div class="small text-muted">SĐT: <?= htmlspecialchars($g['phone']) ?></div>
                            </td>
                            <td><span class="badge bg-info bg-opacity-10 text-dark border border-info"><?= htmlspecialchars($g['languages']) ?></span></td>
                            <td><?= $g['experience_years'] ?> năm</td>
                            <td>
                                <span class="badge <?= ($g['status']=='available')?'bg-success':'bg-warning' ?>">
                                    <?= ($g['status']=='available')?'Sẵn sàng':'Đang bận' ?>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="index.php?action=guide-delete&id=<?= $g['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa nhân sự này?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">Chưa có dữ liệu.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 3. GỌI FOOTER (Để đóng các thẻ div chính) -->
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<!-- ==================================================================== -->
<!-- 4. MODAL FORM (ĐẶT SAU FOOTER ĐỂ NÓ NẰM NGOÀI CÙNG DOM) -->
<!-- Thêm style z-index cực cao để đè lên mọi thứ -->
<!-- ==================================================================== -->
<div class="modal fade" id="addGuideModal" tabindex="-1" style="z-index: 99999 !important;">
    <!-- Backdrop (màn đen) cũng phải chỉnh -->
    <style>.modal-backdrop { z-index: 99998 !important; }</style>

    <div class="modal-dialog" style="max-width: 900px;">
        <form action="index.php?action=guide-store" method="POST" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header bg-white border-bottom py-3">
                    <h5 class="modal-title fw-bold text-primary"><i class="fas fa-id-card-alt me-2"></i>Hồ sơ Hướng dẫn viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body bg-light p-4">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="card p-3 border-0 shadow-sm h-100 text-center bg-white">
                                <label class="form-label fw-bold mb-2">Ảnh chân dung</label>
                                <div class="border border-dashed p-3 rounded mb-3 bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <div class="text-muted"><i class="fas fa-camera fa-3x mb-2 opacity-50"></i><br><small>Tải ảnh lên</small></div>
                                </div>
                                <input type="file" name="image" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card p-4 border-0 shadow-sm bg-white h-100">
                                <h6 class="text-uppercase text-secondary fw-bold mb-3 border-bottom pb-2 small">1. Thông tin cá nhân</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-7">
                                        <label class="fw-bold small">Họ và tên <span class="text-danger">*</span></label>
                                        <input type="text" name="full_name" class="form-control" required placeholder="Nguyễn Văn A">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="fw-bold small">Giới tính</label>
                                        <select name="gender" class="form-select">
                                            <option value="male">Nam</option>
                                            <option value="female">Nữ</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-bold small">Ngày sinh</label>
                                        <input type="date" name="dob" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-bold small">Số điện thoại <span class="text-danger">*</span></label>
                                        <input type="text" name="phone" class="form-control" required placeholder="09xxxxxxxx">
                                    </div>
                                </div>
                                <h6 class="text-uppercase text-secondary fw-bold mb-3 border-bottom pb-2 small">2. Chuyên môn & Nghiệp vụ</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="fw-bold small">Số thẻ HDV</label>
                                        <input type="text" name="license_number" class="form-control" placeholder="VD: 12345/HDV">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-bold small">Kinh nghiệm (Năm)</label>
                                        <input type="number" name="experience_years" class="form-control" value="1">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="fw-bold small">Ngôn ngữ thành thạo</label>
                                        <input type="text" name="languages" class="form-control" placeholder="VD: Anh, Trung, Hàn...">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="fw-bold small">Địa chỉ thường trú</label>
                                        <input type="text" name="address" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-white py-3">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm"><i class="fas fa-save me-2"></i> Lưu Hồ Sơ</button>
                </div>
            </div>
        </form>
    </div>
</div>