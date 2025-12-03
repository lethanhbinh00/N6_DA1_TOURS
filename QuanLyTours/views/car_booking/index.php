<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid py-4">

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Thao tác thành công!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-secondary"><i class="fas fa-car me-2"></i>Đặt dịch vụ (Xe/KS)</h4>
        <a href="index.php?action=carbooking-create" class="btn btn-success">
            <i class="fas fa-plus-circle me-1"></i> Tạo đặt dịch vụ
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-3">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Dịch vụ</th>
                        <th>Khách</th>
                        <th>Ngày</th>
                        <th>Số lượng</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($bookings)): ?>
                        <?php foreach ($bookings as $b): ?>
                            <tr>
                                <td><?= htmlspecialchars($b['id']) ?></td>
                                <td><?= htmlspecialchars($b['service_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($b['customer_name']) ?></td>
                                <td><?= htmlspecialchars($b['date']) ?></td>
                                <td><?= htmlspecialchars($b['quantity']) ?></td>
                                <td>
                                    <a href="index.php?action=carbooking-edit&id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?action=carbooking-delete&id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa đặt dịch vụ?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">Chưa có dữ liệu.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>