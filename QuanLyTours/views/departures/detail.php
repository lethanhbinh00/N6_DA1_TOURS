<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary"><i class="fas fa-route me-2"></i>Chi tiết Lịch khởi hành</h4>
        <a href="index.php?action=departure-list" class="btn btn-outline-secondary btn-sm">Quay lại</a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <?php $tourStart = $departure['start_date'] ?? '';
                    $tourId = $departure['tour_id'] ?? ''; ?>
                    <h5 class="fw-bold">
                        <?= htmlspecialchars($departure['id']) ?> —
                        <a href="index.php?action=booking-list&tour_id=<?= urlencode($tourId) ?>&date_from=<?= urlencode($tourStart) ?>&date_to=<?= urlencode($tourStart) ?>">Tour #<?= htmlspecialchars($tourId) ?></a>
                    </h5>
                    <div class="small text-muted">Ngày khởi hành: <?= htmlspecialchars($departure['start_date'] ?? '') ?></div>
                    <div class="mt-3">
                        <h6 class="fw-bold">Lịch trình (Itinerary)</h6>
                        <?php if (!empty($itinerary)): ?>
                            <ul class="list-group">
                                <?php foreach ($itinerary as $it): ?>
                                    <li class="list-group-item">
                                        <strong>Ngày <?= $it['day_number'] ?>:</strong>
                                        <div><?= htmlspecialchars($it['title'] ?? 'Không tiêu đề') ?></div>
                                        <div class="small text-muted"><?= nl2br(htmlspecialchars($it['description'] ?? '')) ?></div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="text-muted">Chưa có lịch trình chi tiết.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <form method="POST" action="index.php?action=departure-report-store">
                <input type="hidden" name="departure_id" value="<?= htmlspecialchars($departure['id']) ?>">
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-light"><strong>Báo cáo HDV &amp; Diễn biến</strong></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Nội dung báo cáo</label>
                            <textarea name="content" class="form-control" rows="5" placeholder="Ghi lại diễn biến, sự cố, phản hồi..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ảnh (URL hoặc danh sách, tuỳ sau)</label>
                            <input type="text" name="photos" class="form-control" placeholder="link1.jpg, link2.jpg">
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-light"><strong>Danh sách khách tham gia</strong></div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã BK</th>
                                    <th>Khách</th>
                                    <th>SĐT</th>
                                    <th>Số người</th>
                                    <th>Ghi chú</th>
                                    <th>Trạng thái</th>
                                    <th>Điểm danh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($passengers)): ?>
                                    <?php foreach ($passengers as $p): ?>
                                        <?php $a = $attendanceRecords[$p['id']] ?? null;
                                        $sel = $a['status'] ?? '';
                                        $noteVal = $a['note'] ?? ''; ?>
                                        <tr>
                                            <td class="fw-bold text-primary"><?= htmlspecialchars($p['booking_code'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($p['customer_name'] ?? '') ?>
                                                <?php if (!empty($p['customer_id_card'])): ?><div class="small text-muted">ID: <?= htmlspecialchars($p['customer_id_card']) ?></div><?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($p['customer_phone'] ?? '') ?><br>
                                                <div class="small text-muted"><?= htmlspecialchars($p['customer_email'] ?? '') ?></div>
                                            </td>
                                            <td><?= ($p['adults'] + ($p['children'] ?? 0)) ?></td>
                                            <td><?= htmlspecialchars($p['note'] ?? '') ?><br><?php if (!empty($p['pickup_location'])): ?><div class="small text-muted">Đón: <?= htmlspecialchars($p['pickup_location']) ?></div><?php endif; ?></td>
                                            <td>
                                                <?php $st = $p['status'] ?? 'confirmed';
                                                $cls = ($st == 'completed') ? 'success' : (($st == 'deposited') ? 'warning text-dark' : (($st == 'cancelled') ? 'danger' : 'secondary'));
                                                ?>
                                                <span class="badge bg-<?= $cls ?> small"><?= ucfirst($st) ?></span>
                                                <?php if (!empty($p['total_price'])): ?><div class="small text-muted mt-1"><?= number_format($p['total_price']) ?> đ</div><?php endif; ?>
                                            </td>
                                            <td>
                                                <select name="attendance[<?= $p['id'] ?>][status]" class="form-select form-select-sm">
                                                    <option value="present" <?= ($sel == 'present') ? 'selected' : '' ?>>Có mặt</option>
                                                    <option value="late" <?= ($sel == 'late') ? 'selected' : '' ?>>Đến muộn</option>
                                                    <option value="absent" <?= ($sel == 'absent') ? 'selected' : '' ?>>Vắng</option>
                                                </select>
                                                <input type="text" name="attendance[<?= $p['id'] ?>][note]" value="<?= htmlspecialchars($noteVal) ?>" class="form-control form-control-sm mt-1" placeholder="Ghi chú">
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">Chưa có khách</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="text-end">
                    <button class="btn btn-primary px-4">Lưu Báo cáo & Điểm danh</button>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="fw-bold">Thông tin nhanh</h6>
                    <div>Seats: <?= htmlspecialchars($departure['seats'] ?? '') ?></div>
                    <div class="small text-muted">Tour ID: <a href="<?= $booking_link ?>"><?= htmlspecialchars($departure['tour_id']) ?></a></div>
                    <div class="mt-1"><a href="<?= $booking_link ?>" class="badge bg-primary text-white small">Xem đặt: <?= $booking_count ?></a></div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="fw-bold">Tình trạng điểm danh</h6>
                    <?php $as = $attendanceSummary ?? ['total' => 0, 'present' => 0, 'late' => 0, 'absent' => 0, 'complete' => false]; ?>
                    <div>Tổng khách: <strong><?= $as['total'] ?></strong></div>
                    <div class="text-success">Có mặt: <strong><?= $as['present'] ?></strong></div>
                    <div class="text-warning">Đến muộn: <strong><?= $as['late'] ?></strong></div>
                    <div class="text-danger">Vắng: <strong><?= $as['absent'] ?></strong></div>
                    <div class="mt-2">
                        <?php if ($as['total'] == 0): ?>
                            <span class="badge bg-secondary">Chưa có khách</span>
                        <?php elseif ($as['complete']): ?>
                            <span class="badge bg-success">Đã điểm danh đầy đủ</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">Chưa hoàn tất ( thiếu <?= max(0, $as['total'] - ($as['present'] + $as['late'])) ?> )</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>