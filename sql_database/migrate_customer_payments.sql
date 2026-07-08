-- ============================================================
--  Fix&Go — Customer Payments Migration
--  Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

USE fixandgo;

CREATE TABLE IF NOT EXISTS customer_payments (
  id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  customer_id     INT UNSIGNED    NOT NULL,
  reference       VARCHAR(64)     NOT NULL UNIQUE,
  paymongo_id     VARCHAR(128)    NULL     COMMENT 'PayMongo checkout session ID',
  amount          DECIMAL(10,2)   NOT NULL COMMENT 'Total in PHP pesos',
  currency        CHAR(3)         NOT NULL DEFAULT 'PHP',
  status          ENUM('pending','paid','cancelled','failed')
                                  NOT NULL DEFAULT 'pending',
  payment_method  ENUM('gcash','card') NOT NULL DEFAULT 'gcash',
  checkout_url    TEXT            NULL,
  cart_snapshot   JSON            NULL     COMMENT 'Cart items at time of payment',
  paid_at         DATETIME        NULL,
  created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
                                           ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_cp_customer  (customer_id),
  INDEX idx_cp_reference (reference),
  INDEX idx_cp_status    (status),
  CONSTRAINT fk_cp_customer
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'customer_payments table created.' AS status;
