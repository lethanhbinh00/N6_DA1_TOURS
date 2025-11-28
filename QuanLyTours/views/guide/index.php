<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Hướng dẫn viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-secondary">🚩 Đội ngũ Hướng dẫn viên</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGuideModal">
            <i class="fas fa-user-plus me-2"></i>Thêm HDV Mới
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th width="80">Ảnh</th>
                        <th>Họ tên & Thông tin</th>
                        <th>Chuyên môn</th>
                        <th>Kinh nghiệm</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($guides)): ?>
                        <?php foreach ($guides as $g): ?>
                        <tr>
                            <td>
                                <?php if($g['image']): ?>
                                    <img src="public/uploads/<?= $g['image'] ?>" class="rounded-circle border" width="50" height="50" style="object-fit:cover">
                                <?php else: ?>
                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width:50px; height:50px">
                                        <i class="fas fa-user"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-bold text-primary"><?= htmlspecialchars($g['full_name']) ?></div>
                                <div class="small text-muted">
                                    <i class="fas fa-id-card me-1"></i> Thẻ: <?= htmlspecialchars($g['license_number']) ?>
                                </div>
                                <div class="small text-muted">
                                    <i class="fas fa-phone me-1"></i> <?= htmlspecialchars($g['phone']) ?>
                                </div>
                            </td>
                            <td>
                                <?php 
                                    $langs = explode(',', $g['languages']);
                                    foreach($langs as $lang) {
                                        echo '<span class="badge bg-info text-dark me-1">'.trim($lang).'</span>';
                                    }
                                ?>
                            </td>
                            <td><?= $g['experience_years'] ?> năm</td>
                            <td>
                                <?php if($g['status']=='available'): ?>
                                    <span class="badge bg-success">Sẵn sàng</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">Đang bận</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="index.php?action=guide-delete&id=<?= $g['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa nhân sự này?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-4">Chưa có dữ liệu nhân sự.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addGuideModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="index.php?action=guide-store" method="POST" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Hồ sơ Hướng dẫn viên</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4 text-center">
                            <label class="form-label fw-bold">Ảnh chân dung</label>
                            <input type="file" name="image" class="form-control mb-2">
                            <div class="border p-3 bg-light rounded" style="height: 150px; display:flex; align-items:center; justify-content:center;">
                                <span class="text-muted">Preview Ảnh</span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label>Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" name="full_name" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label>Giới tính</label>
                                    <select name="gender" class="form-select">
                                        <option value="male">Nam</option>
                                        <option value="female">Nữ</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label>Ngày sinh</label>
                                    <input type="date" name="dob" class="form-control">
                                </div>
                                <div class="col-md-12">
                                    <label>Số thẻ HDV (License No.)</label>
                                    <input type="text" name="license_number" class="form-control" placeholder="VD: 12345/HDV-QT">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <hr>
                            <h6 class="text-primary">Kỹ năng & Kinh nghiệm</h6>
                        </div>
                        <div class="col-md-8">
                            <label>Ngôn ngữ thành thạo (Cách nhau dấu phẩy)</label>
                            <input type="text" name="languages" class="form-control" placeholder="VD: Tiếng Anh, Tiếng Trung, Tiếng Hàn">
                        </div>
                        <div class="col-md-4">
                            <label>Kinh nghiệm (Năm)</label>
                            <input type="number" name="experience_years" class="form-control" value="1">
                        </div>
                        <div class="col-md-12">
                            <label>Địa chỉ liên hệ</label>
                            <input type="text" name="address" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu Hồ Sơ</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>