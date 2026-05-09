-- ============================================================
-- Fix Product Holders
-- Sets current_holder_id for products that don't have it
-- ============================================================

USE fixandgo;

-- Check current state
SELECT 
    'Before Fix' as status,
    COUNT(*) as total_products,
    COUNT(current_holder_id) as products_with_holder,
    COUNT(*) - COUNT(current_holder_id) as products_without_holder
FROM supplier_products;

-- ============================================================
-- Fix 1: Set current_holder_id from product_submissions
-- ============================================================
UPDATE supplier_products sp
JOIN submission_items si ON si.product_id = sp.id
JOIN product_submissions ps ON ps.id = si.submission_id
SET 
  sp.current_holder_id = ps.owner_id,
  sp.holder_type = 'owner'
WHERE sp.status IN ('owner_received', 'sent_to_owner', 'verified')
  AND ps.owner_id IS NOT NULL
  AND sp.current_holder_id IS NULL;

-- Show how many were updated
SELECT ROW_COUNT() as 'Products updated from product_submissions';

-- ============================================================
-- Fix 2: For products without submission link, 
--        set to the supplier_id (assuming they're the owner)
-- ============================================================
-- Note: This is a fallback. Adjust the logic if needed.
UPDATE supplier_products sp
SET 
  sp.current_holder_id = sp.supplier_id,
  sp.holder_type = 'owner'
WHERE sp.status IN ('owner_received', 'sent_to_owner', 'verified', 'draft')
  AND sp.current_holder_id IS NULL
  AND sp.supplier_id IS NOT NULL;

-- Show how many were updated
SELECT ROW_COUNT() as 'Products updated from supplier_id';

-- ============================================================
-- Verify the fix
-- ============================================================
SELECT 
    'After Fix' as status,
    COUNT(*) as total_products,
    COUNT(current_holder_id) as products_with_holder,
    COUNT(*) - COUNT(current_holder_id) as products_without_holder
FROM supplier_products;

-- Show sample of fixed products
SELECT 
    sp.id,
    sp.category,
    sp.brand,
    LEFT(sp.item_description, 50) as description,
    sp.qty,
    sp.status,
    sp.current_holder_id,
    sp.holder_type,
    u.email as holder_email
FROM supplier_products sp
LEFT JOIN users u ON u.id = sp.current_holder_id
WHERE sp.current_holder_id IS NOT NULL
ORDER BY sp.id DESC
LIMIT 10;

-- ============================================================
-- If you still have products without holder, 
-- you can manually set them to a specific owner:
-- ============================================================
-- Replace 123 with the actual owner user_id
-- UPDATE supplier_products 
-- SET current_holder_id = 123, holder_type = 'owner'
-- WHERE current_holder_id IS NULL 
--   AND status IN ('owner_received', 'sent_to_owner', 'verified');
