<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Hồ sơ Khách hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-4">
        <div class="d-flex justify-content-between mb-3">
            <h4>Hồ sơ: <?= htmlspecialchars($customer['full_name']) ?></h4>
            <a href="index.php?action=customer-list" class="btn btn-secondary">Quay lại</a>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-primary fw-bold">Thông tin cá nhân</h6>
                        <hr>
                        <p><strong>SĐT:</strong> <?= $customer['phone'] ?></p>
                        <p><strong>Email:</strong> <?= $customer['email'] ?></p>
                        <p><strong>Địa chỉ:</strong> <?= $customer['address'] ?></p>
                        <p><strong>Nguồn:</strong> <?= $customer['source'] ?></p>
                        <p><strong>Ghi chú:</strong> <?= $customer['notes'] ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-primary fw-bold">Lịch sử Đặt Tour (Booking)</h6>
                        <hr>
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Ngày đi</th>
                                    <th>Tên Tour</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($history)): ?>
                                <?php foreach($history as $h): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($h['travel_date'])) ?></td>
                                    <td><?= htmlspecialchars($h['tour_name']) ?></td>
                                    <td class="fw-bold text-success"><?= number_format($h['total_price']) ?> ₫</td>
                                    <td>
                                        <span class="badge bg-secondary"><?= $h['status'] ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">Chưa có lịch sử giao dịch.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>