<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa đơn #<?= $booking['booking_code'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f9f9f9; font-family: 'Times New Roman', serif; }
        .invoice-box {
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            border: 1px solid #eee;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }
        .header-title { font-size: 24px; font-weight: bold; color: #333; text-transform: uppercase; }
        .company-info { font-size: 14px; color: #555; }
        /* Chỉ định khi in: Ẩn nút in, nền trắng */
        @media print {
            @page { margin: 0; }
            body { margin: 1.6cm; }
            .no-print { display: none; }
            .invoice-box { box-shadow: none; border: 0; }
        }
    </style>
</head>
<body>

<div class="text-center mb-3 no-print">
    <button onclick="window.print()" class="btn btn-primary btn-lg px-5">🖨️ IN HÓA ĐƠN</button>
    <button onclick="window.close()" class="btn btn-secondary">Đóng</button>
</div>

<div class="invoice-box">
    <div class="row mb-5">
        <div class="col-8">
            <h2 class="text-primary fw-bold mb-1">FOURCHICKENS TRAVEL</h2>
            <div class="company-info">
                Địa chỉ: Số 1 Đại Cồ Việt, Hà Nội<br>
                Hotline: 1900 1234<br>
                Email: support@fourchickens.com
            </div>
        </div>
        <div class="col-4 text-end">
            <h4 class="header-title">PHIẾU XÁC NHẬN</h4>
            <div class="text-muted">Mã đơn: <strong><?= $booking['booking_code'] ?></strong></div>
            <div class="text-muted">Ngày tạo: <?= date('d/m/Y', strtotime($booking['created_at'])) ?></div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-light">
                <div class="card-body">
                    <h5 class="card-title border-bottom pb-2 mb-3">Thông tin khách hàng</h5>
                    <div class="row">
                        <div class="col-6 mb-2"><strong>Họ tên:</strong> <?= $booking['customer_name'] ?></div>
                        <div class="col-6 mb-2"><strong>Điện thoại:</strong> <?= $booking['customer_phone'] ?></div>
                        <div class="col-6 mb-2"><strong>CCCD/CMND:</strong> <?= $booking['customer_id_card'] ?? '--' ?></div>
                        <div class="col-6 mb-2"><strong>Email:</strong> <?= $booking['customer_email'] ?? '--' ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h5 class="fw-bold mb-3">Chi tiết dịch vụ</h5>
    <table class="table table-bordered mb-4">
        <thead class="table-dark">
            <tr>
                <th>Nội dung</th>
                <th class="text-center">Số lượng</th>
                <th class="text-end">Đơn giá</th>
                <th class="text-end">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>Tour: <?= $tour['name'] ?></strong><br>
                    <small>Mã: <?= $tour['code'] ?> | Khởi hành: <?= date('d/m/Y', strtotime($booking['travel_date'])) ?></small>
                </td>
                <td class="text-center">
                    <?= $booking['adults'] ?> Lớn<br>
                    <?= $booking['children'] ?> Trẻ
                </td>
                <td class="text-end">
                    <?= number_format($tour['price_adult']) ?><br>
                    <?= number_format($tour['price_child']) ?>
                </td>
                <td class="text-end fw-bold">
                    <?= number_format($booking['total_price']) ?> ₫
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-end">Tổng giá trị:</td>
                <td class="text-end fw-bold"><?= number_format($booking['total_price']) ?> ₫</td>
            </tr>
            <tr>
                <td colspan="3" class="text-end text-success">Đã thanh toán:</td>
                <td class="text-end text-success fw-bold"><?= number_format($booking['deposit_amount']) ?> ₫</td>
            </tr>
            <tr>
                <td colspan="3" class="text-end text-danger">Còn lại (Phải thu):</td>
                <td class="text-end text-danger fw-bold fs-5"><?= number_format($booking['total_price'] - $booking['deposit_amount']) ?> ₫</td>
            </tr>
        </tfoot>
    </table>

    <div class="row mt-5">
        <div class="col-12 mb-5">
            <strong>Ghi chú:</strong> <?= $booking['note'] ?? 'Không có' ?><br>
            <i>(Vui lòng kiểm tra kỹ thông tin trước khi rời quầy. Xin cảm ơn!)</i>
        </div>
        
        <div class="col-6 text-center">
            <strong>Khách hàng</strong><br>
            <small>(Ký, họ tên)</small>
        </div>
        <div class="col-6 text-center">
            <strong>Người lập phiếu</strong><br>
            <small>(Ký, đóng dấu)</small>
            <br><br><br><br>
            <?= $_SESSION['user_name'] ?? 'Admin' ?>
        </div>
    </div>
</div>

</body>
</html>