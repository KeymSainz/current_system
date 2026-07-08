-- Fix Sales Person Product Status
-- This script updates products that are displayed (is_displayed = 1) 
-- but don't have the correct status for shop display

-- First, let's see what we have
SELECT 
    id,
    category,
    brand,
    item_description,
    qty,
    status,
    is_displayed,
    holder_type,
    current_holder_id
FROM supplier_products
WHERE is_displayed = 1;

-- Update products held by sales person to have correct status
UPDATE supplier_products
SET status = 'sent_to_sales_person'
WHERE holder_type = 'sales_person'
  AND is_displayed = 1
  AND status != 'sent_to_sales_person';

-- Verify the fix
SELECT 
    COUNT(*) as shop_ready_count
FROM supplier_products
WHERE status = 'sent_to_sales_person'
  AND is_displayed = 1
  AND qty > 0;

-- Show all products that should now appear in shop
SELECT 
    id,
    category,
    brand,
    item_description,
    qty,
    srp,
    status,
    is_displayed
FROM supplier_products
WHERE status = 'sent_to_sales_person'
  AND is_displayed = 1
  AND qty > 0
ORDER BY category, item_description;
