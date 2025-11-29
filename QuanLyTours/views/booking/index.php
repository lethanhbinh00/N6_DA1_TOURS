<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid p-4">

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="fas fa-check-circle me-2"></i>
            <?php 
                if($_GET['msg']=='booking_success') echo 'Tạo booking thành công!';
                elseif($_GET['msg']=='updated') echo 'Cập nhật thông tin thành công!';
                elseif($_GET['msg']=='status_updated') echo 'Cập nhật trạng thái thành công!';
                elseif($_GET['msg']=='deleted') echo 'Đã xóa đơn hàng vĩnh viễn!';
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-secondary">
            <i class="fas fa-file-invoice-dollar me-2"></i>Quản lý Booking
        </h4>
        
        <div>
            <a href="index.php?action=index" class="btn btn-outline-secondary me-2 shadow-sm">
                <i class="fas fa-suitcase me-1"></i> DS Tour
            </a>
            <a href="index.php?action=booking-create" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus me-2"></i>Tạo Booking Mới
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4">Mã BK</th>
                        <th>Khách hàng</th>
                        <th>Tour đăng ký</th>
                        <th>Ngày đi</th>
                        <th>Số lượng</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($bookings)): ?>
                        <?php foreach ($bookings as $bk): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-primary">
                                <?= htmlspecialchars($bk['booking_code'] ?? '') ?>
                            </td>
                            
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($bk['customer_name'] ?? 'Khách lẻ') ?></div>
                                
                                <?php if(!empty($bk['customer_id_card'])): ?>
                                    <span class="badge bg-light text-dark border my-1">
                                        <i class="fas fa-id-card me-1 text-secondary"></i> 
                                        <?= htmlspecialchars($bk['customer_id_card'] ?? '') ?>
                                    </span>
                                <?php endif; ?>
                                
                                <div class="small text-muted">
                                    <i class="fas fa-phone-alt me-1" style="font-size: 0.8rem;"></i> 
                                    <?= htmlspecialchars($bk['customer_phone'] ?? '') ?>
                                </div>
                            </td>

                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-dark border">
                                    <?= htmlspecialchars($bk['tour_code'] ?? 'N/A') ?>
                                </span>
                                <div class="small text-truncate fw-bold mt-1" style="max-width: 180px;" title="<?= htmlspecialchars($bk['tour_name'] ?? '') ?>">
                                    <?= htmlspecialchars($bk['tour_name'] ?? 'Tour đã bị xóa') ?>
                                </div>
                            </td>

                            <td>
                                <i class="far fa-calendar-alt text-muted me-1"></i>
                                <?= !empty($bk['travel_date']) ? date('d/m/Y', strtotime($bk['travel_date'])) : '--/--/----' ?>
                            </td>

                            <td>
                                <div><span class="fw-bold"><?= $bk['adults'] ?? 0 ?></span> Lớn</div>
                                <?php if(!empty($bk['children']) && $bk['children'] > 0): ?>
                                    <div class="small text-muted"><span class="fw-bold"><?= $bk['children'] ?></span> Trẻ</div>
                                <?php endif; ?>
                            </td>

                            <td class="fw-bold text-success">
                                <?= number_format($bk['total_price'] ?? 0) ?> ₫
                            </td>

                            <td>
                                <?php 
                                    $st = $bk['status'] ?? 'new';
                                    if($st == 'new') echo '<span class="badge bg-secondary">Mới</span>';
                                    elseif($st == 'confirmed') echo '<span class="badge bg-primary">Đã xác nhận</span>';
                                    elseif($st == 'completed') echo '<span class="badge bg-success">Hoàn tất</span>';
                                    elseif($st == 'cancelled') echo '<span class="badge bg-danger">Hủy</span>';
                                    else echo '<span class="badge bg-dark">Khác</span>';
                                ?>
                            </td>

                            <td class="text-end pe-4 text-nowrap">
                                <?php if($st != 'cancelled'): ?>
                                    <a href="index.php?action=booking-edit&id=<?= $bk['id'] ?>" 
                                       class="btn btn-sm btn-outline-primary me-1" title="Sửa thông tin">
                                       <i class="fas fa-edit"></i>
                                    </a>
                                <?php endif; ?>

                                <?php if($st == 'new'): ?>
                                    <a href="index.php?action=booking-status&id=<?= $bk['id'] ?>&status=confirmed" 
                                       class="btn btn-sm btn-success me-1" title="Xác nhận đơn">
                                       <i class="fas fa-check"></i>
                                    </a>
                                <?php endif; ?>

                                <?php if($st != 'cancelled'): ?>
                                    <a href="index.php?action=booking-status&id=<?= $bk['id'] ?>&status=cancelled" 
                                       class="btn btn-sm btn-warning text-white me-1" title="Hủy đơn hàng"
                                       onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này không?');">
                                       <i class="fas fa-times"></i>
                                    </a>
                                <?php endif; ?>

                                <?php if($st == 'cancelled'): ?>
                                    <a href="index.php?action=booking-delete&id=<?= $bk['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger" title="Xóa vĩnh viễn khỏi hệ thống"
                                       onclick="return confirm('CẢNH BÁO: Hành động này sẽ xóa vĩnh viễn đơn hàng. Bạn có chắc chắn?');">
                                       <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i><br>
                                Chưa có booking nào trong hệ thống.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>