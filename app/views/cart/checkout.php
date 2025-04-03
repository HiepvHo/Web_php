<?php
use App\Helpers\SessionHelper;
require_once 'app/views/header.php';
?>

<div class="container mt-4">
    <h2>Thanh toán</h2>

    <?php if (SessionHelper::hasFlash('error')): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?php echo SessionHelper::getFlash('error'); ?>
        </div>
    <?php endif; ?>

    <!-- Checkout Progress -->
    <div class="checkout-progress mb-4">
        <div class="row text-center">
            <div class="col-4">
                <div class="step completed">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Giỏ hàng</span>
                </div>
            </div>
            <div class="col-4">
                <div class="step active">
                    <i class="fas fa-address-card"></i>
                    <span>Thông tin</span>
                </div>
            </div>
            <div class="col-4">
                <div class="step">
                    <i class="fas fa-check-circle"></i>
                    <span>Hoàn tất</span>
                </div>
            </div>
        </div>
    </div>

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
                        <h5 class="card-title mb-4">
                           <i class="fas fa-user-circle me-2"></i>Thông tin đặt hàng
                       </h5>
                       <form action="/project1/Cart/checkout" method="post" class="needs-validation" novalidate id="checkoutForm">
                           <div class="form-group mb-3">
                               <label for="customer_name" class="form-label">
                                   <i class="fas fa-user me-2"></i>Họ và tên
                               </label>
                               <input type="text" class="form-control <?php echo isset($errors['customer_name']) ? 'is-invalid' : ''; ?>"
                                      id="customer_name" name="customer_name"
                                      value="<?php echo htmlspecialchars($_POST['customer_name'] ?? ''); ?>"
                                      pattern=".{3,}" required>
                               <div class="invalid-feedback">
                                   <?php echo isset($errors['customer_name']) ? $errors['customer_name'] : 'Vui lòng nhập họ tên (ít nhất 3 ký tự)'; ?>
                               </div>
                           </div>

                            <div class="form-group mb-3">
                                <label for="customer_email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Email
                                </label>
                                <input type="email" class="form-control <?php echo isset($errors['customer_email']) ? 'is-invalid' : ''; ?>"
                                       id="customer_email" name="customer_email"
                                       value="<?php echo htmlspecialchars($_POST['customer_email'] ?? ''); ?>"
                                       pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required>
                                <div class="invalid-feedback">
                                    <?php echo isset($errors['customer_email']) ? $errors['customer_email'] : 'Vui lòng nhập email hợp lệ'; ?>
                                </div>
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
                                    <span><?php echo htmlspecialchars($item['name'] ?? ''); ?> × <?php echo $item['quantity'] ?? 0; ?></span>
                                    <span><?php echo number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 0), 0, ',', '.'); ?> đ</span>
                                </div>
                            <?php endforeach; ?>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>Tổng cộng:</strong>
                                <strong><?php echo number_format($total ?? 0, 0, ',', '.'); ?> đ</strong>
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