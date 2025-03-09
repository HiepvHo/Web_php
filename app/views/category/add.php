<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm danh mục mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/project1/public/css/styles.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header fade-in">
            <h1><i class="fas fa-folder-plus me-3"></i>Thêm danh mục mới</h1>
            <p class="mt-2">Tạo danh mục mới để phân loại sản phẩm của bạn</p>
        </div>

        <!-- Main Content -->
        <div class="card slide-up">
            <div class="card-body">
                <form action="/project1/Category/add" method="POST" class="needs-validation" novalidate>
                    <div class="row">
                        <!-- Category Icon -->
                        <div class="col-12 text-center mb-4">
                            <div class="category-icon">
                                <i class="fas fa-folder-open fa-4x text-primary"></i>
                            </div>
                        </div>

                        <!-- Category Name -->
                        <div class="col-md-12 mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-tag me-2"></i>Tên danh mục
                            </label>
                            <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
                                   id="name" name="name" required 
                                   value="<?php echo $_POST['name'] ?? ''; ?>"
                                   placeholder="Nhập tên danh mục">
                            <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Description -->
                        <div class="col-12 mb-3">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left me-2"></i>Mô tả
                            </label>
                            <textarea class="form-control <?php echo isset($errors['description']) ? 'is-invalid' : ''; ?>" 
                                      id="description" name="description" rows="4" 
                                      placeholder="Nhập mô tả chi tiết về danh mục"><?php echo $_POST['description'] ?? ''; ?></textarea>
                            <?php if (isset($errors['description'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['description']; ?></div>
                            <?php endif; ?>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Mô tả giúp người dùng hiểu rõ hơn về danh mục của bạn
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <a href="/project1/Category/list" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Tạo danh mục
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function () {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation');
            
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();

        // Add animation to the category icon
        const categoryIcon = document.querySelector('.category-icon');
        categoryIcon.addEventListener('mouseover', function() {
            this.querySelector('i').style.transform = 'scale(1.1)';
            this.querySelector('i').style.transition = 'transform 0.3s ease';
        });
        categoryIcon.addEventListener('mouseout', function() {
            this.querySelector('i').style.transform = 'scale(1)';
        });
    </script>
</body>
</html>
