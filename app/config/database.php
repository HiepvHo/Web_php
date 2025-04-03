<?php
namespace App\Config;

use PDO;
use PDOException;
use Exception;

class Database {
    private $host = "localhost";
    private $db_name = "my_store";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        try {
            // Debug connection parameters
            error_log("Attempting database connection with:");
            error_log("Host: " . $this->host);
            error_log("Database: " . $this->db_name);
            error_log("Username: " . $this->username);

            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                $this->username, 
                $this->password
            );

            // Set error mode
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Set fetch mode
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            
            // Set charset
            $this->conn->exec("SET NAMES 'utf8'");
            $this->conn->exec("SET CHARACTER SET utf8");
            $this->conn->exec("SET CHARACTER_SET_CONNECTION=utf8");

            // Test the connection
            $test = $this->testConnection();
            if ($test === true) {
                error_log("Database connection successful");
                return $this->conn;
            } else {
                error_log("Database connection successful but test failed: " . $test);
                throw new Exception($test);
            }
        } catch(PDOException $e) {
            error_log("Connection error: " . $e->getMessage());
            throw new Exception("Could not connect to database: " . $e->getMessage());
        }
    }

    private function testConnection() {
        try {
            // Test product table
            $stmt = $this->conn->query("SELECT COUNT(*) as count FROM product");
            $result = $stmt->fetch();
            error_log("Product count: " . $result->count);

            // Test category table
            $stmt = $this->conn->query("SELECT COUNT(*) as count FROM category");
            $result = $stmt->fetch();
            error_log("Category count: " . $result->count);

            // Test join query
            $stmt = $this->conn->query("
                SELECT p.*, c.name as category_name 
                FROM product p 
                LEFT JOIN category c ON p.category_id = c.id 
                LIMIT 1
            ");
            $result = $stmt->fetch();
            if ($result) {
                error_log("Sample product: " . print_r($result, true));
            } else {
                error_log("No products found in test query");
            }

            return true;
        } catch(PDOException $e) {
            return "Database test failed: " . $e->getMessage();
        }
    }
}
