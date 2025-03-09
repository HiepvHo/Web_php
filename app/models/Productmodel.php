<?php
class ProductModel {
    private $conn;
    private $table_name = "product";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getProducts() {
        try {
            $query = "SELECT p.*, c.name as category_name 
                     FROM product p 
                     LEFT JOIN category c ON p.category_id = c.id";
            $stmt = $this->conn->query($query);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            error_log("Error getting products: " . $e->getMessage());
            return [];
        }
    }

    public function getProductById($id) {
        try {
            $query = "SELECT * FROM product WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            error_log("Error getting product by ID: " . $e->getMessage());
            return null;
        }
    }

    public function addProduct($name, $description, $price, $category_id, $image = null) {
        try {
            $query = "INSERT INTO product (name, description, price, category_id, image) 
                     VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([
                $name,
                $description,
                $price,
                $category_id,
                $image
            ]);
            return $result ? true : false;
        } catch(PDOException $e) {
            error_log("Error adding product: " . $e->getMessage());
            return false;
        }
    }

    public function updateProduct($id, $name, $description, $price, $category_id, $image = null) {
        try {
            if ($image !== null) {
                $query = "UPDATE product 
                         SET name = ?, description = ?, price = ?, 
                             category_id = ?, image = ? 
                         WHERE id = ?";
                $params = [$name, $description, $price, $category_id, $image, $id];
            } else {
                $query = "UPDATE product 
                         SET name = ?, description = ?, price = ?, 
                             category_id = ? 
                         WHERE id = ?";
                $params = [$name, $description, $price, $category_id, $id];
            }
            
            $stmt = $this->conn->prepare($query);
            return $stmt->execute($params);
        } catch(PDOException $e) {
            error_log("Error updating product: " . $e->getMessage());
            return false;
        }
    }

    public function deleteProduct($id) {
        try {
            $query = "DELETE FROM product WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$id]);
        } catch(PDOException $e) {
            error_log("Error deleting product: " . $e->getMessage());
            return false;
        }
    }
}
