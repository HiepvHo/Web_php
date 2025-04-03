<?php
use App\Helpers\SessionHelper;
require_once 'app/views/header.php';
?>

<div class="container mt-4">
    <h2>Giỏ hàng</h2>

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

    <!-- Cart Summary -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-4">
                    <h5 class="mb-0"><?php echo count($cartItems); ?></h5>
                    <small class="text-muted">Sản phẩm trong giỏ</small>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-0"><?php echo number_format($total ?? 0, 0, ',', '.'); ?> đ</h5>
                    <small class="text-muted">Tổng tiền</small>
                </div>
                <div class="col-md-4">
                    <a href="/project1/Cart/checkout" class="btn btn-primary <?php echo empty($cartItems) ? 'disabled' : ''; ?>">
                        <i class="fas fa-shopping-cart me-2"></i>Thanh toán
                    </a>
                </div>
            </div>
        </div>
    </div>

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
                                    <span><?php echo htmlspecialchars($item['name'] ?? ''); ?></span>
                                </div>
                            </td>
                            <td><?php echo number_format($item['price'] ?? 0, 0, ',', '.'); ?> đ</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <form action="/project1/Cart/updateQuantity" method="post" class="quantity-form d-flex align-items-center gap-2">
                                        <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                                        <div class="input-group" style="width: 120px;">
                                            <button type="button" class="btn btn-outline-secondary" onclick="decrementQuantity(this)">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>"
                                                   min="1" class="form-control text-center"
                                                   onchange="confirmQuantityChange(this.form)">
                                            <button type="button" class="btn btn-outline-secondary" onclick="incrementQuantity(this)">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </td>
                            <td><?php echo number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 0), 0, ',', '.'); ?> đ</td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm"
                                        onclick="confirmRemoveItem('<?php echo $item['cart_item_id']; ?>', '<?php echo htmlspecialchars($item['name']); ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right"><strong>Tổng cộng:</strong></td>
                        <td><strong><?php echo number_format($total ?? 0, 0, ',', '.'); ?> đ</strong></td>
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

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận thay đổi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="confirmationMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Hủy
                </button>
                <button type="button" class="btn btn-primary" id="confirmButton">
                    <i class="fas fa-check me-2"></i>Xác nhận
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let activeForm = null;
    const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    
    function incrementQuantity(button) {
        const input = button.parentElement.querySelector('input[type="number"]');
        input.value = parseInt(input.value) + 1;
        confirmQuantityChange(button.closest('form'));
    }

    function decrementQuantity(button) {
        const input = button.parentElement.querySelector('input[type="number"]');
        const newValue = parseInt(input.value) - 1;
        if (newValue >= 1) {
            input.value = newValue;
            confirmQuantityChange(button.closest('form'));
        }
    }

    function confirmQuantityChange(form) {
        activeForm = form;
        const quantity = form.querySelector('input[name="quantity"]').value;
        const productName = form.closest('tr').querySelector('.d-flex span').textContent;
        
        document.getElementById('confirmationMessage').innerHTML =
            `Bạn có chắc muốn thay đổi số lượng sản phẩm "${productName}" thành ${quantity}?`;
        
        const confirmButton = document.getElementById('confirmButton');
        confirmButton.onclick = function() {
            activeForm.submit();
            confirmationModal.hide();
        };
        
        confirmationModal.show();
    }

    function confirmRemoveItem(itemId, itemName) {
        document.getElementById('confirmationMessage').innerHTML =
            `Bạn có chắc muốn xóa sản phẩm "${itemName}" khỏi giỏ hàng?`;
        
        const confirmButton = document.getElementById('confirmButton');
        confirmButton.onclick = function() {
            window.location.href = `/project1/Cart/removeItem/${itemId}`;
            confirmationModal.hide();
        };
        
        confirmationModal.show();
    }

    // Add loading indicator to buttons during form submission
    document.querySelectorAll('form.quantity-form').forEach(form => {
        form.addEventListener('submit', function() {
            const buttons = this.querySelectorAll('button');
            buttons.forEach(button => {
                button.disabled = true;
                if (button.type === 'submit') {
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                }
            });
        });
    });
</script>

<?php require_once 'app/views/footer.php'; ?>