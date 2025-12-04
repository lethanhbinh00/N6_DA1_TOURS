<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid p-4">
    
    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="fas fa-check-circle me-2"></i>
            <?php 
                if($_GET['msg']=='success') echo 'Thao tác thành công!';
                elseif($_GET['msg']=='updated') echo 'Cập nhật thành công!';
                elseif($_GET['msg']=='deleted') echo 'Đã xóa dữ liệu!';
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-secondary m-0">
            <i class="fas fa-box-open me-2"></i>Quản lý Tour
        </h4>
        <button class="btn btn-primary shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#tourModalFull">
            <i class="fas fa-plus me-2"></i>Thêm Tour Mới
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4" style="width: 100px;">Hình ảnh</th> 
                        <th>Mã Tour</th>
                        <th>Tên Tour</th>
                        <th>Loại</th>
<<<<<<< HEAD
                        <th>Giá bán</th>
=======
                        <th>Giá vé</th>
>>>>>>> 3394725e0d7f352cac85079cf8b5d5b6f67a905a
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($tours)): ?>
                        <?php foreach ($tours as $tour): ?>
                        <tr>
                            <td class="ps-4">
                                <?php $img = !empty($tour['image']) ? $tour['image'] : 'default.png'; ?>
                                <?php if(file_exists('public/uploads/' . $img) && $img != 'default.png'): ?>
                                    <img src="public/uploads/<?= $img ?>" class="rounded border shadow-sm" style="width: 60px; height: 40px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-secondary bg-opacity-10 rounded d-flex align-items-center justify-content-center text-muted" style="width: 60px; height: 40px;">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td class="fw-bold text-primary"><?= htmlspecialchars($tour['code']) ?></td>
                            
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($tour['name']) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($tour['highlight'] ?? '') ?></small>
                            </td>

                            <td>
                                <?php 
                                    $types = [
                                        'domestic'      => ['label' => 'Trong nước', 'color' => 'bg-info'],
                                        'international' => ['label' => 'Quốc tế',    'color' => 'bg-warning'],
                                        'custom'        => ['label' => 'Yêu cầu',    'color' => 'bg-secondary']
                                    ];
                                    $tType = $tour['type'] ?? 'domestic';
                                    $info = $types[$tType] ?? ['label' => $tType, 'color' => 'bg-secondary'];
                                ?>
                                <span class="badge <?= $info['color'] ?> text-dark bg-opacity-25 border border-dark border-opacity-10">
                                    <?= $info['label'] ?>
                                </span>
                            </td>

                            <td class="fw-bold text-success"><?= number_format($tour['price_adult']) ?> ₫</td>
                            
                            <td class="text-end pe-4 text-nowrap"> 
                                <a href="index.php?action=tour-detail&id=<?= $tour['id'] ?>" class="btn btn-sm btn-outline-info me-1" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="index.php?action=tour-prices&id=<?= $tour['id'] ?>" class="btn btn-sm btn-outline-warning me-1" title="Cấu hình giá theo mùa">
                                    <i class="fas fa-dollar-sign"></i>
                                </a>
                                <a href="index.php?action=edit&id=<?= $tour['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="index.php?action=delete&id=<?= $tour['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa tour này?');" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">Chưa có dữ liệu.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<div class="modal fade" id="tourModalFull" tabindex="-1" style="z-index: 99999 !important;">
    <style>.modal-backdrop { z-index: 99998 !important; }</style>

    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <form action="index.php?action=store" method="POST" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header bg-white border-bottom py-3">
                    <h5 class="modal-title fw-bold text-primary"><i class="fas fa-layer-group me-2"></i>Thêm Tour Mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body bg-light">
                    <ul class="nav nav-tabs nav-fill bg-white pt-2 border rounded-top shadow-sm" id="tourTabs" role="tablist">
                        <li class="nav-item"><button class="nav-link active fw-bold" data-bs-target="#tab-info" data-bs-toggle="tab" type="button">1. Thông tin chung</button></li>
                        <li class="nav-item"><button class="nav-link fw-bold" data-bs-target="#tab-itinerary" data-bs-toggle="tab" type="button">2. Lịch trình</button></li>
                        <li class="nav-item"><button class="nav-link fw-bold" data-bs-target="#tab-price" data-bs-toggle="tab" type="button">3. Bảng giá</button></li>
                        <li class="nav-item"><button class="nav-link fw-bold" data-bs-target="#tab-seo" data-bs-toggle="tab" type="button">4. Hình ảnh</button></li>
                    </ul>

                    <div class="tab-content bg-white p-4 border border-top-0 rounded-bottom shadow-sm">
                        
                        <div class="tab-pane fade show active" id="tab-info">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Mã Tour <span class="text-danger">*</span></label>
                                    <input type="text" name="code" class="form-control" placeholder="T-001" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Tên Tour <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="Tên hiển thị..." required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Loại Tour</label>
                                    <select name="type" class="form-select">
                                        <option value="domestic">Trong nước</option>
                                        <option value="international">Quốc tế</option>
                                        <option value="custom">Theo yêu cầu</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Điểm nổi bật</label>
                                    <textarea name="highlight" class="form-control" rows="3" placeholder="Mô tả ngắn gọn về tour..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-itinerary">
                            <div class="d-flex justify-content-between mb-3 align-items-center">
                                <h6 class="fw-bold text-secondary mb-0">Lịch trình chi tiết</h6>
                                <button type="button" class="btn btn-sm btn-success fw-bold" onclick="addDay()">
                                    <i class="fas fa-plus me-1"></i> Thêm Ngày
                                </button>
                            </div>
                            <div id="itinerary-container">
                                <div class="card mb-3 bg-light border-0 shadow-sm">
                                    <div class="card-body border-start border-4 border-primary">
                                        <h6 class="text-primary fw-bold mb-2">Ngày 1</h6>
                                        <div class="mb-2">
                                            <input type="text" name="itinerary_title[]" class="form-control fw-bold" placeholder="Tiêu đề (VD: Đón khách)">
                                        </div>
                                        <textarea name="itinerary_desc[]" class="form-control mb-2" rows="2" placeholder="Nội dung hoạt động..."></textarea>
                                        <div class="row g-2">
                                            <div class="col-4"><input type="text" name="itinerary_spot[]" class="form-control form-control-sm" placeholder="📍 Điểm đến"></div>
                                            <div class="col-4"><input type="text" name="itinerary_hotel[]" class="form-control form-control-sm" placeholder="🏨 Khách sạn"></div>
                                            <div class="col-4"><input type="text" name="itinerary_meals[]" class="form-control form-control-sm" placeholder="🍽️ Ăn uống"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-price">
                            <div class="alert alert-info py-2 small">
                                <i class="fas fa-info-circle me-1"></i> Giá này là giá cơ bản. Để cấu hình giá Lễ/Tết, vui lòng dùng chức năng <b>"Cấu hình giá"</b> sau khi tạo xong.
                            </div>
                            <div class="row g-4 mt-2">
                                <div class="col-md-6">
                                    <div class="p-3 border rounded bg-light h-100">
                                        <label class="form-label fw-bold text-success"><i class="fas fa-user me-2"></i>Người lớn (>11 tuổi)</label>
                                        <div class="input-group mt-2">
                                            <input type="number" name="price_adult" class="form-control form-control-lg" value="0">
                                            <span class="input-group-text fw-bold">VNĐ</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 border rounded bg-light h-100">
                                        <label class="form-label fw-bold text-primary"><i class="fas fa-child me-2"></i>Trẻ em (5 - 11 tuổi)</label>
                                        <div class="input-group mt-2">
                                            <input type="number" name="price_child" class="form-control form-control-lg" value="0">
                                            <span class="input-group-text fw-bold">VNĐ</span>
                                        </div>
                                        <small class="text-muted d-block mt-2">* Trẻ em dưới 5 tuổi miễn phí</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-seo">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="border p-3 rounded text-center bg-light h-100">
                                        <label class="form-label fw-bold">Ảnh đại diện (Avatar)</label>
                                        <input type="file" name="image" class="form-control mt-2">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="border border-dashed p-4 text-center rounded bg-light h-100">
                                        <i class="fas fa-images fa-2x text-secondary mb-2 opacity-50"></i>
                                        <h6 class="fw-bold">Thư viện ảnh chi tiết</h6>
                                        <input type="file" name="gallery[]" class="form-control w-75 mx-auto mt-2" multiple>
                                        <small class="text-muted d-block mt-2">Giữ phím Ctrl để chọn nhiều ảnh cùng lúc</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-white border-top py-3">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm"><i class="fas fa-save me-2"></i>Lưu Tour</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function addDay() {
        const container = document.getElementById('itinerary-container');
        const dayCount = container.children.length + 1;
        const html = `
            <div class="card mb-3 bg-light border-0 shadow-sm">
                <div class="card-body border-start border-4 border-secondary">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="text-secondary fw-bold">Ngày ${dayCount}</h6>
                        <button type="button" class="btn btn-sm text-danger" onclick="this.closest('.card').remove()"><i class="fas fa-trash"></i></button>
                    </div>
                    <div class="mb-2">
                        <input type="text" name="itinerary_title[]" class="form-control fw-bold" placeholder="Tiêu đề ngày ${dayCount}">
                    </div>
                    <textarea name="itinerary_desc[]" class="form-control mb-2" rows="2" placeholder="Nội dung..."></textarea>
                    <div class="row g-2">
                        <div class="col-4"><input type="text" name="itinerary_spot[]" class="form-control form-control-sm" placeholder="📍 Điểm đến"></div>
                        <div class="col-4"><input type="text" name="itinerary_hotel[]" class="form-control form-control-sm" placeholder="🏨 Khách sạn"></div>
                        <div class="col-4"><input type="text" name="itinerary_meals[]" class="form-control form-control-sm" placeholder="🍽️ Ăn uống"></div>
                    </div>
                </div>
            </div>`;
        container.insertAdjacentHTML('beforeend', html);
    }
</script>