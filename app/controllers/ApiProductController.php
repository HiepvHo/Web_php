<?php
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php';
require_once 'app/config/Database.php';

class ApiProductController {
    private $productModel;
    private $db;

    public function __construct() {
        header('Content-Type: application/json');
        $database = new Database();
        $this->db = $database->getConnection();
        $this->productModel = new ProductModel($this->db);
    }

    private function sendResponse($data, $status = 200) {
        http_response_code($status);
        echo json_encode($data);
        exit();
    }

    private function getRequestData() {
        return json_decode(file_get_contents('php://input'), true);
    }

    // GET /api/products
    public function index() {
        try {
            $products = $this->productModel->getProducts();
            $this->sendResponse(['success' => true, 'data' => $products]);
        } catch (Exception $e) {
            $this->sendResponse(['success' => false, 'error' => 'Failed to fetch products'], 500);
        }
    }

    // GET /api/products/{id}
    public function get($id) {
        try {
            $product = $this->productModel->getProductById($id);
            if (!$product) {
                $this->sendResponse(['success' => false, 'error' => 'Product not found'], 404);
            }
            $this->sendResponse(['success' => true, 'data' => $product]);
        } catch (Exception $e) {
            $this->sendResponse(['success' => false, 'error' => 'Failed to fetch product'], 500);
        }
    }

    // POST /api/products
    public function create() {
        try {
            $data = $this->getRequestData();
            
            // Validate required fields
            if (!isset($data['name']) || !isset($data['price']) || !isset($data['category_id'])) {
                $this->sendResponse([
                    'success' => false, 
                    'error' => 'Missing required fields: name, price, and category_id are required'
                ], 400);
            }

            // Validate price
            if (!is_numeric($data['price']) || $data['price'] <= 0) {
                $this->sendResponse([
                    'success' => false,
                    'error' => 'Invalid price value'
                ], 400);
            }

            $result = $this->productModel->addProduct(
                $data['name'],
                $data['description'] ?? '',
                $data['price'],
                $data['category_id'],
                $data['image'] ?? null
            );

            if ($result) {
                $this->sendResponse(['success' => true, 'message' => 'Product created successfully'], 201);
            } else {
                $this->sendResponse(['success' => false, 'error' => 'Failed to create product'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['success' => false, 'error' => 'Failed to create product'], 500);
        }
    }

    // PUT /api/products/{id}
    public function update($id) {
        try {
            $product = $this->productModel->getProductById($id);
            if (!$product) {
                $this->sendResponse(['success' => false, 'error' => 'Product not found'], 404);
            }

            $data = $this->getRequestData();
            
            // Validate required fields
            if (!isset($data['name']) || !isset($data['price']) || !isset($data['category_id'])) {
                $this->sendResponse([
                    'success' => false,
                    'error' => 'Missing required fields: name, price, and category_id are required'
                ], 400);
            }

            // Validate price
            if (!is_numeric($data['price']) || $data['price'] <= 0) {
                $this->sendResponse([
                    'success' => false,
                    'error' => 'Invalid price value'
                ], 400);
            }

            $result = $this->productModel->updateProduct(
                $id,
                $data['name'],
                $data['description'] ?? $product->description,
                $data['price'],
                $data['category_id'],
                $data['image'] ?? $product->image
            );

            if ($result) {
                $this->sendResponse(['success' => true, 'message' => 'Product updated successfully']);
            } else {
                $this->sendResponse(['success' => false, 'error' => 'Failed to update product'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['success' => false, 'error' => 'Failed to update product'], 500);
        }
    }

    // DELETE /api/products/{id}
    public function delete($id) {
        try {
            $product = $this->productModel->getProductById($id);
            if (!$product) {
                $this->sendResponse(['success' => false, 'error' => 'Product not found'], 404);
            }

            if ($this->productModel->deleteProduct($id)) {
                $this->sendResponse(['success' => true, 'message' => 'Product deleted successfully']);
            } else {
                $this->sendResponse(['success' => false, 'error' => 'Failed to delete product'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['success' => false, 'error' => 'Failed to delete product'], 500);
        }
    }
}