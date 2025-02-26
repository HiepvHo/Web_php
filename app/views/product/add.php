<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/styles.css">
    <script>
        function validateForm() {
            let name = document.getElementById('name').value;
            let price = document.getElementById('price').value;
            let errors = [];
            
            // Kiểm tra tên sản phẩm
            if (name.length < 10 || name.length > 100) {
                errors.push('Tên sản phẩm phải có từ 10 đến 100 ký tự.');
            }
            
            // Kiểm tra giá sản phẩm
            if (price <= 0 || isNaN(price)) {
                errors.push('Giá phải là một số dương lớn hơn 0.');
            }

            // Nếu có lỗi, hiển thị thông báo
            if (errors.length > 0) {
                alert(errors.join('\n'));
                return false;
            }
            
            return true;
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Thêm sản phẩm mới</h1>

        <!-- Hiển thị các lỗi từ phía server nếu có -->
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Form thêm sản phẩm -->
        <form method="POST" action="/project1/Product/add" onsubmit="return validateForm();">
            <label for="name">Tên sản phẩm:</label>
            <input type="text" id="name" name="name" required><br>

            <label for="description">Mô tả:</label>
            <textarea id="description" name="description" required></textarea><br>

            <label for="price">Giá:</label>
            <input type="number" id="price" name="price" step="0.01" required><br>

            <button type="submit">Thêm sản phẩm</button>
        </form>

        <a href="/project1/Product/list">Quay lại danh sách sản phẩm</a>
    </div>
</body>
</html>
