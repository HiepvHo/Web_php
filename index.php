<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base path
define('BASE_PATH', __DIR__);

// Set up error logging
ini_set('log_errors', 1);
ini_set('error_log', BASE_PATH . '/error.log');

try {
    // Autoload function
    spl_autoload_register(function ($className) {
        $file = BASE_PATH . '/app/' . str_replace('\\', '/', $className) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    });

    // Include model and controller base classes
    require_once 'app/models/CategoryModel.php';
    require_once 'app/models/Productmodel.php';

    // Get URL from GET parameter
    $url = $_GET['url'] ?? '';
    $url = rtrim($url, '/');
    $url = filter_var($url, FILTER_SANITIZE_URL);
    $url = explode('/', $url);

    // Debug URL routing
    error_log("URL parts: " . print_r($url, true));

    // Determine controller and action
    $controllerName = isset($url[0]) && $url[0] != '' ? ucfirst($url[0]) . 'Controller' : 'ProductController';
    $action = isset($url[1]) && $url[1] != '' ? $url[1] : 'list';

    error_log("Loading controller: $controllerName, action: $action");

    // Check if controller exists
    $controllerFile = BASE_PATH . '/app/controllers/' . $controllerName . '.php';
    if (!file_exists($controllerFile)) {
        throw new Exception('Controller not found: ' . $controllerName);
    }

    // Include controller file
    require_once $controllerFile;

    // Initialize controller
    $controller = new $controllerName();

    // Check if action exists
    if (!method_exists($controller, $action)) {
        throw new Exception('Action not found: ' . $action);
    }

    // Call action with remaining parameters
    $params = array_slice($url, 2);
    error_log("Calling $controllerName->$action with params: " . print_r($params, true));
    
    call_user_func_array([$controller, $action], $params);

} catch (Exception $e) {
    error_log("Application error: " . $e->getMessage());
    echo "<div style='color:red; padding:20px;'>";
    echo "<h2>Application Error</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    if (isset($e->errorInfo)) {
        echo "<pre>" . print_r($e->errorInfo, true) . "</pre>";
    }
    echo "</div>";
}
?>
