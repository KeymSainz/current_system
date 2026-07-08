-- ============================================================
--  Fix&Go Complete Database Setup
--  Single-file installation: import this in phpMyAdmin
--  Generated: 2026-06-19
-- ============================================================

CREATE DATABASE IF NOT EXISTS fixandgo
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE fixandgo;

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = '';

-- ──────────────────────────────────────────────────────────
-- schema.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
--  Fix&Go — Complete Database Schema
--  Engine : InnoDB | Charset : utf8mb4 (full Unicode + emoji)
--  Run    : mysql -u root -p < schema.sql
--           OR import via phpMyAdmin → Import tab
-- ============================================================

CREATE DATABASE IF NOT EXISTS fixandgo
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- ============================================================
--  1. USERS
--     Stores all account types: customer, sales_person, supplier,
--     supervisor, owner, phone_technician.
--     password_hash is NULL for Google-only accounts.
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
  id            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  first_name    VARCHAR(50)     NOT NULL,
  last_name     VARCHAR(50)     NOT NULL,
  email         VARCHAR(255)    NOT NULL,
  phone         VARCHAR(20)     NULL,
  password_hash VARCHAR(255)    NULL,                          -- NULL = OAuth-only
  role          ENUM('customer','sales_person','supplier','supervisor','owner','phone_technician')
                                NOT NULL DEFAULT 'customer',
  provider      ENUM('local','google') NOT NULL DEFAULT 'local',
  provider_id   VARCHAR(255)    NULL,                          -- Google sub ID
  avatar_url    VARCHAR(500)    NULL,
  is_verified   TINYINT(1)      NOT NULL DEFAULT 0,
  is_active     TINYINT(1)      NOT NULL DEFAULT 1,            -- soft-disable account
  created_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
                                         ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE  KEY uq_email          (email),
  INDEX        idx_role          (role),
  INDEX        idx_provider_id   (provider_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── ALTER: add phone_technician to role ENUM if table already exists ──────
-- Run this if you already created the users table without phone_technician:
-- ALTER TABLE users
--   MODIFY COLUMN role
--     ENUM('customer','sales_person','supplier','supervisor','owner','phone_technician')
--     NOT NULL DEFAULT 'customer';

-- ============================================================
--  2. OTP TOKENS
--     6-digit codes for email verification & password reset.
--     Stored as bcrypt hashes — never plaintext.
-- ============================================================
CREATE TABLE IF NOT EXISTS otp_tokens (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id    INT UNSIGNED NOT NULL,
  otp_hash   VARCHAR(255) NOT NULL,
  purpose    ENUM('verify','reset','login') NOT NULL DEFAULT 'verify',
  expires_at DATETIME     NOT NULL,
  attempts   TINYINT      NOT NULL DEFAULT 0,
  created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_user_purpose (user_id, purpose),
  CONSTRAINT fk_otp_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  3. REMEMBER ME TOKENS
--     SHA-256 hashed tokens stored in httpOnly cookies.
-- ============================================================
CREATE TABLE IF NOT EXISTS remember_tokens (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id    INT UNSIGNED NOT NULL,
  token_hash VARCHAR(64)  NOT NULL,
  expires_at DATETIME     NOT NULL,
  created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_token   (token_hash),
  INDEX      idx_user   (user_id),
  CONSTRAINT fk_remember_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  4. RATE LIMITS
--     Per-IP attempt tracking for login, register, OTP resend.
-- ============================================================
CREATE TABLE IF NOT EXISTS rate_limits (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  identifier   VARCHAR(45)  NOT NULL,   -- IPv4 or IPv6 address
  action       VARCHAR(50)  NOT NULL,   -- 'login' | 'register' | 'resend_otp'
  attempted_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_identifier_action (identifier, action, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  5. SHOPS
--     A shop is owned by a user with role = 'owner'.
--     Technicians and customers are linked to shops via
--     shop_members and bookings respectively.
-- ============================================================
CREATE TABLE IF NOT EXISTS shops (
  id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  owner_id     INT UNSIGNED  NOT NULL,
  name         VARCHAR(100)  NOT NULL,
  description  TEXT          NULL,
  address      VARCHAR(255)  NULL,
  city         VARCHAR(100)  NULL,
  phone        VARCHAR(20)   NULL,
  email        VARCHAR(255)  NULL,
  logo_url     VARCHAR(500)  NULL,
  is_active    TINYINT(1)    NOT NULL DEFAULT 1,
  created_at   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
                                      ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_owner   (owner_id),
  INDEX idx_city    (city),
  CONSTRAINT fk_shop_owner
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  6. SHOP MEMBERS
--     Links technicians to a shop (many-to-one).
-- ============================================================
CREATE TABLE IF NOT EXISTS shop_members (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  shop_id      INT UNSIGNED NOT NULL,
  user_id      INT UNSIGNED NOT NULL,   -- must have role = 'technician'
  joined_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_shop_user (shop_id, user_id),
  CONSTRAINT fk_member_shop
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
  CONSTRAINT fk_member_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  7. TECHNICIAN PROFILES
--     Extended profile info for phone_technician users.
--     One row per technician — created automatically on registration
--     or filled in later via the profile page.
-- ============================================================
CREATE TABLE IF NOT EXISTS technician_profiles (
  id               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  user_id          INT UNSIGNED  NOT NULL,
  specialization   VARCHAR(150)  NULL,    -- e.g. "Screen Repair, Water Damage"
  experience_years TINYINT       NOT NULL DEFAULT 0,
  bio              TEXT          NULL,
  certifications   VARCHAR(500)  NULL,    -- comma-separated or JSON string
  availability     ENUM('available','busy','unavailable') NOT NULL DEFAULT 'available',
  rating_avg       DECIMAL(3,2)  NOT NULL DEFAULT 0.00,  -- cached average rating
  rating_count     INT UNSIGNED  NOT NULL DEFAULT 0,
  created_at       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
                                          ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_user (user_id),
  INDEX idx_availability (availability),
  CONSTRAINT fk_tp_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── ALTER: add technician_profiles if table already exists ───────────────
-- Run this only if you need to add the table to an existing database:
-- (The CREATE TABLE IF NOT EXISTS above handles new installs automatically.)

-- ============================================================
--  8. SERVICES
--     Repair services offered by a shop (e.g. Screen Repair).
-- ============================================================
CREATE TABLE IF NOT EXISTS services (
  id           INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  shop_id      INT UNSIGNED   NOT NULL,
  name         VARCHAR(100)   NOT NULL,
  description  TEXT           NULL,
  price        DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
  duration_min INT            NOT NULL DEFAULT 60,   -- estimated minutes
  is_active    TINYINT(1)     NOT NULL DEFAULT 1,
  created_at   DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_shop (shop_id),
  CONSTRAINT fk_service_shop
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  8. DEVICES
--     Customer-owned devices that can be booked for repair.
-- ============================================================
CREATE TABLE IF NOT EXISTS devices (
  id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  customer_id  INT UNSIGNED  NOT NULL,
  brand        VARCHAR(50)   NOT NULL,   -- e.g. Apple, Samsung
  model        VARCHAR(100)  NOT NULL,   -- e.g. iPhone 14 Pro
  serial_no    VARCHAR(100)  NULL,
  color        VARCHAR(50)   NULL,
  notes        TEXT          NULL,
  created_at   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_customer (customer_id),
  CONSTRAINT fk_device_customer
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  9. BOOKINGS
--     A customer books a service for a device at a shop.
--     Optionally assigned to a specific technician.
-- ============================================================
CREATE TABLE IF NOT EXISTS bookings (
  id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  customer_id     INT UNSIGNED  NOT NULL,
  shop_id         INT UNSIGNED  NOT NULL,
  service_id      INT UNSIGNED  NOT NULL,
  device_id       INT UNSIGNED  NULL,                -- optional
  technician_id   INT UNSIGNED  NULL,                -- assigned after booking
  scheduled_at    DATETIME      NOT NULL,
  status          ENUM(
                    'pending',      -- just booked, awaiting confirmation
                    'confirmed',    -- shop confirmed
                    'in_progress',  -- technician working on it
                    'completed',    -- repair done
                    'cancelled'     -- cancelled by customer or shop
                  )             NOT NULL DEFAULT 'pending',
  problem_desc    TEXT          NULL,                -- customer's description
  technician_notes TEXT         NULL,                -- internal notes
  total_price     DECIMAL(10,2) NULL,
  created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
                                         ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_customer    (customer_id),
  INDEX idx_shop        (shop_id),
  INDEX idx_technician  (technician_id),
  INDEX idx_status      (status),
  INDEX idx_scheduled   (scheduled_at),
  CONSTRAINT fk_booking_customer
    FOREIGN KEY (customer_id)   REFERENCES users(id)    ON DELETE CASCADE,
  CONSTRAINT fk_booking_shop
    FOREIGN KEY (shop_id)       REFERENCES shops(id)    ON DELETE CASCADE,
  CONSTRAINT fk_booking_service
    FOREIGN KEY (service_id)    REFERENCES services(id) ON DELETE RESTRICT,
  CONSTRAINT fk_booking_device
    FOREIGN KEY (device_id)     REFERENCES devices(id)  ON DELETE SET NULL,
  CONSTRAINT fk_booking_tech
    FOREIGN KEY (technician_id) REFERENCES users(id)    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  10. BOOKING STATUS HISTORY
--      Audit trail of every status change on a booking.
-- ============================================================
CREATE TABLE IF NOT EXISTS booking_status_history (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  booking_id  INT UNSIGNED NOT NULL,
  changed_by  INT UNSIGNED NOT NULL,   -- user who made the change
  old_status  VARCHAR(20)  NOT NULL,
  new_status  VARCHAR(20)  NOT NULL,
  note        TEXT         NULL,
  changed_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_booking (booking_id),
  CONSTRAINT fk_history_booking
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
  CONSTRAINT fk_history_user
    FOREIGN KEY (changed_by) REFERENCES users(id)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  11. REVIEWS
--      Customer reviews a completed booking (1 per booking).
-- ============================================================
CREATE TABLE IF NOT EXISTS reviews (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  booking_id  INT UNSIGNED NOT NULL,
  customer_id INT UNSIGNED NOT NULL,
  shop_id     INT UNSIGNED NOT NULL,
  rating      TINYINT      NOT NULL CHECK (rating BETWEEN 1 AND 5),
  comment     TEXT         NULL,
  created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_booking_review (booking_id),   -- one review per booking
  INDEX idx_shop   (shop_id),
  INDEX idx_rating (rating),
  CONSTRAINT fk_review_booking
    FOREIGN KEY (booking_id)  REFERENCES bookings(id) ON DELETE CASCADE,
  CONSTRAINT fk_review_customer
    FOREIGN KEY (customer_id) REFERENCES users(id)    ON DELETE CASCADE,
  CONSTRAINT fk_review_shop
    FOREIGN KEY (shop_id)     REFERENCES shops(id)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  12. MESSAGES
--      In-app chat between customer and technician/shop.
-- ============================================================
CREATE TABLE IF NOT EXISTS messages (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  booking_id  INT UNSIGNED NOT NULL,
  sender_id   INT UNSIGNED NOT NULL,
  body        TEXT         NOT NULL,
  is_read     TINYINT(1)   NOT NULL DEFAULT 0,
  sent_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_booking (booking_id),
  INDEX idx_sender  (sender_id),
  CONSTRAINT fk_msg_booking
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
  CONSTRAINT fk_msg_sender
    FOREIGN KEY (sender_id)  REFERENCES users(id)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  13. NOTIFICATIONS
--      System notifications sent to users (booking updates, etc.)
-- ============================================================
CREATE TABLE IF NOT EXISTS notifications (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id     INT UNSIGNED NOT NULL,
  type        VARCHAR(50)  NOT NULL,   -- 'booking_confirmed', 'otp', etc.
  title       VARCHAR(150) NOT NULL,
  body        TEXT         NOT NULL,
  is_read     TINYINT(1)   NOT NULL DEFAULT 0,
  created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_user_read (user_id, is_read),
  CONSTRAINT fk_notif_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  14. PROMOTIONS
--      Discount codes or offers created by shop owners.
-- ============================================================
CREATE TABLE IF NOT EXISTS promotions (
  id           INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  shop_id      INT UNSIGNED   NOT NULL,
  code         VARCHAR(30)    NOT NULL,
  description  VARCHAR(255)   NULL,
  discount_pct DECIMAL(5,2)   NOT NULL DEFAULT 0.00,  -- percentage off
  valid_from   DATETIME       NOT NULL,
  valid_until  DATETIME       NOT NULL,
  max_uses     INT            NULL,                    -- NULL = unlimited
  used_count   INT            NOT NULL DEFAULT 0,
  is_active    TINYINT(1)     NOT NULL DEFAULT 1,
  created_at   DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_shop_code (shop_id, code),
  INDEX idx_shop (shop_id),
  CONSTRAINT fk_promo_shop
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  15. PAYMENTS
--      Payment record per booking.
-- ============================================================
CREATE TABLE IF NOT EXISTS payments (
  id             INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  booking_id     INT UNSIGNED   NOT NULL,
  amount         DECIMAL(10,2)  NOT NULL,
  currency       VARCHAR(3)     NOT NULL DEFAULT 'USD',
  method         ENUM('cash','card','online') NOT NULL DEFAULT 'cash',
  status         ENUM('pending','paid','refunded','failed')
                                NOT NULL DEFAULT 'pending',
  transaction_id VARCHAR(255)   NULL,   -- from payment gateway
  paid_at        DATETIME       NULL,
  created_at     DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_booking_payment (booking_id),
  INDEX idx_status (status),
  CONSTRAINT fk_payment_booking
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  SAMPLE DATA — remove before going live
--  Password for all sample accounts: Password1
-- ============================================================

-- Sample owner
INSERT IGNORE INTO users
  (first_name, last_name, email, password_hash, role, is_verified)
VALUES
  ('Admin', 'Owner', 'owner@fixandgo.com',
   '$2y$12$0fxHhMJKgJ2atMnINeil3eDBYbozoxI85xI1HmbDSn213l10tN.3a',
   'owner', 1);

-- Sample phone technicians
INSERT IGNORE INTO users
  (first_name, last_name, email, password_hash, role, is_verified)
VALUES
  ('Carlos', 'Reyes',    'carlos@fixandgo.com',
   '$2y$12$0fxHhMJKgJ2atMnINeil3eDBYbozoxI85xI1HmbDSn213l10tN.3a',
   'phone_technician', 1),
  ('Ana',    'Dela Cruz', 'ana@fixandgo.com',
   '$2y$12$0fxHhMJKgJ2atMnINeil3eDBYbozoxI85xI1HmbDSn213l10tN.3a',
   'phone_technician', 1),
  ('Marco',  'Santos',   'marco@fixandgo.com',
   '$2y$12$0fxHhMJKgJ2atMnINeil3eDBYbozoxI85xI1HmbDSn213l10tN.3a',
   'phone_technician', 1);

-- Technician profiles (must run AFTER technician_profiles table is created above)
INSERT IGNORE INTO technician_profiles
  (user_id, specialization, experience_years, bio, availability)
SELECT id, 'Screen Repair, Battery Replacement', 3,
       'Experienced in all major brands. Fast and reliable service.', 'available'
FROM users WHERE email = 'carlos@fixandgo.com' LIMIT 1;

INSERT IGNORE INTO technician_profiles
  (user_id, specialization, experience_years, bio, availability)
SELECT id, 'Water Damage, Charging Port Repair', 5,
       'Specialist in liquid damage recovery and micro-soldering.', 'available'
FROM users WHERE email = 'ana@fixandgo.com' LIMIT 1;

INSERT IGNORE INTO technician_profiles
  (user_id, specialization, experience_years, bio, availability)
SELECT id, 'Software Troubleshooting, Screen Repair', 2,
       'Handles both hardware and software issues efficiently.', 'available'
FROM users WHERE email = 'marco@fixandgo.com' LIMIT 1;

-- Sample customer
INSERT IGNORE INTO users
  (first_name, last_name, email, password_hash, role, is_verified)
VALUES
  ('Maria', 'Santos', 'customer@fixandgo.com',
   '$2y$12$0fxHhMJKgJ2atMnINeil3eDBYbozoxI85xI1HmbDSn213l10tN.3a',
   'customer', 1);

-- Sample shop (owned by the owner above)
INSERT IGNORE INTO shops
  (owner_id, name, description, address, city, phone, email)
SELECT id, 'Fix&Go Main Shop',
       'Fast and reliable phone repairs in the city center.',
       '123 Repair Street', 'Manila', '+63 912 345 6789', 'shop@fixandgo.com'
FROM users WHERE email = 'owner@fixandgo.com' LIMIT 1;

-- Sample services (shop id=1)
INSERT IGNORE INTO services (shop_id, name, description, price, duration_min) VALUES
  (1, 'Screen Replacement',   'Replace cracked or broken screens.',         1500.00, 60),
  (1, 'Battery Replacement',  'Replace old or swollen batteries.',           800.00, 45),
  (1, 'Charging Port Repair', 'Fix loose or non-functional charging ports.', 600.00, 30),
  (1, 'Water Damage Repair',  'Clean and restore water-damaged phones.',    2000.00, 120),
  (1, 'Speaker/Mic Repair',   'Fix audio issues on any device.',             700.00, 45);

-- Sample device for customer
INSERT IGNORE INTO devices (customer_id, brand, model, color)
SELECT id, 'Samsung', 'Galaxy S22', 'Phantom Black'
FROM users WHERE email = 'customer@fixandgo.com' LIMIT 1;

-- Sample booking
INSERT IGNORE INTO bookings
  (customer_id, shop_id, service_id, device_id, scheduled_at, status, problem_desc, total_price)
SELECT
  (SELECT id FROM users WHERE email = 'customer@fixandgo.com' LIMIT 1),
  1, 1,
  (SELECT id FROM devices WHERE brand = 'Samsung' AND model = 'Galaxy S22' LIMIT 1),
  DATE_ADD(NOW(), INTERVAL 1 DAY),
  'confirmed',
  'Screen is cracked on the bottom-left corner.',
  1500.00;

-- ============================================================
--  AUTO-CLEANUP EVENTS
--  Purges expired rows daily. Each event is a single statement
--  so it works in phpMyAdmin without DELIMITER changes.
--  Enable the scheduler once with:
--    SET GLOBAL event_scheduler = ON;
-- ============================================================
DROP EVENT IF EXISTS cleanup_otp_tokens;
CREATE EVENT cleanup_otp_tokens
  ON SCHEDULE EVERY 1 DAY
  STARTS CURRENT_TIMESTAMP
  DO DELETE FROM otp_tokens WHERE expires_at < NOW();

DROP EVENT IF EXISTS cleanup_remember_tokens;
CREATE EVENT cleanup_remember_tokens
  ON SCHEDULE EVERY 1 DAY
  STARTS CURRENT_TIMESTAMP
  DO DELETE FROM remember_tokens WHERE expires_at < NOW();

DROP EVENT IF EXISTS cleanup_rate_limits;
CREATE EVENT cleanup_rate_limits
  ON SCHEDULE EVERY 1 DAY
  STARTS CURRENT_TIMESTAMP
  DO DELETE FROM rate_limits WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 2 HOUR);

-- ──────────────────────────────────────────────────────────
-- migrate_admin.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
-- Fix&Go — Admin Migration
-- Adds 'admin' role to users table and creates the admin account.
-- NOTE: Run admin_setup.php instead — it does all of this automatically.
-- ============================================================

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

-- ──────────────────────────────────────────────────────────
-- migrate_login_attempts.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
--  Fix&Go — Login Attempt Tracking
--  Adds per-account lockout columns to the users table
-- ============================================================

ALTER TABLE users
  ADD COLUMN IF NOT EXISTS login_attempts  TINYINT UNSIGNED NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS locked_until    DATETIME         NULL DEFAULT NULL;

-- Index for fast lockout checks
CREATE INDEX IF NOT EXISTS idx_users_locked ON users (locked_until);

-- ──────────────────────────────────────────────────────────
-- migrate_login_logs.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
--  Fix&Go — Login / Logout Activity Log
--  Run this once to add the user_activity_logs table
-- ============================================================

CREATE TABLE IF NOT EXISTS user_activity_logs (
  id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  user_id     INT UNSIGNED    NOT NULL,
  action      ENUM('login','logout','session_expired','login_failed') NOT NULL,
  ip_address  VARCHAR(45)     NOT NULL DEFAULT '',
  user_agent  VARCHAR(512)    NOT NULL DEFAULT '',
  created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_user_action  (user_id, action),
  INDEX idx_created_at   (created_at),
  CONSTRAINT fk_ual_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Also add last_login_at column to users table for quick lookup
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS last_login_at  DATETIME NULL DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS last_logout_at DATETIME NULL DEFAULT NULL;

-- ──────────────────────────────────────────────────────────
-- migrate_seller_applications.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
-- Fix&Go — Seller Applications Table
-- Stores full application data submitted from Seller Centre
-- ============================================================

CREATE TABLE IF NOT EXISTS seller_applications (
  id               INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  user_id          INT UNSIGNED   NOT NULL,          -- the customer who applied
  role             ENUM('supplier','owner') NOT NULL,
  first_name       VARCHAR(50)    NOT NULL,
  last_name        VARCHAR(50)    NOT NULL,
  email            VARCHAR(255)   NOT NULL,          -- seller email (different from customer)
  phone            VARCHAR(20)    NOT NULL,
  company_name     VARCHAR(150)   NOT NULL,
  shop_name        VARCHAR(150)   NULL,              -- owner only
  -- Document paths (stored in uploads/applications/)
  doc_gov_id       VARCHAR(500)   NULL,
  doc_bir          VARCHAR(500)   NULL,
  doc_dti          VARCHAR(500)   NULL,              -- owner only
  doc_bank         VARCHAR(500)   NULL,
  status           ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  admin_notes      TEXT           NULL,
  reviewed_by      INT UNSIGNED   NULL,
  reviewed_at      DATETIME       NULL,
  submitted_at     DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_user   (user_id),
  INDEX idx_status (status),
  INDEX idx_role   (role),
  CONSTRAINT fk_sa_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────
-- migrate_technician_applications.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
-- Fix&Go — Technician Applications Table
-- Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

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

-- ──────────────────────────────────────────────────────────
-- migrate_add_technicians.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
--  Fix&Go — Migration: Add Phone Technician Support
--  Run this on an EXISTING fixandgo database.
--  Safe to run multiple times (uses IF NOT EXISTS / IGNORE).
-- ============================================================

-- ── Step 1: Add phone_technician to the users role ENUM ──────────────────
ALTER TABLE users
  MODIFY COLUMN role
    ENUM('customer','sales_person','supplier','supervisor','owner','phone_technician')
    NOT NULL DEFAULT 'customer';

-- ── Step 2: Fix users whose role is NULL (failed to insert before ENUM was updated) ──
UPDATE users
SET role = 'phone_technician'
WHERE email IN ('carlos@fixandgo.com', 'ana@fixandgo.com', 'marco@fixandgo.com')
  AND (role IS NULL OR role = '');

-- ── Step 3: Create technician_profiles table ─────────────────────────────
CREATE TABLE IF NOT EXISTS technician_profiles (
  id               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  user_id          INT UNSIGNED  NOT NULL,
  specialization   VARCHAR(150)  NULL,
  experience_years TINYINT       NOT NULL DEFAULT 0,
  bio              TEXT          NULL,
  certifications   VARCHAR(500)  NULL,
  availability     ENUM('available','busy','unavailable') NOT NULL DEFAULT 'available',
  rating_avg       DECIMAL(3,2)  NOT NULL DEFAULT 0.00,
  rating_count     INT UNSIGNED  NOT NULL DEFAULT 0,
  created_at       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
                                          ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_user (user_id),
  INDEX idx_availability (availability),
  CONSTRAINT fk_tp_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Step 4: Technician profiles for the sample users ─────────────────────
INSERT IGNORE INTO technician_profiles
  (user_id, specialization, experience_years, bio, availability)
SELECT id, 'Screen Repair, Battery Replacement', 3,
       'Experienced in all major brands. Fast and reliable service.', 'available'
FROM users WHERE email = 'carlos@fixandgo.com' LIMIT 1;

INSERT IGNORE INTO technician_profiles
  (user_id, specialization, experience_years, bio, availability)
SELECT id, 'Water Damage, Charging Port Repair', 5,
       'Specialist in liquid damage recovery and micro-soldering.', 'available'
FROM users WHERE email = 'ana@fixandgo.com' LIMIT 1;

INSERT IGNORE INTO technician_profiles
  (user_id, specialization, experience_years, bio, availability)
SELECT id, 'Software Troubleshooting, Screen Repair', 2,
       'Handles both hardware and software issues efficiently.', 'available'
FROM users WHERE email = 'marco@fixandgo.com' LIMIT 1;

-- ── Step 5: Also fix Juan Cruz (tech@fixandgo.com) if role is NULL ────────
UPDATE users
SET role = 'phone_technician'
WHERE email = 'tech@fixandgo.com'
  AND (role IS NULL OR role = '');

INSERT IGNORE INTO technician_profiles
  (user_id, specialization, experience_years, bio, availability)
SELECT id, 'General Phone Repair', 1, '', 'available'
FROM users WHERE email = 'tech@fixandgo.com' LIMIT 1;

-- ── Verify ────────────────────────────────────────────────────────────────
SELECT id, first_name, last_name, email, role, is_active, is_verified
FROM users
WHERE role = 'phone_technician';

SELECT 'Migration complete.' AS status;

-- ──────────────────────────────────────────────────────────
-- migrate_user_profile_columns.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
--  Fix&Go — Add profile columns to users table
--  Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

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

-- ──────────────────────────────────────────────────────────
-- migrate_technician_dashboard.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
-- Fix&Go — Technician Dashboard Migration
-- Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

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

-- ──────────────────────────────────────────────────────────
-- migrate_technician_supply_requests.sql
-- ──────────────────────────────────────────────────────────

-- Fix&Go — Technician Supply Requests Migration

CREATE TABLE IF NOT EXISTS technician_supply_requests (
  id                  INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  technician_id       INT UNSIGNED   NOT NULL,
  product_id          INT UNSIGNED   NOT NULL,
  supplier_id         INT UNSIGNED   NOT NULL,
  quantity_requested  INT            NOT NULL DEFAULT 1,
  note                TEXT           NULL,
  status              ENUM('pending','approved','rejected','fulfilled','cancelled') NOT NULL DEFAULT 'pending',
  supplier_notes      TEXT           NULL,
  created_at          DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at          DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_technician (technician_id),
  INDEX idx_supplier   (supplier_id),
  INDEX idx_product    (product_id),
  INDEX idx_status     (status),

  CONSTRAINT fk_tsr_tech     FOREIGN KEY (technician_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_tsr_supplier FOREIGN KEY (supplier_id)   REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_tsr_product  FOREIGN KEY (product_id)    REFERENCES supplier_products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'Technician supply requests migration complete.' AS status;

-- ──────────────────────────────────────────────────────────
-- migrate_messages.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
--  Fix&Go — Messages / Conversations Migration
--  Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

-- ── 1. CONVERSATIONS ─────────────────────────────────────────
-- One row per unique pair of users (e.g. sales_person ↔ customer)
CREATE TABLE IF NOT EXISTS conversations (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_a_id    INT UNSIGNED NOT NULL COMMENT 'lower user id',
  user_b_id    INT UNSIGNED NOT NULL COMMENT 'higher user id',
  created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_pair (user_a_id, user_b_id),
  INDEX idx_user_a (user_a_id),
  INDEX idx_user_b (user_b_id),
  CONSTRAINT fk_conv_a FOREIGN KEY (user_a_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_conv_b FOREIGN KEY (user_b_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 2. MESSAGES ──────────────────────────────────────────────
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

SELECT 'conversations and messages tables created.' AS status;

-- ──────────────────────────────────────────────────────────
-- migrate_fix_messages_columns.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
--  Fix&Go — Fix messages table column names
--  Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

-- Step 1: Drop foreign key on booking_id (if exists)

-- Drop the index that's blocking the column drop
ALTER TABLE messages DROP INDEX IF EXISTS idx_booking;

-- Step 2: Drop booking_id column
ALTER TABLE messages DROP COLUMN IF EXISTS booking_id;

-- Step 3: Rename sent_at → created_at (if sent_at exists)
-- We use a stored procedure trick since ALTER...IF EXISTS isn't in older MySQL
-- Check and rename via a safe approach:
ALTER TABLE messages 
  CHANGE COLUMN IF EXISTS sent_at created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

-- Step 4: Re-enable foreign key checks

-- Verify
DESCRIBE messages;

SELECT 'Migration complete.' AS status;

-- ──────────────────────────────────────────────────────────
-- migrate_message_attachments.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
--  Fix&Go — Add file attachment support to messages table
--  Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

-- Add attachment columns to messages table
ALTER TABLE messages
  ADD COLUMN IF NOT EXISTS file_url   VARCHAR(512) NULL DEFAULT NULL COMMENT 'Relative path to uploaded file',
  ADD COLUMN IF NOT EXISTS file_type  VARCHAR(20)  NULL DEFAULT NULL COMMENT 'image or video',
  ADD COLUMN IF NOT EXISTS file_name  VARCHAR(255) NULL DEFAULT NULL COMMENT 'Original filename';

-- Allow body to be empty (for media-only messages)
ALTER TABLE messages MODIFY COLUMN body TEXT NULL DEFAULT NULL;

-- Create uploads directory reference index
ALTER TABLE messages ADD INDEX IF NOT EXISTS idx_file_type (file_type);

SELECT 'Message attachment columns added.' AS status;
DESCRIBE messages;

-- ──────────────────────────────────────────────────────────
-- migrate_missing_columns.sql
-- ──────────────────────────────────────────────────────────

-- Fix&Go — Add missing optional columns safely
-- Run in phpMyAdmin: Import → select this file → Go

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

-- ──────────────────────────────────────────────────────────
-- migrate_sales_person.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
-- Fix&Go — Sales Person Migration
-- Run this after the main schema is set up
-- ============================================================

-- Sales person products (products they upload for customers to see)
CREATE TABLE IF NOT EXISTS sales_products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sales_person_id INT(10) UNSIGNED NOT NULL,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  category VARCHAR(100),
  price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  stock INT DEFAULT 0,
  image_path VARCHAR(500),
  is_active TINYINT(1) DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_sales_products_person (sales_person_id),
  INDEX idx_sales_products_active (is_active),
  CONSTRAINT fk_sales_products_user FOREIGN KEY (sales_person_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Supply requests from sales person to supervisor
CREATE TABLE IF NOT EXISTS supply_requests (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sales_person_id INT(10) UNSIGNED NOT NULL,
  product_name VARCHAR(255) NOT NULL,
  category VARCHAR(100),
  quantity_requested INT NOT NULL DEFAULT 1,
  reason TEXT,
  status ENUM('pending','approved','rejected') DEFAULT 'pending',
  supervisor_notes TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_supply_requests_person (sales_person_id),
  INDEX idx_supply_requests_status (status),
  CONSTRAINT fk_supply_requests_user FOREIGN KEY (sales_person_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────
-- migrate_owner_inventory.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
--  Fix&Go — Migration: Owner Inventory Table
--  Run in phpMyAdmin: Import → select this file → Go
--  
--  This table stores products that owners have purchased from
--  suppliers via PayMongo payments.
-- ============================================================

CREATE TABLE IF NOT EXISTS owner_inventory (
  id                INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  owner_id          INT UNSIGNED    NOT NULL,
  supplier_id       INT UNSIGNED    NOT NULL,
  supplier_product_id INT UNSIGNED  NOT NULL,  -- reference to supplier_products
  payment_id        INT UNSIGNED    NOT NULL,  -- reference to owner_payments
  
  -- Product details (snapshot at time of purchase)
  category          VARCHAR(100)    NOT NULL,
  brand             VARCHAR(100)    NULL,
  item_description  VARCHAR(255)    NOT NULL,
  qty               INT UNSIGNED    NOT NULL DEFAULT 1,
  unit_price        DECIMAL(10,2)   NOT NULL,  -- price per unit at purchase
  total_price       DECIMAL(10,2)   NOT NULL,  -- qty * unit_price
  image_path        VARCHAR(500)    NULL,
  notes             TEXT            NULL,
  
  purchased_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_at        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
                                             ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_owner       (owner_id),
  INDEX idx_supplier    (supplier_id),
  INDEX idx_payment     (payment_id),
  INDEX idx_product     (supplier_product_id),
  INDEX idx_category    (category),
  
  CONSTRAINT fk_oi_owner
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_oi_supplier
    FOREIGN KEY (supplier_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_oi_product
    FOREIGN KEY (supplier_product_id) REFERENCES supplier_products(id) ON DELETE CASCADE,
  CONSTRAINT fk_oi_payment
    FOREIGN KEY (payment_id) REFERENCES owner_payments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'owner_inventory table created successfully.' AS status;

-- ──────────────────────────────────────────────────────────
-- migrate_customer_orders.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
--  Fix&Go — Customer Orders & Reviews Migration
--  Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

-- ── 1. CUSTOMER ORDERS ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS customer_orders (
  id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  customer_id     INT UNSIGNED    NOT NULL,
  product_id      INT UNSIGNED    NOT NULL,
  quantity        INT UNSIGNED    NOT NULL DEFAULT 1,
  unit_price      DECIMAL(10,2)   NOT NULL,
  total_amount    DECIMAL(10,2)   NOT NULL,
  status          ENUM('pending','processing','completed','cancelled')
                                  NOT NULL DEFAULT 'pending',
  payment_method  ENUM('cod','gcash','card') NOT NULL DEFAULT 'cod',
  notes           TEXT            NULL,
  created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
                                           ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_customer  (customer_id),
  INDEX idx_product   (product_id),
  INDEX idx_status    (status),
  CONSTRAINT fk_co_customer
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_co_product
    FOREIGN KEY (product_id) REFERENCES supplier_products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 2. PRODUCT REVIEWS ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS product_reviews (
  id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  product_id  INT UNSIGNED    NOT NULL,
  customer_id INT UNSIGNED    NOT NULL,
  order_id    INT UNSIGNED    NULL,
  rating      TINYINT UNSIGNED NOT NULL DEFAULT 5,
  review_text TEXT            NULL,
  created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_pr_product  (product_id),
  INDEX idx_pr_customer (customer_id),
  CONSTRAINT fk_pr_product
    FOREIGN KEY (product_id) REFERENCES supplier_products(id) ON DELETE CASCADE,
  CONSTRAINT fk_pr_customer
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_pr_order
    FOREIGN KEY (order_id) REFERENCES customer_orders(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'customer_orders and product_reviews tables created.' AS status;

-- ──────────────────────────────────────────────────────────
-- migrate_customer_payments.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
--  Fix&Go — Customer Payments Migration
--  Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

CREATE TABLE IF NOT EXISTS customer_payments (
  id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  customer_id     INT UNSIGNED    NOT NULL,
  reference       VARCHAR(64)     NOT NULL UNIQUE,
  paymongo_id     VARCHAR(128)    NULL     COMMENT 'PayMongo checkout session ID',
  amount          DECIMAL(10,2)   NOT NULL COMMENT 'Total in PHP pesos',
  currency        CHAR(3)         NOT NULL DEFAULT 'PHP',
  status          ENUM('pending','paid','cancelled','failed')
                                  NOT NULL DEFAULT 'pending',
  payment_method  ENUM('gcash','card') NOT NULL DEFAULT 'gcash',
  checkout_url    TEXT            NULL,
  cart_snapshot   JSON            NULL     COMMENT 'Cart items at time of payment',
  paid_at         DATETIME        NULL,
  created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
                                           ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_cp_customer  (customer_id),
  INDEX idx_cp_reference (reference),
  INDEX idx_cp_status    (status),
  CONSTRAINT fk_cp_customer
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'customer_payments table created.' AS status;

-- ──────────────────────────────────────────────────────────
-- migrate_customer_address.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
--  Fix&Go — Customer Address Migration
--  Adds delivery address fields to the users table
-- ============================================================

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

-- ──────────────────────────────────────────────────────────
-- migrate_bookings_extended.sql
-- ──────────────────────────────────────────────────────────

-- Fix&Go — Extend bookings table with repair intake form fields

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

-- ──────────────────────────────────────────────────────────
-- migrate_shop_image.sql
-- ──────────────────────────────────────────────────────────

-- Fix&Go — Add shop_image to users and phone_photo to bookings

ALTER TABLE users
  ADD COLUMN IF NOT EXISTS shop_image VARCHAR(500) NULL AFTER profile_image;

ALTER TABLE bookings
  ADD COLUMN IF NOT EXISTS phone_photo VARCHAR(500) NULL AFTER expected_fix;

SELECT 'Shop image + phone photo migration complete.' AS status;

-- ──────────────────────────────────────────────────────────
-- migrate_marketplace_profile_columns.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
--  Fix&Go — Add marketplace profile columns to users table
--  Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

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

-- ──────────────────────────────────────────────────────────
-- migrate_technician_orders.sql
-- ──────────────────────────────────────────────────────────

-- Fix&Go — Technician Orders Migration

-- Technician orders (purchases from suppliers/owners)
CREATE TABLE IF NOT EXISTS technician_orders (
  id                  INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  technician_id       INT UNSIGNED   NOT NULL,
  seller_id           INT UNSIGNED   NOT NULL,          -- supplier or owner
  seller_role         ENUM('supplier','owner') NOT NULL DEFAULT 'supplier',
  fulfillment_type    ENUM('pickup','delivery') NOT NULL DEFAULT 'delivery',
  delivery_address    TEXT           NULL,              -- full address for delivery
  payment_method      ENUM('cod','gcash','card') NOT NULL DEFAULT 'cod',
  payment_status      ENUM('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  order_status        ENUM('pending','confirmed','preparing','ready','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  subtotal            DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
  shipping_fee        DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
  total_amount        DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
  reference           VARCHAR(100)   NULL,
  paymongo_id         VARCHAR(200)   NULL,
  checkout_url        TEXT           NULL,
  notes               TEXT           NULL,
  seller_notes        TEXT           NULL,
  created_at          DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at          DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_tech   (technician_id),
  INDEX idx_seller (seller_id),
  INDEX idx_status (order_status),
  INDEX idx_ref    (reference),

  CONSTRAINT fk_to_tech   FOREIGN KEY (technician_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_to_seller FOREIGN KEY (seller_id)     REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Technician order items
CREATE TABLE IF NOT EXISTS technician_order_items (
  id          INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  order_id    INT UNSIGNED   NOT NULL,
  product_id  INT UNSIGNED   NOT NULL,
  product_name VARCHAR(255)  NOT NULL,
  category    VARCHAR(100)   NULL,
  unit_price  DECIMAL(10,2)  NOT NULL,
  quantity    INT            NOT NULL DEFAULT 1,
  subtotal    DECIMAL(10,2)  NOT NULL,

  PRIMARY KEY (id),
  INDEX idx_order   (order_id),
  INDEX idx_product (product_id),

  CONSTRAINT fk_toi_order   FOREIGN KEY (order_id)   REFERENCES technician_orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_toi_product FOREIGN KEY (product_id) REFERENCES supplier_products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'Technician orders migration complete.' AS status;

-- ──────────────────────────────────────────────────────────
-- migrate_add_product_statuses.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
--  Fix&Go — Add Product Status Migration
--  Adds 'draft' and 'sent_to_supervisor' statuses to supplier_products
-- ============================================================

-- Add new status values to the ENUM
ALTER TABLE supplier_products
  MODIFY COLUMN status 
    ENUM('pending','verified','rejected','owner_received','draft','sent_to_supervisor','sent_to_sales_person')
    NOT NULL DEFAULT 'pending';

-- Update existing owner products to 'verified' if they don't have a status
UPDATE supplier_products sp
INNER JOIN users u ON sp.supplier_id = u.id
SET sp.status = 'verified'
WHERE u.role = 'owner' 
  AND sp.status = 'pending';

SELECT 'Migration completed successfully!' AS message;

-- ──────────────────────────────────────────────────────────
-- migrate_add_purchase_quantities.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
--  Fix&Go — Migration: Add purchase_quantities to owner_payments
--  Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

-- Add purchase_quantities column to store custom quantities per product
ALTER TABLE owner_payments
ADD COLUMN purchase_quantities JSON NULL COMMENT 'Custom quantities per product ID' 
AFTER product_ids;

SELECT 'purchase_quantities column added to owner_payments table.' AS status;

-- ──────────────────────────────────────────────────────────
-- migrate_product_transfers.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
--  Fix&Go — Product Transfer System Migration
--  Enables Owner → Supervisor → Sales Person product flow
-- ============================================================

-- ============================================================
--  1. PRODUCT TRANSFERS TABLE
--     Tracks product transfers between users in the hierarchy:
--     - Owner → Supervisor
--     - Supervisor → Sales Person
-- ============================================================
CREATE TABLE IF NOT EXISTS product_transfers (
  id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  product_id      INT UNSIGNED  NOT NULL,           -- from supplier_products
  from_user_id    INT UNSIGNED  NOT NULL,           -- sender (owner or supervisor)
  to_user_id      INT UNSIGNED  NOT NULL,           -- recipient (supervisor or sales_person)
  transfer_type   ENUM('owner_to_supervisor', 'supervisor_to_sales') NOT NULL,
  quantity        INT UNSIGNED  NOT NULL DEFAULT 1,
  status          ENUM('pending', 'accepted', 'rejected') NOT NULL DEFAULT 'pending',
  notes           TEXT          NULL,                -- optional transfer notes
  transferred_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  responded_at    DATETIME      NULL,                -- when recipient accepted/rejected
  
  PRIMARY KEY (id),
  INDEX idx_product       (product_id),
  INDEX idx_from_user     (from_user_id),
  INDEX idx_to_user       (to_user_id),
  INDEX idx_transfer_type (transfer_type),
  INDEX idx_status        (status),
  
  CONSTRAINT fk_transfer_product
    FOREIGN KEY (product_id) REFERENCES supplier_products(id) ON DELETE CASCADE,
  CONSTRAINT fk_transfer_from
    FOREIGN KEY (from_user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_transfer_to
    FOREIGN KEY (to_user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  2. ADD CURRENT HOLDER TRACKING TO SUPPLIER_PRODUCTS
--     Tracks who currently has the product in the chain
-- ============================================================
ALTER TABLE supplier_products 
  ADD COLUMN IF NOT EXISTS current_holder_id INT UNSIGNED NULL AFTER status,
  ADD COLUMN IF NOT EXISTS holder_type ENUM('owner', 'supervisor', 'sales_person') NULL AFTER current_holder_id;

-- Add foreign key for current_holder_id
ALTER TABLE supplier_products
  ADD CONSTRAINT fk_sp_current_holder
    FOREIGN KEY (current_holder_id) REFERENCES users(id) ON DELETE SET NULL;

-- Add index for faster queries
ALTER TABLE supplier_products
  ADD INDEX IF NOT EXISTS idx_current_holder (current_holder_id);

-- ============================================================
--  3. UPDATE EXISTING PRODUCTS
--     Set current_holder_id to owner_id for products that are 'owner_received'
-- ============================================================
UPDATE supplier_products sp
JOIN product_submissions ps ON ps.id = (
  SELECT si.submission_id 
  FROM submission_items si 
  WHERE si.product_id = sp.id 
  LIMIT 1
)
SET 
  sp.current_holder_id = ps.owner_id,
  sp.holder_type = 'owner'
WHERE sp.status = 'owner_received' 
  AND ps.owner_id IS NOT NULL
  AND sp.current_holder_id IS NULL;

-- ============================================================
--  4. ADD STAFF RELATIONSHIP TABLE
--     Links supervisors and sales persons to their owner
-- ============================================================
CREATE TABLE IF NOT EXISTS staff_assignments (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  owner_id    INT UNSIGNED NOT NULL,           -- the shop owner
  staff_id    INT UNSIGNED NOT NULL,           -- supervisor or sales_person
  staff_role  ENUM('supervisor', 'sales_person') NOT NULL,
  assigned_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  is_active   TINYINT(1)   NOT NULL DEFAULT 1,
  
  PRIMARY KEY (id),
  UNIQUE KEY uq_owner_staff (owner_id, staff_id),
  INDEX idx_owner (owner_id),
  INDEX idx_staff (staff_id),
  
  CONSTRAINT fk_staff_owner
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_staff_member
    FOREIGN KEY (staff_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  5. PRODUCT TRANSFER HISTORY
--     Audit trail of all product movements
-- ============================================================
CREATE TABLE IF NOT EXISTS product_transfer_history (
  id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  product_id      INT UNSIGNED  NOT NULL,
  from_user_id    INT UNSIGNED  NULL,              -- NULL if initial receipt from supplier
  to_user_id      INT UNSIGNED  NOT NULL,
  action          VARCHAR(50)   NOT NULL,          -- 'received_from_supplier', 'sent_to_supervisor', 'sent_to_sales', 'accepted', 'rejected'
  quantity        INT UNSIGNED  NOT NULL DEFAULT 1,
  notes           TEXT          NULL,
  created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (id),
  INDEX idx_product (product_id),
  INDEX idx_from_user (from_user_id),
  INDEX idx_to_user (to_user_id),
  
  CONSTRAINT fk_history_product
    FOREIGN KEY (product_id) REFERENCES supplier_products(id) ON DELETE CASCADE,
  CONSTRAINT fk_history_from
    FOREIGN KEY (from_user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_history_to
    FOREIGN KEY (to_user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  6. CREATE HISTORY ENTRIES FOR EXISTING PRODUCTS
--     Backfill history for products already received by owners
-- ============================================================
INSERT INTO product_transfer_history (product_id, from_user_id, to_user_id, action, quantity, notes)
SELECT 
  sp.id,
  ps.supplier_id,
  ps.owner_id,
  'received_from_supplier',
  sp.qty,
  'Migrated from existing product_submissions'
FROM supplier_products sp
JOIN submission_items si ON si.product_id = sp.id
JOIN product_submissions ps ON ps.id = si.submission_id
WHERE sp.status = 'owner_received' 
  AND ps.owner_id IS NOT NULL
  AND NOT EXISTS (
    SELECT 1 FROM product_transfer_history pth 
    WHERE pth.product_id = sp.id
  );

-- ============================================================
--  VERIFICATION QUERIES
-- ============================================================

-- Check product_transfers table
SELECT 'product_transfers table created' AS status, COUNT(*) AS row_count 
FROM product_transfers;

-- Check staff_assignments table
SELECT 'staff_assignments table created' AS status, COUNT(*) AS row_count 
FROM staff_assignments;

-- Check product_transfer_history table
SELECT 'product_transfer_history table created' AS status, COUNT(*) AS row_count 
FROM product_transfer_history;

-- Check updated supplier_products
SELECT 
  'supplier_products updated' AS status,
  COUNT(*) AS total_products,
  COUNT(current_holder_id) AS products_with_holder
FROM supplier_products;

-- Show sample of products with holders
SELECT 
  sp.id,
  sp.item_description,
  sp.status,
  sp.holder_type,
  u.email AS current_holder_email
FROM supplier_products sp
LEFT JOIN users u ON u.id = sp.current_holder_id
WHERE sp.current_holder_id IS NOT NULL
LIMIT 5;

-- ──────────────────────────────────────────────────────────
-- migrate_cancel_reason.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
--  Fix&Go — Add cancel_reason to customer_orders
--  Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

ALTER TABLE customer_orders
  ADD COLUMN IF NOT EXISTS cancel_reason VARCHAR(300) NULL
    COMMENT 'Customer-provided reason for cancellation'
    AFTER notes,
  ADD COLUMN IF NOT EXISTS cancel_notes VARCHAR(500) NULL
    COMMENT 'Additional notes provided by customer on cancellation'
    AFTER cancel_reason;

SELECT 'cancel_reason and cancel_notes columns added to customer_orders.' AS status;

-- ──────────────────────────────────────────────────────────
-- migrate_paymongo.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
--  Fix&Go — Migration: PayMongo Payments Table
--  Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

CREATE TABLE IF NOT EXISTS owner_payments (
  id            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  owner_id      INT UNSIGNED    NOT NULL,
  reference     VARCHAR(50)     NOT NULL,          -- e.g. FG-ABC123DEF4
  paymongo_id   VARCHAR(100)    NULL,              -- PayMongo checkout session ID
  amount        DECIMAL(12,2)   NOT NULL,          -- total in PHP
  currency      VARCHAR(3)      NOT NULL DEFAULT 'PHP',
  status        ENUM('pending','paid','failed','cancelled','refunded')
                                NOT NULL DEFAULT 'pending',
  checkout_url  VARCHAR(500)    NULL,              -- PayMongo hosted checkout URL
  product_ids   JSON            NULL,              -- array of supplier_product IDs purchased
  paid_at       DATETIME        NULL,
  created_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
                                         ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_reference (reference),
  INDEX idx_owner  (owner_id),
  INDEX idx_status (status),
  CONSTRAINT fk_op_owner
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'owner_payments table created.' AS status;

-- ──────────────────────────────────────────────────────────
-- migrate_document_approvals.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
-- Fix&Go — Document Approvals Table
-- Tracks per-document approval status for seller/technician applications
-- Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

CREATE TABLE IF NOT EXISTS document_approvals (
  id               INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  application_id   INT UNSIGNED   NOT NULL,
  document_type    VARCHAR(30)    NOT NULL,   -- gov_id | cert | bir | dti | bank
  status           ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  rejection_reason TEXT           NULL,
  reviewed_by      INT UNSIGNED   NULL,
  reviewed_at      DATETIME       NULL,
  created_at       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_app_doc (application_id, document_type),
  INDEX idx_application (application_id),
  INDEX idx_status (status),

  CONSTRAINT fk_da_application
    FOREIGN KEY (application_id) REFERENCES seller_applications(id) ON DELETE CASCADE,
  CONSTRAINT fk_da_reviewer
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Also ensure seller_applications has overall_status column
ALTER TABLE seller_applications
  ADD COLUMN IF NOT EXISTS overall_status
    ENUM('pending','docs_approved','approved','rejected') NOT NULL DEFAULT 'pending'
    AFTER status;

SELECT 'Document approvals migration complete.' AS status;

-- ──────────────────────────────────────────────────────────
-- migrate_supervisor_reports.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
--  Fix&Go — Supervisor Reports Table Migration
--  Creates table to store supervisor reports sent to owner
-- ============================================================

CREATE TABLE IF NOT EXISTS supervisor_reports (
  id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  supervisor_id   INT UNSIGNED    NOT NULL,
  owner_id        INT UNSIGNED    NOT NULL,
  report_year     INT             NOT NULL,
  report_month    INT             NOT NULL,
  total_products  INT             NOT NULL DEFAULT 0,
  total_quantity  INT             NOT NULL DEFAULT 0,
  total_value     DECIMAL(12,2)   NOT NULL DEFAULT 0.00,
  report_data     JSON            NULL COMMENT 'Full report data including products',
  sent_at         DATETIME        NULL,
  created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
                                           ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_supervisor (supervisor_id),
  INDEX idx_owner (owner_id),
  INDEX idx_period (report_year, report_month),
  UNIQUE KEY uq_supervisor_period (supervisor_id, owner_id, report_year, report_month),
  
  CONSTRAINT fk_sr_supervisor
    FOREIGN KEY (supervisor_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_sr_owner
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'supervisor_reports table created successfully!' AS status;

-- ──────────────────────────────────────────────────────────
-- migrate_sales_display.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
-- Fix&Go — Sales Person Display Flag Migration
-- Adds is_displayed column so sales person can choose which
-- products from their inventory to show to customers
-- ============================================================

ALTER TABLE supplier_products
  ADD COLUMN IF NOT EXISTS is_displayed TINYINT(1) NOT NULL DEFAULT 0
  COMMENT '1 = sales person chose to display this to customers';

CREATE INDEX IF NOT EXISTS idx_sp_displayed ON supplier_products(is_displayed);

-- ──────────────────────────────────────────────────────────
-- migrate_technician_credentials.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
--  Fix&Go — Technician Credentials & Documents Table
--  Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

CREATE TABLE IF NOT EXISTS technician_credentials (
  id              INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  technician_id   INT UNSIGNED   NOT NULL,
  doc_type        VARCHAR(30)    NOT NULL COMMENT 'gov_id | bir | tech_cert | bank | dti | skill_cert | nstp | tesda | custom',
  label           VARCHAR(120)   NOT NULL COMMENT 'Display label shown to customers',
  file_url        VARCHAR(512)   NOT NULL COMMENT 'Relative path to uploaded file',
  file_name       VARCHAR(255)   NOT NULL COMMENT 'Original file name',
  file_ext        VARCHAR(10)    NOT NULL COMMENT 'pdf | jpg | png | etc.',
  is_image        TINYINT(1)     NOT NULL DEFAULT 0,
  display_order   TINYINT        NOT NULL DEFAULT 0,
  created_at      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_tech  (technician_id),
  INDEX idx_type  (doc_type),

  CONSTRAINT fk_tc_tech
    FOREIGN KEY (technician_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'technician_credentials table created.' AS status;
DESCRIBE technician_credentials;

-- ──────────────────────────────────────────────────────────
-- migrate_technician_credentials_v2.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
--  Fix&Go — Update technician_credentials table
--  Adds: is_video column, shop_images & work_video types
--  Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

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

-- ──────────────────────────────────────────────────────────
-- migrate_technician_reviews.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
--  Fix&Go — Technician Reviews Table
--  Run in phpMyAdmin: Import → select this file → Go
-- ============================================================

CREATE TABLE IF NOT EXISTS technician_reviews (
  id              INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  booking_id      INT UNSIGNED   NOT NULL,
  technician_id   INT UNSIGNED   NOT NULL,
  customer_id     INT UNSIGNED   NOT NULL,
  rating          TINYINT        NOT NULL COMMENT '1–5 stars',
  comment         TEXT           NULL,
  media_1_url     VARCHAR(512)   NULL COMMENT 'Photo or video URL (proof of repair)',
  media_1_type    VARCHAR(10)    NULL COMMENT 'image or video',
  media_2_url     VARCHAR(512)   NULL,
  media_2_type    VARCHAR(10)    NULL,
  media_3_url     VARCHAR(512)   NULL,
  media_3_type    VARCHAR(10)    NULL,
  created_at      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE  KEY uq_booking_review (booking_id),
  INDEX   idx_technician (technician_id),
  INDEX   idx_customer   (customer_id),
  INDEX   idx_rating     (rating),

  CONSTRAINT fk_tr_booking
    FOREIGN KEY (booking_id)    REFERENCES bookings(id)  ON DELETE CASCADE,
  CONSTRAINT fk_tr_technician
    FOREIGN KEY (technician_id) REFERENCES users(id)     ON DELETE CASCADE,
  CONSTRAINT fk_tr_customer
    FOREIGN KEY (customer_id)   REFERENCES users(id)     ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add constraint check
ALTER TABLE technician_reviews
  ADD CONSTRAINT chk_rating CHECK (rating BETWEEN 1 AND 5);

SELECT 'technician_reviews table created.' AS status;
DESCRIBE technician_reviews;

-- ──────────────────────────────────────────────────────────
-- complete_database_setup.sql
-- ──────────────────────────────────────────────────────────

-- ============================================================
--  Fix&Go — Complete Database Setup
--  Includes: Core tables + Supplier Products + Owner Inventory + Staff Management
--  Engine : InnoDB | Charset : utf8mb4 (full Unicode + emoji)
--  Run    : mysql -u root -p < complete_database_setup.sql
--           OR import via phpMyAdmin → Import tab
-- ============================================================

CREATE DATABASE IF NOT EXISTS fixandgo
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- ============================================================
--  1. USERS
--     Stores all account types: customer, sales_person, supplier,
--     supervisor, owner, phone_technician.
--     password_hash is NULL for Google-only accounts.
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
  id            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  first_name    VARCHAR(50)     NOT NULL,
  last_name     VARCHAR(50)     NOT NULL,
  email         VARCHAR(255)    NOT NULL,
  phone         VARCHAR(20)     NULL,
  password_hash VARCHAR(255)    NULL,                          -- NULL = OAuth-only
  role          ENUM('customer','sales_person','supplier','supervisor','owner','phone_technician')
                                NOT NULL DEFAULT 'customer',
  provider      ENUM('local','google') NOT NULL DEFAULT 'local',
  provider_id   VARCHAR(255)    NULL,                          -- Google sub ID
  avatar_url    VARCHAR(500)    NULL,
  is_verified   TINYINT(1)      NOT NULL DEFAULT 0,
  is_active     TINYINT(1)      NOT NULL DEFAULT 1,            -- soft-disable account
  created_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
                                         ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE  KEY uq_email          (email),
  INDEX        idx_role          (role),
  INDEX        idx_provider_id   (provider_id),
  INDEX        idx_is_active     (is_active),
  INDEX        idx_is_verified   (is_verified)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  2. OTP TOKENS
--     6-digit codes for email verification & password reset.
--     Stored as bcrypt hashes — never plaintext.
-- ============================================================
CREATE TABLE IF NOT EXISTS otp_tokens (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id    INT UNSIGNED NOT NULL,
  otp_hash   VARCHAR(255) NOT NULL,
  purpose    ENUM('verify','reset','login') NOT NULL DEFAULT 'verify',
  expires_at DATETIME     NOT NULL,
  attempts   TINYINT      NOT NULL DEFAULT 0,
  created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_user_purpose (user_id, purpose),
  CONSTRAINT fk_otp_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  3. REMEMBER ME TOKENS
--     SHA-256 hashed tokens stored in httpOnly cookies.
-- ============================================================
CREATE TABLE IF NOT EXISTS remember_tokens (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id    INT UNSIGNED NOT NULL,
  token_hash VARCHAR(64)  NOT NULL,
  expires_at DATETIME     NOT NULL,
  created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_token   (token_hash),
  INDEX      idx_user   (user_id),
  CONSTRAINT fk_remember_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  4. RATE LIMITS
--     Per-IP attempt tracking for login, register, OTP resend.
-- ============================================================
CREATE TABLE IF NOT EXISTS rate_limits (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  identifier   VARCHAR(45)  NOT NULL,   -- IPv4 or IPv6 address
  action       VARCHAR(50)  NOT NULL,   -- 'login' | 'register' | 'resend_otp'
  attempted_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_identifier_action (identifier, action, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  5. SHOPS
--     A shop is owned by a user with role = 'owner'.
--     Technicians and customers are linked to shops via
--     shop_members and bookings respectively.
-- ============================================================
CREATE TABLE IF NOT EXISTS shops (
  id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  owner_id     INT UNSIGNED  NOT NULL,
  name         VARCHAR(100)  NOT NULL,
  description  TEXT          NULL,
  address      VARCHAR(255)  NULL,
  city         VARCHAR(100)  NULL,
  phone        VARCHAR(20)   NULL,
  email        VARCHAR(255)  NULL,
  logo_url     VARCHAR(500)  NULL,
  is_active    TINYINT(1)    NOT NULL DEFAULT 1,
  created_at   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
                                      ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_owner   (owner_id),
  INDEX idx_city    (city),
  CONSTRAINT fk_shop_owner
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  6. SHOP MEMBERS
--     Links technicians to a shop (many-to-one).
-- ============================================================
CREATE TABLE IF NOT EXISTS shop_members (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  shop_id      INT UNSIGNED NOT NULL,
  user_id      INT UNSIGNED NOT NULL,   -- must have role = 'technician'
  joined_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_shop_user (shop_id, user_id),
  CONSTRAINT fk_member_shop
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
  CONSTRAINT fk_member_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  7. TECHNICIAN PROFILES
--     Extended profile info for phone_technician users.
--     One row per technician — created automatically on registration
--     or filled in later via the profile page.
-- ============================================================
CREATE TABLE IF NOT EXISTS technician_profiles (
  id               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  user_id          INT UNSIGNED  NOT NULL,
  specialization   VARCHAR(150)  NULL,    -- e.g. "Screen Repair, Water Damage"
  experience_years TINYINT       NOT NULL DEFAULT 0,
  bio              TEXT          NULL,
  certifications   VARCHAR(500)  NULL,    -- comma-separated or JSON string
  availability     ENUM('available','busy','unavailable') NOT NULL DEFAULT 'available',
  rating_avg       DECIMAL(3,2)  NOT NULL DEFAULT 0.00,  -- cached average rating
  rating_count     INT UNSIGNED  NOT NULL DEFAULT 0,
  created_at       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
                                          ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_user (user_id),
  INDEX idx_availability (availability),
  CONSTRAINT fk_tp_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  8. SERVICES
--     Repair services offered by a shop (e.g. Screen Repair).
-- ============================================================
CREATE TABLE IF NOT EXISTS services (
  id           INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  shop_id      INT UNSIGNED   NOT NULL,
  name         VARCHAR(100)   NOT NULL,
  description  TEXT           NULL,
  price        DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
  duration_min INT            NOT NULL DEFAULT 60,   -- estimated minutes
  is_active    TINYINT(1)     NOT NULL DEFAULT 1,
  created_at   DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_shop (shop_id),
  CONSTRAINT fk_service_shop
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  9. DEVICES
--     Customer-owned devices that can be booked for repair.
-- ============================================================
CREATE TABLE IF NOT EXISTS devices (
  id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  customer_id  INT UNSIGNED  NOT NULL,
  brand        VARCHAR(50)   NOT NULL,   -- e.g. Apple, Samsung
  model        VARCHAR(100)  NOT NULL,   -- e.g. iPhone 14 Pro
  serial_no    VARCHAR(100)  NULL,
  color        VARCHAR(50)   NULL,
  notes        TEXT          NULL,
  created_at   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_customer (customer_id),
  CONSTRAINT fk_device_customer
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  10. BOOKINGS
--     A customer books a service for a device at a shop.
--     Optionally assigned to a specific technician.
-- ============================================================
CREATE TABLE IF NOT EXISTS bookings (
  id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  customer_id     INT UNSIGNED  NOT NULL,
  shop_id         INT UNSIGNED  NOT NULL,
  service_id      INT UNSIGNED  NOT NULL,
  device_id       INT UNSIGNED  NULL,                -- optional
  technician_id   INT UNSIGNED  NULL,                -- assigned after booking
  scheduled_at    DATETIME      NOT NULL,
  status          ENUM(
                    'pending',      -- just booked, awaiting confirmation
                    'confirmed',    -- shop confirmed
                    'in_progress',  -- technician working on it
                    'completed',    -- repair done
                    'cancelled'     -- cancelled by customer or shop
                  )             NOT NULL DEFAULT 'pending',
  problem_desc    TEXT          NULL,                -- customer's description
  technician_notes TEXT         NULL,                -- internal notes
  total_price     DECIMAL(10,2) NULL,
  created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
                                         ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_customer    (customer_id),
  INDEX idx_shop        (shop_id),
  INDEX idx_technician  (technician_id),
  INDEX idx_status      (status),
  INDEX idx_scheduled   (scheduled_at),
  CONSTRAINT fk_booking_customer
    FOREIGN KEY (customer_id)   REFERENCES users(id)    ON DELETE CASCADE,
  CONSTRAINT fk_booking_shop
    FOREIGN KEY (shop_id)       REFERENCES shops(id)    ON DELETE CASCADE,
  CONSTRAINT fk_booking_service
    FOREIGN KEY (service_id)    REFERENCES services(id) ON DELETE RESTRICT,
  CONSTRAINT fk_booking_device
    FOREIGN KEY (device_id)     REFERENCES devices(id)  ON DELETE SET NULL,
  CONSTRAINT fk_booking_tech
    FOREIGN KEY (technician_id) REFERENCES users(id)    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  11. BOOKING STATUS HISTORY
--      Audit trail of every status change on a booking.
-- ============================================================
CREATE TABLE IF NOT EXISTS booking_status_history (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  booking_id  INT UNSIGNED NOT NULL,
  changed_by  INT UNSIGNED NOT NULL,   -- user who made the change
  old_status  VARCHAR(20)  NOT NULL,
  new_status  VARCHAR(20)  NOT NULL,
  note        TEXT         NULL,
  changed_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_booking (booking_id),
  CONSTRAINT fk_history_booking
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
  CONSTRAINT fk_history_user
    FOREIGN KEY (changed_by) REFERENCES users(id)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  12. REVIEWS
--      Customer reviews a completed booking (1 per booking).
-- ============================================================
CREATE TABLE IF NOT EXISTS reviews (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  booking_id  INT UNSIGNED NOT NULL,
  customer_id INT UNSIGNED NOT NULL,
  shop_id     INT UNSIGNED NOT NULL,
  rating      TINYINT      NOT NULL CHECK (rating BETWEEN 1 AND 5),
  comment     TEXT         NULL,
  created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_booking_review (booking_id),   -- one review per booking
  INDEX idx_shop   (shop_id),
  INDEX idx_rating (rating),
  CONSTRAINT fk_review_booking
    FOREIGN KEY (booking_id)  REFERENCES bookings(id) ON DELETE CASCADE,
  CONSTRAINT fk_review_customer
    FOREIGN KEY (customer_id) REFERENCES users(id)    ON DELETE CASCADE,
  CONSTRAINT fk_review_shop
    FOREIGN KEY (shop_id)     REFERENCES shops(id)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  13. MESSAGES
--      In-app chat between customer and technician/shop.
-- ============================================================
CREATE TABLE IF NOT EXISTS messages (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  booking_id  INT UNSIGNED NOT NULL,
  sender_id   INT UNSIGNED NOT NULL,
  body        TEXT         NOT NULL,
  is_read     TINYINT(1)   NOT NULL DEFAULT 0,
  sent_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_booking (booking_id),
  INDEX idx_sender  (sender_id),
  CONSTRAINT fk_msg_booking
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
  CONSTRAINT fk_msg_sender
    FOREIGN KEY (sender_id)  REFERENCES users(id)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  14. NOTIFICATIONS
--      System notifications sent to users (booking updates, etc.)
-- ============================================================
CREATE TABLE IF NOT EXISTS notifications (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id     INT UNSIGNED NOT NULL,
  type        VARCHAR(50)  NOT NULL,   -- 'booking_confirmed', 'otp', etc.
  title       VARCHAR(150) NOT NULL,
  body        TEXT         NOT NULL,
  is_read     TINYINT(1)   NOT NULL DEFAULT 0,
  created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_user_read (user_id, is_read),
  CONSTRAINT fk_notif_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  15. PROMOTIONS
--      Discount codes or offers created by shop owners.
-- ============================================================
CREATE TABLE IF NOT EXISTS promotions (
  id           INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  shop_id      INT UNSIGNED   NOT NULL,
  code         VARCHAR(30)    NOT NULL,
  description  VARCHAR(255)   NULL,
  discount_pct DECIMAL(5,2)   NOT NULL DEFAULT 0.00,  -- percentage off
  valid_from   DATETIME       NOT NULL,
  valid_until  DATETIME       NOT NULL,
  max_uses     INT            NULL,                    -- NULL = unlimited
  used_count   INT            NOT NULL DEFAULT 0,
  is_active    TINYINT(1)     NOT NULL DEFAULT 1,
  created_at   DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_shop_code (shop_id, code),
  INDEX idx_shop (shop_id),
  CONSTRAINT fk_promo_shop
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  16. PAYMENTS (PayMongo Integration)
--      Payment record per transaction.
-- ============================================================
CREATE TABLE IF NOT EXISTS payments (
  id                  INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  user_id             INT UNSIGNED   NOT NULL,
  amount              DECIMAL(10,2)  NOT NULL,
  currency            VARCHAR(3)     NOT NULL DEFAULT 'PHP',
  status              ENUM('pending','paid','failed','refunded')
                                     NOT NULL DEFAULT 'pending',
  payment_intent_id   VARCHAR(255)   NULL,   -- PayMongo payment intent ID
  payment_method_id   VARCHAR(255)   NULL,   -- PayMongo payment method ID
  checkout_url        TEXT           NULL,   -- PayMongo checkout URL
  metadata            TEXT           NULL,   -- JSON metadata (cart items, etc.)
  paid_at             DATETIME       NULL,
  created_at          DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at          DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP
                                              ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_user   (user_id),
  INDEX idx_status (status),
  INDEX idx_payment_intent (payment_intent_id),
  CONSTRAINT fk_payment_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  17. SUPPLIER PRODUCTS
--      Products submitted by suppliers AND products owned by shop owners.
--      When owner buys from supplier, products are copied with owner as supplier_id.
-- ============================================================
CREATE TABLE IF NOT EXISTS supplier_products (
  id                INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  supplier_id       INT UNSIGNED   NOT NULL,   -- user_id (role=supplier OR owner)
  name              VARCHAR(150)   NOT NULL,
  description       TEXT           NULL,
  category          VARCHAR(50)    NULL,
  price             DECIMAL(10,2)  NOT NULL,
  stock_quantity    INT            NOT NULL DEFAULT 0,
  image_path        VARCHAR(255)   NULL,
  status            ENUM('pending','verified','rejected','owner_received','draft','sent_to_supervisor')
                                   NOT NULL DEFAULT 'pending',
  rejection_reason  TEXT           NULL,
  submitted_at      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  reviewed_at       DATETIME       NULL,
  updated_at        DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP
                                            ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_supplier (supplier_id),
  INDEX idx_status   (status),
  INDEX idx_category (category),
  CONSTRAINT fk_sp_supplier
    FOREIGN KEY (supplier_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  18. OWNER INVENTORY (Optional Purchase Tracking)
--      Tracks owner purchases from suppliers for history/reporting.
--      The actual products go into supplier_products with owner as supplier_id.
-- ============================================================
CREATE TABLE IF NOT EXISTS owner_inventory (
  id                INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  owner_id          INT UNSIGNED   NOT NULL,
  payment_id        INT UNSIGNED   NOT NULL,
  supplier_id       INT UNSIGNED   NOT NULL,   -- original supplier
  product_id        INT UNSIGNED   NOT NULL,   -- original supplier_products.id
  product_name      VARCHAR(150)   NOT NULL,
  quantity          INT            NOT NULL,
  unit_price        DECIMAL(10,2)  NOT NULL,
  total_price       DECIMAL(10,2)  NOT NULL,
  purchased_at      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  INDEX idx_owner    (owner_id),
  INDEX idx_payment  (payment_id),
  INDEX idx_supplier (supplier_id),
  CONSTRAINT fk_oi_owner
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_oi_payment
    FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE,
  CONSTRAINT fk_oi_supplier
    FOREIGN KEY (supplier_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  AUTO-CLEANUP EVENTS
--  Purges expired rows daily. Each event is a single statement
--  so it works in phpMyAdmin without DELIMITER changes.
--  Enable the scheduler once with:
--    SET GLOBAL event_scheduler = ON;
-- ============================================================
DROP EVENT IF EXISTS cleanup_otp_tokens;
CREATE EVENT cleanup_otp_tokens
  ON SCHEDULE EVERY 1 DAY
  STARTS CURRENT_TIMESTAMP
  DO DELETE FROM otp_tokens WHERE expires_at < NOW();

DROP EVENT IF EXISTS cleanup_remember_tokens;
CREATE EVENT cleanup_remember_tokens
  ON SCHEDULE EVERY 1 DAY
  STARTS CURRENT_TIMESTAMP
  DO DELETE FROM remember_tokens WHERE expires_at < NOW();

DROP EVENT IF EXISTS cleanup_rate_limits;
CREATE EVENT cleanup_rate_limits
  ON SCHEDULE EVERY 1 DAY
  STARTS CURRENT_TIMESTAMP
  DO DELETE FROM rate_limits WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 2 HOUR);

-- ============================================================
--  SAMPLE DATA — remove before going live
--  Password for all sample accounts: Password1
--  Hash: $2y$12$0fxHhMJKgJ2atMnINeil3eDBYbozoxI85xI1HmbDSn213l10tN.3a
-- ============================================================

-- Sample owner
INSERT IGNORE INTO users
  (first_name, last_name, email, phone, password_hash, role, is_verified, is_active)
VALUES
  ('Admin', 'Owner', 'owner@fixandgo.com', '+63 912 345 6789',
   '$2y$12$0fxHhMJKgJ2atMnINeil3eDBYbozoxI85xI1HmbDSn213l10tN.3a',
   'owner', 1, 1);

-- Sample supplier
INSERT IGNORE INTO users
  (first_name, last_name, email, phone, password_hash, role, is_verified, is_active)
VALUES
  ('John', 'Supplier', 'supplier@fixandgo.com', '+63 912 345 6790',
   '$2y$12$0fxHhMJKgJ2atMnINeil3eDBYbozoxI85xI1HmbDSn213l10tN.3a',
   'supplier', 1, 1);

-- Sample phone technicians
INSERT IGNORE INTO users
  (first_name, last_name, email, phone, password_hash, role, is_verified, is_active)
VALUES
  ('Carlos', 'Reyes', 'carlos@fixandgo.com', '+63 912 345 6791',
   '$2y$12$0fxHhMJKgJ2atMnINeil3eDBYbozoxI85xI1HmbDSn213l10tN.3a',
   'phone_technician', 1, 1),
  ('Ana', 'Dela Cruz', 'ana@fixandgo.com', '+63 912 345 6792',
   '$2y$12$0fxHhMJKgJ2atMnINeil3eDBYbozoxI85xI1HmbDSn213l10tN.3a',
   'phone_technician', 1, 1),
  ('Marco', 'Santos', 'marco@fixandgo.com', '+63 912 345 6793',
   '$2y$12$0fxHhMJKgJ2atMnINeil3eDBYbozoxI85xI1HmbDSn213l10tN.3a',
   'phone_technician', 1, 1);

-- Technician profiles (must run AFTER technician_profiles table is created above)
INSERT IGNORE INTO technician_profiles
  (user_id, specialization, experience_years, bio, availability)
SELECT id, 'Screen Repair, Battery Replacement', 3,
       'Experienced in all major brands. Fast and reliable service.', 'available'
FROM users WHERE email = 'carlos@fixandgo.com' LIMIT 1;

INSERT IGNORE INTO technician_profiles
  (user_id, specialization, experience_years, bio, availability)
SELECT id, 'Water Damage, Charging Port Repair', 5,
       'Specialist in liquid damage recovery and micro-soldering.', 'available'
FROM users WHERE email = 'ana@fixandgo.com' LIMIT 1;

INSERT IGNORE INTO technician_profiles
  (user_id, specialization, experience_years, bio, availability)
SELECT id, 'Software Troubleshooting, Screen Repair', 2,
       'Handles both hardware and software issues efficiently.', 'available'
FROM users WHERE email = 'marco@fixandgo.com' LIMIT 1;

-- Sample customer
INSERT IGNORE INTO users
  (first_name, last_name, email, phone, password_hash, role, is_verified, is_active)
VALUES
  ('Maria', 'Santos', 'customer@fixandgo.com', '+63 912 345 6796',
   '$2y$12$0fxHhMJKgJ2atMnINeil3eDBYbozoxI85xI1HmbDSn213l10tN.3a',
   'customer', 1, 1);

-- Sample shop (owned by the owner above)
INSERT IGNORE INTO shops
  (owner_id, name, description, address, city, phone, email)
SELECT id, 'Fix&Go Main Shop',
       'Fast and reliable phone repairs in the city center.',
       '123 Repair Street', 'Manila', '+63 912 345 6789', 'shop@fixandgo.com'
FROM users WHERE email = 'owner@fixandgo.com' LIMIT 1;

-- Sample services (shop id=1)
INSERT IGNORE INTO services (shop_id, name, description, price, duration_min) VALUES
  (1, 'Screen Replacement',   'Replace cracked or broken screens.',         1500.00, 60),
  (1, 'Battery Replacement',  'Replace old or swollen batteries.',           800.00, 45),
  (1, 'Charging Port Repair', 'Fix loose or non-functional charging ports.', 600.00, 30),
  (1, 'Water Damage Repair',  'Clean and restore water-damaged phones.',    2000.00, 120),
  (1, 'Speaker/Mic Repair',   'Fix audio issues on any device.',             700.00, 45);

-- Sample device for customer
INSERT IGNORE INTO devices (customer_id, brand, model, color)
SELECT id, 'Samsung', 'Galaxy S22', 'Phantom Black'
FROM users WHERE email = 'customer@fixandgo.com' LIMIT 1;

-- Sample supplier products
-- Get supplier_id first, then insert products
SET @supplier_id = (SELECT id FROM users WHERE email = 'supplier@fixandgo.com' LIMIT 1);

INSERT IGNORE INTO supplier_products
  (supplier_id, name, description, category, price, stock_quantity, status)
VALUES
  (@supplier_id, 'iPhone 14 Pro Screen', 'Original OLED display for iPhone 14 Pro', 'Screens', 8500.00, 50, 'verified'),
  (@supplier_id, 'Samsung Galaxy S23 Battery', 'High-capacity replacement battery', 'Batteries', 1200.00, 100, 'verified'),
  (@supplier_id, 'USB-C Charging Cable', 'Fast charging cable 1.5m', 'Accessories', 250.00, 200, 'verified');

-- ============================================================
--  SETUP COMPLETE
--  Next steps:
--  1. Enable event scheduler: SET GLOBAL event_scheduler = ON;
--  2. Update backend/config.php with your database credentials
--  3. Test login with sample accounts (password: Password1)
-- ============================================================

-- ──────────────────────────────────────────────────────────
-- FINAL
-- ──────────────────────────────────────────────────────────

SET FOREIGN_KEY_CHECKS = 1;

SELECT 'FixAndGo database setup complete. All tables created.' AS status;
