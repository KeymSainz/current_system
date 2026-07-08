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

USE fixandgo;

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
