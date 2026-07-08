-- Fix&Go — Technician Orders Migration
USE fixandgo;

-- Technician orders (purchases from suppliers/owners)
CREATE TABLE IF NOT EXISTS technician_orders (
  id                  INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  technician_id       INT UNSIGNED   NOT NULL,
  seller_id           INT UNSIGNED   NOT NULL,          -- supplier or owner
  seller_role         ENUM('supplier','owner') NOT NULL DEFAULT 'supplier',
  fulfillment_type    ENUM('pickup','delivery') NOT NULL DEFAULT 'delivery',
  delivery_address    TEXT           NULL,              -- full address for delivery
  payment_method      ENUM('cod','gcash','card') NOT NULL DEFAULT 'cod',
  payment_status      ENUM('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  order_status        ENUM('pending','confirmed','preparing','ready','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  subtotal            DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
  shipping_fee        DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
  total_amount        DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
  reference           VARCHAR(100)   NULL,
  paymongo_id         VARCHAR(200)   NULL,
  checkout_url        TEXT           NULL,
  notes               TEXT           NULL,
  seller_notes        TEXT           NULL,
  created_at          DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at          DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_tech   (technician_id),
  INDEX idx_seller (seller_id),
  INDEX idx_status (order_status),
  INDEX idx_ref    (reference),

  CONSTRAINT fk_to_tech   FOREIGN KEY (technician_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_to_seller FOREIGN KEY (seller_id)     REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Technician order items
CREATE TABLE IF NOT EXISTS technician_order_items (
  id          INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  order_id    INT UNSIGNED   NOT NULL,
  product_id  INT UNSIGNED   NOT NULL,
  product_name VARCHAR(255)  NOT NULL,
  category    VARCHAR(100)   NULL,
  unit_price  DECIMAL(10,2)  NOT NULL,
  quantity    INT            NOT NULL DEFAULT 1,
  subtotal    DECIMAL(10,2)  NOT NULL,

  PRIMARY KEY (id),
  INDEX idx_order   (order_id),
  INDEX idx_product (product_id),

  CONSTRAINT fk_toi_order   FOREIGN KEY (order_id)   REFERENCES technician_orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_toi_product FOREIGN KEY (product_id) REFERENCES supplier_products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'Technician orders migration complete.' AS status;
