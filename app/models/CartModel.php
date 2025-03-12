<?php
class CartModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createCart($sessionId) {
        $sql = "INSERT INTO cart (session_id) VALUES (?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sessionId]);
        return $this->db->lastInsertId();
    }

    public function getCart($sessionId) {
        $sql = "SELECT cart.*, ci.id as cart_item_id, ci.quantity, 
                p.id as product_id, p.name, p.price, p.image
                FROM cart 
                LEFT JOIN cart_item ci ON cart.id = ci.cart_id
                LEFT JOIN product p ON ci.product_id = p.id
                WHERE cart.session_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sessionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addItem($cartId, $productId, $quantity = 1) {
        // Check if item already exists in cart
        $sql = "SELECT id, quantity FROM cart_item WHERE cart_id = ? AND product_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$cartId, $productId]);
        $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingItem) {
            // Update quantity if item exists
            $newQuantity = $existingItem['quantity'] + $quantity;
            $sql = "UPDATE cart_item SET quantity = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$newQuantity, $existingItem['id']]);
        } else {
            // Add new item if it doesn't exist
            $sql = "INSERT INTO cart_item (cart_id, product_id, quantity) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$cartId, $productId, $quantity]);
        }
    }

    public function updateItemQuantity($cartItemId, $quantity) {
        $sql = "UPDATE cart_item SET quantity = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$quantity, $cartItemId]);
    }

    public function removeItem($cartItemId) {
        $sql = "DELETE FROM cart_item WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$cartItemId]);
    }

    public function clearCart($cartId) {
        $sql = "DELETE FROM cart_item WHERE cart_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$cartId]);
    }

    public function getCartTotal($cartId) {
        $sql = "SELECT SUM(p.price * ci.quantity) as total
                FROM cart_item ci
                JOIN product p ON ci.product_id = p.id
                WHERE ci.cart_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$cartId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function getCartId($sessionId) {
        $sql = "SELECT id FROM cart WHERE session_id = ? ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sessionId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return $this->createCart($sessionId);
        }
        
        return $result['id'];
    }
}