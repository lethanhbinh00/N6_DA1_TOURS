<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật Tour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4 mb-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Cập nhật Tour: <?= htmlspecialchars($tour['name']) ?></h5>
            <a href="index.php" class="btn btn-sm btn-light">Quay lại danh sách</a>
        </div>
        <div class="card-body">
            
            <form action="index.php?action=update" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $tour['id'] ?>">
                
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Mã Tour</label>
                        <input type="text" name="code" class="form-control" value="<?= htmlspecialchars($tour['code']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tên Tour</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($tour['name']) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Phân loại</label>
                        <select name="type" class="form-select">
                            <option value="domestic" <?= $tour['type']=='domestic'?'selected':'' ?>>Trong nước</option>
                            <option value="international" <?= $tour['type']=='international'?'selected':'' ?>>Quốc tế</option>
                            <option value="custom" <?= $tour['type']=='custom'?'selected':'' ?>>Theo yêu cầu</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label>Giá Người lớn</label>
                        <input type="number" name="price_adult" class="form-control" value="<?= $tour['price_adult'] ?>">
                    </div>
                    <div class="col-md-6">
                        <label>Giá Trẻ em</label>
                        <input type="number" name="price_child" class="form-control" value="<?= $tour['price_child'] ?>">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Điểm nổi bật</label>
                        <input type="text" name="highlight" class="form-control" value="<?= htmlspecialchars($tour['highlight']) ?>" required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Hình ảnh hiện tại</label><br>
                        <?php if(!empty($tour['image'])): ?>
                            <img src="public/uploads/<?= $tour['image'] ?>" height="80" class="mb-2 rounded border">
                        <?php endif; ?>
                        <input type="file" name="image" class="form-control">
                        <small class="text-muted">Chọn ảnh mới nếu muốn thay đổi.</small>
                    </div>

                    <div class="col-12">
                        <label>Lịch trình chi tiết</label>
                        <div id="itinerary-container">
                            <?php foreach($itineraries as $index => $day): ?>
                            <div class="card mb-2 bg-light border">
                                <div class="card-body p-3">
                                    <h6>Ngày <?= $index + 1 ?></h6>
                                    <input type="text" name="itinerary_title[]" class="form-control mb-1 fw-bold" value="<?= htmlspecialchars($day['title']) ?>" placeholder="Tiêu đề">
                                    <textarea name="itinerary_desc[]" class="form-control mb-1" rows="2"><?= htmlspecialchars($day['description']) ?></textarea>
                                    <div class="row g-1">
                                        <div class="col"><input type="text" name="itinerary_spot[]" class="form-control form-control-sm" value="<?= htmlspecialchars($day['spot']??'') ?>" placeholder="Điểm đến"></div>
                                        <div class="col"><input type="text" name="itinerary_hotel[]" class="form-control form-control-sm" value="<?= htmlspecialchars($day['accommodation']??'') ?>" placeholder="Khách sạn"></div>
                                        <div class="col"><input type="text" name="itinerary_meals[]" class="form-control form-control-sm" value="<?= htmlspecialchars($day['meals']??'') ?>" placeholder="Ăn uống"></div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-success mt-2" onclick="addDay()">+ Thêm ngày</button>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <button type="submit" class="btn btn-primary px-5">LƯU CẬP NHẬT</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function addDay() {
        const container = document.getElementById('itinerary-container');
        const dayCount = container.children.length + 1;
        const html = `
            <div class="card mb-2 bg-light border">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <h6>Ngày ${dayCount} (Mới)</h6>
                        <button type="button" class="btn btn-sm text-danger" onclick="this.closest('.card').remove()">Xóa</button>
                    </div>
                    <input type="text" name="itinerary_title[]" class="form-control mb-1 fw-bold" placeholder="Tiêu đề">
                    <textarea name="itinerary_desc[]" class="form-control mb-1" rows="2" placeholder="Mô tả"></textarea>
                    <div class="row g-1">
                        <div class="col"><input type="text" name="itinerary_spot[]" class="form-control form-control-sm" placeholder="Điểm đến"></div>
                        <div class="col"><input type="text" name="itinerary_hotel[]" class="form-control form-control-sm" placeholder="Khách sạn"></div>
                        <div class="col"><input type="text" name="itinerary_meals[]" class="form-control form-control-sm" placeholder="Ăn uống"></div>
                    </div>
                </div>
            </div>`;
        container.insertAdjacentHTML('beforeend', html);
    }
</script>
</body>
</html>