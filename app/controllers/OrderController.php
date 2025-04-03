<?php
namespace App\Controllers;

use App\Models\OrderModel;
use App\Config\Database;
use App\Helpers\SessionHelper;
use Exception;

class OrderController {
    private $orderModel;
    private $db;

    public function __construct() {
        SessionHelper::init();
        
        $database = new Database();
        $this->db = $database->getConnection();
        $this->orderModel = new OrderModel($this->db);
    }

    private function setFlashMessage($type, $message) {
        SessionHelper::setFlash($type, $message);
    }

    public function list() {
        try {
            // Validate and sanitize page number
            $page = max(1, isset($_GET['page']) ? (int)$_GET['page'] : 1);
            $perPage = 10;

            $totalOrders = $this->orderModel->getOrderCount();
            $totalPages = max(1, ceil($totalOrders / $perPage));

            // Ensure page number doesn't exceed total pages
            $page = min($page, $totalPages);

            $orders = $this->orderModel->listOrders($page, $perPage);

            include 'app/views/order/list.php';
        } catch (Exception $e) {
            error_log($e->getMessage());
            $this->setFlashMessage('error', 'Có lỗi xảy ra khi tải danh sách đơn hàng');
            $orders = [];
            $totalPages = 1;
            include 'app/views/order/list.php';
        }
    }

    public function view($orderId) {
        if (!$orderId || !is_numeric($orderId)) {
            $this->setFlashMessage('error', 'Mã đơn hàng không hợp lệ');
            header('Location: /project1/Order/list');
            exit();
        }

        try {
            $order = $this->orderModel->getOrder($orderId);
            if (!$order) {
                $this->setFlashMessage('error', 'Đơn hàng không tồn tại');
                header('Location: /project1/Order/list');
                exit();
            }

            include 'app/views/order/view.php';
        } catch (Exception $e) {
            error_log($e->getMessage());
            $this->setFlashMessage('error', 'Có lỗi xảy ra khi tải thông tin đơn hàng');
            header('Location: /project1/Order/list');
            exit();
        }
    }

    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Phương thức không hợp lệ');
            header('Location: /project1/Order/list');
            exit();
        }

        $orderId = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
        $status = $_POST['status'] ?? null;
        $validStatuses = ['pending', 'processing', 'completed', 'cancelled'];

        if (!$orderId || !$status || !in_array($status, $validStatuses)) {
            $this->setFlashMessage('error', 'Thông tin cập nhật không hợp lệ');
            header('Location: /project1/Order/list');
            exit();
        }

        try {
            $this->orderModel->updateOrderStatus($orderId, $status);
            $this->setFlashMessage('success', 'Cập nhật trạng thái đơn hàng thành công');
        } catch (Exception $e) {
            error_log($e->getMessage());
            $this->setFlashMessage('error', 'Không thể cập nhật trạng thái đơn hàng');
        }

        header('Location: /project1/Order/view/' . $orderId);
        exit();
    }
}