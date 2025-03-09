<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/project1/public/css/styles.css">
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="page-header fade-in">
            <h1><i class="fas fa-edit me-3"></i>Chỉnh sửa sản phẩm</h1>
            <p class="text-muted">Cập nhật thông tin sản phẩm</p>
        </div>

        <!-- Main Form Card -->
        <div class="card slide-in">
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger fade-in">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Lỗi!</strong> Vui lòng kiểm tra lại thông tin.
                        <ul class="mb-0 mt-2">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="/project1/Product/edit/<?php echo $product->id; ?>" method="POST" enctype="multipart/form-data" id="productForm">
                    <!-- Image Upload Section -->
                    <div class="text-center mb-4 fade-in">
                        <div class="image-upload" id="imageUploadContainer">
                            <img id="imagePreview" 
                                 src="<?php echo !empty($product->image) ? '/project1/public/' . $product->image : '/project1/public/images/default-product.jpg'; ?>" 
                                 class="image-preview mb-2">
                            <div class="upload-placeholder" id="uploadPlaceholder" style="display: none;">
                                <i class="fas fa-cloud-upload-alt fa-3x mb-3"></i>
                                <div>Kéo thả ảnh hoặc click để chọn</div>
                                <small class="text-muted">Hỗ trợ JPG, PNG hoặc GIF (Tối đa 5MB)</small>
                            </div>
                            <input type="file" class="d-none" id="image" name="image" accept="image/*">
                        </div>
                    </div>

                    <div class="row">
                        <!-- Product Name -->
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-tag me-2"></i>Tên sản phẩm
                            </label>
                            <input type="text" class="form-control" id="name" name="name" required
                                   value="<?php echo htmlspecialchars($product->name); ?>"
                                   placeholder="Nhập tên sản phẩm">
                        </div>

                        <!-- Price -->
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">
                                <i class="fas fa-dollar-sign me-2"></i>Giá
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="price" name="price" 
                                       min="0" step="1000" required
                                       value="<?php echo htmlspecialchars($product->price); ?>"
                                       placeholder="Nhập giá sản phẩm">
                                <span class="input-group-text">VND</span>
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label">
                                <i class="fas fa-folder me-2"></i>Danh mục
                            </label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Chọn danh mục</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category->id; ?>"
                                            <?php echo $product->category_id == $category->id ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Description -->
                        <div class="col-12 mb-4">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left me-2"></i>Mô tả
                            </label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="4" required placeholder="Nhập mô tả sản phẩm"><?php echo htmlspecialchars($product->description); ?></textarea>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='/project1/Product/list'">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-2"></i>Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image upload preview
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');
        const uploadPlaceholder = document.getElementById('uploadPlaceholder');
        const imageUploadContainer = document.getElementById('imageUploadContainer');

        imageUploadContainer.addEventListener('click', () => imageInput.click());
        imageUploadContainer.addEventListener('dragover', (e) => {
            e.preventDefault();
            imageUploadContainer.classList.add('border-primary');
        });
        imageUploadContainer.addEventListener('dragleave', () => {
            imageUploadContainer.classList.remove('border-primary');
        });
        imageUploadContainer.addEventListener('drop', (e) => {
            e.preventDefault();
            imageUploadContainer.classList.remove('border-primary');
            if (e.dataTransfer.files.length) {
                imageInput.files = e.dataTransfer.files;
                handleImagePreview(e.dataTransfer.files[0]);
            }
        });

        imageInput.addEventListener('change', (e) => {
            if (e.target.files.length) {
                handleImagePreview(e.target.files[0]);
            }
        });

        function handleImagePreview(file) {
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                    uploadPlaceholder.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        }

        // Form validation
        const form = document.getElementById('productForm');
        form.addEventListener('submit', (e) => {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });

        // Price formatting
        const priceInput = document.getElementById('price');
        priceInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            if (value !== '') {
                value = parseInt(value).toLocaleString('vi-VN');
                e.target.value = value.replace(/\./g, '');
            }
        });
    </script>
</body>
</html>
