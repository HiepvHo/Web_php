<?php
namespace App\Models;

use PDO;
use PDOException;

class UserModel {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($username, $password, $phone = null, $address = null) {
        try {
            // Check if username already exists
            if ($this->getUserByUsername($username)) {
                return false;
            }

            $query = "INSERT INTO users (username, password, phone, address) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            
            // Hash password before storing
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            return $stmt->execute([$username, $hashed_password, $phone, $address]);
        } catch(PDOException $e) {
            error_log("Error registering user: " . $e->getMessage());
            return false;
        }
    }

    public function login($username, $password) {
        try {
            $user = $this->getUserByUsername($username);
            
            if ($user && password_verify($password, $user->password)) {
                return $user;
            }
            
            return false;
        } catch(PDOException $e) {
            error_log("Error logging in: " . $e->getMessage());
            return false;
        }
    }

    public function getUserByUsername($username) {
        try {
            $query = "SELECT * FROM users WHERE username = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$username]);
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            error_log("Error getting user by username: " . $e->getMessage());
            return false;
        }
    }

    public function getUserById($id) {
        try {
            $query = "SELECT * FROM users WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            error_log("Error getting user by ID: " . $e->getMessage());
            return false;
        }
    }
}