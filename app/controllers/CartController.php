<?php
namespace App\Controllers;

use App\Models\CartModel;
use App\Models\ProductModel;
use App\Models\OrderModel;
use App\Config\Database;
use App\Helpers\SessionHelper;
use PDO;
use Exception;

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
        // Initialize cart data
        $cartId = $this->getOrCreateCartId();
        $cartItems = $this->cartModel->getCart($_SESSION['session_id']);
        $total = $this->cartModel->getCartTotal($cartId);
        
        // If cart is empty, redirect to cart view
        if (empty($cartItems)) {
            SessionHelper::setFlash('error', 'Giỏ hàng trống');
            header('Location: /project1/Cart/viewCart');
            exit();
        }

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

                    // Commit transaction
                    $this->db->commit();

                    // Redirect to success page with order ID
                    SessionHelper::setFlash('success', 'Đặt hàng thành công!');
                    header('Location: /project1/Cart/orderSuccess/' . $orderId);
                    exit();

                } catch (Exception $e) {
                    // Rollback transaction on error
                    $this->db->rollBack();
                    error_log("Checkout error: " . $e->getMessage());
                    SessionHelper::setFlash('error', 'Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại.');
                    $errors['checkout'] = 'Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại.';
                }
            }
        }
        
        // Show checkout page
        include 'app/views/cart/checkout.php';
    }

    private function createOrder($data) {
        $sql = "INSERT INTO `order` (customer_name, customer_email, customer_phone, customer_address, total_amount, status)
                VALUES (:name, :email, :phone, :address, :total, 'pending')";
        
        $cartId = $this->getOrCreateCartId();
        $total = $this->cartModel->getCartTotal($cartId);
        
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
        $cartId = $this->getOrCreateCartId();
        $cartItems = $this->cartModel->getCart($_SESSION['session_id']);
        
        $sql = "INSERT INTO order_item (order_id, product_id, quantity, price)
                VALUES (:order_id, :product_id, :quantity, :price)";
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($cartItems as $item) {
            $stmt->execute([
                ':order_id' => $orderId,
                ':product_id' => $item->product_id,
                ':quantity' => $item->quantity,
                ':price' => $item->price
            ]);
        }
        
        // Clear the cart after successful order creation
        $this->cartModel->clearCart($cartId);
        SessionHelper::updateCartCount(0);
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