<?php
use App\Helpers\SessionHelper;
require_once 'app/views/header.php';
?>

<div class="container mt-4">
    <div class="text-center">
        <div class="mb-4">
            <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
        </div>
        <h2 class="mb-4">Đặt hàng thành công!</h2>
        
        <?php if (SessionHelper::hasFlash('success')): ?>
            <div class="alert alert-success">
                <?php echo SessionHelper::getFlash('success'); ?>
            </div>
        <?php endif; ?>

        <p class="lead">Cảm ơn bạn đã mua hàng. Chúng tôi sẽ xử lý đơn hàng của bạn trong thời gian sớm nhất.</p>
        
        <div class="mt-4">
            <a href="/project1/Product/list" class="btn btn-primary">
                <i class="fas fa-shopping-cart me-2"></i>Tiếp tục mua sắm
            </a>
        </div>
    </div>
</div>

<?php require_once 'app/views/footer.php'; ?>