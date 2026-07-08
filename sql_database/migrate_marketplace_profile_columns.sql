-- ============================================================
--  Fix&Go — Add marketplace profile columns to users table
--  Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

USE fixandgo;

-- Profile image (separate from avatar_url for marketplace display)
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS profile_image VARCHAR(500) NULL DEFAULT NULL
  AFTER avatar_url;

-- Bio/description for technicians and suppliers
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS bio TEXT NULL DEFAULT NULL
  AFTER profile_image;

-- Specializations for technicians (comma-separated)
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS specializations VARCHAR(500) NULL DEFAULT NULL
  AFTER bio;

-- Shop name for suppliers and sales persons
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS shop_name VARCHAR(255) NULL DEFAULT NULL
  AFTER specializations;

SELECT 'Marketplace profile columns added successfully.' AS status;
