-- ============================================================
--  Fix&Go — Repair payment columns (run in phpMyAdmin)
-- ============================================================
USE fixandgo;

-- Technician-side payment columns
ALTER TABLE bookings
  ADD COLUMN IF NOT EXISTS payment_method      ENUM('cash','bank_transfer','gcash','maya','other') NULL,
  ADD COLUMN IF NOT EXISTS repair_fee          DECIMAL(10,2) NULL COMMENT 'Total repair fee (labor+parts)',
  ADD COLUMN IF NOT EXISTS labor_fee           DECIMAL(10,2) NULL COMMENT 'Labor / service fee',
  ADD COLUMN IF NOT EXISTS parts_fee           DECIMAL(10,2) NULL COMMENT 'Parts / replacement cost',
  ADD COLUMN IF NOT EXISTS payment_note        VARCHAR(255)  NULL COMMENT 'Account or reference',
  ADD COLUMN IF NOT EXISTS payment_status      ENUM('unpaid','paid','pending_collection') NULL,
  ADD COLUMN IF NOT EXISTS receipt_path        VARCHAR(500)  NULL COMMENT 'Receipt uploaded by technician',
  ADD COLUMN IF NOT EXISTS price_photo_path    VARCHAR(500)  NULL COMMENT 'Product/parts price photo';

-- Customer-side payment columns
ALTER TABLE bookings
  ADD COLUMN IF NOT EXISTS customer_payment_method  ENUM('cash','gcash','maya','bank_transfer','card','online','other') NULL,
  ADD COLUMN IF NOT EXISTS customer_payment_status  ENUM('pending','paid') NOT NULL DEFAULT 'pending',
  ADD COLUMN IF NOT EXISTS customer_payment_note    VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS customer_paid_at         DATETIME    NULL,
  ADD COLUMN IF NOT EXISTS parts_replaced           TEXT        NULL COMMENT 'JSON array of parts replaced by technician';

-- Repair payments table (for PayMongo checkout sessions)
CREATE TABLE IF NOT EXISTS repair_payments (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  booking_id    INT UNSIGNED NOT NULL,
  customer_id   INT UNSIGNED NOT NULL,
  technician_id INT UNSIGNED NULL,
  reference     VARCHAR(100) NOT NULL UNIQUE,
  paymongo_id   VARCHAR(100) NULL,
  amount        DECIMAL(10,2) NOT NULL,
  currency      CHAR(3) NOT NULL DEFAULT 'PHP',
  status        ENUM('pending','paid','failed','cancelled') NOT NULL DEFAULT 'pending',
  checkout_url  VARCHAR(1000) NULL,
  paid_at       DATETIME NULL,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_booking   (booking_id),
  INDEX idx_customer  (customer_id),
  INDEX idx_reference (reference)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Indexes
ALTER TABLE bookings
  ADD INDEX IF NOT EXISTS idx_payment_method          (payment_method),
  ADD INDEX IF NOT EXISTS idx_payment_status          (payment_status),
  ADD INDEX IF NOT EXISTS idx_customer_payment_status (customer_payment_status);

SELECT 'Migration complete.' AS status;
DESCRIBE bookings;
