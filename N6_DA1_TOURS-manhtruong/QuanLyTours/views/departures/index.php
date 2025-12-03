<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid p-4">

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success">Thao tác thành công!</div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Quản lý khởi hành</h4>
        <a href="index.php?action=departure-create" class="btn btn-primary">Thêm khởi hành</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tour</th>
                        <th>Ngày khởi hành</th>
                        <th>Số chỗ</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($departures)): ?>
                        <?php foreach ($departures as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars($d['id']) ?></td>
                                <td><?= htmlspecialchars($d['tour_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($d['start_date']) ?></td>
                                <td><?= htmlspecialchars($d['seats']) ?></td>
                                <td>
                                    <a href="index.php?action=departure-edit&id=<?= $d['id'] ?>" class="btn btn-sm btn-primary">Sửa</a>
                                    <a href="index.php?action=departure-delete&id=<?= $d['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa khởi hành?');">Xóa</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center">Chưa có dữ liệu.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>