<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý Khách hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-light">

    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-secondary">👥 Danh sách Khách hàng</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                <i class="fas fa-plus me-2"></i>Thêm Khách hàng
            </button>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>Họ tên</th>
                            <th>Liên hệ</th>
                            <th>Nguồn</th>
                            <th>Ghi chú</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($customers)): ?>
                        <?php foreach ($customers as $cus): ?>
                        <tr>
                            <td class="fw-bold text-primary">
                                <?= htmlspecialchars($cus['full_name']) ?>
                            </td>
                            <td>
                                <div><i class="fas fa-phone-alt text-secondary me-1"></i>
                                    <?= htmlspecialchars($cus['phone']) ?></div>
                                <div class="small text-muted"><i class="fas fa-envelope text-secondary me-1"></i>
                                    <?= htmlspecialchars($cus['email']) ?></div>
                            </td>
                            <td><span class="badge bg-info text-dark"><?= htmlspecialchars($cus['source']) ?></span>
                            </td>
                            <td class="text-muted small"><?= htmlspecialchars($cus['notes']) ?></td>
                            <td>
                                <a href="index.php?action=customer-detail&id=<?= $cus['id'] ?>"
                                    class="btn btn-sm btn-outline-info" title="Xem lịch sử">
                                    <i class="fas fa-history"></i>
                                </a>
                                <a href="index.php?action=customer-delete&id=<?= $cus['id'] ?>"
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Xóa khách hàng này?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">Chưa có khách hàng nào.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addCustomerModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="index.php?action=customer-store" method="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Thêm Khách hàng mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Họ tên <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Địa chỉ</label>
                            <input type="text" name="address" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Nguồn khách</label>
                            <select name="source" class="form-select">
                                <option value="Direct">Trực tiếp / Hotline</option>
                                <option value="Facebook">Facebook</option>
                                <option value="Website">Website</option>
                                <option value="Referral">Người quen giới thiệu</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Ghi chú</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Lưu thông tin</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>