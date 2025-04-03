<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

use App\Helpers\SessionHelper;

// Define base path
define('BASE_PATH', __DIR__);

// Set up error logging
ini_set('log_errors', 1);
ini_set('error_log', BASE_PATH . '/error.log');

// Require Composer's autoloader
require 'vendor/autoload.php';

try {

    // Set CORS headers for API requests
    if (strpos($_GET['url'] ?? '', 'api/') === 0) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: *');
        header('Content-Type: application/json');
        
        // Handle preflight requests
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }
    } else {
        // Initialize session for non-API requests
        SessionHelper::init();
    }

    // Get URL from GET parameter
    $url = $_GET['url'] ?? '';
    $url = rtrim($url, '/');
    $url = filter_var($url, FILTER_SANITIZE_URL);
    $url = explode('/', $url);

    // Debug URL routing
    error_log("URL parts: " . print_r($url, true));

    // Determine controller and action
    if (isset($url[0]) && $url[0] === 'api') {
        // Handle API routes
        $controllerName = isset($url[1]) ? ucfirst($url[1]) . 'Controller' : 'ProductController';
        $controllerFile = BASE_PATH . '/app/controllers/Api/' . $controllerName . '.php';
        
        // Map HTTP methods to actions
        $method = $_SERVER['REQUEST_METHOD'];
        $params = array_slice($url, 2);
        
        switch ($method) {
            case 'GET':
                $action = !empty($params) ? 'get' : 'index';
                break;
            case 'POST':
                // Special case for auth endpoints
                if ($url[1] === 'auth') {
                    $action = $params[0] ?? 'login';
                } else {
                    $action = 'create';
                }
                break;
            case 'PUT':
                $action = 'update';
                break;
            case 'DELETE':
                $action = 'delete';
                break;
            default:
                $action = 'index';
        }
    } else {
        // Handle web routes
        $controllerName = isset($url[0]) && $url[0] != '' ? ucfirst($url[0]) . 'Controller' : 'ProductController';
        $action = isset($url[1]) && $url[1] != '' ? $url[1] : 'list';
        $controllerFile = BASE_PATH . '/app/controllers/' . $controllerName . '.php';
        $params = array_slice($url, 2);
    }

    error_log("Loading controller: $controllerName, action: $action");
    if (!file_exists($controllerFile)) {
        throw new Exception('Controller not found: ' . $controllerName);
    }

    // Include controller file
    require_once $controllerFile;

    // Initialize controller with namespace
    $controllerClass = isset($url[0]) && $url[0] === 'api'
        ? "\\App\\Controllers\\Api\\{$controllerName}"
        : "\\App\\Controllers\\{$controllerName}";
    
    $controller = new $controllerClass();

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
