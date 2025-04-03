<?php
use App\Helpers\SessionHelper;
require_once 'app/views/header.php';
?>

<div class="container mt-4">
    <h2>Quản lý đơn hàng</h2>

    <?php if (SessionHelper::hasFlash('error')): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?php echo SessionHelper::getFlash('error'); ?>
        </div>
    <?php endif; ?>

    <?php if (SessionHelper::hasFlash('success')): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            <?php echo SessionHelper::getFlash('success'); ?>
        </div>
    <?php endif; ?>

    <!-- Order Summary -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-3">
                    <h5 class="mb-0"><?php echo $totalOrders; ?></h5>
                    <small class="text-muted">Tổng số đơn hàng</small>
                </div>
                <div class="col-md-3">
                    <h5 class="mb-0"><?php echo $page; ?>/<?php echo $totalPages; ?></h5>
                    <small class="text-muted">Trang hiện tại</small>
                </div>
                <div class="col-md-3">
                    <h5 class="mb-0"><?php echo count($orders); ?></h5>
                    <small class="text-muted">Đơn hàng hiển thị</small>
                </div>
                <div class="col-md-3">
                    <h5 class="mb-0"><?php echo ($page - 1) * 10 + 1; ?>-<?php echo min($page * 10, $totalOrders); ?></h5>
                    <small class="text-muted">Hiển thị</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Ngày đặt</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Chưa có đơn hàng nào</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong><br>
                                        <small><?php echo htmlspecialchars($order['customer_email']); ?></small>
                                    </td>
                                    <td><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> đ</td>
                                    <td>
                                        <?php
                                        $statusClass = [
                                            'pending' => 'warning',
                                            'processing' => 'info',
                                            'completed' => 'success',
                                            'cancelled' => 'danger'
                                        ];
                                        $statusText = [
                                            'pending' => 'Chờ xử lý',
                                            'processing' => 'Đang xử lý',
                                            'completed' => 'Hoàn thành',
                                            'cancelled' => 'Đã hủy'
                                        ];
                                        ?>
                                        <span class="badge bg-<?php echo $statusClass[$order['status']]; ?>">
                                            <?php echo $statusText[$order['status']]; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <a href="/project1/Order/view/<?php echo $order['id']; ?>" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i> Chi tiết
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <!-- Previous Button -->
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>" <?php echo $page <= 1 ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>

                        <!-- Page Numbers -->
                        <?php
                        $start = max(1, min($page - 2, $totalPages - 4));
                        $end = min($totalPages, max(5, $page + 2));
                        
                        if ($start > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=1">1</a>
                            </li>
                            <?php if ($start > 2): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif;
                        endif;

                        for ($i = $start; $i <= $end; $i++): ?>
                            <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor;

                        if ($end < $totalPages): ?>
                            <?php if ($end < $totalPages - 1): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $totalPages; ?>"><?php echo $totalPages; ?></a>
                            </li>
                        <?php endif; ?>

                        <!-- Next Button -->
                        <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>" <?php echo $page >= $totalPages ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'app/views/footer.php'; ?>