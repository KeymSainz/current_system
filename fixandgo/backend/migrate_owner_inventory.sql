-- ============================================================
--  Fix&Go — Migration: Owner Inventory Table
--  Run in phpMyAdmin: Import → select this file → Go
--  
--  This table stores products that owners have purchased from
--  suppliers via PayMongo payments.
-- ============================================================

USE fixandgo;

CREATE TABLE IF NOT EXISTS owner_inventory (
  id                INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  owner_id          INT UNSIGNED    NOT NULL,
  supplier_id       INT UNSIGNED    NOT NULL,
  supplier_product_id INT UNSIGNED  NOT NULL,  -- reference to supplier_products
  payment_id        INT UNSIGNED    NOT NULL,  -- reference to owner_payments
  
  -- Product details (snapshot at time of purchase)
  category          VARCHAR(100)    NOT NULL,
  brand             VARCHAR(100)    NULL,
  item_description  VARCHAR(255)    NOT NULL,
  qty               INT UNSIGNED    NOT NULL DEFAULT 1,
  unit_price        DECIMAL(10,2)   NOT NULL,  -- price per unit at purchase
  total_price       DECIMAL(10,2)   NOT NULL,  -- qty * unit_price
  image_path        VARCHAR(500)    NULL,
  notes             TEXT            NULL,
  
  purchased_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_at        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
                                             ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_owner       (owner_id),
  INDEX idx_supplier    (supplier_id),
  INDEX idx_payment     (payment_id),
  INDEX idx_product     (supplier_product_id),
  INDEX idx_category    (category),
  
  CONSTRAINT fk_oi_owner
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_oi_supplier
    FOREIGN KEY (supplier_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_oi_product
    FOREIGN KEY (supplier_product_id) REFERENCES supplier_products(id) ON DELETE CASCADE,
  CONSTRAINT fk_oi_payment
    FOREIGN KEY (payment_id) REFERENCES owner_payments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'owner_inventory table created successfully.' AS status;
