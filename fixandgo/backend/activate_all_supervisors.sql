-- ============================================================
-- Activate All Supervisors
-- Run this if supervisors are registered but not showing up
-- ============================================================

-- Activate and verify all supervisors
UPDATE users 
SET is_active = 1, 
    is_verified = 1,
    updated_at = NOW()
WHERE role = 'supervisor';

-- Check the result
SELECT 
    id,
    CONCAT(first_name, ' ', last_name) AS name,
    email,
    role,
    is_active,
    is_verified,
    created_at
FROM users 
WHERE role = 'supervisor'
ORDER BY created_at DESC;

-- Expected output:
-- All supervisors should have:
-- - is_active = 1
-- - is_verified = 1
