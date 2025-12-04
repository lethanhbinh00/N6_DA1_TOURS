<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary">✏️ Cập nhật thông tin</h4>
        <a href="index.php?action=user-list" class="btn btn-outline-secondary btn-sm">Hủy bỏ</a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="index.php?action=user-update" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                        
                        <div class="row g-4">
                            <div class="col-md-4 text-center">
                                <label class="form-label fw-bold">Ảnh đại diện</label>
                                <div class="mb-3">
                                    <?php if(!empty($user['avatar'])): ?>
                                        <img src="public/uploads/imguser/<?= $user['avatar'] ?>" class="rounded-circle border" width="100" height="100">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center border" style="width:100px; height:100px"><i class="fas fa-user fa-2x text-secondary"></i></div>
                                    <?php endif; ?>
                                </div>
                                <input type="file" name="avatar" class="form-control form-control-sm">
                                <small class="text-muted">Chọn ảnh mới để thay đổi</small>
                            </div>

                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="fw-bold small">Email (Không thể thay đổi)</label>
                                    <input type="text" class="form-control bg-light" value="<?= $user['email'] ?>" readonly disabled>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="fw-bold small">Họ và tên</label>
                                    <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                                </div>
                                
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="fw-bold small">Số điện thoại</label>
                                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="fw-bold small">Mật khẩu mới</label>
                                        <input type="password" name="password" class="form-control" placeholder="Để trống nếu không đổi">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="fw-bold small">Vai trò</label>
                                        <select name="role" class="form-select">
                                            <option value="sale" <?= ($user['role']=='sale')?'selected':'' ?>>Nhân viên Sale</option>
                                            <option value="operator" <?= ($user['role']=='operator')?'selected':'' ?>>Điều hành</option>
                                            <option value="guide" <?= ($user['role']=='guide')?'selected':'' ?>>Hướng dẫn viên</option>
                                            <option value="admin" <?= ($user['role']=='admin')?'selected':'' ?>>Admin</option>
                                        </select>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="fw-bold small">Trạng thái</label>
                                        <select name="status" class="form-select">
                                            <option value="active" <?= ($user['status']=='active')?'selected':'' ?>>Hoạt động</option>
                                            <option value="locked" <?= ($user['status']=='locked')?'selected':'' ?>>Khóa</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="text-end mt-3">
                                    <button type="submit" class="btn btn-primary fw-bold px-4">LƯU THAY ĐỔI</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>