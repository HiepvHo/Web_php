<?php
namespace App\Controllers\Api;

use App\Models\ProductModel;
use App\Config\Database;

class ProductController extends BaseController {
    private $productModel;
    private $db;

    public function __construct() {
        parent::__construct();
        header('Content-Type: application/json');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        $database = new Database();
        $this->db = $database->getConnection();
        $this->productModel = new ProductModel($this->db);
    }

    // GET /api/product
    public function index() {
        try {
            $products = $this->productModel->getProducts();
            $this->sendResponse(['success' => true, 'data' => $products]);
        } catch (Exception $e) {
            $this->sendResponse(['success' => false, 'error' => 'Failed to fetch products'], 500);
        }
    }

    // GET /api/product/123
    public function get($id = null) {
        if (!$id) {
            $this->sendResponse(['success' => false, 'error' => 'Product ID is required'], 400);
        }

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

    // POST /api/product
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendResponse(['success' => false, 'error' => 'Method not allowed'], 405);
        }

        // Require authentication
        if (!$this->authenticate()) {
            return;
        }

        try {
            $data = $this->getRequestData();
            
            if (!isset($data['name']) || !isset($data['price']) || !isset($data['category_id'])) {
                $this->sendResponse([
                    'success' => false, 
                    'error' => 'Missing required fields: name, price, and category_id are required'
                ], 400);
            }

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
                null
            );

            if ($result) {
                $this->sendResponse(['success' => true, 'message' => 'Product created successfully'], 201);
            } else {
                $this->sendResponse(['success' => false, 'error' => 'Failed to create product'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['success' => false, 'error' => 'Failed to create product: ' . $e->getMessage()], 500);
        }
    }

    // PUT /api/product/123
    public function update($id = null) {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            $this->sendResponse(['success' => false, 'error' => 'Method not allowed'], 405);
        }

        // Require authentication
        if (!$this->authenticate()) {
            return;
        }

        if (!$id) {
            $this->sendResponse(['success' => false, 'error' => 'Product ID is required'], 400);
        }

        try {
            $product = $this->productModel->getProductById($id);
            if (!$product) {
                $this->sendResponse(['success' => false, 'error' => 'Product not found'], 404);
            }

            $data = $this->getRequestData();
            
            if (!isset($data['name']) || !isset($data['price']) || !isset($data['category_id'])) {
                $this->sendResponse([
                    'success' => false,
                    'error' => 'Missing required fields: name, price, and category_id are required'
                ], 400);
            }

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
                null
            );

            if ($result) {
                $this->sendResponse(['success' => true, 'message' => 'Product updated successfully']);
            } else {
                $this->sendResponse(['success' => false, 'error' => 'Failed to update product'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['success' => false, 'error' => 'Failed to update product: ' . $e->getMessage()], 500);
        }
    }

    // DELETE /api/product/123
    public function delete($id = null) {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            $this->sendResponse(['success' => false, 'error' => 'Method not allowed'], 405);
        }

        // Require authentication
        if (!$this->authenticate()) {
            return;
        }

        if (!$id) {
            $this->sendResponse(['success' => false, 'error' => 'Product ID is required'], 400);
        }

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
            $this->sendResponse(['success' => false, 'error' => 'Failed to delete product: ' . $e->getMessage()], 500);
        }
    }
}