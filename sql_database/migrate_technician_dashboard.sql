-- ============================================================
-- Fix&Go — Technician Dashboard Migration
-- Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

USE fixandgo;

-- ── 1. Ensure supplier_products supports phone_technician holder ──────────
ALTER TABLE supplier_products
  MODIFY COLUMN holder_type ENUM('owner','sales_person','phone_technician') NULL;

-- ── 2. Ensure is_displayed column exists on supplier_products ─────────────
ALTER TABLE supplier_products
  ADD COLUMN IF NOT EXISTS is_displayed TINYINT(1) NOT NULL DEFAULT 0;

-- ── 3. Bookings table (repair jobs) ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS bookings (
  id                  INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  customer_id         INT UNSIGNED   NOT NULL,
  technician_id       INT UNSIGNED   NULL,
  device_model        VARCHAR(150)   NULL,
  issue_description   TEXT           NULL,
  status              ENUM('pending','confirmed','in_progress','completed','cancelled')
                                     NOT NULL DEFAULT 'pending',
  scheduled_at        DATETIME       NULL,
  total_amount        DECIMAL(10,2)  NULL DEFAULT 0.00,
  notes               TEXT           NULL,
  created_at          DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at          DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_customer    (customer_id),
  INDEX idx_technician  (technician_id),
  INDEX idx_status      (status),

  CONSTRAINT fk_booking_customer
    FOREIGN KEY (customer_id)   REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_booking_tech
    FOREIGN KEY (technician_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 4. Technician profiles table ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS technician_profiles (
  id               INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  user_id          INT UNSIGNED   NOT NULL UNIQUE,
  specialization   VARCHAR(500)   NULL,
  experience_years TINYINT        NOT NULL DEFAULT 0,
  bio              TEXT           NULL,
  availability     ENUM('available','busy','offline') NOT NULL DEFAULT 'available',
  rating_avg       DECIMAL(3,2)   NOT NULL DEFAULT 0.00,
  rating_count     INT UNSIGNED   NOT NULL DEFAULT 0,
  created_at       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  CONSTRAINT fk_tp_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 5. Add specializations column to users if missing ────────────────────
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS specializations VARCHAR(500) NULL AFTER bio,
  ADD COLUMN IF NOT EXISTS shop_name       VARCHAR(150) NULL AFTER specializations;

-- ── 6. Conversations & messages (if not already created) ─────────────────
CREATE TABLE IF NOT EXISTS conversations (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_a_id  INT UNSIGNED NOT NULL,
  user_b_id  INT UNSIGNED NOT NULL,
  created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_conv (user_a_id, user_b_id),
  INDEX idx_user_a (user_a_id),
  INDEX idx_user_b (user_b_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS messages (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  conversation_id INT UNSIGNED NOT NULL,
  sender_id       INT UNSIGNED NOT NULL,
  body            TEXT         NOT NULL,
  is_read         TINYINT(1)   NOT NULL DEFAULT 0,
  created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_conv   (conversation_id),
  INDEX idx_sender (sender_id),
  CONSTRAINT fk_msg_conv   FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
  CONSTRAINT fk_msg_sender FOREIGN KEY (sender_id)       REFERENCES users(id)         ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'Technician dashboard migration complete.' AS status;
