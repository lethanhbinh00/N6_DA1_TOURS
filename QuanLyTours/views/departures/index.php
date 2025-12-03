<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid py-4">

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Thao tác thành công!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-secondary"><i class="fas fa-calendar-alt me-2"></i>Quản lý khởi hành</h4>
        <a href="index.php?action=departure-create" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Thêm khởi hành
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-3">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
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
                                    <a href="index.php?action=departure-edit&id=<?= $d['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?action=departure-delete&id=<?= $d['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa khởi hành?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">Chưa có dữ liệu.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>