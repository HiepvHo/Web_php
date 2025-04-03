<?php
namespace App\Models;

use PDO;

class CategoryModel {
    private $conn;
    private $table_name = "category";

    // Constructor nhận kết nối cơ sở dữ liệu
    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả danh mục
    public function getCategories() {
        $query = "SELECT id, name, description, status, created_at FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }

    // Lấy danh mục theo ID
    public function getCategoryById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result;
    }

    // Thêm danh mục mới
    public function addCategory($name, $description) {
        $query = "INSERT INTO " . $this->table_name . " (name, description) VALUES (:name, :description)";
        $stmt = $this->conn->prepare($query);
        $name = htmlspecialchars(strip_tags($name));
        $description = htmlspecialchars(strip_tags($description));
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Cập nhật danh mục
    public function updateCategory($id, $name, $description) {
        $query = "UPDATE " . $this->table_name . " SET name=:name, description=:description WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $name = htmlspecialchars(strip_tags($name));
        $description = htmlspecialchars(strip_tags($description));
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Xóa danh mục
    public function deleteCategory($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
