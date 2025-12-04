<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="index.php?action=user-list" class="text-decoration-none text-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
            <h4 class="fw-bold text-primary mt-2">Thông tin nhân sự: <?= htmlspecialchars($user['full_name']) ?></h4>
        </div>
        <a href="index.php?action=user-edit&id=<?= $user['id'] ?>" class="btn btn-primary btn-sm"><i class="fas fa-edit me-2"></i>Chỉnh sửa</a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0 text-center p-4">
                <div class="mb-3">
                    <?php 
                        $ava = (!empty($user['avatar']) && file_exists('public/uploads/imguser/'.$user['avatar'])) 
                            ? 'public/uploads/imguser/'.$user['avatar'] 
                            : 'https://ui-avatars.com/api/?name='.urlencode($user['full_name']).'&size=150'; 
                    ?>
                    <img src="<?= $ava ?>" class="rounded-circle border p-1" width="120" height="120" style="object-fit:cover">
                </div>
                
                <h3 class="fw-bold"><?= htmlspecialchars($user['full_name']) ?></h3>
                <p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>
                
                <div class="d-flex justify-content-center gap-2 mb-4">
                    <span class="badge bg-primary"><?= $user['role'] ?></span>
                    <span class="badge <?= ($user['status']=='active')?'bg-success':'bg-secondary' ?>">
                        <?= ($user['status']=='active')?'Hoạt động':'Đã khóa' ?>
                    </span>
                </div>

                <div class="list-group list-group-flush text-start">
                    <div class="list-group-item d-flex justify-content-between py-3">
                        <span class="text-muted fw-bold">Mã nhân viên (ID):</span>
                        <span>#<?= $user['id'] ?></span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between py-3">
                        <span class="text-muted fw-bold">Số điện thoại:</span>
                        <span><?= htmlspecialchars($user['phone'] ?? 'Chưa cập nhật') ?></span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between py-3">
                        <span class="text-muted fw-bold">Ngày tạo:</span>
                        <span><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>