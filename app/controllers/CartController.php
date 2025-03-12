<?php
require_once 'app/models/CartModel.php';
require_once 'app/models/ProductModel.php';
require_once 'app/config/Database.php';

class CartController {
    private $cartModel;
    private $productModel;
    private $db;

    public function __construct() {
        session_start();
        if (!isset($_SESSION['session_id'])) {
            $_SESSION['session_id'] = session_id();
        }

        $database = new Database();
        $this->db = $database->getConnection();
        $this->cartModel = new CartModel($this->db);
        $this->productModel = new ProductModel($this->db);
        
        // Update cart count in session
        $this->updateCartCount();
    }

    private function updateCartCount() {
        $cartId = $this->getOrCreateCartId();
        $cartItems = $this->cartModel->getCart($_SESSION['session_id']);
        $_SESSION['cart_count'] = array_sum(array_column($cartItems, 'quantity'));
    }

    private function getOrCreateCartId() {
        return $this->cartModel->getCartId($_SESSION['session_id']);
    }

    public function viewCart() {
        $cartId = $this->getOrCreateCartId();
        $cartItems = $this->cartModel->getCart($_SESSION['session_id']);
        $total = $this->cartModel->getCartTotal($cartId);
        
        include 'app/views/cart/view.php';
    }

    public function addToCart($productId) {
        if (!$productId) {
            $_SESSION['error'] = 'Invalid product';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }

        try {
            $cartId = $this->getOrCreateCartId();
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            
            if ($quantity < 1) {
                $quantity = 1;
            }

            $this->cartModel->addItem($cartId, $productId, $quantity);
            $this->updateCartCount();
            $_SESSION['success'] = 'Thêm vào giỏ hàng thành công';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to add product to cart';
            error_log($e->getMessage());
        }

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    public function updateQuantity() {
        if (!isset($_POST['cart_item_id']) || !isset($_POST['quantity'])) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /project1/Cart/viewCart');
            exit();
        }

        $cartItemId = $_POST['cart_item_id'];
        $quantity = (int)$_POST['quantity'];

        if ($quantity < 1) {
            $this->cartModel->removeItem($cartItemId);
        } else {
            $this->cartModel->updateItemQuantity($cartItemId, $quantity);
        }
        $this->updateCartCount();

        header('Location: /project1/Cart/viewCart');
        exit();
    }

    public function removeItem($cartItemId) {
        if ($cartItemId) {
            $this->cartModel->removeItem($cartItemId);
            $this->updateCartCount();
            $_SESSION['success'] = 'Đã xóa sản phẩm khỏi giỏ hàng';
        }

        header('Location: /project1/Cart/viewCart');
        exit();
    }

    public function checkout() {
        $cartId = $this->getOrCreateCartId();
        $cartItems = $this->cartModel->getCart($_SESSION['session_id']);
        $total = $this->cartModel->getCartTotal($cartId);

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate form data
            $customerName = trim($_POST['customer_name'] ?? '');
            $customerEmail = trim($_POST['customer_email'] ?? '');
            $customerPhone = trim($_POST['customer_phone'] ?? '');
            $customerAddress = trim($_POST['customer_address'] ?? '');

            if (empty($customerName)) {
                $errors['customer_name'] = 'Tên không được để trống';
            }
            if (empty($customerEmail) || !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
                $errors['customer_email'] = 'Email không hợp lệ';
            }
            if (empty($customerPhone)) {
                $errors['customer_phone'] = 'Số điện thoại không được để trống';
            }
            if (empty($customerAddress)) {
                $errors['customer_address'] = 'Địa chỉ không được để trống';
            }

            if (empty($cartItems)) {
                $errors['cart'] = 'Giỏ hàng trống';
            }

            if (empty($errors)) {
                try {
                    // Start transaction
                    $this->db->beginTransaction();

                    // Create order
                    require_once 'app/models/OrderModel.php';
                    $orderModel = new OrderModel($this->db);

                    $orderData = [
                        'customer_name' => $customerName,
                        'customer_email' => $customerEmail,
                        'customer_phone' => $customerPhone,
                        'customer_address' => $customerAddress,
                        'total_amount' => $total,
                        'items' => array_map(function($item) {
                            return [
                                'product_id' => $item['product_id'],
                                'quantity' => $item['quantity'],
                                'price' => $item['price']
                            ];
                        }, $cartItems)
                    ];

                    $orderId = $orderModel->createOrder($orderData);

                    // Clear the cart
                    $this->cartModel->clearCart($cartId);

                    $this->db->commit();

                    // Redirect to success page
                    $_SESSION['success'] = 'Đặt hàng thành công! Mã đơn hàng của bạn là: ' . $orderId;
                    header('Location: /project1/Cart/orderSuccess/' . $orderId);
                    exit();

                } catch (Exception $e) {
                    $this->db->rollBack();
                    $errors['system'] = 'Có lỗi xảy ra khi xử lý đơn hàng';
                    error_log($e->getMessage());
                }
            }
        }

        include 'app/views/cart/checkout.php';
    }

    public function orderSuccess($orderId) {
        require_once 'app/models/OrderModel.php';
        $orderModel = new OrderModel($this->db);
        $order = $orderModel->getOrder($orderId);
        
        if (!$order) {
            header('Location: /project1/Product/list');
            exit();
        }

        include 'app/views/cart/order_success.php';
    }
}