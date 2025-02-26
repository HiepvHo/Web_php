<?php

// Include model file
// require_once '/app/controllers/ProductController.php';

// Lấy URL từ tham số GET
$url = $_GET['url'] ?? ''; // Lấy giá trị url từ tham số GET
$url = rtrim($url, '/'); // Loại bỏ dấu "/" ở cuối
$url = filter_var($url, FILTER_SANITIZE_URL); // Lọc URL
$url = explode('/', $url); // Chia URL thành mảng

// Kiểm tra phần đầu tiên của URL để xác định controller
$controllerName = isset($url[0]) && $url[0] != '' ? ucfirst($url[0]) . 'Controller' : 'ProductController';

// Kiểm tra phần thứ hai của URL để xác định action
$action = isset($url[1]) && $url[1] != '' ? $url[1] : 'index';

// Kiểm tra xem controller có tồn tại không
if (!file_exists('app/controllers/' . $controllerName . '.php')) {
    die('Controller not found');
}

// Include controller file
require_once 'app/controllers/' . $controllerName . '.php';

// Khởi tạo controller
$controller = new $controllerName();

// Kiểm tra xem action có tồn tại trong controller không
if (!method_exists($controller, $action)) {
    die('Action not found');
}

// Gọi action với các tham số còn lại (nếu có)
call_user_func_array([$controller, $action], array_slice($url, 2));

?>
