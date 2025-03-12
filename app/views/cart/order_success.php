<?php require_once 'app/views/header.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 64px;"></i>
                    </div>
                    
                    <h2 class="card-title">Đặt hàng thành công!</h2>
                    <p class="card-text">Cảm ơn bạn đã đặt hàng. Mã đơn hàng của bạn là: <strong>#<?php echo $order[0]['id']; ?></strong></p>
                    
                    <hr>
                    
                    <div class="text-left">
                        <h5>Thông tin đơn hàng</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Họ và tên:</strong> <?php echo htmlspecialchars($order[0]['customer_name']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($order[0]['customer_email']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order[0]['customer_phone']); ?></p>
                                <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order[0]['customer_address']); ?></p>
                            </div>
                        </div>

                        <h5 class="mt-4">Chi tiết đơn hàng</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Số lượng</th>
                                        <th>Đơn giá</th>
                                        <th>Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td><?php echo number_format($item['price'], 0, ',', '.'); ?> đ</td>
                                            <td><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> đ</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-right"><strong>Tổng cộng:</strong></td>
                                        <td><strong><?php echo number_format($order[0]['total_amount'], 0, ',', '.'); ?> đ</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="mt-4">
                        <p class="text-muted">
                            Chúng tôi sẽ gửi email xác nhận đơn hàng và thông tin chi tiết đến địa chỉ email của bạn.
                        </p>
                        <a href="/project1/Product/list" class="btn btn-primary">
                            <i class="fas fa-shopping-bag"></i> Tiếp tục mua sắm
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'app/views/footer.php'; ?>