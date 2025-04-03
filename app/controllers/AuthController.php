<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Config\Database;
use App\Helpers\SessionHelper;

class AuthController {
    private $userModel;
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->userModel = new UserModel($this->db);
    }

    public function register() {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');

            // Validation
            if (empty($username)) {
                $errors['username'] = 'Username is required';
            } elseif (strlen($username) < 3) {
                $errors['username'] = 'Username must be at least 3 characters';
            }

            if (empty($password)) {
                $errors['password'] = 'Password is required';
            } elseif (strlen($password) < 6) {
                $errors['password'] = 'Password must be at least 6 characters';
            }

            if ($password !== $confirm_password) {
                $errors['confirm_password'] = 'Passwords do not match';
            }

            if (empty($phone)) {
                $errors['phone'] = 'Phone number is required';
            } elseif (!preg_match('/^[0-9]{10,11}$/', $phone)) {
                $errors['phone'] = 'Invalid phone number format';
            }

            if (empty($address)) {
                $errors['address'] = 'Address is required';
            }

            // If no errors, attempt registration
            if (empty($errors)) {
                if ($this->userModel->register($username, $password, $phone, $address)) {
                    SessionHelper::setFlash('success', 'Registration successful! Please login.');
                    header('Location: /project1/Auth/login');
                    exit();
                } else {
                    $errors['register'] = 'Username already exists or registration failed';
                }
            }
        }

        include 'app/views/auth/register.php';
    }

    public function login() {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            // Validation
            if (empty($username)) {
                $errors['username'] = 'Username is required';
            }

            if (empty($password)) {
                $errors['password'] = 'Password is required';
            }

            // If no errors, attempt login
            if (empty($errors)) {
                $user = $this->userModel->login($username, $password);
                if ($user) {
                    SessionHelper::init();
                    $_SESSION['user_id'] = $user->id;
                    $_SESSION['username'] = $user->username;
                    $_SESSION['is_logged_in'] = true;

                    header('Location: /project1/Product/list');
                    exit();
                } else {
                    $errors['login'] = 'Invalid username or password';
                }
            }
        }

        include 'app/views/auth/login.php';
    }

    public function logout() {
        SessionHelper::init();
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['is_logged_in']);
        
        SessionHelper::destroy();
        
        header('Location: /project1/Auth/login');
        exit();
    }
}