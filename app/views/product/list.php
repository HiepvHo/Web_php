<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Danh sách sản phẩm</h1>
        <a href="/project1/Product/add" class="btn btn-primary">Thêm sản phẩm mới</a>
        <ul>
            <?php foreach ($products as $product): ?>
                <li>
                    <h2><?php echo htmlspecialchars($product->getName(), ENT_QUOTES, 'UTF-8'); ?></h2>
                    <p><?php echo htmlspecialchars($product->getDescription(), ENT_QUOTES, 'UTF-8'); ?></p>
                    <p>Giá: <?php echo htmlspecialchars($product->getPrice(), ENT_QUOTES, 'UTF-8'); ?></p>
                    <a href="/project1/Product/edit/<?php echo $product->getID(); ?>" class="btn btn-warning">Sửa</a>
                    <a href="/project1/Product/delete/<?php echo $product->getID(); ?>" class="btn btn-danger" 
                       onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">Xóa</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
