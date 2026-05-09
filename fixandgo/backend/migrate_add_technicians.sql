-- ============================================================
--  Fix&Go — Migration: Add Phone Technician Support
--  Run this on an EXISTING fixandgo database.
--  Safe to run multiple times (uses IF NOT EXISTS / IGNORE).
-- ============================================================

USE fixandgo;

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
