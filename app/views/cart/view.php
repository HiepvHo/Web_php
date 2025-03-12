<?php require_once 'app/views/header.php'; ?>

<div class="container mt-4">
    <h2>Giỏ hàng</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (empty($cartItems)): ?>
        <div class="alert alert-info">
            Giỏ hàng trống. <a href="/project1/Product/list">Tiếp tục mua sắm</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Tổng</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if ($item['image']): ?>
                                        <img src="/project1/public/<?php echo htmlspecialchars($item['image']); ?>" 
                                            alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                            class="img-thumbnail mr-3" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php endif; ?>
                                    <span><?php echo htmlspecialchars($item['name']); ?></span>
                                </div>
                            </td>
                            <td><?php echo number_format($item['price'], 0, ',', '.'); ?> đ</td>
                            <td>
                                <form action="/project1/Cart/updateQuantity" method="post" class="form-inline">
                                    <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                           min="1" class="form-control" style="width: 80px" onchange="this.form.submit()">
                                </form>
                            </td>
                            <td><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> đ</td>
                            <td>
                                <a href="/project1/Cart/removeItem/<?php echo $item['cart_item_id']; ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right"><strong>Tổng cộng:</strong></td>
                        <td><strong><?php echo number_format($total, 0, ',', '.'); ?> đ</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <a href="/project1/Product/list" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                </a>
            </div>
            <div class="col-md-6 text-right">
                <a href="/project1/Cart/checkout" class="btn btn-primary">
                    Tiến hành thanh toán <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'app/views/footer.php'; ?>