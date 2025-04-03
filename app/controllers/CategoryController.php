<?php
namespace App\Controllers;

use App\Models\CategoryModel;
use App\Config\Database;
use App\Helpers\SessionHelper;
use Exception;

class CategoryController {
    private $conn;
    private $categoryModel;

    public function __construct() {
        // Initialize session
        SessionHelper::init();
        
        // Kết nối đến cơ sở dữ liệu
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->categoryModel = new CategoryModel($this->conn);
    }

    private function setFlashMessage($type, $message) {
        SessionHelper::setFlash($type, $message);
    }

    public function index() {
        $this->list();
    }

    public function list()
    {
        $categoryModel = new CategoryModel($this->conn); // Tạo đối tượng CategoryModel
        $categories = $categoryModel->getCategories(); // Lấy tất cả các danh mục
    
        // Truyền dữ liệu vào view
        include 'app/views/category/list.php';
    }
    

    public function add() {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if (empty($name)) {
                $errors['name'] = 'Tên danh mục là bắt buộc';
            }

            if (empty($errors)) {
                try {
                    if ($this->categoryModel->addCategory($name, $description)) {
                        $this->setFlashMessage('success', 'Thêm danh mục thành công');
                        header('Location: /project1/Category/list');
                        exit();
                    }
                } catch (Exception $e) {
                    $errors['system'] = 'Không thể thêm danh mục';
                    error_log($e->getMessage());
                }
            }
        }

        include 'app/views/category/add.php';
    }

    public function edit($id) {
        $errors = [];
        $category = $this->categoryModel->getCategoryById($id);
        
        if (!$category) {
            $this->setFlashMessage('error', 'Danh mục không tồn tại');
            header('Location: /project1/Category/list');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if (empty($name)) {
                $errors['name'] = 'Tên danh mục là bắt buộc';
            }

            if (empty($errors)) {
                try {
                    if ($this->categoryModel->updateCategory($id, $name, $description)) {
                        $this->setFlashMessage('success', 'Cập nhật danh mục thành công');
                        header('Location: /project1/Category/list');
                        exit();
                    }
                } catch (Exception $e) {
                    $errors['system'] = 'Không thể cập nhật danh mục';
                    error_log($e->getMessage());
                }
            }
        }

        include 'app/views/category/edit.php';
    }

    public function delete($id) {
        try {
            $category = $this->categoryModel->getCategoryById($id);
            if (!$category) {
                $response = ['success' => false, 'message' => 'Danh mục không tồn tại'];
            } else {
                if ($this->categoryModel->deleteCategory($id)) {
                    $response = ['success' => true, 'message' => 'Xóa danh mục thành công'];
                } else {
                    $response = ['success' => false, 'message' => 'Không thể xóa danh mục'];
                }
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $response = ['success' => false, 'message' => 'Có lỗi xảy ra khi xóa danh mục'];
        }

        // Return JSON response for AJAX request
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
}
