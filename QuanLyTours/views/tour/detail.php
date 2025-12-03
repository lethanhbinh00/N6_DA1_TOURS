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
                                <div class="text-center py-5 text-muted">Chưa có thông tin chính sách.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>