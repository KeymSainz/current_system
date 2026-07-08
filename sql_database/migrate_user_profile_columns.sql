-- ============================================================
--  Fix&Go — Add profile columns to users table
--  Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

USE fixandgo;

-- Avatar URL
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS avatar_url VARCHAR(500) NULL DEFAULT NULL
  AFTER phone;

-- Gender
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS gender ENUM('male','female','other') NULL DEFAULT NULL
  AFTER avatar_url;

-- Date of birth
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS date_of_birth DATE NULL DEFAULT NULL
  AFTER gender;

SELECT 'Profile columns added.' AS status;
