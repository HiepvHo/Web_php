<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý danh mục</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/project1/public/css/styles.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header fade-in">
            <h1><i class="fas fa-folder me-3"></i>Quản lý danh mục</h1>
            <p class="mt-2">Quản lý các danh mục sản phẩm trong hệ thống</p>
        </div>

        <!-- Action Buttons -->
        <div class="card slide-up mb-4">
            <div class="card-body">
                <div class="d-flex gap-3">
                    <a href="/project1/Category/add" class="btn btn-primary">
                        <i class="fas fa-folder-plus me-2"></i>Thêm danh mục mới
                    </a>
                    <a href="/project1/Product/list" class="btn btn-secondary">
                        <i class="fas fa-box me-2"></i>Xem sản phẩm
                    </a>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if (isset($_SESSION['flash_messages']) && !empty($_SESSION['flash_messages'])): ?>
            <?php foreach ($_SESSION['flash_messages'] as $message): ?>
                <div class="alert alert-<?php echo $message['type']; ?> fade-in">
                    <?php echo $message['message']; ?>
                </div>
            <?php endforeach; ?>
            <?php $_SESSION['flash_messages'] = []; ?>
        <?php endif; ?>

        <!-- Categories Grid -->
        <div class="grid">
            <?php if (empty($categories)): ?>
                <div class="card text-center p-5 fade-in">
                    <div class="empty-state">
                        <i class="fas fa-folder-open fa-4x mb-3 text-muted"></i>
                        <h3>Chưa có danh mục nào</h3>
                        <p class="text-muted">Hãy thêm danh mục mới để bắt đầu</p>
                        <a href="/project1/Category/add" class="btn btn-primary mt-3">
                            <i class="fas fa-folder-plus me-2"></i>Thêm danh mục ngay
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($categories as $category): ?>
                    <div class="card fade-in">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title">
                                    <i class="fas fa-folder me-2 text-primary"></i>
                                    <?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>
                                </h5>
                                <span class="badge bg-primary rounded-pill">
                                    <?php echo $category->product_count ?? 0; ?> sản phẩm
                                </span>
                            </div>
                            <p class="card-text text-muted mb-3">
                                <?php echo htmlspecialchars($category->description ?? 'Không có mô tả', ENT_QUOTES, 'UTF-8'); ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    <?php echo date('d/m/Y', strtotime($category->created_at)); ?>
                                </small>
                                <div class="btn-group">
                                    <a href="/project1/Category/edit/<?php echo $category->id; ?>" 
                                       class="btn btn-secondary btn-sm">
                                        <i class="fas fa-edit me-1"></i>Sửa
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm delete-btn" 
                                            data-id="<?php echo $category->id; ?>"
                                            data-name="<?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>">
                                        <i class="fas fa-trash-alt me-1"></i>Xóa
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                        Xác nhận xóa
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa danh mục "<span id="categoryName"></span>"?</p>
                    <p class="text-danger">
                        <i class="fas fa-exclamation-circle me-1"></i>
                        Lưu ý: Tất cả sản phẩm trong danh mục này sẽ không còn danh mục.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Hủy
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">
                        <i class="fas fa-trash-alt me-1"></i>Xóa
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Delete confirmation
        let deleteId = null;
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                deleteId = this.dataset.id;
                document.getElementById('categoryName').textContent = this.dataset.name;
                deleteModal.show();
            });
        });

        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (deleteId) {
                const button = this;
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Đang xóa...';
                
                fetch(`/project1/Category/delete/${deleteId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const categoryCard = document.querySelector(`[data-id="${deleteId}"]`).closest('.card');
                            categoryCard.style.transition = 'all 0.3s ease';
                            categoryCard.style.opacity = '0';
                            categoryCard.style.transform = 'scale(0.8)';
                            
                            setTimeout(() => {
                                categoryCard.remove();
                                deleteModal.hide();
                                
                                const alert = document.createElement('div');
                                alert.className = 'alert alert-success fade-in';
                                alert.innerHTML = '<i class="fas fa-check-circle me-2"></i>Xóa danh mục thành công!';
                                document.querySelector('.container').insertBefore(alert, document.querySelector('.grid'));
                                
                                setTimeout(() => {
                                    alert.style.opacity = '0';
                                    setTimeout(() => alert.remove(), 300);
                                }, 3000);
                            }, 300);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        button.disabled = false;
                        button.innerHTML = '<i class="fas fa-trash-alt me-1"></i>Xóa';
                    });
            }
        });

        // Add scroll animation
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('slide-up');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });

        document.querySelectorAll('.card').forEach(card => {
            observer.observe(card);
        });
    </script>
</body>
</html>
