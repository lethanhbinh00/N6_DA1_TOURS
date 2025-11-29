<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-secondary">📦 Quản lý Booking</h4>
    <div>
        <a href="index.php?action=index" class="btn btn-outline-secondary me-2">
            <i class="fas fa-suitcase me-1"></i> Danh Sách Tour
        </a>
        <a href="index.php?action=booking-create" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tạo Booking Mới
        </a>
    </div>
</div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th>Mã BK</th>
                        <th>Khách hàng</th>
                        <th>Tour đăng ký</th>
                        <th>Ngày đi</th>
                        <th>Số lượng</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($bookings)): ?>
                        <?php foreach ($bookings as $bk): ?>
                        <tr>
                            <td class="fw-bold text-primary"><?= htmlspecialchars($bk['booking_code']) ?></td>
                            
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($bk['customer_name']) ?></div>
                                <div class="small text-muted"><?= htmlspecialchars($bk['customer_phone']) ?></div>
                            </td>

                            <td>
                                <span class="badge bg-light text-dark border">
                                    <?= htmlspecialchars($bk['tour_code']) ?>
                                </span><br>
                                <small class="text-truncate d-inline-block" style="max-width: 200px;">
                                    <?= htmlspecialchars($bk['tour_name']) ?>
                                </small>
                            </td>

                            <td><?= date('d/m/Y', strtotime($bk['travel_date'])) ?></td>

                            <td>
                                <i class="fas fa-user text-secondary"></i> <?= $bk['adults'] ?> Lớn
                                <?php if($bk['children'] > 0): ?>
                                    <br><i class="fas fa-child text-secondary"></i> <?= $bk['children'] ?> Trẻ
                                <?php endif; ?>
                            </td>

                            <td class="fw-bold text-success">
                                <?= number_format($bk['total_price']) ?> ₫
                            </td>

                            <td>
                                <?php 
                                    $status = $bk['status'];
                                    $color = 'bg-secondary';
                                    $label = 'Mới';
                                    
                                    if($status == 'confirmed') { $color = 'bg-primary'; $label = 'Đã xác nhận'; }
                                    if($status == 'completed') { $color = 'bg-success'; $label = 'Hoàn tất'; }
                                    if($status == 'cancelled') { $color = 'bg-danger'; $label = 'Đã hủy'; }
                                ?>
                                <span class="badge <?= $color ?>"><?= $label ?></span>
                            </td>

                            <td>
                                <?php if($bk['status'] == 'new'): ?>
                                    <a href="index.php?action=booking-status&id=<?= $bk['id'] ?>&status=confirmed" 
                                    class="btn btn-sm btn-success" 
                                    title="Xác nhận đơn">
                                    <i class="fas fa-check"></i>
                                    </a>
                                <?php endif; ?>

                                <?php if($bk['status'] != 'cancelled'): ?>
                                    <a href="index.php?action=booking-status&id=<?= $bk['id'] ?>&status=cancelled" 
                                    class="btn btn-sm btn-warning text-white" 
                                    title="Hủy đơn"
                                    onclick="return confirm('Bạn muốn hủy đơn hàng này?');">
                                    <i class="fas fa-times"></i>
                                    </a>
                                <?php endif; ?>

                                <?php if($bk['status'] == 'cancelled'): ?>
                                    <a href="index.php?action=booking-delete&id=<?= $bk['id'] ?>" 
                                    class="btn btn-sm btn-outline-danger" 
                                    title="Xóa vĩnh viễn"
                                    onclick="return confirm('Xóa vĩnh viễn đơn hàng này?');">
                                    <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                               </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center py-4">Chưa có booking nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>