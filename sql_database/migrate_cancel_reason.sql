-- ============================================================
--  Fix&Go — Add cancel_reason to customer_orders
--  Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

USE fixandgo;

ALTER TABLE customer_orders
  ADD COLUMN IF NOT EXISTS cancel_reason VARCHAR(300) NULL
    COMMENT 'Customer-provided reason for cancellation'
    AFTER notes,
  ADD COLUMN IF NOT EXISTS cancel_notes VARCHAR(500) NULL
    COMMENT 'Additional notes provided by customer on cancellation'
    AFTER cancel_reason;

SELECT 'cancel_reason and cancel_notes columns added to customer_orders.' AS status;
