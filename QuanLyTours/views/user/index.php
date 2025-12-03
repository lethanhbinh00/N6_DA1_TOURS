<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid p-4">
    
    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="fas fa-check-circle me-2"></i>
            <?php 
                if($_GET['msg']=='success') echo 'Tạo tài khoản thành công!';
                elseif($_GET['msg']=='updated') echo 'Cập nhật thành công!';
                elseif($_GET['msg']=='deleted') echo 'Đã xóa tài khoản!';
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-secondary">👥 Quản lý Tài khoản & Nhân sự</h4>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fas fa-user-plus me-2"></i>Thêm Nhân viên
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4">Nhân viên</th>
                        <th>Liên hệ</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <?php 
                                        $ava = (!empty($u['avatar']) && file_exists('public/uploads/imguser/'.$u['avatar'])) 
                                            ? 'public/uploads/imguser/'.$u['avatar'] 
                                            : 'https://ui-avatars.com/api/?name='.urlencode($u['full_name']).'&background=random'; 
                                    ?>
                                    <img src="<?= $ava ?>" class="rounded-circle me-3 border shadow-sm" width="40" height="40" style="object-fit:cover">
                                    <div>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($u['full_name']) ?></div>
                                        <small class="text-muted">ID: <?= $u['id'] ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div><i class="fas fa-envelope me-1 text-muted" style="font-size:0.8rem"></i> <?= htmlspecialchars($u['email']) ?></div>
                                <div class="small text-muted"><i class="fas fa-phone me-1" style="font-size:0.8rem"></i> <?= htmlspecialchars($u['phone'] ?? '--') ?></div>
                            </td>
                            <td>
                                <?php 
                                    $roles = ['admin'=>'Quản trị viên', 'sale'=>'NV Kinh doanh', 'operator'=>'Điều hành', 'guide'=>'Hướng dẫn viên'];
                                    $colors = ['admin'=>'danger', 'sale'=>'primary', 'operator'=>'info', 'guide'=>'warning'];
                                ?>
                                <span class="badge bg-<?= $colors[$u['role']] ?? 'secondary' ?> bg-opacity-75 border border-<?= $colors[$u['role']] ?? 'secondary' ?>">
                                    <?= $roles[$u['role']] ?? $u['role'] ?>
                                </span>
                            </td>
                            <td>
                                <?php if($u['status'] == 'active'): ?>
                                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Hoạt động</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><i class="fas fa-lock me-1"></i>Đã khóa</span>
                                <?php endif; ?>
                            </td>
                            
                            <td class="text-end pe-4 text-nowrap">
                                <a href="index.php?action=user-detail&id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-info me-1" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="index.php?action=user-edit&id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Sửa thông tin">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="index.php?action=user-delete&id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?');" title="Xóa">
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

<div class="modal fade" id="addUserModal" tabindex="-1" style="z-index: 99999 !important;">
    <style>.modal-backdrop { z-index: 99998 !important; }</style>
    <div class="modal-dialog">
        <form action="index.php?action=user-store" method="POST" enctype="multipart/form-data">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold"><i class="fas fa-user-plus me-2"></i>Thêm Tài khoản Mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light p-4">
                    <div class="text-center mb-3">
                        <div class="border border-dashed p-3 rounded bg-white d-inline-block">
                            <i class="fas fa-camera fa-2x text-secondary"></i><br>
                            <input type="file" name="avatar" class="form-control form-control-sm mt-2">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold small">Họ và tên</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="fw-bold small">Email (Tên đăng nhập)</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="fw-bold small">Mật khẩu</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold small">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control" placeholder="09xxxx">
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="fw-bold small">Vai trò</label>
                            <select name="role" class="form-select">
                                <option value="sale">Nhân viên Sale</option>
                                <option value="operator">Điều hành</option>
                                <option value="guide">Hướng dẫn viên</option>
                                <option value="admin" class="text-danger fw-bold">Quản trị viên (Admin)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="fw-bold small">Trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="active">Hoạt động</option>
                                <option value="locked">Khóa</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top py-3">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">TẠO TÀI KHOẢN</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>