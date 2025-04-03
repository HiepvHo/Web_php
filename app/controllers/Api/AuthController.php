<?php
namespace App\Controllers\Api;

use App\Models\UserModel;
use App\Config\Database;
use App\Utils\JWTHandler;

class AuthController extends BaseController {
    private $userModel;
    private $db;

    public function __construct() {
        parent::__construct();
        $database = new Database();
        $this->db = $database->getConnection();
        $this->userModel = new UserModel($this->db);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->sendResponse(['error' => 'Method not allowed'], 405);
        }

        $data = $this->getRequestData();
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        // Validation
        if (empty($username) || empty($password)) {
            return $this->sendResponse(['error' => 'Username and password are required'], 400);
        }

        // Attempt login
        $user = $this->userModel->login($username, $password);
        if (!$user) {
            return $this->sendResponse(['error' => 'Invalid credentials'], 401);
        }

        // Generate JWT token
        $userData = [
            'id' => $user->id,
            'email' => $user->username
        ];
        
        $token = $this->jwtHandler->generateToken($userData);

        return $this->sendResponse([
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'username' => $user->username
            ]
        ]);
    }

    public function validateToken() {
        if (!$this->authenticate()) {
            return; // authenticate() will handle the error response
        }

        return $this->sendResponse([
            'message' => 'Token is valid',
            'user' => $this->getCurrentUser()
        ]);
    }
}