-- ============================================================
--  Fix&Go — Product Transfer System Migration
--  Enables Owner → Supervisor → Sales Person product flow
-- ============================================================

USE fixandgo;

-- ============================================================
--  1. PRODUCT TRANSFERS TABLE
--     Tracks product transfers between users in the hierarchy:
--     - Owner → Supervisor
--     - Supervisor → Sales Person
-- ============================================================
CREATE TABLE IF NOT EXISTS product_transfers (
  id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  product_id      INT UNSIGNED  NOT NULL,           -- from supplier_products
  from_user_id    INT UNSIGNED  NOT NULL,           -- sender (owner or supervisor)
  to_user_id      INT UNSIGNED  NOT NULL,           -- recipient (supervisor or sales_person)
  transfer_type   ENUM('owner_to_supervisor', 'supervisor_to_sales') NOT NULL,
  quantity        INT UNSIGNED  NOT NULL DEFAULT 1,
  status          ENUM('pending', 'accepted', 'rejected') NOT NULL DEFAULT 'pending',
  notes           TEXT          NULL,                -- optional transfer notes
  transferred_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  responded_at    DATETIME      NULL,                -- when recipient accepted/rejected
  
  PRIMARY KEY (id),
  INDEX idx_product       (product_id),
  INDEX idx_from_user     (from_user_id),
  INDEX idx_to_user       (to_user_id),
  INDEX idx_transfer_type (transfer_type),
  INDEX idx_status        (status),
  
  CONSTRAINT fk_transfer_product
    FOREIGN KEY (product_id) REFERENCES supplier_products(id) ON DELETE CASCADE,
  CONSTRAINT fk_transfer_from
    FOREIGN KEY (from_user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_transfer_to
    FOREIGN KEY (to_user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  2. ADD CURRENT HOLDER TRACKING TO SUPPLIER_PRODUCTS
--     Tracks who currently has the product in the chain
-- ============================================================
ALTER TABLE supplier_products 
  ADD COLUMN IF NOT EXISTS current_holder_id INT UNSIGNED NULL AFTER status,
  ADD COLUMN IF NOT EXISTS holder_type ENUM('owner', 'supervisor', 'sales_person') NULL AFTER current_holder_id;

-- Add foreign key for current_holder_id
ALTER TABLE supplier_products
  ADD CONSTRAINT fk_sp_current_holder
    FOREIGN KEY (current_holder_id) REFERENCES users(id) ON DELETE SET NULL;

-- Add index for faster queries
ALTER TABLE supplier_products
  ADD INDEX IF NOT EXISTS idx_current_holder (current_holder_id);

-- ============================================================
--  3. UPDATE EXISTING PRODUCTS
--     Set current_holder_id to owner_id for products that are 'owner_received'
-- ============================================================
UPDATE supplier_products sp
JOIN product_submissions ps ON ps.id = (
  SELECT si.submission_id 
  FROM submission_items si 
  WHERE si.product_id = sp.id 
  LIMIT 1
)
SET 
  sp.current_holder_id = ps.owner_id,
  sp.holder_type = 'owner'
WHERE sp.status = 'owner_received' 
  AND ps.owner_id IS NOT NULL
  AND sp.current_holder_id IS NULL;

-- ============================================================
--  4. ADD STAFF RELATIONSHIP TABLE
--     Links supervisors and sales persons to their owner
-- ============================================================
CREATE TABLE IF NOT EXISTS staff_assignments (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  owner_id    INT UNSIGNED NOT NULL,           -- the shop owner
  staff_id    INT UNSIGNED NOT NULL,           -- supervisor or sales_person
  staff_role  ENUM('supervisor', 'sales_person') NOT NULL,
  assigned_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  is_active   TINYINT(1)   NOT NULL DEFAULT 1,
  
  PRIMARY KEY (id),
  UNIQUE KEY uq_owner_staff (owner_id, staff_id),
  INDEX idx_owner (owner_id),
  INDEX idx_staff (staff_id),
  
  CONSTRAINT fk_staff_owner
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_staff_member
    FOREIGN KEY (staff_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  5. PRODUCT TRANSFER HISTORY
--     Audit trail of all product movements
-- ============================================================
CREATE TABLE IF NOT EXISTS product_transfer_history (
  id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  product_id      INT UNSIGNED  NOT NULL,
  from_user_id    INT UNSIGNED  NULL,              -- NULL if initial receipt from supplier
  to_user_id      INT UNSIGNED  NOT NULL,
  action          VARCHAR(50)   NOT NULL,          -- 'received_from_supplier', 'sent_to_supervisor', 'sent_to_sales', 'accepted', 'rejected'
  quantity        INT UNSIGNED  NOT NULL DEFAULT 1,
  notes           TEXT          NULL,
  created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (id),
  INDEX idx_product (product_id),
  INDEX idx_from_user (from_user_id),
  INDEX idx_to_user (to_user_id),
  
  CONSTRAINT fk_history_product
    FOREIGN KEY (product_id) REFERENCES supplier_products(id) ON DELETE CASCADE,
  CONSTRAINT fk_history_from
    FOREIGN KEY (from_user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_history_to
    FOREIGN KEY (to_user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  6. CREATE HISTORY ENTRIES FOR EXISTING PRODUCTS
--     Backfill history for products already received by owners
-- ============================================================
INSERT INTO product_transfer_history (product_id, from_user_id, to_user_id, action, quantity, notes)
SELECT 
  sp.id,
  ps.supplier_id,
  ps.owner_id,
  'received_from_supplier',
  sp.qty,
  'Migrated from existing product_submissions'
FROM supplier_products sp
JOIN submission_items si ON si.product_id = sp.id
JOIN product_submissions ps ON ps.id = si.submission_id
WHERE sp.status = 'owner_received' 
  AND ps.owner_id IS NOT NULL
  AND NOT EXISTS (
    SELECT 1 FROM product_transfer_history pth 
    WHERE pth.product_id = sp.id
  );

-- ============================================================
--  VERIFICATION QUERIES
-- ============================================================

-- Check product_transfers table
SELECT 'product_transfers table created' AS status, COUNT(*) AS row_count 
FROM product_transfers;

-- Check staff_assignments table
SELECT 'staff_assignments table created' AS status, COUNT(*) AS row_count 
FROM staff_assignments;

-- Check product_transfer_history table
SELECT 'product_transfer_history table created' AS status, COUNT(*) AS row_count 
FROM product_transfer_history;

-- Check updated supplier_products
SELECT 
  'supplier_products updated' AS status,
  COUNT(*) AS total_products,
  COUNT(current_holder_id) AS products_with_holder
FROM supplier_products;

-- Show sample of products with holders
SELECT 
  sp.id,
  sp.item_description,
  sp.status,
  sp.holder_type,
  u.email AS current_holder_email
FROM supplier_products sp
LEFT JOIN users u ON u.id = sp.current_holder_id
WHERE sp.current_holder_id IS NOT NULL
LIMIT 5;

