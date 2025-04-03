<?php
namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Config\Database;
use Exception;

class ProductController {
    private $productModel;
    private $categoryModel;
    private $db;
    private $uploadDir;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->productModel = new ProductModel($this->db);
        $this->categoryModel = new CategoryModel($this->db);
        
        // Define upload directory relative to project root
        $this->uploadDir = 'public/images/uploads/';
        
        // Create upload directory if it doesn't exist
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    public function list() {
        $products = $this->productModel->getProducts();
        include 'app/views/product/list.php';
    }

    private function handleImageUpload($file) {
        try {
            if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
                return [
                    'success' => false,
                    'error' => 'No file uploaded or upload error occurred'
                ];
            }

            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file['type'], $allowedTypes)) {
                return [
                    'success' => false,
                    'error' => 'Only JPG, PNG and GIF files are allowed'
                ];
            }

            // Validate file size (5MB max)
            if ($file['size'] > 5 * 1024 * 1024) {
                return [
                    'success' => false,
                    'error' => 'File size must be less than 5MB'
                ];
            }

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extension;
            $filepath = $this->uploadDir . $filename;

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                error_log("Failed to move uploaded file to: " . $filepath);
                return [
                    'success' => false,
                    'error' => 'Failed to save the file'
                ];
            }

            return [
                'success' => true,
                'filename' => 'images/uploads/' . $filename
            ];
        } catch (Exception $e) {
            error_log("Image upload error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'An error occurred while uploading the file'
            ];
        }
    }

    public function add() {
        $errors = [];
        $categories = $this->categoryModel->getCategories();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = str_replace(',', '', $_POST['price'] ?? '');
            $category_id = $_POST['category_id'] ?? '';

            // Basic validation
            if (empty($name)) {
                $errors['name'] = 'Tên sản phẩm không được để trống';
            }
            if (empty($description)) {
                $errors['description'] = 'Mô tả không được để trống';
            }
            if (!is_numeric($price) || $price <= 0) {
                $errors['price'] = 'Giá sản phẩm không hợp lệ';
            }
            if (empty($category_id)) {
                $errors['category_id'] = 'Vui lòng chọn danh mục';
            }

            // Handle image upload if no errors so far
            $image = null;
            if (empty($errors) && isset($_FILES['image'])) {
                $uploadResult = $this->handleImageUpload($_FILES['image']);
                if (!$uploadResult['success']) {
                    $errors['image'] = $uploadResult['error'];
                } else {
                    $image = $uploadResult['filename'];
                }
            }

            // If no errors, add product
            if (empty($errors)) {
                if ($this->productModel->addProduct($name, $description, $price, $category_id, $image)) {
                    header('Location: /project1/Product/list');
                    exit();
                } else {
                    $errors['database'] = 'Không thể thêm sản phẩm';
                }
            }
        }

        include 'app/views/product/add.php';
    }

    public function edit($id) {
        $product = $this->productModel->getProductById($id);
        if (!$product) {
            header('Location: /project1/Product/list');
            exit();
        }

        $categories = $this->categoryModel->getCategories();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = str_replace(',', '', $_POST['price'] ?? '');
            $category_id = $_POST['category_id'] ?? '';
            
            // Keep existing image by default
            $image = $product->image;

            // Handle new image upload if provided
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->handleImageUpload($_FILES['image']);
                if (!$uploadResult['success']) {
                    $errors['image'] = $uploadResult['error'];
                } else {
                    // Delete old image if exists
                    if ($product->image && file_exists($product->image)) {
                        unlink($product->image);
                    }
                    $image = $uploadResult['filename'];
                }
            }

            if (empty($errors)) {
                if ($this->productModel->updateProduct($id, $name, $description, $price, $category_id, $image)) {
                    header('Location: /project1/Product/list');
                    exit();
                } else {
                    $errors['database'] = 'Không thể cập nhật sản phẩm';
                }
            }
        }

        include 'app/views/product/edit.php';
    }

    public function delete($id) {
        $product = $this->productModel->getProductById($id);
        if ($product && $product->image) {
            $imagePath = $product->image;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        if ($this->productModel->deleteProduct($id)) {
            header('Location: /project1/Product/list');
            exit();
        }
    }
}
