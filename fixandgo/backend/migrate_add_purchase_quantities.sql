-- ============================================================
--  Fix&Go — Migration: Add purchase_quantities to owner_payments
--  Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

USE fixandgo;

-- Add purchase_quantities column to store custom quantities per product
ALTER TABLE owner_payments
ADD COLUMN purchase_quantities JSON NULL COMMENT 'Custom quantities per product ID' 
AFTER product_ids;

SELECT 'purchase_quantities column added to owner_payments table.' AS status;
