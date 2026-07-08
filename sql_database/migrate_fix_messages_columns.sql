-- ============================================================
--  Fix&Go — Fix messages table column names
--  Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

USE fixandgo;

-- Step 1: Drop foreign key on booking_id (if exists)
SET FOREIGN_KEY_CHECKS = 0;

-- Drop the index that's blocking the column drop
ALTER TABLE messages DROP INDEX IF EXISTS idx_booking;

-- Step 2: Drop booking_id column
ALTER TABLE messages DROP COLUMN IF EXISTS booking_id;

-- Step 3: Rename sent_at → created_at (if sent_at exists)
-- We use a stored procedure trick since ALTER...IF EXISTS isn't in older MySQL
-- Check and rename via a safe approach:
ALTER TABLE messages 
  CHANGE COLUMN IF EXISTS sent_at created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

-- Step 4: Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Verify
DESCRIBE messages;

SELECT 'Migration complete.' AS status;
