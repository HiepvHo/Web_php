<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Sửa sản phẩm</h1>

        <form method="POST" action="/project1/Product/edit/<?php echo $product->getID(); ?>">
            <label for="name">Tên sản phẩm:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product->getName(), ENT_QUOTES, 'UTF-8'); ?>" required><br>

            <label for="description">Mô tả:</label>
            <textarea id="description" name="description" required><?php echo htmlspecialchars($product->getDescription(), ENT_QUOTES, 'UTF-8'); ?></textarea><br>

            <label for="price">Giá:</label>
            <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($product->getPrice(), ENT_QUOTES, 'UTF-8'); ?>" required><br>

            <button type="submit">Lưu thay đổi</button>
        </form>

        <a href="/project1/Product/list">Quay lại danh sách sản phẩm</a>
    </div>
</body>
</html>
