-- Create database
CREATE DATABASE IF NOT EXISTS my_store DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE my_store;

-- Drop existing tables if they exist
DROP TABLE IF EXISTS product;
DROP TABLE IF EXISTS category;

-- Create category table
CREATE TABLE category (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create product table
CREATE TABLE product (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    category_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES category(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample categories
INSERT INTO category (name, description) VALUES
('Điện thoại', 'Các loại điện thoại di động'),
('Laptop', 'Máy tính xách tay'),
('Phụ kiện', 'Phụ kiện điện tử'),
('Máy tính bảng', 'Các loại máy tính bảng');

-- Insert sample products
INSERT INTO product (name, description, price, category_id) VALUES
('iPhone 13', 'iPhone 13 mới nhất với camera kép', 23990000, 1),
('Samsung Galaxy S21', 'Điện thoại Samsung cao cấp', 19990000, 1),
('MacBook Pro M1', 'Laptop Apple với chip M1', 29990000, 2),
('Dell XPS 13', 'Laptop mỏng nhẹ cao cấp', 25990000, 2),
('Tai nghe AirPods Pro', 'Tai nghe không dây chống ồn', 4990000, 3),
('iPad Pro 2021', 'Máy tính bảng mạnh mẽ', 20990000, 4),
('Chuột Magic Mouse', 'Chuột không dây của Apple', 2490000, 3),
('Samsung Tab S7', 'Máy tính bảng Android cao cấp', 15990000, 4);