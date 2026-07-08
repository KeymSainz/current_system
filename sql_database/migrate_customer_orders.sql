-- ============================================================
--  Fix&Go — Customer Orders & Reviews Migration
--  Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

USE fixandgo;

-- ── 1. CUSTOMER ORDERS ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS customer_orders (
  id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  customer_id     INT UNSIGNED    NOT NULL,
  product_id      INT UNSIGNED    NOT NULL,
  quantity        INT UNSIGNED    NOT NULL DEFAULT 1,
  unit_price      DECIMAL(10,2)   NOT NULL,
  total_amount    DECIMAL(10,2)   NOT NULL,
  status          ENUM('pending','processing','completed','cancelled')
                                  NOT NULL DEFAULT 'pending',
  payment_method  ENUM('cod','gcash','card') NOT NULL DEFAULT 'cod',
  notes           TEXT            NULL,
  created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
                                           ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_customer  (customer_id),
  INDEX idx_product   (product_id),
  INDEX idx_status    (status),
  CONSTRAINT fk_co_customer
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_co_product
    FOREIGN KEY (product_id) REFERENCES supplier_products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 2. PRODUCT REVIEWS ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS product_reviews (
  id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  product_id  INT UNSIGNED    NOT NULL,
  customer_id INT UNSIGNED    NOT NULL,
  order_id    INT UNSIGNED    NULL,
  rating      TINYINT UNSIGNED NOT NULL DEFAULT 5,
  review_text TEXT            NULL,
  created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_pr_product  (product_id),
  INDEX idx_pr_customer (customer_id),
  CONSTRAINT fk_pr_product
    FOREIGN KEY (product_id) REFERENCES supplier_products(id) ON DELETE CASCADE,
  CONSTRAINT fk_pr_customer
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_pr_order
    FOREIGN KEY (order_id) REFERENCES customer_orders(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'customer_orders and product_reviews tables created.' AS status;
