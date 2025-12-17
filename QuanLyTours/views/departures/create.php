<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary"><i class="fas fa-bus me-2"></i> Thêm Lịch Khởi Hành</h4>
        <a href="index.php?action=departure-list" class="btn btn-outline-secondary btn-sm">Quay lại</a>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form method="POST" action="index.php?action=departure-store">

                        <h6 class="text-primary border-bottom pb-2 mb-3 fw-bold">1. Thông tin tour</h6>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Chọn Tour</label>
                                <select name="tour_id" class="form-select" required>
                                    <option value="">-- Chọn tour --</option>
                                    <?php if (!empty($tours)): ?>
                                        <?php foreach ($tours as $t): ?>
                                            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Hướng dẫn viên</label>
                                <select name="guide_id" class="form-select">
                                    <option value="">-- Chọn HDV --</option>
                                    <?php if (!empty($guides)): ?>
                                        <?php foreach ($guides as $g): ?>
                                            <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['full_name']) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Số ghế</label>
                                <input type="number" name="seats" class="form-control" min="1" value="20" required>
                            </div>
                        </div>

                        <h6 class="text-primary border-bottom pb-2 mb-3 fw-bold">2. Thời gian khởi hành</h6>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ngày khởi hành</label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ghi chú (tùy chọn)</label>
                                <input type="text" name="note" class="form-control" placeholder="Ví dụ: Điểm tập trung tại cổng A">
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="index.php?action=departure-list" class="btn btn-secondary me-2">Hủy</a>
                            <button class="btn btn-primary px-4 fw-bold"><i class="fas fa-save me-1"></i> Lưu Lịch</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>