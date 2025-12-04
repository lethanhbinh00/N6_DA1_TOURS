<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<<<<<<< HEAD
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="index.php?action=index" class="text-decoration-none text-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
            <h4 class="fw-bold mt-2">💰 Cấu hình giá: <?= htmlspecialchars($tour['name']) ?></h4>
=======

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="index.php?action=index" class="text-decoration-none text-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <h4 class="fw-bold mt-2">💰 Cấu hình giá: <?= isset($tour['name']) ? htmlspecialchars($tour['name']) : '' ?></h4>
>>>>>>> 3394725e0d7f352cac85079cf8b5d5b6f67a905a
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold">Thêm bảng giá mới</div>
                <div class="card-body">
                    <form action="index.php?action=tour-price-store" method="POST">
<<<<<<< HEAD
                        <input type="hidden" name="tour_id" value="<?= $tour['id'] ?>">
=======
                        <input type="hidden" name="tour_id" value="<?= isset($tour['id']) ? $tour['id'] : 0 ?>">
>>>>>>> 3394725e0d7f352cac85079cf8b5d5b6f67a905a
                        <div class="mb-3">
                            <label>Tên mùa/dịp (VD: Tết 2025)</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="row g-2 mb-3">
<<<<<<< HEAD
                            <div class="col-6"><label>Từ ngày</label><input type="date" name="start_date" class="form-control" required></div>
                            <div class="col-6"><label>Đến ngày</label><input type="date" name="end_date" class="form-control" required></div>
                        </div>
                        <div class="mb-3"><label>Giá người lớn</label><input type="number" name="price_adult" class="form-control" required></div>
                        <div class="mb-3"><label>Giá trẻ em</label><input type="number" name="price_child" class="form-control" required></div>
=======
                            <div class="col-6">
                                <label>Từ ngày</label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label>Đến ngày</label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Giá người lớn</label>
                            <input type="number" name="price_adult" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Giá trẻ em</label>
                            <input type="number" name="price_child" class="form-control" required>
                        </div>
>>>>>>> 3394725e0d7f352cac85079cf8b5d5b6f67a905a
                        <button type="submit" class="btn btn-success w-100">Lưu bảng giá</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
<<<<<<< HEAD
                            <tr><th>Tên đợt</th><th>Thời gian áp dụng</th><th>Giá Lớn/Nhỏ</th><th>Thao tác</th></tr>
=======
                            <tr>
                                <th>Tên đợt</th>
                                <th>Thời gian áp dụng</th>
                                <th>Giá Lớn/Nhỏ</th>
                                <th>Thao tác</th>
                            </tr>
>>>>>>> 3394725e0d7f352cac85079cf8b5d5b6f67a905a
                        </thead>
                        <tbody>
                            <tr class="table-primary">
                                <td class="fw-bold">Giá Mặc Định</td>
                                <td>Luôn áp dụng (nếu không vào mùa)</td>
                                <td>
<<<<<<< HEAD
                                    <div class="text-success fw-bold"><?= number_format($tour['price_adult']) ?> ₫</div>
                                    <div class="small text-primary"><?= number_format($tour['price_child']) ?> ₫</div>
                                </td>
                                <td><span class="badge bg-secondary">Mặc định</span></td>
                            </tr>
                            <?php foreach($prices as $p): ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars($p['name']) ?></td>
                                <td><?= date('d/m', strtotime($p['start_date'])) ?> - <?= date('d/m/Y', strtotime($p['end_date'])) ?></td>
                                <td>
                                    <div class="text-success fw-bold"><?= number_format($p['price_adult']) ?> ₫</div>
                                    <div class="small text-primary"><?= number_format($p['price_child']) ?> ₫</div>
                                </td>
                                <td>
                                    <a href="index.php?action=tour-price-delete&id=<?= $p['id'] ?>&tour_id=<?= $tour['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
=======
                                    <div class="text-success fw-bold"><?= isset($tour['price_adult']) ? number_format($tour['price_adult']) : 0 ?> ₫</div>
                                    <div class="small text-primary"><?= isset($tour['price_child']) ? number_format($tour['price_child']) : 0 ?> ₫</div>
                                </td>
                                <td><span class="badge bg-secondary">Mặc định</span></td>
                            </tr>

                            <?php if(!empty($prices)): ?>
                                <?php foreach($prices as $p): ?>
                                    <tr>
                                        <td class="fw-bold"><?= htmlspecialchars($p['name']) ?></td>
                                        <td><?= date('d/m', strtotime($p['start_date'])) ?> - <?= date('d/m/Y', strtotime($p['end_date'])) ?></td>
                                        <td>
                                            <div class="text-success fw-bold"><?= number_format($p['price_adult']) ?> ₫</div>
                                            <div class="small text-primary"><?= number_format($p['price_child']) ?> ₫</div>
                                        </td>
                                        <td>
                                            <a href="index.php?action=tour-price-delete&id=<?= $p['id'] ?>&tour_id=<?= $tour['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>

>>>>>>> 3394725e0d7f352cac85079cf8b5d5b6f67a905a
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<<<<<<< HEAD
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
=======

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
>>>>>>> 3394725e0d7f352cac85079cf8b5d5b6f67a905a
