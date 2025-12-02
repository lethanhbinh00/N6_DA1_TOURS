<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="index.php?action=customer-list" class="text-decoration-none text-secondary mb-1 d-block">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
            <h4 class="fw-bold text-primary m-0">
                <i class="fas fa-id-badge me-2"></i>Hồ sơ khách hàng
            </h4>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 fw-bold text-uppercase text-secondary small">
                    Thông tin cá nhân
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-2" style="width: 80px; height: 80px;">
                            <i class="fas fa-user fa-3x text-secondary opacity-50"></i>
                        </div>
                        <h5 class="fw-bold mb-0"><?= htmlspecialchars($customer['full_name']) ?></h5>
                        <span class="badge bg-info bg-opacity-10 text-dark border border-info mt-2">
                            <?= htmlspecialchars($customer['source']) ?>
                        </span>
                    </div>
                    
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">SĐT:</span>
                            <span class="fw-bold"><?= htmlspecialchars($customer['phone']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Email:</span>
                            <span class="fw-bold"><?= htmlspecialchars($customer['email']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">CCCD:</span>
                            <span class="fw-bold"><?= htmlspecialchars($customer['id_card'] ?? '--') ?></span>
                        </li>
                        <li class="list-group-item px-0">
                            <span class="text-muted d-block mb-1">Địa chỉ:</span>
                            <span class="fw-bold"><?= htmlspecialchars($customer['address']) ?></span>
                        </li>
                         <li class="list-group-item px-0">
                            <span class="text-muted d-block mb-1">Ghi chú:</span>
                            <span class="fst-italic text-secondary"><?= htmlspecialchars($customer['notes']) ?></span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="card shadow-sm border-0 bg-primary text-white">
                <div class="card-body text-center">
                    <h6 class="text-uppercase small opacity-75">Tổng số lần đi tour</h6>
                    <h2 class="display-4 fw-bold mb-0"><?= count($history) ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 fw-bold text-uppercase text-secondary small">
                    <i class="fas fa-history me-2"></i>Lịch sử đặt tour (Booking)
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-secondary small">
                            <tr>
                                <th class="ps-4">Mã BK</th>
                                <th>Tên Tour</th>
                                <th>Ngày đi</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($history)): ?>
                                <?php foreach($history as $h): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-primary">
                                        <?= $h['booking_code'] ?>
                                    </td>
                                    <td>
                                        <span class="d-inline-block text-truncate" style="max-width: 200px;">
                                            <?= htmlspecialchars($h['tour_name']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($h['travel_date'])) ?></td>
                                    <td class="fw-bold text-success">
                                        <?= number_format($h['total_price']) ?> ₫
                                    </td>
                                    <td>
                                        <?php 
                                            $st = $h['status'];
                                            if($st=='new') echo '<span class="badge bg-secondary">Mới</span>';
                                            elseif($st=='confirmed') echo '<span class="badge bg-primary">Đã xác nhận</span>';
                                            elseif($st=='deposited') echo '<span class="badge bg-warning text-dark">Đã cọc</span>';
                                            elseif($st=='completed') echo '<span class="badge bg-success">Hoàn tất</span>';
                                            elseif($st=='cancelled') echo '<span class="badge bg-danger">Hủy</span>';
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-calendar-times fa-3x mb-3 opacity-50"></i><br>
                                        Khách hàng này chưa đặt tour nào.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>