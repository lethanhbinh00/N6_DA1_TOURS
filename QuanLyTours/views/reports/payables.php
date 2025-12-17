<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php
$totalCostVal = $r['total_cost'] ?? 0;
    $paidSupplierVal = $r['total_paid_supplier'] ?? 0;

    // Logic kiểm tra trạng thái
    if ($totalCostVal == 0) {
        $statusBadge = '<span class="badge bg-secondary">Không có dịch vụ</span>';
    } elseif ($paidSupplierVal >= $totalCostVal) {
        $statusBadge = '<span class="badge bg-success">Đã thanh toán</span>';
    } else {
        $statusBadge = '<span class="badge bg-danger">Chưa thanh toán</span>';
    }
?>

<style>
.report-table th {
    font-size: 0.9rem;
}

.report-table td {
    font-size: 0.95rem;
}

.report-summary-box {
    padding: 15px;
    border-left: 5px solid #dc3545;
    background-color: #f8d7da;
}
</style>

<div class="container-fluid p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-danger"><i class="fas fa-file-invoice-dollar me-2"></i>Báo cáo Công nợ Phải trả
            (Payables)</h4>
    </div>

    <?php 
        // Trong View này, ta cần tính tổng các chi phí CHƯA THANH TOÁN
        // Giả định Controller đã fetch các services cần thanh toán vào biến $unpaidServices
        // Nếu Controller chưa fetch được, chúng ta sẽ phải dùng tạm dữ liệu có sẵn.
        
        // Vì Controller hiện tại đang dùng getFinancialSummary(), ta sẽ dùng tạm cấu trúc đó:
        $unpaidServices = $reports ?? []; // Lấy dữ liệu từ $reports

        // Tính tổng chi phí chưa thanh toán (tổng hợp từ bảng booking_services)
        $totalPayable = 0;
        foreach($unpaidServices as $r) {
            // Đây là bước giả định, vì $reports chỉ có tổng COST, không phải UNPAID COST
            // Cần logic Controller/Model chi tiết hơn.
            $totalPayable += $r['total_cost']; 
        }

    ?>
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="report-summary-box shadow-sm">
                <div class="small fw-bold text-danger">TỔNG CÔNG NỢ DỰ KIẾN</div>
                <h4 class="fw-bold text-danger"><?= number_format($totalPayable) ?> ₫</h4>
            </div>
        </div>
        <div class="col-md-8">
            <p class="small text-muted mt-2">
                * Lưu ý: Để hiển thị chính xác Công nợ, hệ thống cần được truy vấn trực tiếp các dịch vụ (Service Cost)
                chưa có trạng thái "Đã thanh toán" cho NCC.
                Bảng dưới đây hiển thị **TỔNG CHI PHÍ** của mỗi Booking, và cần được tinh chỉnh sau này.
            </p>
        </div>
    </div>


    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-striped table-hover align-middle mb-0 report-table">
                <thead class="bg-light text-secondary text-center">
                    <tr>
                        <th class="ps-3 text-start">Mã Booking & Tour</th>
                        <th>Ngày khởi hành</th>
                        <th class="text-end">Chi phí Dịch vụ (Công nợ)</th>
                        <th>Trạng thái</th>
                        <th>HDV Phụ trách</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reports)): foreach ($reports as $r): ?>
                    <?php 
        // Logic điều chỉnh Trạng thái
        // Nếu tổng chi phí > 0 và chưa có ghi nhận thanh toán đầy đủ trong service
        $isPaid = ($r['total_cost'] > 0 && $r['total_cost'] <= $r['total_paid_supplier']); // Giả định có trường này từ SQL
        
        $statusBadge = '<span class="badge bg-danger">Chưa thanh toán</span>';
        if ($r['total_cost'] == 0) {
            $statusBadge = '<span class="badge bg-secondary">Không có dịch vụ</span>';
        } elseif ($isPaid) {
            $statusBadge = '<span class="badge bg-success">Đã thanh toán</span>';
        }
    ?>
                    <tr>
                        <td class="ps-3 text-start">
                            <a href="index.php?action=booking-detail&id=<?= $r['booking_id'] ?>"
                                class="fw-bold text-primary">
                                <?= htmlspecialchars($r['booking_code']) ?>
                            </a>
                            <div class="small text-muted"><?= htmlspecialchars($r['tour_name'] ?? 'N/A') ?></div>
                        </td>
                        <td class="text-center"><?= date('d/m/Y', strtotime($r['travel_date'])) ?></td>
                        <td class="text-end fw-bold text-danger"><?= number_format($r['total_cost']) ?> ₫</td>

                        <td class="text-center">
                            <?= $statusBadge ?>
                        </td>

                        <td class="text-center">
                            <?php if (!empty($r['guide_name'])): ?>
                            <span class="fw-bold text-dark"><i
                                    class="fas fa-user-check me-1"></i><?= htmlspecialchars($r['guide_name']) ?></span>
                            <?php else: ?>
                            <span class="text-muted small"><i>Chưa phân công</i></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Không tìm thấy dữ liệu công nợ.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>