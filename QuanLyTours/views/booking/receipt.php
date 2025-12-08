<?php 
// Đảm bảo rằng file này được gọi từ BookingController->receipt()
// và biến $payment đã chứa dữ liệu chi tiết giao dịch
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phiếu Thu - <?= htmlspecialchars($payment['booking_code'] ?? 'N/A') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; padding: 20px; font-size: 13px; }
        .receipt-box { max-width: 600px; margin: 0 auto; border: 1px solid #ccc; padding: 30px; }
        .header h4 { font-weight: bold; }
        .details strong { font-weight: 600; }
        @media print {
            body { background: none; }
            .receipt-box { border: none; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="receipt-box shadow">
    <div class="header text-center border-bottom pb-3 mb-4">
        <h4 class="text-primary">PHIẾU THU TIỀN CỌC/THANH TOÁN</h4>
        <div class="small text-muted">FourChickens Travel - Hotline: 1900 1234</div>
    </div>

    <div class="details mb-4">
        <p><strong>Mã Phiếu Thu:</strong> PT-<?= htmlspecialchars($payment['id'] ?? '') ?></p>
        <p><strong>Booking ID:</strong> <?= htmlspecialchars($payment['booking_code'] ?? '---') ?></p>
        <p><strong>Khách hàng:</strong> <?= htmlspecialchars($payment['customer_name'] ?? '---') ?></p>
        <p><strong>Điện thoại:</strong> <?= htmlspecialchars($payment['customer_phone'] ?? '---') ?></p>
    </div>

    <div class="amount-box text-center border p-4 bg-light mb-4">
        <p class="mb-1 text-muted">SỐ TIỀN ĐÃ THU:</p>
        <h2 class="text-success fw-bold"><?= number_format($payment['amount'] ?? 0) ?> VNĐ</h2>
    </div>

    <div class="info-table small">
        <table class="table table-sm table-borderless">
            <tr>
                <td style="width: 40%;" class="text-muted">Hình thức thanh toán:</td>
                <td><strong><?= htmlspecialchars($payment['payment_method'] ?? '---') ?></strong></td>
            </tr>
            <tr>
                <td class="text-muted">Ngày giao dịch:</td>
                <td><strong><?= date('H:i:s d/m/Y', strtotime($payment['payment_date'] ?? 'now')) ?></strong></td>
            </tr>
            <tr>
                <td class="text-muted">Nhân viên thu:</td>
                <td><strong><?= htmlspecialchars($payment['collector_name'] ?? 'Admin') ?></strong></td>
            </tr>
            <tr>
                <td class="text-muted">Ghi chú:</td>
                <td><?= nl2br(htmlspecialchars($payment['note'] ?? 'Không có')) ?></td>
            </tr>
        </table>
    </div>
    
    <div class="footer mt-5 d-flex justify-content-between">
        <div class="text-center" style="width: 45%;">
            <p class="fw-bold">Người nộp tiền</p>
            <p style="margin-top: 60px;">(Ký, ghi rõ họ tên)</p>
        </div>
        <div class="text-center" style="width: 45%;">
            <p class="fw-bold">Nhân viên thu</p>
            <p style="margin-top: 60px;">(Ký, ghi rõ họ tên)</p>
        </div>
    </div>
</div>

<div class="text-center mt-4 no-print">
    <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print me-2"></i> In Phiếu Thu</button>
    <button onclick="window.close()" class="btn btn-secondary">Đóng</button>
</div>

</body>
</html>