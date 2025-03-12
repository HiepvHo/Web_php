<?php
class OrderModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createOrder($orderData) {
        try {
            $this->db->beginTransaction();

            // Create order
            $sql = "INSERT INTO `order` (customer_name, customer_email, customer_phone, 
                    customer_address, total_amount, status) 
                    VALUES (?, ?, ?, ?, ?, 'pending')";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $orderData['customer_name'],
                $orderData['customer_email'],
                $orderData['customer_phone'],
                $orderData['customer_address'],
                $orderData['total_amount']
            ]);

            $orderId = $this->db->lastInsertId();

            // Create order items
            $sql = "INSERT INTO order_item (order_id, product_id, quantity, price) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);

            foreach ($orderData['items'] as $item) {
                $stmt->execute([
                    $orderId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['price']
                ]);
            }

            $this->db->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getOrder($orderId) {
        $sql = "SELECT o.*, 
                oi.product_id, oi.quantity, oi.price,
                p.name as product_name
                FROM `order` o
                LEFT JOIN order_item oi ON o.id = oi.order_id
                LEFT JOIN product p ON oi.product_id = p.id
                WHERE o.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateOrderStatus($orderId, $status) {
        $sql = "UPDATE `order` SET status = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $orderId]);
    }

    public function listOrders($page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT o.*, COUNT(oi.id) as item_count
                FROM `order` o
                LEFT JOIN order_item oi ON o.id = oi.order_id
                GROUP BY o.id
                ORDER BY o.created_at DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$perPage, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderCount() {
        $sql = "SELECT COUNT(*) as count FROM `order`";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    public function getOrdersByStatus($status) {
        $sql = "SELECT * FROM `order` WHERE status = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}