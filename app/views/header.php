<?php
use App\Helpers\SessionHelper;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý cửa hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/project1/public/css/styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/project1/Product/list">
                <i class="fas fa-store me-2"></i>My Store
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/project1/Product/list">Sản phẩm</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/project1/Category/list">Danh mục</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/project1/Order/list">Đơn hàng</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <a href="/project1/Cart/viewCart" class="btn btn-outline-primary position-relative me-2">
                        <i class="fas fa-shopping-cart"></i>
                        <?php if (isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $_SESSION['cart_count']; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <?php if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in']): ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['username']); ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/project1/Auth/logout">Đăng xuất</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="/project1/Auth/login" class="btn btn-outline-primary me-2">Đăng nhập</a>
                        <a href="/project1/Auth/register" class="btn btn-primary">Đăng ký</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>