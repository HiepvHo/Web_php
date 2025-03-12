<?php
require_once 'app/models/OrderModel.php';
require_once 'app/config/Database.php';

class OrderController {
    private $orderModel;
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->orderModel = new OrderModel($this->db);
    }

    public function list() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 10;

        $orders = $this->orderModel->listOrders($page, $perPage);
        $totalOrders = $this->orderModel->getOrderCount();
        $totalPages = ceil($totalOrders / $perPage);

        include 'app/views/order/list.php';
    }

    public function view($orderId) {
        if (!$orderId) {
            header('Location: /project1/Order/list');
            exit();
        }

        $order = $this->orderModel->getOrder($orderId);
        if (!$order) {
            $_SESSION['error'] = 'Đơn hàng không tồn tại';
            header('Location: /project1/Order/list');
            exit();
        }

        include 'app/views/order/view.php';
    }

    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /project1/Order/list');
            exit();
        }

        $orderId = $_POST['order_id'] ?? null;
        $status = $_POST['status'] ?? null;

        if (!$orderId || !$status) {
            $_SESSION['error'] = 'Thiếu thông tin cập nhật';
            header('Location: /project1/Order/list');
            exit();
        }

        try {
            $this->orderModel->updateOrderStatus($orderId, $status);
            $_SESSION['success'] = 'Cập nhật trạng thái đơn hàng thành công';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Không thể cập nhật trạng thái đơn hàng';
            error_log($e->getMessage());
        }

        header('Location: /project1/Order/view/' . $orderId);
        exit();
    }
}