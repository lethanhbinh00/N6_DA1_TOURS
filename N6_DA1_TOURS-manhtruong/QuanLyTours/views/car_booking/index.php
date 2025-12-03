<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid p-4">

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success">Thao tác thành công!</div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Đặt dịch vụ (Xe/KS)</h4>
        <a href="index.php?action=carbooking-create" class="btn btn-primary">Tạo đặt dịch vụ</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
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
                                    <a href="index.php?action=carbooking-edit&id=<?= $b['id'] ?>" class="btn btn-sm btn-primary">Sửa</a>
                                    <a href="index.php?action=carbooking-delete&id=<?= $b['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa đặt dịch vụ?');">Xóa</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">Chưa có dữ liệu.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>