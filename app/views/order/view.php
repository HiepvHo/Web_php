<?php
use App\Helpers\SessionHelper;
require_once 'app/views/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Chi tiết đơn hàng #<?php echo $order[0]['id']; ?></h2>
        <a href="/project1/Order/list" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <?php if (SessionHelper::hasFlash('error')): ?>
        <div class="alert alert-danger">
            <?php echo SessionHelper::getFlash('error'); ?>
        </div>
    <?php endif; ?>

    <?php if (SessionHelper::hasFlash('success')): ?>
        <div class="alert alert-success">
            <?php echo SessionHelper::getFlash('success'); ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Order Information -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Thông tin đơn hàng</h5>
                </div>
                <div class="card-body">
                    <p><strong>Mã đơn hàng:</strong> #<?php echo $order[0]['id']; ?></p>
                    <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order[0]['created_at'])); ?></p>
                    <p><strong>Tổng tiền:</strong> <?php echo number_format($order[0]['total_amount'], 0, ',', '.'); ?> đ</p>
                    
                    <form action="/project1/Order/updateStatus" method="post" class="mt-3">
                        <input type="hidden" name="order_id" value="<?php echo $order[0]['id']; ?>">
                        <div class="form-group">
                            <label for="status"><strong>Trạng thái:</strong></label>
                            <select name="status" id="status" class="form-control" onchange="this.form.submit()">
                                <?php
                                $statuses = [
                                    'pending' => 'Chờ xử lý',
                                    'processing' => 'Đang xử lý',
                                    'completed' => 'Hoàn thành',
                                    'cancelled' => 'Đã hủy'
                                ];
                                foreach ($statuses as $value => $label):
                                ?>
                                    <option value="<?php echo $value; ?>" 
                                            <?php echo $order[0]['status'] === $value ? 'selected' : ''; ?>>
                                        <?php echo $label; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Thông tin khách hàng</h5>
                </div>
                <div class="card-body">
                    <p><strong>Họ tên:</strong> <?php echo htmlspecialchars($order[0]['customer_name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($order[0]['customer_email']); ?></p>
                    <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order[0]['customer_phone']); ?></p>
                    <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order[0]['customer_address']); ?></p>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Chi tiết sản phẩm</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Giá</th>
                                    <th>Số lượng</th>
                                    <th>Tổng</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                        <td><?php echo number_format($item['price'], 0, ',', '.'); ?> đ</td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> đ</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                                    <td><strong><?php echo number_format($order[0]['total_amount'], 0, ',', '.'); ?> đ</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'app/views/footer.php'; ?>