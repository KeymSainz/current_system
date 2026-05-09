-- ============================================================
-- Fix All Products Ownership
-- Reset all products to their correct owners
-- ============================================================

USE fixandgo;

-- Method 1: Set current_holder_id based on supplier_id
-- This assumes the supplier_id is the owner who currently has the product
UPDATE supplier_products
SET current_holder_id = supplier_id,
    holder_type = 'owner'
WHERE current_holder_id IS NULL 
   OR current_holder_id != supplier_id;

-- Verify the fix
SELECT 
    COUNT(*) as total_products,
    COUNT(CASE WHEN current_holder_id = supplier_id THEN 1 END) as correctly_owned,
    COUNT(CASE WHEN current_holder_id != supplier_id THEN 1 END) as incorrectly_owned
FROM supplier_products;

-- Show sample of fixed products
SELECT 
    sp.id,
    sp.item_description,
    sp.qty,
    sp.supplier_id,
    sp.current_holder_id,
    sp.holder_type,
    u.email as owner_email
FROM supplier_products sp
LEFT JOIN users u ON u.id = sp.current_holder_id
LIMIT 10;
