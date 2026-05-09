-- ============================================================
--  Fix&Go — Add Product Status Migration
--  Adds 'draft' and 'sent_to_supervisor' statuses to supplier_products
-- ============================================================

USE fixandgo;

-- Add new status values to the ENUM
ALTER TABLE supplier_products
  MODIFY COLUMN status 
    ENUM('pending','verified','rejected','owner_received','draft','sent_to_supervisor','sent_to_sales_person')
    NOT NULL DEFAULT 'pending';

-- Update existing owner products to 'verified' if they don't have a status
UPDATE supplier_products sp
INNER JOIN users u ON sp.supplier_id = u.id
SET sp.status = 'verified'
WHERE u.role = 'owner' 
  AND sp.status = 'pending';

SELECT 'Migration completed successfully!' AS message;
