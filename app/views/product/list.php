<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/project1/public/css/styles.css">
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="page-header fade-in">
                <h1><i class="fas fa-box-open me-3"></i>Sản phẩm</h1>
                <p class="text-muted">Danh sách sản phẩm</p>
            </div>
            <div>
                <a href="/project1/Cart/viewCart" class="btn btn-outline-primary position-relative">
                    <i class="fas fa-shopping-cart"></i> Giỏ hàng
                    <?php if (isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?php echo $_SESSION['cart_count']; ?>
                        </span>
                    <?php endif; ?>
                </a>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons slide-in">
            <button class="btn btn-primary" onclick="window.location.href='/project1/Product/add'">
                <i class="fas fa-plus-circle me-2"></i>Thêm sản phẩm
            </button>
            <button class="btn btn-success" onclick="window.location.href='/project1/Category/list'">
                <i class="fas fa-list me-2"></i>Quản lý danh mục
            </button>
        </div>

        <!-- Search Bar -->
        <div class="search-bar fade-in">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Tìm kiếm sản phẩm..." class="form-control">
        </div>

        <!-- Products Grid -->
        <div class="grid">
            <?php if (empty($products)): ?>
                <div class="text-center text-muted mt-5">
                    <i class="fas fa-box-open fa-3x mb-3"></i>
                    <h3>Chưa có sản phẩm nào</h3>
                    <p>Hãy thêm sản phẩm đầu tiên của bạn!</p>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="card fade-in product-card">
                        <div class="position-relative">
                            <?php if (!empty($product->image)): ?>
                                <img src="/project1/public/<?php echo htmlspecialchars($product->image); ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($product->name); ?>"
                                     onerror="this.src='/project1/public/images/default-product.jpg'">
                            <?php else: ?>
                                <img src="/project1/public/images/default-product.jpg" 
                                     class="card-img-top" 
                                     alt="Default product image">
                            <?php endif; ?>
                            <div class="price-tag">
                                <?php echo number_format($product->price, 0, ',', '.'); ?> VND
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="category-badge">
                                <i class="fas fa-tag me-1"></i>
                                <?php echo htmlspecialchars($product->category_name); ?>
                            </div>
                            <h5 class="card-title"><?php echo htmlspecialchars($product->name); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($product->description); ?></p>
                            
                            <div class="mt-3">
                                <form action="/project1/Cart/addToCart/<?php echo $product->id; ?>"
                                      method="post"
                                      class="d-flex gap-2 mb-2">
                                    <div class="input-group">
                                        <input type="number"
                                               name="quantity"
                                               value="1"
                                               min="1"
                                               class="form-control"
                                               style="width: 80px">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-cart-plus me-1"></i>Thêm vào giỏ
                                        </button>
                                    </div>
                                </form>

                                <div class="d-flex justify-content-between">
                                    <button onclick="window.location.href='/project1/Product/edit/<?php echo $product->id; ?>'"
                                            class="btn btn-warning">
                                        <i class="fas fa-edit me-1"></i>Sửa
                                    </button>
                                    <button onclick="confirmDelete(<?php echo $product->id; ?>)"
                                            class="btn btn-danger">
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xác nhận xóa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa sản phẩm này?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Xóa</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Delete confirmation
        let productIdToDelete = null;
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

        function confirmDelete(productId) {
            productIdToDelete = productId;
            deleteModal.show();
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (productIdToDelete) {
                window.location.href = `/project1/Product/delete/${productIdToDelete}`;
            }
            deleteModal.hide();
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const products = document.querySelectorAll('.product-card');
            
            products.forEach(product => {
                const title = product.querySelector('.card-title').textContent.toLowerCase();
                const description = product.querySelector('.card-text').textContent.toLowerCase();
                const category = product.querySelector('.category-badge').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || 
                    description.includes(searchTerm) || 
                    category.includes(searchTerm)) {
                    product.style.display = '';
                } else {
                    product.style.display = 'none';
                }
            });
        });

        // Animate elements on scroll
        function animateOnScroll() {
            const elements = document.querySelectorAll('.fade-in');
            elements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const windowHeight = window.innerHeight;
                if (elementTop < windowHeight - 100) {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }
            });
        }

        window.addEventListener('scroll', animateOnScroll);
        window.addEventListener('load', animateOnScroll);
    </script>
</body>
</html>
