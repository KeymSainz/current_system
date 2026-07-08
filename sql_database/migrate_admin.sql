-- ============================================================
-- Fix&Go — Admin Migration
-- Adds 'admin' role to users table and creates the admin account.
-- NOTE: Run admin_setup.php instead — it does all of this automatically.
-- ============================================================

USE fixandgo;

-- 1. Add 'admin' to the role ENUM
ALTER TABLE users
  MODIFY COLUMN role
    ENUM('customer','sales_person','supplier','supervisor','owner','phone_technician','admin')
    NOT NULL DEFAULT 'customer';

-- 2. Add ban columns
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS is_banned TINYINT(1) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS banned_reason VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS banned_at DATETIME NULL;

-- 3. Add seller application status columns
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS application_status
    ENUM('none','pending','approved','rejected') NOT NULL DEFAULT 'none',
  ADD COLUMN IF NOT EXISTS application_notes TEXT NULL,
  ADD COLUMN IF NOT EXISTS reviewed_by INT UNSIGNED NULL,
  ADD COLUMN IF NOT EXISTS reviewed_at DATETIME NULL;

-- 4. Admin account
--    Email:    keymlingas@gmail.com
--    Password: hakim1234
--    Hash below is for 'hakim1234' generated with bcrypt cost 12
INSERT INTO users
  (first_name, last_name, email, password_hash, role, is_verified, is_active)
VALUES
  ('Fix&Go', 'Admin',
   'keymlingas@gmail.com',
   '$2y$12$eImiTXuWVxfM37uY4JANjQ==',
   'admin', 1, 1)
ON DUPLICATE KEY UPDATE
  role          = 'admin',
  is_verified   = 1,
  is_active     = 1;

-- IMPORTANT: The hash above is a placeholder.
-- Visit this URL to set the real hash automatically:
--   http://localhost/current_system/fixandgo/backend/admin_setup.php
