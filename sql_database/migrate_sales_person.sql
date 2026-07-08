-- ============================================================
-- Fix&Go — Sales Person Migration
-- Run this after the main schema is set up
-- ============================================================

USE fixandgo;

-- Sales person products (products they upload for customers to see)
CREATE TABLE IF NOT EXISTS sales_products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sales_person_id INT(10) UNSIGNED NOT NULL,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  category VARCHAR(100),
  price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  stock INT DEFAULT 0,
  image_path VARCHAR(500),
  is_active TINYINT(1) DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_sales_products_person (sales_person_id),
  INDEX idx_sales_products_active (is_active),
  CONSTRAINT fk_sales_products_user FOREIGN KEY (sales_person_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Supply requests from sales person to supervisor
CREATE TABLE IF NOT EXISTS supply_requests (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sales_person_id INT(10) UNSIGNED NOT NULL,
  product_name VARCHAR(255) NOT NULL,
  category VARCHAR(100),
  quantity_requested INT NOT NULL DEFAULT 1,
  reason TEXT,
  status ENUM('pending','approved','rejected') DEFAULT 'pending',
  supervisor_notes TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_supply_requests_person (sales_person_id),
  INDEX idx_supply_requests_status (status),
  CONSTRAINT fk_supply_requests_user FOREIGN KEY (sales_person_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
