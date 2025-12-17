<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
    .filter-bar {
        background: #fff;
        border-bottom: 1px solid #eee;
        padding: 12px 16px;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
        margin-bottom: 18px;
    }
</style>

<div class="container-fluid p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-secondary"><i class="fas fa-car me-2 text-primary"></i>Danh sách đặt dịch vụ xe</h4>
        <a href="index.php?action=car-booking-create" class="btn btn-primary shadow-sm"><i class="fas fa-plus me-2"></i>Đặt xe</a>
    </div>

    <div class="filter-bar">
        <form class="row g-3 align-items-end" method="GET">
            <input type="hidden" name="action" value="car-booking">
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Từ khóa</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="keyword" class="form-control border-start-0" placeholder="Tên KH / SĐT" value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                </div>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100"><i class="fas fa-search me-1"></i> Lọc</button>
            </div>
        </form>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary text-center">
                    <tr>
                        <th style="width:50px;">#</th>
                        <th class="text-start">Khách hàng</th>
                        <th class="text-start">Dịch vụ</th>
                        <th>Ngày</th>
                        <th>Số lượng</th>
                        <th style="width:140px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($carBookings)): ?>
                        <?php foreach ($carBookings as $k => $b): ?>
                            <tr>
                                <td class="text-center ps-3"><?= $k + 1 ?></td>
                                <td class="text-start">
                                    <div class="fw-bold"><?= htmlspecialchars($b['customer_name']) ?></div>
                                    <div class="small text-muted"><?= htmlspecialchars($b['phone']) ?></div>
                                </td>
                                <td class="text-start"><?= htmlspecialchars($b['service_name']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($b['date']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($b['quantity']) ?></td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="index.php?action=car-booking-edit&id=<?= $b['id'] ?>" class="btn btn-sm btn-warning" title="Sửa"><i class="fas fa-edit"></i></a>
                                        <a onclick="return confirm('Xóa đơn này?')" href="index.php?action=car-booking-delete&id=<?= $b['id'] ?>" class="btn btn-sm btn-danger" title="Xóa"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Chưa có đơn đặt xe nào</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>