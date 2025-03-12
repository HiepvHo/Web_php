<?php require_once 'app/views/header.php'; ?>

<div class="container mt-4">
    <h2>Thanh toán</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (empty($cartItems)): ?>
        <div class="alert alert-info">
            Giỏ hàng trống. <a href="/project1/Product/list">Tiếp tục mua sắm</a>
        </div>
    <?php else: ?>
        <div class="row">
            <!-- Checkout Form -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Thông tin đặt hàng</h5>
                        <form action="/project1/Cart/checkout" method="post">
                            <div class="form-group">
                                <label for="customer_name">Họ và tên</label>
                                <input type="text" class="form-control <?php echo isset($errors['customer_name']) ? 'is-invalid' : ''; ?>" 
                                       id="customer_name" name="customer_name" 
                                       value="<?php echo htmlspecialchars($_POST['customer_name'] ?? ''); ?>" required>
                                <?php if (isset($errors['customer_name'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['customer_name']; ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="customer_email">Email</label>
                                <input type="email" class="form-control <?php echo isset($errors['customer_email']) ? 'is-invalid' : ''; ?>" 
                                       id="customer_email" name="customer_email" 
                                       value="<?php echo htmlspecialchars($_POST['customer_email'] ?? ''); ?>" required>
                                <?php if (isset($errors['customer_email'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['customer_email']; ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="customer_phone">Số điện thoại</label>
                                <input type="tel" class="form-control <?php echo isset($errors['customer_phone']) ? 'is-invalid' : ''; ?>" 
                                       id="customer_phone" name="customer_phone" 
                                       value="<?php echo htmlspecialchars($_POST['customer_phone'] ?? ''); ?>" required>
                                <?php if (isset($errors['customer_phone'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['customer_phone']; ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="customer_address">Địa chỉ giao hàng</label>
                                <textarea class="form-control <?php echo isset($errors['customer_address']) ? 'is-invalid' : ''; ?>" 
                                          id="customer_address" name="customer_address" rows="3" required><?php 
                                    echo htmlspecialchars($_POST['customer_address'] ?? ''); 
                                ?></textarea>
                                <?php if (isset($errors['customer_address'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['customer_address']; ?></div>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg btn-block mt-4">
                                Đặt hàng
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Đơn hàng của bạn</h5>
                        <div class="order-summary">
                            <?php foreach ($cartItems as $item): ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <span><?php echo htmlspecialchars($item['name']); ?> × <?php echo $item['quantity']; ?></span>
                                    <span><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> đ</span>
                                </div>
                            <?php endforeach; ?>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>Tổng cộng:</strong>
                                <strong><?php echo number_format($total, 0, ',', '.'); ?> đ</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="/project1/Cart/viewCart" class="btn btn-outline-secondary btn-block mt-3">
                    <i class="fas fa-arrow-left"></i> Quay lại giỏ hàng
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'app/views/footer.php'; ?>