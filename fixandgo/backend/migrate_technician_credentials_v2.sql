-- ============================================================
--  Fix&Go — Update technician_credentials table
--  Adds: is_video column, shop_images & work_video types
--  Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

USE fixandgo;

-- Add is_video flag if not already present
ALTER TABLE technician_credentials
  ADD COLUMN IF NOT EXISTS is_video TINYINT(1) NOT NULL DEFAULT 0 AFTER is_image;

-- Increase display_order to SMALLINT to handle more items
ALTER TABLE technician_credentials
  MODIFY COLUMN display_order SMALLINT NOT NULL DEFAULT 0;

-- Add experience_years and description to technician_profiles if not present
ALTER TABLE technician_profiles
  ADD COLUMN IF NOT EXISTS experience_years TINYINT UNSIGNED NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS description      TEXT NULL;

-- Also add description column to users table for quick access
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS description TEXT NULL AFTER bio;

SELECT 'technician_credentials v2 migration complete.' AS status;
DESCRIBE technician_credentials;