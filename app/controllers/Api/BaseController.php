<?php
namespace App\Controllers\Api;

use App\Utils\JWTHandler;

class BaseController {
    protected JWTHandler $jwtHandler;
    protected ?array $currentUser = null;

    public function __construct() {
        $this->jwtHandler = new JWTHandler();
    }

    protected function sendResponse($data, $status = 200) {
        if (!headers_sent()) {
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: *');
        }
        http_response_code($status);
        echo json_encode($data);
        exit();
    }

    protected function getRequestData() {
        return json_decode(file_get_contents('php://input'), true);
    }

    protected function authenticate() {
        $token = $this->jwtHandler->getTokenFromHeader();
        
        if (!$token) {
            $this->sendResponse(['error' => 'No token provided'], 401);
        }

        $payload = $this->jwtHandler->validateToken($token);
        
        if (!$payload) {
            $this->sendResponse(['error' => 'Invalid or expired token'], 401);
        }

        $this->currentUser = $payload;
        return true;
    }

    protected function getCurrentUser(): ?array {
        return $this->currentUser;
    }
}