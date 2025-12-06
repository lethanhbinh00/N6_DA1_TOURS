<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php?action=index">Quản lý Tour</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($tour['code']) ?></li>
            </ol>
        </nav>
        <a href="index.php?action=index" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-2"></i> Quay lại
        </a>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="position-relative">
                    <?php if(!empty($tour['image']) && file_exists('public/uploads/' . $tour['image'])): ?>
                        <img src="public/uploads/<?= $tour['image'] ?>" class="card-img-top" style="height: 250px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 250px;">
                            <i class="fas fa-image fa-3x text-secondary opacity-50"></i>
                        </div>
                    <?php endif; ?>
                    <span class="position-absolute top-0 end-0 m-3 badge bg-warning text-dark shadow-sm">
                        <?= ($tour['type']=='domestic') ? 'Trong nước' : 'Quốc tế' ?>
                    </span>
                </div>
                
                <div class="card-body">
                    <h4 class="fw-bold text-primary mb-1"><?= htmlspecialchars($tour['name']) ?></h4>
                    <p class="text-muted small mb-3"><i class="fas fa-barcode me-1"></i> <?= htmlspecialchars($tour['code']) ?></p>
                    
                    <div class="d-flex justify-content-between bg-light p-3 rounded mb-3">
                        <div class="text-center">
                            <small class="d-block text-muted">Người lớn</small>
                            <span class="fw-bold text-success fs-5"><?= number_format($tour['price_adult']) ?> ₫</span>
                        </div>
                        <div class="text-center border-start ps-3">
                            <small class="d-block text-muted">Trẻ em</small>
                            <span class="fw-bold text-primary fs-5"><?= number_format($tour['price_child']) ?> ₫</span>
                        </div>
                    </div>

                    <h6 class="fw-bold text-secondary"><i class="fas fa-star me-2"></i>Điểm nổi bật</h6>
                    <p class="text-muted small text-justify">
                        <?= nl2br(htmlspecialchars($tour['highlight'] ?? 'Chưa cập nhật mô tả.')) ?>
                    </p>
                </div>
            </div>

            <?php if(!empty($gallery)): ?>
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-bold">📸 Thư viện ảnh</div>
                <div class="card-body">
                    <div class="row g-2">
                        <?php foreach($gallery as $img): ?>
                            <div class="col-4">
                                <img src="public/uploads/<?= $img['image_path'] ?>" class="img-fluid rounded border" 
                                     style="height: 70px; width: 100%; object-fit: cover; cursor: pointer;"
                                     onclick="window.open(this.src, '_blank')">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white">
                    <ul class="nav nav-tabs card-header-tabs" id="detailTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active fw-bold" data-bs-toggle="tab" data-bs-target="#tab-schedule">
                                <i class="fas fa-map-marked-alt me-2"></i>Lịch trình chi tiết
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#tab-policy">
                                <i class="fas fa-shield-alt me-2"></i>Chính sách & Điều khoản
                            </button>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body tab-content">
                    <div class="tab-pane fade show active" id="tab-schedule">
                        <?php if (!empty($itineraries)): ?>
                            <div class="timeline">
                                <?php foreach ($itineraries as $day): ?>
                                    <div class="d-flex mb-4">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" 
                                                 style="width: 50px; height: 50px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                                N<?= $day['day_number'] ?>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="fw-bold text-uppercase text-primary mb-1">
                                                <?= htmlspecialchars($day['title']) ?>
                                            </h6>
                                            
                                            <div class="mb-2 d-flex flex-wrap gap-2">
                                                <?php if(!empty($day['spot'])): ?>
                                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">
                                                        <i class="fas fa-map-marker-alt me-1"></i> <?= $day['spot'] ?>
                                                    </span>
                                                <?php endif; ?>
                                                <?php if(!empty($day['accommodation'])): ?>
                                                    <span class="badge bg-info bg-opacity-10 text-info border border-info">
                                                        <i class="fas fa-bed me-1"></i> <?= $day['accommodation'] ?>
                                                    </span>
                                                <?php endif; ?>
                                                <?php if(!empty($day['meals'])): ?>
                                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">
                                                        <i class="fas fa-utensils me-1"></i> <?= $day['meals'] ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="bg-light p-3 rounded border border-light text-secondary" style="font-size: 0.95rem; line-height: 1.6;">
                                                <?= nl2br(htmlspecialchars($day['description'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5 text-muted">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" class="mb-3 opacity-50">
                                <p>Chưa cập nhật lịch trình.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="tab-pane fade" id="tab-policy">
                        <div class="p-3">
                            <?php if(!empty($tour['policy'])): ?>
                                <div class="alert alert-warning border-0 shadow-sm">
                                    <h6 class="alert-heading fw-bold"><i class="fas fa-exclamation-circle me-2"></i>Lưu ý quan trọng:</h6>
                                    <hr>
                                    <div style="white-space: pre-line;">
                                        <?= htmlspecialchars($tour['policy']) ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="text-start py-5 text-muted">
                                    <h3 class="text-center text-dark mb-4">CHÍNH SÁCH & ĐIỀU KHOẢN TOUR DU LỊCH</h3>
                                    <hr>
                                    
                                    <h4>I. ĐĂNG KÝ VÀ THANH TOÁN</h4>
                                    <ul class="list-unstyled">
                                        <li>
                                            <strong>1. Xác nhận Đăng ký:</strong> Việc đăng ký tour chỉ có hiệu lực khi Quý khách cung cấp đầy đủ thông tin cá nhân chính xác và thực hiện đặt cọc theo quy định.
                                        </li>
                                        <li>
                                            <strong>2. Giá Tour Bao Gồm:</strong> Chi tiết các dịch vụ đã bao gồm (vé máy bay, khách sạn, bữa ăn, phí tham quan, bảo hiểm, v.v.) được ghi rõ trong chương trình tour cụ thể.
                                        </li>
                                        <li>
                                            <strong>3. Đặt Cọc:</strong> Quý khách phải thanh toán đặt cọc **[X]%** tổng giá trị tour ngay khi đăng ký để giữ chỗ.
                                        </li>
                                        <li>
                                            <strong>4. Thanh toán Phần còn lại:</strong> Số tiền còn lại phải được thanh toán chậm nhất **[Y] ngày** trước ngày khởi hành. Nếu quá thời hạn này, tour sẽ tự động bị hủy và Quý khách mất tiền đặt cọc.
                                        </li>
                                    </ul>

                                    <h4>II. CHÍNH SÁCH HỦY TOUR VÀ HOÀN TIỀN</h4>
                                    <p>Chính sách này áp dụng cho việc hủy tour từ phía khách hàng (không bao gồm trường hợp bất khả kháng):</p>
                                    <table class="table table-bordered table-sm">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Thời gian Hủy (So với Ngày Khởi hành)</th>
                                                <th>Phí Hủy Tour (Trên Tổng Giá Trị Tour)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Trước 30 ngày</td>
                                                <td>Phí hành chính: 10%</td>
                                            </tr>
                                            <tr>
                                                <td>Từ 15 đến 29 ngày</td>
                                                <td>30%</td>
                                            </tr>
                                            <tr>
                                                <td>Từ 07 đến 14 ngày</td>
                                                <td>50%</td>
                                            </tr>
                                            <tr>
                                                <td>Trong vòng 07 ngày hoặc vắng mặt</td>
                                                <td>100%</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <p class="small text-danger"><em>* Lưu ý: Thời gian hủy được tính theo ngày làm việc (trừ Thứ 7, Chủ Nhật và ngày lễ).</em></p>

                                    <h4>III. TRÁCH NHIỆM VÀ QUYỀN HẠN</h4>
                                    <ul class="list-unstyled">
                                        <li>
                                            <strong>1. Trách nhiệm của Công ty:</strong> Cung cấp đầy đủ và đúng dịch vụ theo chương trình đã cam kết. Giải quyết các sự cố phát sinh trên cơ sở hợp tác, ưu tiên quyền lợi khách hàng.
                                        </li>
                                        <li>
                                            <strong>2. Trách nhiệm của Khách hàng:</strong> Đảm bảo hộ chiếu (còn hạn trên 6 tháng) và các giấy tờ tùy thân, thị thực (visa) hợp lệ. Tuân thủ pháp luật nước sở tại và sự hướng dẫn của trưởng đoàn.
                                        </li>
                                        <li>
                                            <strong>3. Trường hợp Bất khả kháng:</strong> Nếu tour bị hủy hoặc thay đổi do thiên tai, dịch bệnh, chiến tranh hoặc các yếu tố khách quan khác, hai bên sẽ thỏa thuận về việc hoàn tiền hoặc chuyển sang tour khác. Công ty được miễn trừ trách nhiệm bồi thường thiệt hại trong các trường hợp này.
                                        </li>
                                    </ul>

                                    <p class="mt-4"><em>Bằng việc đăng ký tour, Quý khách được xem là đã đọc, hiểu rõ và đồng ý với toàn bộ Chính sách & Điều khoản trên.</em></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>