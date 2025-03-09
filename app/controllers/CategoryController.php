<?php
require_once 'app/models/CategoryModel.php';
require_once 'app/config/Database.php';

class CategoryController {
    private $conn;
    private $categoryModel;

    public function __construct() {
        // Kết nối đến cơ sở dữ liệu
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->categoryModel = new CategoryModel($this->conn);
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
            $name = $_POST['name'];
            $description = $_POST['description'];

            if (empty($name)) {
                $errors[] = 'Tên danh mục là bắt buộc.';
            }

            if (count($errors) == 0) {
                if ($this->categoryModel->addCategory($name, $description)) {
                    header('Location: /project1/Category/list');
                    exit();
                } else {
                    $errors[] = 'Thêm danh mục không thành công.';
                }
            }
        }

        include 'app/views/category/add.php';
    }

    public function edit($id) {
        $category = $this->categoryModel->getCategoryById($id);
        if (!$category) {
            die('Danh mục không tồn tại');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'];

            if ($this->categoryModel->updateCategory($id, $name, $description)) {
                header('Location: /project1/Category/list');
                exit();
            } else {
                $errors[] = 'Cập nhật danh mục không thành công.';
            }
        }

        include 'app/views/category/edit.php';
    }

    public function delete($id) {
        if ($this->categoryModel->deleteCategory($id)) {
            header('Location: /project1/Category/list');
            exit();
        } else {
            die('Xóa danh mục không thành công');
        }
    }
}
?>
