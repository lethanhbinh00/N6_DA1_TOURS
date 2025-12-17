<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
    .report-table th { font-size: 0.9rem; }
    .report-table td { font-size: 0.95rem; }
    .report-summary-box { padding: 15px; border-left: 5px solid; transition: transform 0.2s; }
    .report-summary-box:hover { transform: translateY(-5px); }
</style>

<div class="container-fluid p-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-success"><i class="fas fa-chart-bar me-2"></i>Báo cáo Lãi/Lỗ (Profitability Report)</h4>
        <button class="btn btn-sm btn-outline-primary" onclick="window.print()">
            <i class="fas fa-print me-1"></i> Xuất báo cáo (PDF)
        </button>
    </div>

    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <form action="index.php" method="GET" class="row g-3 align-items-end">
                <input type="hidden" name="action" value="reports-profitability">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Từ ngày khởi hành</label>
                    <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($_GET['date_from'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Đến ngày khởi hành</label>
                    <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($_GET['date_to'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Lọc báo cáo</button>
                </div>
                <div class="col-md-2">
                    <a href="index.php?action=reports-profitability" class="btn btn-outline-secondary w-100"><i class="fas fa-sync-alt"></i> Đặt lại</a>
                </div>
            </form>
        </div>
    </div>
    
    <?php 
        // Tính toán tổng số liệu an toàn với ?? 0
        $totalRevenue = array_sum(array_map(fn($item) => $item['total_revenue'] ?? 0, $reports));
        $totalCost = array_sum(array_map(fn($item) => $item['total_cost'] ?? 0, $reports));
        $totalProfit = $totalRevenue - $totalCost;
        $profitColor = $totalProfit >= 0 ? 'text-success' : 'text-danger';
    ?>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="report-summary-box bg-white shadow-sm border-success">
                <div class="small fw-bold text-success text-uppercase">TỔNG DOANH THU (Dự kiến)</div>
                <h4 class="fw-bold mt-2"><?= number_format($totalRevenue) ?> ₫</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="report-summary-box bg-white shadow-sm border-danger">
                <div class="small fw-bold text-danger text-uppercase">TỔNG CHI PHÍ ĐẦU VÀO</div>
                <h4 class="fw-bold mt-2"><?= number_format($totalCost) ?> ₫</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="report-summary-box bg-white shadow-sm border-primary">
                <div class="small fw-bold text-primary text-uppercase">TỔNG LỢI NHUẬN GỘP</div>
                <h4 class="fw-bold mt-2 <?= $profitColor ?>"><?= number_format($totalProfit) ?> ₫</h4>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0 report-table">
                    <thead class="bg-light text-secondary text-center">
                        <tr>
                            <th class="ps-3 text-start">Mã Booking & Tour</th>
                            <th>Ngày khởi hành</th>
                            <th class="text-end">Doanh thu (A)</th>
                            <th class="text-end">Chi phí (B)</th>
                            <th class="text-end">Lợi nhuận (A-B)</th>
                            <th class="text-end">Đã thu từ khách</th>
                            <th class="text-center">% Thu hồi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($reports)): foreach ($reports as $r): ?>
                        <?php 
                            // Xử lý dữ liệu an toàn để tránh lỗi Warning
                            $revenue = (float)($r['total_revenue'] ?? 0);
                            $cost    = (float)($r['total_cost'] ?? 0);
                            $paid    = (float)($r['total_paid'] ?? 0);
                            $profit  = $revenue - $cost;
                            
                            $profitColorClass = $profit >= 0 ? 'text-success' : 'text-danger';
                            $percentPaid = $revenue > 0 ? round(($paid / $revenue) * 100) : 0;
                        ?>
                        <tr>
                            <td class="ps-3 text-start">
                                <a href="index.php?action=booking-detail&id=<?= $r['booking_id'] ?>" class="fw-bold text-primary text-decoration-none">
                                    <?= htmlspecialchars($r['booking_code'] ?? '---') ?>
                                </a>
                                <div class="small text-muted text-truncate" style="max-width: 250px;">
                                    <?= htmlspecialchars($r['tour_name'] ?? 'Tour không xác định') ?>
                                </div>
                            </td>
                            <td class="text-center"><?= date('d/m/Y', strtotime($r['travel_date'] ?? 'now')) ?></td>
                            <td class="text-end fw-bold text-success"><?= number_format($revenue) ?> ₫</td>
                            <td class="text-end fw-bold text-danger"><?= number_format($cost) ?> ₫</td>
                            <td class="text-end fw-bold <?= $profitColorClass ?>"><?= number_format($profit) ?> ₫</td>
                            <td class="text-end"><?= number_format($paid) ?> ₫</td>
                            <td class="text-center">
                                <div class="progress" style="height: 10px; width: 80px; margin: 0 auto;">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: <?= $percentPaid ?>%"></div>
                                </div>
                                <small class="fw-bold mt-1 d-block"><?= $percentPaid ?>%</small>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="7" class="text-center py-5 text-muted">Không tìm thấy dữ liệu Booking trong giai đoạn này.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>