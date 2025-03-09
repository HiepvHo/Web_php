<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'app/config/database.php';

try {
    echo "<h2>Database Connection Test</h2>";
    
    // Create database connection
    $database = new Database();
    $db = $database->getConnection();
    echo "<p style='color:green'>✓ Database connection successful</p>";

    // Test category table
    $stmt = $db->query("SELECT COUNT(*) as count FROM category");
    $result = $stmt->fetch();
    echo "<p>Categories found: " . $result->count . "</p>";
    
    // Show all categories
    $stmt = $db->query("SELECT * FROM category");
    $categories = $stmt->fetchAll();
    echo "<h3>Categories:</h3>";
    echo "<pre>" . print_r($categories, true) . "</pre>";

    // Test product table
    $stmt = $db->query("SELECT COUNT(*) as count FROM product");
    $result = $stmt->fetch();
    echo "<p>Products found: " . $result->count . "</p>";
    
    // Show all products with categories
    $stmt = $db->query("
        SELECT p.*, c.name as category_name 
        FROM product p 
        LEFT JOIN category c ON p.category_id = c.id
    ");
    $products = $stmt->fetchAll();
    echo "<h3>Products:</h3>";
    echo "<pre>" . print_r($products, true) . "</pre>";

} catch(Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
    
    if ($e instanceof PDOException) {
        echo "<p>SQL State: " . $e->getCode() . "</p>";
    }
}