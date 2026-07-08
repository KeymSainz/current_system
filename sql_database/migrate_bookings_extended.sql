-- Fix&Go — Extend bookings table with repair intake form fields
USE fixandgo;

-- Add intake form columns (safe IF NOT EXISTS)
ALTER TABLE bookings
  ADD COLUMN IF NOT EXISTS contact_number    VARCHAR(30)  NULL AFTER customer_id,
  ADD COLUMN IF NOT EXISTS address           TEXT         NULL AFTER contact_number,
  ADD COLUMN IF NOT EXISTS device_name       VARCHAR(150) NULL AFTER address,
  ADD COLUMN IF NOT EXISTS fault_description TEXT         NULL AFTER problem_desc,
  ADD COLUMN IF NOT EXISTS phone_history     TEXT         NULL AFTER fault_description,
  ADD COLUMN IF NOT EXISTS expected_fix      TEXT         NULL AFTER phone_history;

-- Add technician cert doc column to seller_applications if not present
ALTER TABLE seller_applications
  ADD COLUMN IF NOT EXISTS doc_cert VARCHAR(500) NULL AFTER doc_bank;

SELECT 'Bookings extended migration complete.' AS status;
