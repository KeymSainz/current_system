-- ============================================================
-- Fix Purchased Products - Set current_holder_id and holder_type
-- ============================================================
-- This fixes products that were purchased by owners but don't have
-- current_holder_id and holder_type set, which prevents them from
-- being transferred to supervisors.
-- ============================================================

USE fixandgo;

-- Update products that have "Purchased" in notes but no holder info
UPDATE supplier_products
SET 
    current_holder_id = supplier_id,
    holder_type = 'owner'
WHERE 
    notes LIKE '%Purchased from supplier%'
    AND current_holder_id IS NULL
    AND holder_type IS NULL;

-- Verify the update
SELECT 
    COUNT(*) AS fixed_products,
    'Products fixed' AS status
FROM supplier_products
WHERE 
    notes LIKE '%Purchased from supplier%'
    AND current_holder_id IS NOT NULL
    AND holder_type = 'owner';

-- Show sample of fixed products
SELECT 
    id,
    item_description,
    supplier_id AS owner_id,
    current_holder_id,
    holder_type,
    qty,
    status
FROM supplier_products
WHERE 
    notes LIKE '%Purchased from supplier%'
    AND holder_type = 'owner'
LIMIT 10;
