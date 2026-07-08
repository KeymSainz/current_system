-- ============================================================
--  Fix&Go — Migration: PayMongo Payments Table
--  Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

USE fixandgo;

CREATE TABLE IF NOT EXISTS owner_payments (
  id            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  owner_id      INT UNSIGNED    NOT NULL,
  reference     VARCHAR(50)     NOT NULL,          -- e.g. FG-ABC123DEF4
  paymongo_id   VARCHAR(100)    NULL,              -- PayMongo checkout session ID
  amount        DECIMAL(12,2)   NOT NULL,          -- total in PHP
  currency      VARCHAR(3)      NOT NULL DEFAULT 'PHP',
  status        ENUM('pending','paid','failed','cancelled','refunded')
                                NOT NULL DEFAULT 'pending',
  checkout_url  VARCHAR(500)    NULL,              -- PayMongo hosted checkout URL
  product_ids   JSON            NULL,              -- array of supplier_product IDs purchased
  paid_at       DATETIME        NULL,
  created_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
                                         ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_reference (reference),
  INDEX idx_owner  (owner_id),
  INDEX idx_status (status),
  CONSTRAINT fk_op_owner
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'owner_payments table created.' AS status;
