-- ============================================================
-- Fix&Go — Technician Applications Table
-- Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

USE fixandgo;

-- Extend seller_applications role ENUM to include phone_technician
ALTER TABLE seller_applications
  MODIFY COLUMN role ENUM('supplier','owner','phone_technician') NOT NULL;

-- Add technician-specific columns if not present
ALTER TABLE seller_applications
  ADD COLUMN IF NOT EXISTS shop_address    TEXT          NULL AFTER shop_name,
  ADD COLUMN IF NOT EXISTS address_lat     DECIMAL(10,7) NULL AFTER shop_address,
  ADD COLUMN IF NOT EXISTS address_lng     DECIMAL(10,7) NULL AFTER address_lat,
  ADD COLUMN IF NOT EXISTS specializations VARCHAR(500)  NULL AFTER address_lng,
  ADD COLUMN IF NOT EXISTS experience_yrs  TINYINT       NULL AFTER specializations,
  ADD COLUMN IF NOT EXISTS doc_cert        VARCHAR(500)  NULL AFTER doc_bank,
  ADD COLUMN IF NOT EXISTS entity_type     ENUM('sole_proprietorship','corporation','one_person_corp') NULL AFTER doc_cert,
  ADD COLUMN IF NOT EXISTS business_name   VARCHAR(255)  NULL AFTER entity_type,
  ADD COLUMN IF NOT EXISTS general_location VARCHAR(255) NULL AFTER business_name,
  ADD COLUMN IF NOT EXISTS zip_code        VARCHAR(20)   NULL AFTER general_location,
  ADD COLUMN IF NOT EXISTS business_email  VARCHAR(255)  NULL AFTER zip_code,
  ADD COLUMN IF NOT EXISTS suffix          VARCHAR(20)   NULL AFTER last_name,
  ADD COLUMN IF NOT EXISTS middle_name     VARCHAR(80)   NULL AFTER suffix;

SELECT 'Technician applications migration complete.' AS status;
