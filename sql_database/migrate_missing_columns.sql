-- Fix&Go — Add missing optional columns safely
-- Run in phpMyAdmin: Import → select this file → Go

USE fixandgo;

-- customer_orders: cancel columns
ALTER TABLE customer_orders
  ADD COLUMN IF NOT EXISTS cancel_reason VARCHAR(300) NULL AFTER notes,
  ADD COLUMN IF NOT EXISTS cancel_notes  VARCHAR(500) NULL AFTER cancel_reason;

-- users: address columns
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS address_line     VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS barangay         VARCHAR(100) NULL,
  ADD COLUMN IF NOT EXISTS city             VARCHAR(100) NULL,
  ADD COLUMN IF NOT EXISTS province         VARCHAR(100) NULL,
  ADD COLUMN IF NOT EXISTS region           VARCHAR(100) NULL,
  ADD COLUMN IF NOT EXISTS zip_code         VARCHAR(10)  NULL,
  ADD COLUMN IF NOT EXISTS address_verified TINYINT(1)   NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS phone            VARCHAR(30)  NULL;

SELECT 'Missing columns added successfully.' AS status;
