-- ============================================================
--  Fix&Go — Customer Address Migration
--  Adds delivery address fields to the users table
-- ============================================================

USE fixandgo;

ALTER TABLE users
  ADD COLUMN IF NOT EXISTS address_line   VARCHAR(255) NULL COMMENT 'House/Unit/Street',
  ADD COLUMN IF NOT EXISTS barangay       VARCHAR(100) NULL,
  ADD COLUMN IF NOT EXISTS city           VARCHAR(100) NULL,
  ADD COLUMN IF NOT EXISTS province       VARCHAR(100) NULL,
  ADD COLUMN IF NOT EXISTS region         VARCHAR(100) NULL,
  ADD COLUMN IF NOT EXISTS zip_code       VARCHAR(10)  NULL,
  ADD COLUMN IF NOT EXISTS address_verified TINYINT(1) NOT NULL DEFAULT 0
    COMMENT '1 = customer has filled all address fields';

SELECT 'Customer address columns added.' AS status;
