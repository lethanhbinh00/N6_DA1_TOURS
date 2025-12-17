<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-secondary"><i class="fas fa-route me-2 text-primary"></i>Danh sách Lịch khởi hành</h4>
        <a href="index.php?action=departure-create" class="btn btn-primary shadow-sm"><i class="fas fa-plus me-2"></i>Thêm Lịch</a>
    </div>

    <div class="filter-bar mb-3">
        <form method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="action" value="departure-list">
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Từ khóa</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="keyword" class="form-control border-start-0" placeholder="Tìm theo tên tour" value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Tình trạng</label>
                <select name="status" class="form-select">
                    <option value="">-- Tất cả --</option>
                    <option value="upcoming" <?= (($_GET['status'] ?? '') == 'upcoming') ? 'selected' : '' ?>>Sắp khởi hành</option>
                    <option value="running" <?= (($_GET['status'] ?? '') == 'running') ? 'selected' : '' ?>>Đang chạy</option>
                    <option value="completed" <?= (($_GET['status'] ?? '') == 'completed') ? 'selected' : '' ?>>Hoàn thành</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i>Lọc</button>
            </div>
            <div class="col-md-2">
                <a href="index.php?action=departure-list" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary text-center">
                    <tr>
                        <th style="width:50px;">#</th>
                        <th class="text-start">Tour</th>
                        <th>Ngày khởi hành</th>
                        <th>Số ghế</th>
                        <th>Ngày tạo</th>
                        <th style="width:160px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($departures)): ?>
                        <?php foreach ($departures as $k => $row): ?>
                            <tr>
                                <td class="text-center"><?= $k + 1 ?></td>
                                <td class="text-start fw-bold"><?= htmlspecialchars($row['tour_name'] ?? 'N/A') ?></td>
                                <td class="text-center"><?= htmlspecialchars($row['start_date']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($row['seats']) ?></td>
                                <td class="text-center small text-muted"><?= htmlspecialchars($row['created_at']) ?></td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="index.php?action=departure-detail&id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Chi tiết</a>
                                        <a href="index.php?action=departure-edit&id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                                        <a href="index.php?action=departure-delete&id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa lịch trình này?')">Xóa</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Chưa có lịch khởi hành.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>