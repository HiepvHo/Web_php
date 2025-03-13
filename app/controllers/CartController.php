<?php
require_once 'app/models/CartModel.php';
require_once 'app/models/ProductModel.php';
require_once 'app/config/Database.php';
require_once 'app/helpers/SessionHelper.php';

class CartController {
    private $cartModel;
    private $productModel;
    private $db;

    public function __construct() {
        SessionHelper::init();
        
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
        SessionHelper::updateCartCount(array_sum(array_column($cartItems, 'quantity')));
    }

    private function getOrCreateCartId() {
        return $this->cartModel->getCartId($_SESSION['session_id']);
    }

    private function setFlashMessage($type, $message) {
        SessionHelper::setFlash($type, $message);
    }

    public function viewCart() {
        $cartId = $this->getOrCreateCartId();
        $cartItems = $this->cartModel->getCart($_SESSION['session_id']);
        $total = $this->cartModel->getCartTotal($cartId);
        
        include 'app/views/cart/view.php';
    }

    public function addToCart($productId) {
        if (!$productId) {
            $this->setFlashMessage('error', 'Sản phẩm không hợp lệ');
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
            $this->setFlashMessage('success', 'Thêm vào giỏ hàng thành công');
        } catch (Exception $e) {
            $this->setFlashMessage('error', 'Không thể thêm sản phẩm vào giỏ hàng');
            error_log($e->getMessage());
        }

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    public function updateQuantity() {
        if (!isset($_POST['cart_item_id']) || !isset($_POST['quantity'])) {
            $this->setFlashMessage('error', 'Yêu cầu không hợp lệ');
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate form data
            $errors = $this->validateCheckoutForm($_POST);
            
            if (empty($errors)) {
                try {
                    // Start transaction
                    $this->db->beginTransaction();

                    // Create order
                    $orderId = $this->createOrder($_POST);

                    // Create order details
                    $this->createOrderDetails($orderId);

                    // Clear cart
                    unset($_SESSION['cart']);

                    // Commit transaction
                    $this->db->commit();

                    // Redirect to success page
                    SessionHelper::setFlash('success', 'Đặt hàng thành công!');
                    header('Location: /project1/Cart/success');
                    exit;

                } catch (Exception $e) {
                    // Rollback transaction on error
                    $this->db->rollBack();
                    SessionHelper::setFlash('error', 'Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại.');
                }
            }
            
            // If there are errors, show them on the checkout page
            $cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
            $total = $this->calculateTotal($cartItems);
            $this->view('cart/checkout', ['cartItems' => $cartItems, 'total' => $total, 'errors' => $errors]);
        } else {
            // Show checkout page for GET request
            $cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
            $total = $this->calculateTotal($cartItems);
            $this->view('cart/checkout', ['cartItems' => $cartItems, 'total' => $total]);
        }
    }

    private function createOrder($data) {
        $sql = "INSERT INTO orders (customer_name, customer_email, customer_phone, customer_address, total_amount, created_at) 
                VALUES (:name, :email, :phone, :address, :total, NOW())";
        
        $total = $this->calculateTotal($_SESSION['cart']);
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name' => $data['customer_name'],
            ':email' => $data['customer_email'],
            ':phone' => $data['customer_phone'],
            ':address' => $data['customer_address'],
            ':total' => $total
        ]);
        
        return $this->db->lastInsertId();
    }

    private function createOrderDetails($orderId) {
        $sql = "INSERT INTO order_details (order_id, product_id, quantity, price) 
                VALUES (:order_id, :product_id, :quantity, :price)";
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($_SESSION['cart'] as $item) {
            $stmt->execute([
                ':order_id' => $orderId,
                ':product_id' => $item['id'],
                ':quantity' => $item['quantity'],
                ':price' => $item['price']
            ]);
        }
    }

    private function validateCheckoutForm($data) {
        $errors = [];

        $customerName = trim($data['customer_name'] ?? '');
        $customerEmail = trim($data['customer_email'] ?? '');
        $customerPhone = trim($data['customer_phone'] ?? '');
        $customerAddress = trim($data['customer_address'] ?? '');

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

        if (empty($_SESSION['cart'])) {
            $errors['cart'] = 'Giỏ hàng trống';
        }

        return $errors;
    }

    private function calculateTotal($cartItems) {
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    public function orderSuccess($orderId) {
        require_once 'app/models/OrderModel.php';
        $orderModel = new OrderModel($this->db);
        $order = $orderModel->getOrder($orderId);
        
        if (!$order) {
            $this->setFlashMessage('error', 'Không tìm thấy đơn hàng');
            header('Location: /project1/Product/list');
            exit();
        }

        include 'app/views/cart/order_success.php';
    }
}